<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MansaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nume_mansa' => ['required', 'string', 'max:255'],
            'concurs' => ['required', 'string', 'max:255'],
            'lac' => ['required', 'string', 'max:255'],
            'status_mansa' => ['required', 'string', 'max:255'],
            'numar_participanti' => ['required', 'integer'],
            // 'data_inceperii' => ['required', 'string', 'max:255', 'after_or_equal:' . now()->addMinute()->toDateString()],
            // 'data_inchiderii' => ['required', 'string', 'max:255', 'after_or_equal:' . now()->addMinute()->toDateString()],
        ]);

        $concurs = Sheets::getSheetRowByValue('Concursuri', $request->concurs);
        $lac = Sheets::getSheetRowByValue('Lacuri', $request->lac);

        $data = [
            Sheets::count("Manse"),
            $concurs['id'],
            $lac['id'],
            $concurs['Nume'],
            $lac['Nume'],
            $request->nume_mansa,
            'start',
            'stop',
            $request->status_mansa,
            0,
            $request->numar_participanti,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ];

        $query = Sheets::insert('Manse', $data);

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
