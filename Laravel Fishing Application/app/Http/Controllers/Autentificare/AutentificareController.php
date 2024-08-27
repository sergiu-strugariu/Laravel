<?php

namespace App\Http\Controllers\Autentificare;

use App\Http\Controllers\Controller;
use App\Mail\LoginEmailFishArena;
use App\Mail\RegisterEmailFishArena;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AutentificareController extends Controller
{
    public function index()
    {
        $result = trim(str_replace(url('/'), '', strtok(url()->previous(), '?')), '/');

        if ($result === 'inregistrare') {
            return Inertia::render("Auth/Autentificare", [
                "warning" => [
                    "message" => "Cont inregistrat cu succes, te poti loga acum",
                    "status" => true
                ]
            ]);
        }

        return Inertia::render("Auth/Autentificare");
    }

    public function autentificare(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

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

        Token::create([
            'user_id' => $user->id,
            'token' => $token,
            'used' => 0,
            'expire_at' => date('Y-m-d H:i:s', strtotime("+ 5 minutes")),
        ]);

        Mail::to($email)->send(new LoginEmailFishArena($email, $token));

        return Inertia::render('Auth/Autentificare', [
            "warning" => [
                'message' => "Cont gasit cu succes, ai primit un mail pentru a te loga.",
                'status' => true
            ]
        ]);
    }
}
