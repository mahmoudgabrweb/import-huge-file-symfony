<?php

namespace App\Service;

use SplFileObject;

class LogsImporterService
{
    /**
     * It reads the last line of a file and returns it as an integer
     * 
     * @param string projectDir The directory of the project you want to scan.
     * 
     * @return int The last line number that was scanned.
     */
    public function getLastScannedLine(string $projectDir): int
    {
        $lastScannedLineNumber = 0;
        $updatesFileDir = "$projectDir/public/updates.txt";
        if (!is_file($updatesFileDir)) {
            fopen($updatesFileDir, 'w');
        }
        $updatesFile = new SplFileObject($updatesFileDir);
        $scannedLine = $updatesFile->fgets();
        if ($scannedLine && $scannedLine != "") {
            $lastScannedLineNumber = $scannedLine;
        }
        return $lastScannedLineNumber;
    }

    /**
     * It writes the current line number to a file
     * 
     * @param string projectDir The directory of the project you're scanning.
     * @param int currentLineNumber The current line number of the file being scanned.
     */
    public function addLastScannedLineNumber(string $projectDir, int $currentLineNumber): void
    {
        $updatesFileDir = "$projectDir/public/updates.txt";
        $updatesFile = new SplFileObject($updatesFileDir, "w");
        $updatesFile->fwrite($currentLineNumber);
    }

    /**
     * It takes a string, splits it into an array, and returns an array
     * 
     * @param string line The line of the log file that we're currently parsing.
     * 
     * @return array An array with the following keys:
     * - serviceName
     * - triggeredAt
     * - requestDetails
     * - statusCode
     */
    public function formatLine(string $line): array
    {
        $lineArr = explode(" ", $line);
        $serviceName = $lineArr[0];
        $triggeredAt = str_replace("[", "", $lineArr[3]) . " " . str_replace("]", "", $lineArr[4]);
        $requestDetails = str_replace('"', "", $lineArr[5]) . " " . $lineArr[6] . " " . str_replace('"', "", $lineArr[7]);
        $statusCode = intval($lineArr[8]);

        $triggeredAtDateTime = new \DateTimeImmutable($triggeredAt);

        return [
            "serviceName" => $serviceName, "triggeredAt" => $triggeredAtDateTime,
            "requestDetails" => $requestDetails, "statusCode" => $statusCode
        ];
    }
}
