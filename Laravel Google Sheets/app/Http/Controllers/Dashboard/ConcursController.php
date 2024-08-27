<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ConcursController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'string'],
            'nume_concurs' => ['required', 'string', 'max:255'],
            'descriere' => ['required', 'string', 'max:255'],
            'reguli' => ['required', 'string', 'max:255'],
            'data_inceperii' => ['required', 'string', 'max:255', 'after_or_equal:' . now()->addMinute()->toDateString()],
            'data_inchiderii' => ['required', 'string', 'max:255', 'after_or_equal:data_inceperii'],
        ]);
        
        $user = Verifications::GetUserAuth($request);

        $saveImage = Verifications::Base64ToImage($request);

        if (!$saveImage) {
            return Inertia::render('Dashboard/Dashboard', [
                "warning" => [
                    'message' => "A intervenit o erroare la salvarea imaginei, te rugam incearca mai tarziu.",
                    'status' => false
                ]
            ]);
        }

        $data = [
            Sheets::count("Concursuri"),
            $request->nume_concurs,
            $user['id'],
            $user['Prenume'] . ' ' . $user['Nume'],
            $request->descriere,
            $request->reguli,
            date('d-m-Y H:i:s', strtotime($request->data_inceperii)),
            date('d-m-Y H:i:s', strtotime($request->data_inchiderii)),
            $saveImage,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ];

        $query = Sheets::insert('Concursuri', $data);

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
