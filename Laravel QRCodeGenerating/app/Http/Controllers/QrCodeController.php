<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $url = $request->url;
        $size = $request->size ?? 100;
    
        return Inertia::render("Home", [
            'qrcode' => QrCode::size($size)->generate($url)->__toString()
        ]);
    }
}
