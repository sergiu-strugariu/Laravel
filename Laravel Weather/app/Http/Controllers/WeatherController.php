<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http as HTTP;
use Inertia\Inertia;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $city = $request->input('city');
        $apiKey = env('WEATHER_API_KEY');
        $response = HTTP::get("https://api.openweathermap.org/data/2.5/weather?units=metric&q=$city&appid=$apiKey");

        $weatherData = $response->json();

        return Inertia::render('Home', [
            'weatherData' => $weatherData
        ]);
    }
}
