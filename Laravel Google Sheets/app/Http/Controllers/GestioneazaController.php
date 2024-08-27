<?php

namespace App\Http\Controllers;

use App\Models\Sheets;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GestioneazaController extends Controller
{
    public function index(Request $request)
    {
        $concurs = Sheets::getSheetRowByValue('Concursuri', $request->id);
        $participanti = Sheets::getSheetRowWhere('Inscrieri', 'Nume Concurs', $concurs['Nume']);
        $manse = Sheets::getSheetRowWhere('Manse', 'ID Concurs', $concurs['id']);


        return Inertia::render('Dashboard/Gestionare/GestioneazaConcursuri', [
            'concurs' => $concurs,
            'participanti' => $participanti,
            'manse' => $manse
        ]);
    }

    public function updateParticipant(Request $request)
    {
        $stand = Sheets::getSheetRowByValue("Standuri", $request->stand_id);

        $user = $request->user;

        $data = [
            $user['id'],
            $user['ID Masa'],
            $user['ID Pescar'],
            $user['Status Inscriere'],
            $user['Nume Concurs'],
            $user['Nume Mansa'],
            $user['Nume Pescar'],
            $stand['id'],
            $request->sector,
            $user['Nume Lac'],
            $stand['Nume Stand'],
            $request->puncte_penalizare,
            $request->nume_trofeu,
            date('Y-m-d H:i:s'),
            $user['created_at'],
        ];

        Sheets::edit('Inscrieri', intval($user['id']) + 1, $data);

        return;
    }

    public function getStanduri(Request $request)
    {
        $lacuri = Sheets::getSheetRowWhere('Standuri', 'Nume Lac', $request->numeLac);
        return $lacuri;
    }

    public function getParticipant(Request $request)
    {
        $participant = Sheets::getSheetRowWhere('Inscrieri', 'ID Pescar', $request->id);
        return $participant[$request->id];
    }
}
