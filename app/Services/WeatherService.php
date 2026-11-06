<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Fetch current weather at given coordinates using Open-Meteo API.
     * Includes timeout, error fallback, and safe response handling.
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        try {
            $response = Http::timeout(4)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ]);

            if ($response->successful()) {
                $current = $response->json('current') ?? [];
                $weatherCode = $current['weather_code'] ?? 0;

                return [
                    'available' => true,
                    'temperature_c' => $current['temperature_2m'] ?? null,
                    'humidity_pct' => $current['relative_humidity_2m'] ?? null,
                    'wind_speed_kmh' => $current['wind_speed_10m'] ?? null,
                    'condition' => $this->interpretWeatherCode($weatherCode),
                    'source' => 'Open-Meteo Real-time Atmospheric API',
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Weather API request failed for [{$latitude}, {$longitude}]: " . $e->getMessage());
        }

        // Graceful fallback when network fails or timeout occurs
        return [
            'available' => false,
            'condition' => 'Weather data temporarily unavailable',
            'temperature_c' => null,
            'wind_speed_kmh' => null,
            'source' => 'Offline Fallback',
        ];
    }

    protected function interpretWeatherCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            in_array($code, [1, 2, 3]) => 'Partly Cloudy',
            in_array($code, [45, 48]) => 'Foggy / Low Visibility',
            in_array($code, [51, 53, 55, 61, 63, 65]) => 'Rain / Maritime Swell',
            in_array($code, [80, 81, 82, 95, 96, 99]) => 'Severe Storm / Thunderstorm',
            default => 'Moderate Conditions',
        };
    }
}

