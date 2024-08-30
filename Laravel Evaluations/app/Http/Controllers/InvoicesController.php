<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Paper;
use App\Models\PaperType;
use App\Models\Project;
use App\Models\Task;
use App\Services\SmartBillService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use Yajra\DataTables\DataTables;


class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()->get()->sortBy("name")->pluck('id', 'name');
        $projects = Project::query()->get()->sortBy("name")->pluck('id', 'name');

        return view("invoices.index", compact('clients', 'projects'));
    }

    public function viewFile($fileName, Request $request)
    {
        $pdfData = Storage::disk('local')->get("invoices/" . $fileName);

        return response()->make($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"'
        ]);
    }

    public function getAllInvoices(Request $request)
    {
        $stmt = Invoice::query()->with(['client', 'project']);
        if ($request->has('filter')) {
            $filters = $request->get('filter');
            foreach ($filters as $name => $value) {
                switch ($name) {
                    case "project_id":
                    case "client_id":
                        $stmt->where($name, $value);
                        break;
                    case "billed_date":
                        $values = explode(" - ", $value);
                        $startDate = Carbon::createFromFormat("m/d/Y H:i:s", $values[0] . " 00:00:00");
                        $endDate = Carbon::createFromFormat("m/d/Y H:i:s", $values[1] . " 00:00:00")->addDays(1);

                        $stmt->whereBetween('created_at', [
                            $startDate,
                            $endDate,
                        ]);
                        break;
                    case 'billed_period':
                        $values = explode(" - ", $value);
                        $stmt->where(function($q) use ($values) {
                            $q->whereBetween('date_from', [
                                date('Y-m-d H:i:s', strtotime($values[0])),
                                date('Y-m-d H:i:s', strtotime($values[1])),
                            ])->orWhereBetween('date_to', [
                                date('Y-m-d H:i:s', strtotime($values[0])),
                                date('Y-m-d H:i:s', strtotime($values[1])),
                            ]);
                        });

                        break;
                }
            }

        }

        return Datatables::of($stmt)->make(true);
    }

    function exportAnnex($id, Request $request)
    {
        $paperTypes = PaperType::select('id', 'name')->get()->toArray();
        $invoice = Invoice::query()->where('id', $id)->firstOrFail();

        $headers = [
            "id" => "Task Id",
            "name" => "Name",
            "language" => "Language",
        ];

        // Make the test types dynamic so we can add new ones
        foreach ($paperTypes as $paperType) {
            $headers["test_" . $paperType['id']] = $paperType['name'];
            $headers["test_" . $paperType['id'] . "_date"] = $paperType['name'] . " date";
        }

        $headers['added_by'] = "Added By";
        $headers['created_at'] = "Date Added";
        $headers['bill_client'] = "Bill Client";
        $headers['total'] = "Total";

        $papers = Paper::query()
            ->with('report')
            ->where('invoice_id', $id)
            ->get();

        $papersByTask = [];

        foreach ($papers as $paper) {
            if (empty($papersByTask[$paper->task_id])) {
                $papersByTask[$paper->task_id] = [];
            }

            $papersByTask[$paper->task_id][] = $paper;
        }

        $paperIds = $papers->pluck('id')->toArray();

        $tasks = Task::select('id', 'custom_period_cost', 'name', 'created_at', 'added_by_id', 'language_id', 'project_id')
            ->with('language')
            ->with('addedBy')
            ->with('project')
            ->whereHas('papers', function($q) use ($paperIds) {
                $q->whereIn('id', $paperIds);
            })
            ->get()
            ->toArray();

        $results = [];
        $grandTotal = 0;
        foreach ($tasks as $task) {
            $newRow = [];
            $total = 0;
            foreach ($headers as $headerId => $headerName) {
                if ($headerId == "bill_client") {
                    $newRow[$headerName] = (!!$task['project']['default_bill_client'] ? "Yes" : "No");
                } else if ($headerId == "added_by") {
                    $newRow[$headerName] = $task['added_by']['first_name'] . " " . $task['added_by']['last_name'];
                } else if ($headerId == "language") {
                    $newRow[$headerName] = $task['language']['name'];
                } else if (!empty($task[$headerId])) {
                    $newRow[$headerName] = $task[$headerId];
                } else {
                    // Loop to get all of the tests
                    foreach ($papersByTask[$task['id']] as $paper) {
                        if ($headerId == "test_" . $paper->paper_type_id) {

                            // Add the cost to the excel and to the row total
                            $newRow[$headerName] = $paper->cost;
                            $total += $paper->cost;

                            // If it is a speaking test, it might have a custom period cost
                            if ($paper->paper_type_id == TEST_SPEAKING) {
                                $total += $task['custom_period_cost'];
                            }
                        } else if ($headerId == "test_" . $paper->paper_type_id . "_date") {
                            $newRow[$headerName] = $paper->report->created_at->format("Y-m-d");
                        }
                    };
                }

                if (empty($newRow[$headerName])) {
                    $newRow[$headerName] = "";
                }
            }
            $newRow[$headers['total']] = $total;
            $grandTotal += $total;

            // Push new row to results
            $results[] = $newRow;
        }

        for ($x = 0; $x < 2; $x++) {
            $newEmptyRow = [];
            foreach ($headers as $headerId => $headerName) {
                $newEmptyRow[$headerName] = "";
                if ($x == 1 && $headerId == "total") {
                    $newEmptyRow[$headerName] = $grandTotal;
                }
            }

            $results[] = $newEmptyRow;
        }


        return Excel::create(str_replace(" ", "-", $invoice->name),
            function ($excel) use ($results) {
                $excel->sheet('Tasks', function ($sheet) use ($results) {
                    $sheet->setOrientation('portrait');
                    $sheet->fromArray($results);
                });

            })->download("xlsx");
    }
}