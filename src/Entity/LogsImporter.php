<?php

namespace App\Entity;

use App\Repository\LogsImporterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogsImporterRepository::class)]
class LogsImporter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $service_name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $triggered_at = null;

    #[ORM\Column(length: 255)]
    private ?string $request_details = null;

    #[ORM\Column]
    private ?int $status_code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->service_name;
    }

    public function setServiceName(string $service_name): self
    {
        $this->service_name = $service_name;

        return $this;
    }

    public function getTriggeredAt(): ?\DateTimeImmutable
    {
        return $this->triggered_at;
    }

    public function setTriggeredAt(\DateTimeImmutable $triggered_at): self
    {
        $this->triggered_at = $triggered_at;

        return $this;
    }

    public function getRequestDetails(): ?string
    {
        return $this->request_details;
    }

    public function setRequestDetails(string $request_details): self
    {
        $this->request_details = $request_details;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $status_code): self
    {
        $this->status_code = $status_code;

        return $this;
    }
}
