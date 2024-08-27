<?php

namespace App\Http\Controllers\Autentificare;

use App\Http\Controllers\Controller;
use App\Mail\LoginEmailFishArena;
use App\Mail\RegisterEmailFishArena;
use App\Models\Sheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AuthentificationController extends Controller
{
    public function index()
    {
        $result = trim(str_replace(url('/'), '', strtok(url()->previous(), '?')), '/');

        if ($result === 'inregistrare') {
            return Inertia::render("Auth/Autentificare", [
                "warning" => [
                    'message' => "Cont inregistrat cu succes, te poti loga acum.",
                    'status' => true,
                ]
            ]);
        }

        return Inertia::render("Auth/Autentificare");
    }

    public function autentificare(Request $request)
    {
        
        $email = strtolower($request->email);

        $users = Sheets::get("Utilizatori");

        $user = null;

        foreach ($users as $u) {
            if (strtolower($u['Email']) === $email) {
                $user = Sheets::getSheetRowByValue("Utilizatori", $u['Email']);
                break;
            }
        }

        if (!$user) {
            Mail::to($email)->send(new RegisterEmailFishArena($email));

            return Inertia::render('Auth/Autentificare', [
                "warning" => [
                    'message' => "Contul nu a fost gasit, ai primit un email de inregistrare.",
                    'status' => true
                ]
            ]);
        }

        $token = substr(md5(time()), 0, 60);

        $data = [
            Sheets::count('Login Tokens'),
            $user['id'], 
            $token,
            '0',
            date('Y-m-d H:i:s', strtotime("+ 5 minutes")),
            date('Y-m-d H:i:s')
        ];

        Mail::to($email)->send(new LoginEmailFishArena($email, $token));

        Sheets::insert('Login Tokens', $data);

        return Inertia::render('Auth/Autentificare', [
            "warning" => [
                'message' => "Cont gasit cu succes, ai primit un mail pentru a te loga.",
                'status' => true
            ]
        ]);
    }
}
