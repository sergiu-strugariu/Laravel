<?php

namespace App\Http\Controllers\Autentificare;

use App\Http\Controllers\Controller;
use App\Models\Sheets;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    private $registration;

    public function __construct(RegistrationController $registration)
    {
        $this->registration = $registration;
    }

    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        $user = Socialite::driver($provider)->user();
        $email = strtolower($user->email);

        $users = Sheets::get("Utilizatori");

        $existingUser = null;

        foreach ($users as $u) {
            if (strtolower($u['Email']) === $email) {
                $existingUser = Sheets::getSheetRowByValue("Utilizatori", $u['Email']);
                break;
            }
        }

        if ($existingUser) {
            if (!$existingUser["Mobil"]) {
                return redirect()->route("provider.inregistrare", ['email' => $email]);
            }

            return $this->registration->login($request, $email);
        }

        $user = [
            Sheets::count("Utilizatori"),
            $user->user['given_name'],
            $user->user['family_name'],
            "Pescar",
            $email,
            '',
            '',
            '',
            $user->id,
            '',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ];

        $store = Sheets::insert("Utilizatori", $user);

        if (!$store) {
            Inertia::render("Auth/Inregistrare", [
                "warning" => [
                    'message' => "A intervenit o erroare, te rugam incearca mai tarziu.",
                    'status' => false
                ]
            ]);
        }

        return to_route('provider.inregistrare', ['email' => $email]);
    }

    public function index()
    {
        return Inertia::render('Auth/ProviderInregistrare');
    }

    public function update(Request $request)
    {
        $request->validate([
            'sex' => ['required', 'boolean'],
            'mobile' => ['required', 'string', "max:255", "phone:AUTO"],
            'data_nasterii' => ['required', 'date', function ($attribute, $value, $fail) {
                if ((new DateTime($value))->diff(new DateTime())->y < 16) {
                    $fail('Trebuie sa ai cel putin 16 ani.');
                }
            }]
        ]);

        $email = $request->email;

        $user = Sheets::getSheetRowByValue("Utilizatori", $email);

        $update = [
            $user['id'],
            $user['Prenume'],
            $user['Nume'],
            $user['Tip'],
            $user['Email'],
            $request->mobile,
            date('d-m-Y', strtotime($request->data_nasterii)),
            $request->sex ? 'M' : 'F',
            $user['Google ID']
        ];

        $update = Sheets::edit("Utilizatori", intval($user['id']) + 1, $update);

        if (!$update) {
            return Inertia::render('Auth/ProviderInregistrare', [
                "warning" => [
                    'message' => "A intervenit o erroare, te rugam incearca mai tarziu.",
                    'status' => false
                ]
            ]);
        }

        return Inertia::render('Auth/Autentificare', [
            "warning" => [
                'message' => "Cont creeat cu succes! Te poti loga acum!",
                'status' => true
            ]
        ]);
    }
}
