<?php
namespace MyWorkshop\service;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use mysqli;
use MyWorkshop\models\reparation;
use Ramsey\Uuid\Nonstandard\Uuid;

class reparationService
{
    function connect()
    {
        $db = parse_ini_file("../../config/db_config.ini");
        return new mysqli($db["host"], $db["user"], $db["pwd"], $db["db_name"]);
    }

    function getReparation($id = null, $type)
    {
        $managerImage = new ImageManager(new Driver());

        $mysqli = $this->connect();

        $sql_sentence = "SELECT * FROM reparation WHERE id = " . (int)$id;

        $result = $mysqli->query($sql_sentence);

        if (!$result) {
            throw new \Exception("Error en la consulta: " . $mysqli->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row["image"])) {
                $imageObject = $managerImage->read($row["image"]);
            } else {
                $imageObject = null;
            }
        
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
        return $data;
    }

    function insertReparation($idWorkshop, $name, $date, $matricula, $image)
{
    $managerImage = new ImageManager(new Driver());
    $mysqli = $this->connect();
    $uuid = uuid::uuid4()->toString();

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
        throw new \Exception("Error al insertar la reparación: " . $mysqli->error);
    }
}


}