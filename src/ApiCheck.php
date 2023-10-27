<?php

namespace Mactape\IsDayOff;

use DateTimeInterface;

class ApiCheck
{
    public function check(?DateTimeInterface $date = null): bool
    {
        $date = $date ?? new \DateTimeImmutable();

        $file = $this->getYearlyFile($date);

        return $file[(int) $date->format('z')];
    }

    private function getYearlyFile(DateTimeInterface $date): string
    {
        $year = $date->format('Y');

        $filename = "day-off-$year.txt";

        $file = __DIR__ . "/../../../../storage/app/$filename";

        $data = file_get_contents($file);

        if (!$data || strlen($data) < 365) {
            file_put_contents($file, $this->apiRequest($year));
        }

        return file_get_contents($file);
    }

    private function apiRequest(string $year): bool|string
    {
        try {
            $response = file_get_contents("https://isdayoff.ru/api/getdata?year=$year");
        } catch (\Exception $e) {
            throw new ApiHttpException('IsDayOff API: ' .$e->getCode() . ' ' . $e->getMessage());
        }

        return $response;
    }
}
