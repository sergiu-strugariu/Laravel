<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Models\Verifications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $concursuri = Sheets::get('Concursuri');
        $lacuri = Sheets::get("Lacuri");

        return Inertia::render('Dashboard/Dashboard', [
            'concursuri' => $concursuri,
            'lacuri' => $lacuri,
        ]);
    }
}
