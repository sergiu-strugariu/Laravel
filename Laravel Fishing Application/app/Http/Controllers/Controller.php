<?php

namespace App\Http\Controllers;

use App\Models\Lac;
use App\Models\Palmares;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return Inertia::render('Home');
    }

    public function import()
    {
        return Inertia::render('Import');
    }

    public function storeCSV(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            dd('No file was uploaded');
        }

        $filePath = $file->getPathname();

        $fileContents = file($filePath);

        foreach ($fileContents as $line) {
            $data = str_getcsv($line);

            Palmares::create([
                'assigned_to' => null,
                'luna' => $data[0],
                'an' => $data[1],
                'organizator' => $data[2],
                'pescar' => $data[3],
                'data_concurs' => $data[4],
                'lac' => $data[5],
                'nume_concurs' => $data[6],
                'mansa' => $data[7],
                'stand' => $data[8],
                'cantitate' => $data[9],
                'puncte' => $data[10],
                'loc_sector' => $data[11],
                'loc_general' => $data[12],
            ]);
        }

//        foreach ($fileContents as $line) {
//            $data = str_getcsv($line);
//
//            Concursuri2024::create([
//                'timestamp' => $data[0],
//                'observatii' => $data[1],
//                'lac' => $data[2],
//                'tip_concurs' => $data[3],
//                'data_inceput' => $data[4],
//                'data_inchidere' => $data[5],
//                'organizator' => $data[6],
//                'telefon_organizator' => $data[7],
//                'email_organizator' => $data[8],
//                'nume_concurs' => $data[9],
//                'link' => $data[10],
//                'modalitate_organizare' => $data[11],
//            ]);
//        }

        dd('Success');
    }
}
