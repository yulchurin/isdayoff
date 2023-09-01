<?php

namespace Mactape\IsDayOff;

use Carbon\CarbonInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class ApiCheck
{
    private string $uri = 'https://isdayoff.ru/api/getdata?';

    public function check(?CarbonInterface $date = null): bool
    {
        $date = $date ?? now();

        return $this->requestYearly($date);
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

    /**
     * @throws ApiHttpException
     */
    private function apiRequest(string $request): bool|string
    {
        $client = new Client;

        try {
            $response = $client->get("$this->uri$request");

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new ApiHttpException('IsDayOff API: ' .$e->getCode() . ' ' . $e->getMessage());
        }
    }
}
