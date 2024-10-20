<?php

namespace Mactape\IsDayOff;

use DateTimeInterface;

class ApiCheck
{
    /**
     * @throws \Exception
     */
    public function check(DateTimeInterface|string|null $date = null): bool
    {
        $date = $date ?? new \DateTimeImmutable();

        if (is_string($date)) {
            $date = new \DateTimeImmutable($date);
        }

        $file = $this->getYearlyFile($date);

        return $file[(int) $date->format('z')];
    }

    private function getYearlyFile(DateTimeInterface $date): string
    {
        $year = $date->format('Y');

        $filename = "day-off-$year.txt";

        $file = storage_path("app/$filename");

        if (file_exists($file) === false) {
            file_put_contents($file, $this->apiRequest($year));
        }

        if (file_exists($file) === true) {
            $data = file_get_contents($file);

            if (strlen($data) < 365) {
                unlink($file);
                file_put_contents($file, $this->apiRequest($year));
            }
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
