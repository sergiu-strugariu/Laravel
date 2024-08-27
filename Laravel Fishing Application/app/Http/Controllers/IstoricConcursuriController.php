<?php

namespace App\Http\Controllers;

use App\Models\Palmares;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IstoricConcursuriController extends Controller
{
    public function index()
    {
        $palmares = Palmares::all();
        $names = array_column($palmares->toArray(), 'pescar');
        $unique_names = array_unique($names);

        return Inertia::render('Palmares/Asociaza', [
            'concursuri' => array_slice($unique_names, 0, 1000)
        ]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $palmares_selected = $request->conturi_asociere;

        $matchedUsers = [];

        foreach ($palmares_selected as $p_selected) {
            $palmares = Palmares::where('pescar', $p_selected)->get();

            foreach ($palmares as $p) {
                if ($p->assigned_to === null) {
                    $p->assigned_to = $user->id;
                    $p->save();
                } else {
                    $usersAssigned = User::whereIn('id', [$p->assigned_to])->get();
                    foreach ($usersAssigned as $assignedUser) {
                        $matchedUsers[$assignedUser->id] = $assignedUser->prenume . ' ' . $assignedUser->nume;
                    }
                }
            }
        }

        if (!empty($matchedUsers)) {
            $warningMessage = "Palmaresul selectat de tine este deja asociat la urmatorii utilizatori: " . implode(', ', $matchedUsers);
            return Inertia::render("Palmares/Asociaza", [
                "warning" => [
                    'message' => $warningMessage,
                    'status' => true,
                ]
            ]);
        }

        return to_route('vizualizeaza');
    }

    public  function view()
    {
        if (Auth::check()) {
            $palmares = Auth::user()->palmares;

            return Inertia::render('Palmares/Vizualizeaza', [
                'istoric' => $palmares
            ]);
        }

        return redirect('/');
    }
}
