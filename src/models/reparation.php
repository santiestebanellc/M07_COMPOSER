<?php
namespace MyWorkshop\models;

use DateTime;

class Reparation
{
    private $id;
    private string $uuid;
    private int $idWorkshop;
    private string $name;
    private DateTime $registerDate;
    private string $licensePlate;
    private ?string $image;

    public function __construct(string $uuid, int $idWorkshop, string $name, string $registerDate, string $licensePlate, ?string $image)
    {
        $this->uuid = $uuid;
        $this->idWorkshop = $idWorkshop;
        $this->name = $name;
        $this->registerDate = new DateTime($registerDate);
        $this->licensePlate = $licensePlate;
        $this->image = $image;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getIdWorkshop(): int
    {
        return $this->idWorkshop;
    }

    public function setIdWorkshop(int $idWorkshop): self
    {
        $this->idWorkshop = $idWorkshop;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRegisterDate(): DateTime
    {
        return $this->registerDate;
    }

    public function setRegisterDate(string $registerDate): self
    {
        $this->registerDate = new DateTime($registerDate);

        return $this;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
