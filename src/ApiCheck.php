<?php

namespace Mactape\IsDayOff;

use Carbon\CarbonInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApiCheck
{
    private string $uri = 'https://isdayoff.ru/api/getdata?';

    private array $responseCodes = [
        0 => 'workday',
        1 => 'dayOff',
        2 => 'halfWork',
        4 => 'remoteWorkDay',
        100 => 'date error',
        101 => 'not found',
        199 => 'api error',
    ];
    private array $errors = [100, 101, 199];

    public function check(?CarbonInterface $date = null): bool
    {
        $date = $date ?? now();

        return $this->requestYearly($date);
    }

    public function isDayOffApi(CarbonInterface $date): bool
    {
        return $this->day($date) === 1;
    }

    private function day(CarbonInterface $date): bool|int
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        $request = "year=$year&month=$month&day=$day";

        return $this->apiRequest($request);
    }

    private function requestYearly(CarbonInterface $date): bool
    {
        $year = $date->year;
        $filename = "day-off-$year.txt";

        if (
            ! Storage::exists($filename)
            || Storage::size($filename) < 365
        ) {
            Storage::disk('local')->put($filename, $this->apiRequest("year=$year"));
        }

        $file = Storage::get("day-off-$year.txt");

        return (bool) $file[$date->dayOfYear - 1];
    }

    private function apiRequest(string $request): bool|string
    {
        $client = new Client;

        try {
            $response = $client->get("$this->uri$request");
            $code = $response->getBody()->getContents();

            if (in_array((int) $code, $this->errors)) {
                Log::channel('api-day-off')->warning($this->responseCodes[$code]);
                return false;
            }

            return $code;
        } catch (GuzzleException $e) {
            Log::channel('api-day-off')->error($e->getCode() . ' ' . $e->getMessage());
            return false;
        }
    }
}
