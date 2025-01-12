<?php
namespace MyWorkshop\controllers;

use MyWorkshop\service\reparationService;

class reparationController
{
    function getReparation()
    {
        $reparationService = new reparationService();
        $response = $reparationService->getReparation($_SESSION["id"], $_SESSION["userType"]);
        return $response;
    }

    function insertReparation()
    {
        $reparationService = new reparationService();
        $response = $reparationService->insertReparation(
            $_POST["insertIdWorkshop"],
            $_POST["insertName"],
            $_POST["insertDate"],
            $_POST["insertMatricula"],
            $_FILES["insertImage"]["tmp_name"]
        );
        return $response;
    }

}
