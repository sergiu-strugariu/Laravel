<?php

namespace App\Http\Controllers\Concursuri;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcursuriController extends Controller
{
    public function index()
    {
        $concursuri = Sheets::get('Concursuri');

        if ($concursuri) {
            foreach ($concursuri as $concurs) {
                $manse = Sheets::getSheetRowWhere("Manse", "ID Concurs", $concurs['id']);
            }
    
            foreach ($manse as $mansa) {
                $this->addParticipant($mansa);
            }
        }

        return Inertia::render('Concursuri', [
            'concursuri' => $concursuri
        ]);
    }

    public function inscriere(Request $request)
    {
        $user = Verifications::GetUserAuth($request);

        foreach ($request->manse as $mansa) {
            $data = [
                Sheets::count("Inscrieri"),
                $mansa['id'],
                $user['id'],
                $mansa['Status Mansa'],
                $request->concurs['Nume'],
                $mansa['Nume Mansa'],
                $user['Nume'] . ' ' . $user['Prenume'],
                '',
                '',
                $mansa['Nume Lac'],
                '',
                '',
                '',
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ];

            Sheets::insert("Inscrieri", $data);
        }

        return true;
    }

    public function addParticipant($mansa)
    {
        if (!Sheets::count('Inscrieri')) {
            return;
        }

        $update = [
            $mansa['id'],
            $mansa['ID Concurs'],
            $mansa['ID Lac'],
            $mansa['Nume Concurs'],
            $mansa['Nume Lac'],
            $mansa['Nume Mansa'],
            $mansa['Start Mansa'],
            $mansa['Final Mansa'],
            $mansa['Status Mansa'],
            Sheets::countWhere('Inscrieri', 'ID Masa', intval($mansa['id'])),
            $mansa['Participanti Maximi'],
            date('Y-m-d H:i:s'),
            $mansa['created_at'],
        ];

        return Sheets::edit('Manse', intval($mansa['id']) + 1, $update);
    }

    public function getMansaDetails(Request $request)
    {
        $mansa = Sheets::getSheetRowWhere('Manse', "id", $request->id);
        return $mansa[$request->id];
    }

    public function getConcurs(Request $request)
    {
        $concurs = Sheets::getSheetRowByValue("Concursuri", $request->id);
        $manse = Sheets::getSheetRowWhere("Manse", "ID Concurs", $concurs['id']);

        return [
            'concurs' => $concurs,
            'manse' => $manse
        ];
    }

    public function concursuri2024()
    {
        $concursuri2024 = Sheets::get('Calendar 2024');
        
        return Inertia::render('Concursuri2024', [
            'concursuri' => $concursuri2024
        ]);
    }
}
