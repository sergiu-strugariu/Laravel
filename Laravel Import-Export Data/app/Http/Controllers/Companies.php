<?php

namespace App\Http\Controllers;

use App\Exports\CompanyExport;
use App\Imports\CompanyImport;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel as ExcelCSV;
use Maatwebsite\Excel\Facades\Excel;

class Companies extends Controller
{
    public function index()
    {
        return Inertia::render("Import", [
            'companies' => Company::all()
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv'],
        ]);

        $import = Excel::import(new CompanyImport, $request->file);

        if (!$import) {
            return Inertia::render("Import", [
                'message' => "Error!",
            ]);
        }

        $array = Excel::toArray(new CompanyImport, $request->file);

        return Inertia::render("Import", [
            'message' => "Success!",
            'companies' =>  $array[0]
        ]);
    }

    public function export()
    {
        return Excel::download(new CompanyExport, 'companies.csv', ExcelCSV::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
