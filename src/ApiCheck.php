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

        $sep = DIRECTORY_SEPARATOR;

        $file = __DIR__ . "$sep..$sep..$sep..$sep.." . $sep. "storage$sep" . $sep . "app$sep$filename";

        if (!file_exists($file)) {
            file_put_contents($file, $this->apiRequest($year));
        }

        $data = file_get_contents($file);

        if (strlen($data) < 365) {
            unlink($file);
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
