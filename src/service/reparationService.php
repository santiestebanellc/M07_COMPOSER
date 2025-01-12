<?php
namespace MyWorkshop\service;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use mysqli;
use MyWorkshop\models\reparation;
use Ramsey\Uuid\Nonstandard\Uuid;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class reparationService
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('reparationService');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app_workshop.log', Logger::DEBUG));
    }

    public function connect(): mysqli
    {
        try {
            $db = parse_ini_file("../../config/db_config.ini");
            $mysqli = new mysqli($db["host"], $db["user"], $db["pwd"], $db["db_name"]);

            if ($mysqli->connect_error) {
                $this->logger->error("CONN: Failed to connect to database - " . $mysqli->connect_error);
                throw new \Exception("Database connection failed: " . $mysqli->connect_error);
            }

            $this->logger->info("CONN: Successfully connected to database");
            return $mysqli;

        } catch (\Exception $e) {
            $this->logger->error("CONN: Error during connection - " . $e->getMessage());
            throw $e;
        }
    }

    public function getReparation($id = null, $type)
    {
        $managerImage = new ImageManager(new Driver());
        $mysqli = $this->connect();

        try {
            $sql_sentence = "SELECT * FROM reparation WHERE id = " . (int)$id;

            $result = $mysqli->query($sql_sentence);

            if (!$result) {
                $this->logger->warning("DQL: Failed to execute query - $sql_sentence");
                throw new \Exception("Error in query: " . $mysqli->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $imageObject = !empty($row["image"]) ? $managerImage->read($row["image"]) : null;

                if ($type != "employee" && $imageObject) {
                    $imageObject->pixelate(30);
                    $row["uuid"] = "************************************";
                }

                $reparation = new reparation(
                    $row["uuid"],
                    $row["idWorkshop"],
                    $row["name"],
                    $row["registerDate"],
                    $row["licensePlate"],
                    $imageObject ? $imageObject->toPng() : null
                );
                $reparation->setId($row["id"]);
                $data[] = $reparation;
            }

            $this->logger->info("DQL: Query executed successfully - $sql_sentence | Rows returned: " . count($data));
            return $data;

        } catch (\Exception $e) {
            $this->logger->error("DQL: Error during query execution - " . $e->getMessage());
            throw $e;
        }
    }

    public function insertReparation($idWorkshop, $name, $date, $matricula, $image)
    {
        $managerImage = new ImageManager(new Driver());
        $mysqli = $this->connect();
        $uuid = Uuid::uuid4()->toString();

        try {
            $imageData = file_get_contents($image);
            $imageObject = $managerImage->read($imageData);
            $imageObject->resize(1366, 768);
            $imageObject->text("{$uuid}-{$matricula}", 100, 75, function ($font) {
                $font->file(__DIR__ . "/../../resources/fonts/OpenSans-VariableFont_wdth,wght.ttf");
                $font->size(34);
                $font->color('#FF0000');
                $font->align('left');
                $font->valign('top');
            });

            $imageData = file_get_contents($imageObject->toPng()->toDataUri());
            $imageData = $mysqli->real_escape_string($imageData);

            $sql_sentence = "INSERT INTO `workshop`.`reparation` (`uuid`, `idWorkshop`, `name`, `registerDate`, `licensePlate`, `image`) 
                            VALUES ('$uuid', $idWorkshop, '$name', '$date', '$matricula', '$imageData');";

            if ($mysqli->query($sql_sentence)) {
                $select_sql = "SELECT * FROM `workshop`.`reparation` WHERE `uuid` = '$uuid'";
                $row = $mysqli->query($select_sql)->fetch_assoc();

                $this->logger->info("DML: Reparation inserted successfully - UUID: $uuid");

                $reparation = new reparation(
                    $row["uuid"],
                    $row["idWorkshop"],
                    $row["name"],
                    $row["registerDate"],
                    $row["licensePlate"],
                    $row["image"]
                );
                $reparation->setId($row["id"]);
                return $reparation;
            } else {
                $this->logger->warning("DML: Failed to execute insert - $sql_sentence");
                throw new \Exception("Error inserting reparation: " . $mysqli->error);
            }

        } catch (\Exception $e) {
            $this->logger->error("DML: Error during insertion - " . $e->getMessage());
            throw $e;
        }
    }
}
