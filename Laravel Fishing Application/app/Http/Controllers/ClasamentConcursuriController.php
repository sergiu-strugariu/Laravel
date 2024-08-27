<?php

namespace App\Http\Controllers;

use App\Models\AlocareStand;
use App\Models\Cantar;
use App\Models\Concurs;
use App\Models\Sector;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClasamentConcursuriController extends Controller
{
    public function view()
    {
        $concursuri = Concurs::all();
        $concursuri_all = Concurs::all();

        $names = array_column($concursuri->toArray(), 'nume');

        return Inertia::render('Clasament/Asociaza', [
            'concursuri' => array_slice($names, 0, 1000),
            'concursuri_all' => $concursuri_all,
        ]);
    }

    public function get(Request $request)
    {
        $concursuri = array_column(Concurs::all()->toArray(), 'nume');
        $concursuri_all = Concurs::all();
        $concurs = Concurs::where('nume', $request->concurs)->get()->first();

        $data = [];

        foreach ( Sector::all() as $sector) {
            $sectorData = [];
            $alocati = AlocareStand::where(['concurs_id'=> $concurs->id, "sector_id"=> $sector->id])
            ->orderBy('stand_id')
            ->distinct('stand_id')
            ->get("stand_id"); 

            foreach ($alocati as $alocare) {
                $sectorData[] = Cantar::where(['stand_id'=> $alocare->stand_id, "concurs_id"=> $concurs->id])->get();
            }
        
            $data[$sector->nume] = $sectorData;
        }

        dd($data);
        return Inertia::render('Clasament/Asociaza', [
            'concursuri' => array_slice($concursuri, 0, 1000),
            'concursuri_all' => $concursuri_all,
        ]);
    }

    // public function get(Request $request)
    // {
    //     $concursuri = array_column(Concurs::all()->toArray(), 'nume');
    //     $concursuri_all = Concurs::all();

    //     $concurs = Concurs::where('nume', $request->concurs)->get()->first();

    //     if (!$concurs) {
    //         return Inertia::render("Clasament/Asociaza", [
    //             'concursuri' => array_slice($concursuri, 0, 1000),
    //             'concursuri_all' => $concursuri_all,
    //             "warning" => [
    //                 "message" => "Concursul nu a fost gasit, te rugam sa selectezi unul din lista.",
    //                 "status" => false
    //             ]
    //         ]);
    //     }

    //     $cantarire = Cantar::where('concurs_id', $concurs->id)->get()->first();

    //     if (!$cantarire) {
    //         return Inertia::render("Clasament/Asociaza", [
    //             'concursuri' => array_slice($concursuri, 0, 1000),
    //             'concursuri_all' => $concursuri_all,
    //             "warning" => [
    //                 "message" => "Cantarirea concursului nu a fost facuta inca.",
    //                 "status" => false
    //             ]
    //         ]);
    //     }

    //     $alocare = AlocareStand::where('concurs_id', $concurs->id)->get()->first();

    //     $data = [
    //         'concurs' => $concurs,
    //         'sector' => Sector::where('id', $alocare->sector_id)->get()->first(),
    //         'stand' => Stand::where('id', $cantarire->stand_id)->get()->first(),
    //         'pescar' => User::where('id', $alocare->pescar_id)->get()->first(),
    //         'cantarire' => $cantarire->cantitate
    //     ];

    //     dd($data);

    //     return Inertia::render('Clasament/Asociaza', [
    //         'concursuri' => array_slice($concursuri, 0, 1000),
    //         'concursuri_all' => $concursuri_all,
    //     ]);
    // }
}
