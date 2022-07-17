<?php

namespace App\Command;

use App\Controller\LogsImporterController;
use App\Service\LogsImporterService;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogsImporterCommand extends Command
{
    protected static $defaultName = "app:import-logs";

    private $output;

    private $currentLineNumber;

    private $entityManager;

    private $logsImporterService;

    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        $this->logsImporterService = new LogsImporterService();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        pcntl_signal(SIGINT, [$this, "doTerminate"]);

        $logsFileDir = $this->projectDir . "/public/logs.txt";

        $lastScannedLineNumber = $this->logsImporterService->getLastScannedLine($this->projectDir);

        if (is_file($logsFileDir)) {
            $logsImporterController = new LogsImporterController();

            $logsFile = new SplFileObject($logsFileDir);
            $logsFile->seek($lastScannedLineNumber);
            while (!$logsFile->eof()) {
                $currentLine = $logsFile->fgets();
                $logsImporterController->saveNew($currentLine, $this->entityManager);
                $this->currentLineNumber = $logsFile->key();
            }
            $logsFile = null;
        }

        return $this->currentLineNumber;
    }

    public function doTerminate(): void
    {
        $this->output->writeln("Current line: " . $this->currentLineNumber);
        $this->output->writeln("Terminated ... ");

        $this->logsImporterService->addLastScannedLineNumber($this->projectDir, $this->currentLineNumber);

        die;
    }
}
