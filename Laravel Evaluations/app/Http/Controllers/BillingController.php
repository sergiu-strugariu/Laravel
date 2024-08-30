<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Paper;
use App\Models\Project;
use App\Services\SmartBillService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class BillingController extends Controller
{
    /**
     * @var SmartBillService
     */
    private $smartBillService;

    /**
     * BillingController constructor.
     * @param SmartBillService $smartBillService
     */
    public function __construct(SmartBillService $smartBillService)
    {
        $this->smartBillService = $smartBillService;
    }

    public function index()
    {
        $clients = Client::whereHas('projects', function($q) {
            $q->where("default_bill_client", 1);
        })->get()->sortBy("name")->pluck('id', 'name');

        return view('billing.index', array(
            "clients" => $clients->flip()
        ));
    }

    public function getClientProjects($id, Request $request)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonth(1);

        $client = Client::find($id);
        $date = $request->get("date");

        if ($date) {
            $dateParts = explode(" - ", $date);
            $startDate = Carbon::createFromFormat("d-m-Y H:i:s", $dateParts[0] . " 00:00:00");
            $endDate = Carbon::createFromFormat("d-m-Y H:i:s", $dateParts[1] . " 00:00:00")->addDays(1);
        }

        /**
         * Paper query - can be optimized further
         * @TODO: select only needed columns
         * @param $paper
         */
        $paperFilter = function($paper) use($startDate, $endDate) {
            $paper
                ->with(['report' => function($q) use($startDate, $endDate) {
                    $q->where("created_at", ">=", $startDate)
                        ->where("created_at", "<", $endDate);
                }])->whereHas('report', function($q) use($startDate, $endDate) {
                    $q->where("created_at", ">=", $startDate)
                        ->where("created_at", "<", $endDate);
                });
        };

        /**
         * Task query - can be optimized further
         * @TODO: select only needed columns
         * @param $task
         */
        $taskFilter = function($task) use ($paperFilter, $startDate, $endDate) {
            $task
                ->with(['papers' => $paperFilter])
                ->where(function($q) use ($paperFilter) {
                    $q->whereHas('papers', $paperFilter);
                });
        };

        $projects = Project::where('client_id', $id)
            ->where("default_bill_client", 1)
            ->with(['tasks' => $taskFilter])
            ->whereHas('tasks', $taskFilter)
            ->get();

        return [
            "client" => $client,
            "projects" => $projects,
        ];
    }

    public function generateInvoice($id, Request $request)
    {
        $startDate = Carbon::now()->subMonth(1);
        $endDate = Carbon::now();

        $client = Client::find($id);
        $date = $request->get("date");

        if ($date) {
            $dateParts = explode(" - ", $date);
            $startDate = Carbon::createFromFormat("d-m-Y H:i:s", $dateParts[0] . " 00:00:00");
            $endDate = Carbon::createFromFormat("d-m-Y H:i:s", $dateParts[1] . " 00:00:00")->addDays(1);
        }

        $ids = $request->get('ids');
        $isDraft = $request->get('draft', false);

        if (!$ids) {
            return ajaxResponse(ERROR, 'No ids sent');
        }

        $paperFilter = function($paper) use($startDate, $endDate) {
            $paper
                ->with(['report' => function($q) use($startDate, $endDate) {
                    $q->where("created_at", ">=", $startDate)
                    ->where("created_at", "<", $endDate);
            }])->whereHas('report', function($q) use($startDate, $endDate) {
                $q->where("created_at", ">=", $startDate)
                    ->where("created_at", "<", $endDate);
            });
        };

        $taskFilter = function($task) use ($paperFilter, $startDate, $endDate) {
            $task
                ->with(['papers' => $paperFilter])
                ->where(function($q) use ($paperFilter) {
                    $q->whereHas('papers', $paperFilter);
                });
        };

        $projects = Project::where('client_id', $id)
            ->whereIn("id", $ids)
            ->where("default_bill_client", 1)
            ->with(['tasks' => $taskFilter])
            ->whereHas('tasks', $taskFilter)
            ->get();

        $invoices = [];
        $response = [];
        $billedPapers = [];
        foreach ($projects as $project) {
            $invoiceId = $project->billing_distinct ? "p_" . $project->id : "c_" . $project->client_id;
            $billingData = $project->billing_distinct ? $project : $client;

            if (!isset($invoices[$invoiceId])) {
                $invoices[$invoiceId] = [
                    'billing_contract_date' => Carbon::createFromFormat("d/m/Y", $billingData['billing_contract_date'])->format("d-m-Y"),
                    'billing_contract_no' => $billingData['billing_contract_no'],
                    'billing_capital' => $billingData['billing_capital'],
                    'billing_bank' => $billingData['billing_bank'],
                    'billing_iban' => $billingData['billing_iban'],
                    'billing_address' => $billingData['billing_address'],
                    'billing_cif' => $billingData['billing_cif'],
                    'billing_registry' => $billingData['billing_registry'],
                    'billing_company_name' => $billingData['billing_company_name'],
                    'products' => [],
                    'project' => $project
                ];
            }

            if (!isset($invoices[$invoiceId]['products'][$project->id])) {
                $invoices[$invoiceId]['products'][$project->id] = [
                    'billed_paper_ids' => [],
                    'value' => 0,
                    'annex' => $project->billing_contract_annex,
                    'annex_date' => Carbon::createFromFormat("d/m/Y", $project->billing_contract_annex_date)->format("d-m-Y")
                ];
            }

            foreach ($project['tasks'] as $task) {
                foreach ($task['papers'] as $paper) {
                    if (!$paper['invoice_id']) {
                        $invoices[$invoiceId]['products'][$project->id]['value'] += $paper['cost'];
                        $invoices[$invoiceId]['products'][$project->id]['billed_paper_ids'][] = $paper['id'];

                        if ($paper['paper_type_id'] == TEST_SPEAKING) {
                            $invoices[$invoiceId]['products'][$project->id]['value'] += $task["custom_period_cost"];
                        }
                    }
                }
            }

        }

        foreach ($invoices as $invoiceData) {
            $invoice = array(
                'client' 		=> array(
                    'name' 			=> $invoiceData['billing_company_name'],
                    'vatCode' 		=> $invoiceData['billing_cif'],
                    'regCom' 		=> $invoiceData['billing_registry'],
                    'address' 		=> $invoiceData['billing_address'],
                    'bank' 		    => $invoiceData['billing_bank'],
                    'iban' 		    => $invoiceData['billing_iban'],
                    'isTaxPayer' 	=> false,
                    'city' 			=> "--",
                    'country' 		=> "--",
                    'email' 		=> "--",
                ),
                'currency' => 'EUR',
                'issueDate' 	=> date('Y-m-d'),
                'isDraft' 		=> false,
                'mentions' 		=> "",
                'observations' 	=> "",
                'products' 		=> []
            );

            $billedPapers = [];
            foreach ($invoiceData['products'] as $code => $product) {
                $invoice['products'][] = array(
                    'name' 				=> "Contravaloare prestari servicii de evaluare lingvistica, perioada {$startDate->format("d-m-Y")} - {$endDate->subDay(1)->format("d-m-Y")} , conform anexa nr. {$product['annex']}/{$product['annex_date']} la contract nr. {$invoiceData['billing_contract_no']} / {$invoiceData['billing_contract_date']}",
                    'code' 				=> $code,
                    'isDiscount' 		=> false,
                    'measuringUnitName' => "project",
                    'currency' 			=> "EUR",
                    'quantity' 			=> 1,
                    'price' 			=> $product['value'],
                    'isTaxIncluded' 	=> false,
                    'taxName' 			=> "Normala",
                    'taxPercentage' 	=> 19,
                    'isService' 		=> true,
                );

                $billedPapers = array_merge($billedPapers, $product['billed_paper_ids']);
            }

            $invoiceResponse = $this->smartBillService->createInvoice($invoice);


            if (!$isDraft) {
                $project = $invoiceData['project'];
                $filename = $invoiceResponse['series'] . '_' . $invoiceResponse['number'] . '_' . date("Y_m_d_H_i_s", time()) . '.pdf';
                $pdfData = $this->smartBillService->PDFInvoice($invoiceResponse['series'], $invoiceResponse['number']);

                // Save file to public storage
                Storage::disk('local')->put("invoices/" . $filename, $pdfData);

                // Create invoice entry in DB
                $invoice = new Invoice();
                $invoice->name = $invoiceResponse['series'] . " " . $invoiceResponse['number'];
                $invoice->file = $filename;
                $invoice->client_id = $client->id;
                $invoice->project_id = $project->id;
                $invoice->date_from = $startDate;
                $invoice->date_to = $endDate;
                $invoice->save();

                $q = Paper::query()
                    ->whereIn("id", $billedPapers)
                    ->update([
                        "invoice_id" => $invoice->id
                    ]);
            }

            $response[] = $invoiceResponse;
        }

        if ($isDraft) {
            $data = $response[0];

            $filename = $data['series'] . '_' . $data['number'] . '_' . date("Y_m_d_H_i_s",time()) . '.pdf';

            $pdfData = $this->smartBillService->PDFInvoice($data['series'], $data['number']);
            try {
                $this->smartBillService->deleteInvoice($data['series'], $data['number']);
            } catch (Exception $e) {
                // Leave it alone
            }

            return response()->make($pdfData, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"'
            ]);
        }

        return [
            "data" => $invoices,
            "invoices" => $response
        ];
    }

    public function getBillingInformation(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        if (!$type || !$request) {
            return ['errorMsg' => "Please send the correct information!"];
        }

        $data = $type == "client" ? Client::find($id) : Project::find($id);

        if ($type == "client" || ($type == "project" && $data->billing_distinct)) {
            $fields = [
                'billing' => [
                    'Company Name' => $data->billing_company_name,
                    'Registrul Comertului' => $data->billing_registry,
                    'CIF' => $data->billing_cif,
                    'Adresa' => $data->billing_address,
                    'IBAN' => $data->billing_iban,
                    'Banca' => $data->billing_bank,
                    'Capital Social' => $data->billing_capital,
                    'Contract nr' => $data->billing_contract_no,
                    'Data Contract' => $data->billing_contract_date,
                ]
            ];
        }

        if ($type == "project") {
            $fields['annex'] = [
                "Numar Anexa" => $data->billing_contract_annex,
                "Data Anexa" => $data->billing_contract_annex_date,

            ];
        }

        return $fields;
    }
}