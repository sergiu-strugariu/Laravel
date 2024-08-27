<?php

namespace App\Http\Controllers;

use App\Models\ShortURL;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Nette\Utils\Random;

class URLController extends Controller
{
    public function store(Request $request) 
    {
        $url = $request->url;

        $request->validate([
            'url' => ['required', 'url'],
        ]);

        $existing = ShortURL::where('original_url', $url)->first();

        if (!$existing) {
            $shorturl = ShortURL::create([
                "original_url" => $url,
                "short_url" => Random::generate(7, '0-9a-z'),
            ]);

            return Inertia::render("Home", [
                'shorturl' => "http://localhost:8000/" . $shorturl->short_url
            ]);
        }

        return Inertia::render("Home", [
            'shorturl' => "http://localhost:8000/" .  $existing->short_url
        ]);
    }

    public function redirect($shorturl)
    {
        $shorturl = ShortURL::where('short_url', $shorturl)->first();
        return Inertia::location($shorturl->original_url);
    }
}
