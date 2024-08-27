<?php

namespace App\Http\Controllers;

use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IstoricConcursuriController extends Controller
{
    public function index()
    {
        $concursuri = Sheets::get('Concursuri 2023');

        $pescarNames = array_column($concursuri, 'Pescar');
        $uniquePescarNames = array_unique($pescarNames);

        return Inertia::render('IstoricConcursuri/Asociaza', [
            'concursuri' => array_slice($uniquePescarNames, 0, 1000)
        ]);
    }

    public function search(Request $request)
    {
        $user = Verifications::GetUserAuth($request);

        $utilizatori = Sheets::get('Utilizatori');

        if (!$request->conturi_asociere) {
            return Inertia::render("IstoricConcursuri/Asociaza", [
                "warning" => [
                    'message' => "Te rog să cauți numele în listă.",
                    'status' => false,
                ]
            ]);
        }

        $matchedUsers = [];
        
        foreach ($utilizatori as $index => $utilizator) {
            $istoric = $utilizator['Istoric Asociere'];
        
            $conturiExistente = array_filter($request->conturi_asociere, function ($cont) use ($istoric) {
                return strpos($istoric, $cont) !== false;
            });
        
            if (count($conturiExistente) > 0) {
                $matchedUsers[] = $utilizator;
            }
        }

        if (count($matchedUsers) > 0) {
            return Inertia::render("IstoricConcursuri/Asociaza", [
                "warning" => [
                    'message' => "Palmaresul selectat de tine este deja asociat la urmatorii utilizatori: " . implode(', ', array_map(function($user) {
                        return $user['Prenume'] . ' ' . $user['Nume'];
                    }, $matchedUsers)),
                    'status' => true,
                ]
            ]);
        }

        $istoricUser = explode(',', $user['Istoric Asociere']);
        $updatedConturiAsociere =  $request->conturi_asociere;

        $updatedIstoric =  implode(',', array_unique(array_merge($istoricUser, $updatedConturiAsociere)));

        $update = [
            $user['id'],
            $user['Prenume'],
            $user['Nume'],
            $user['Tip'],
            $user['Email'],
            $user['Mobil'],
            $user['Data Nasterii'],
            $user['Sex'],
            $user['Google ID'],
            $updatedIstoric,
            date('Y-m-d H:i:s'),
            $user['created_at']
        ];

        Sheets::edit('Utilizatori', $user['id'] + 1, $update);

        return to_route('vizualizeaza');
    }


    public function view(Request $request)
    {
        $user = Verifications::GetUserAuth($request);
        $asocieri = explode(",", $user['Istoric Asociere']);
        $combinedIstoric = [];

        foreach ($asocieri as $name) {
            $istoric = Sheets::getSheetRowWhere('Concursuri 2023', 'Pescar', $name);

            if (!empty($istoric)) {
                $combinedIstoric = array_merge($combinedIstoric, $istoric);
            }
        }

        return Inertia::render("IstoricConcursuri/Vizualizeaza", [
            'istoric' => $combinedIstoric
        ]);
    }
}
