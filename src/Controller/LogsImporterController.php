<?php

namespace App\Controller;

use App\Entity\LogsImporter;
use App\Service\LogsImporterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogsImporterController extends AbstractController
{
    #[Route('/logs/importer', name: 'app_logs_importer')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LogsImporterController.php',
        ]);
    }

    /**
     * It takes a string as a parameter, formats it, and saves it to the database
     * 
     * @param string requestBody The raw data that was sent to the API.
     * 
     * @return bool A boolean value.
     */
    public function saveNew(string $requestBody, EntityManagerInterface $entityManager): bool
    {
        $logsImporterService = new LogsImporterService();
        $logsRawData = $logsImporterService->formatLine($requestBody);

        $logsImporter = new LogsImporter();
        $logsImporter->setServiceName($logsRawData['serviceName']);
        $logsImporter->setTriggeredAt($logsRawData['triggeredAt']);
        $logsImporter->setRequestDetails($logsRawData['requestDetails']);
        $logsImporter->setStatusCode($logsRawData['statusCode']);

        $respository = $entityManager->getRepository(LogsImporter::class);
        $respository->add($logsImporter, true);

        return true;
    }

    #[Route('/count', name: 'app_logs_filter')]
    public function count(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $filters['serviceName'] = $request->query->get("serviceNames");
        $filters['startDate'] = $request->query->get("startDate");
        $filters['endDate'] = $request->query->get("endDate");
        $filters['statusCode'] = $request->query->get("statusCode");

        $respository = $entityManager->getRepository(LogsImporter::class);
        $results = $respository->filterByFields($filters);
        return $this->json([
            'counter' => count($results)
        ]);
    }
}
