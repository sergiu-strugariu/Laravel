<?php

namespace App\Http\Controllers\Autentificare;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use App\Providers\RouteServiceProvider;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function index(Request $request)
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

        return Inertia::render("Auth/Inregistrare");
    }

    public function store(Request $request)
    {
        $user = Sheets::getSheetRowByValue("Utilizatori", $request->email);

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
                if ((new DateTime($value))->diff(new DateTime())->y < 16) {
                    $fail('Trebuie sa ai cel putin 16 ani.');
                }
            }]
        ]);

        $user = [
            Sheets::count("Utilizatori"),
            $request->prenume,
            $request->nume,
            "Pescar",
            $request->email,
            $request->mobile,
            date('d-m-Y', strtotime($request->data_nasterii)),
            $request->sex ? 'M' : 'F',
            '',
            '',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ];

        $store = Sheets::insert("Utilizatori", $user);

        if (!$store) {
            Inertia::render("Auth/Autentificare", [
                "warning" => [
                    'message' => "A intervenit o erroare, te rugam incearca mai tarziu.",
                    'status' => false
                ]
            ]);
        }

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

        $token = Sheets::getSheetRowByValue('Login Tokens', $token);

        $users = Sheets::get("Utilizatori");

        $user = null;

        foreach ($users as $u) {
            if (strtolower($u['Email']) === $email) {
                $user = Sheets::getSheetRowByValue("Utilizatori", $u['Email']);
                break;
            }
        }

        if (!$user['Google ID']) {

            $tokenId = $token['id'] + 1;

            if ($token['used']) {
                return Inertia::render('Auth/Autentificare', [
                    "warning" => [
                        'message' => "Se pare ca link-ul a fost deja folosit, te rugan sa incerci din nou.",
                        'status' => false
                    ]
                ]);
            }

            $expire_at = new DateTime($token['expire_at']);
            $current_time = new DateTime();

            if ($expire_at < $current_time) {
                return Inertia::render('Auth/Autentificare', [
                    "warning" => [
                        'message' => "Se pare ca link-ul a expirat, te rugan sa incerci din nou.",
                        'status' => false
                    ]
                ]);
            }

            $update = [
                $token['id'],
                $token['ID Utilizator'],
                $token['token'],
                1,
                $token['expire_at'],
                $token['created_at'],
            ];

            $update = Sheets::edit('Login Tokens', $tokenId, $update);
        }

        session(['user' => $user]);

        return redirect(RouteServiceProvider::HOME);
    }
}
