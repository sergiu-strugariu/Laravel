<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StandController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nume_stand' => ['required', 'string', 'max:255'],
            'lac' => ['required', 'string', 'max:255'],
        ]);

        $lac = Sheets::getSheetRowByValue('Lacuri', $request->lac);

        $data = [
            Sheets::count("Standuri"),
            $request->nume_stand,
            $lac['id'],
            $lac['Nume'],
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ];

        $query = Sheets::insert('Standuri', $data);

        if (!$query) {
            return Inertia::render('Dashboard/Dashboard', [
                "warning" => [
                    'message' => "A intervenit o erroare la salvarea concursului, te rugam incearca mai tarziu.",
                    'status' => false
                ]
            ]);
        }

        return Redirect::route('dashboard');
    }
}
