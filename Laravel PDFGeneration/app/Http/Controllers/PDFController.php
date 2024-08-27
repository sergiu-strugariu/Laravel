<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function report(Request $request)
    {
        $param = $request->input('param');

        $users = User::all();

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdfgeneration', compact('users', 'param'));
        return $pdf->stream('users.pdf');
    }
}
