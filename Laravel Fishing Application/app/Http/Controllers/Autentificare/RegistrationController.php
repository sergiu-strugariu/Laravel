<?php

namespace App\Http\Controllers\Autentificare;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function redirect(Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return Inertia::render('Auth/Autentificare', [
                "warning" => [
                    'message' => "Nu am reusit sa gasim adresa de email, te rugam sa intri din nou pe linkul de pe adresa ta de email.",
                    'status' => false
                ]
            ]);
        }

        return Inertia::render('Auth/Inregistrare');
    }

    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            return Inertia::render("Auth/Autentificare", [
                "warning" => [
                    'message' => "Exista deja un cont cu aceasta adresa de email! Te rugam sa te loghezi.",
                    'status' => false
                ]
            ]);
        }

        $request->validate([
            'nume' => ['required', 'string', 'max:255'],
            'prenume' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'boolean'],
            'mobile' => ['required', 'string', "max:255", "phone:AUTO"],
            'data_nasterii' => ['required', 'date', function ($attribute, $value, $fail) {
                if ((new \DateTime($value))->diff(new \DateTime())->y < 16) {
                    $fail('Trebuie sa ai cel putin 16 ani.');
                }
            }]
        ]);

        User::create([
            'prenume' => $request->prenume,
            'nume' => $request->nume,
            'tip' => 'Pescar',
            'email' => $request->email,
            'mobile' => $request->mobile,
            'data_nasterii' => $request->data_nasterii,
            'sex' => $request->sex,
            'google_id' => null,
            'istoric_asociere' => null
        ]);

        return to_route('autentificare');
    }

    public function login(Request $request, string $email = null, $token = null)
    {
        if (!$email) {
            $email = $request->email;
        }

        if (!$token) {
            $token = $request->token;
        }

        $token = Token::where('token', $token)->first();
        $user = User::where('email', $email)->first();

        if (!$user->google_id) {
            $token_id = $token->id;

            if ($token->used) {
                return Inertia::render('Auth/Authentificare', [
                    "warning" => [
                        'message' => "Se pare ca link-ul a fost deja folosit, te rugan sa incerci din nou.",
                        'status' => false
                    ]
                ]);
            }

            $expire_at = new \DateTime($token->expire_at);
            $current_time = new \DateTime();

            if ($expire_at < $current_time) {
                return Inertia::render('Auth/Authentificare', [
                    "warning" => [
                        'message' => "Se pare ca link-ul a expirat, te rugan sa incerci din nou.",
                        'status' => false
                    ]
                ]);
            }

            $token->used = true;
            $token->save();
        }
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
