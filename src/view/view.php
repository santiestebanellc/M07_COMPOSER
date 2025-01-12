<?php
namespace MyWorkshop\view;

require "../../vendor/autoload.php";

use MyWorkshop\controllers\reparationController;

session_start();

if (isset($_POST["role"])) {
    $_SESSION["userType"] = $_POST["role"];
}

if (!isset($_SESSION["userType"]) || !in_array($_SESSION["userType"], ["client", "employee"])) {
    header("Location: ../../public/index.php");
    exit;
}

$reparationController = new reparationController();
$reparations = [];
$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["searchId"])) {

    $_SESSION["id"] = intval($_POST["searchId"]);

    try {
        $reparations = $reparationController->getReparation();
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["insertName"])) {
    try {
        $newReparation = $reparationController->insertReparation();

        if ($newReparation) {
            $success = "Reparación añadida con éxito.";
            $_SESSION["id"] = $newReparation->getId();
            $reparations = [$newReparation];
        }
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

function render($reparation, $key)
{
    ?>
    <div class="col">
        <div class="card mb-4 shadow-sm">
            <a class="mt-3" href="#" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $key; ?>">
                <img src="data:image/png;base64, <?php echo base64_encode($reparation->getImage()); ?>" alt="Imagen de reparación"
                    class="card-img-top img-fluid rounded mx-auto d-block" style="max-width: 60%; height: auto;">
            </a>
            <div class="card-body">
                <h5 class="card-title"><b>ID:</b> <?php echo htmlspecialchars($reparation->getId()); ?></h5>
                <p class="card-text"><b>ID Workshop:</b> <?php echo htmlspecialchars($reparation->getIdWorkshop()); ?></p>
                <p class="card-text"><b>UUID:</b> <?php echo htmlspecialchars($reparation->getUuid()); ?></p>
                <p class="card-text"><b>Nombre:</b> <?php echo htmlspecialchars($reparation->getName()); ?></p>
                <p class="card-text"><b>Fecha de Registro:</b> <?php echo htmlspecialchars($reparation->getRegisterDate()->format("Y-m-d")); ?></p>
                <p class="card-text"><b>Matrícula:</b> <?php echo htmlspecialchars($reparation->getLicensePlate()); ?></p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal<?php echo $key; ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $key; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel<?php echo $key; ?>">Imagen de Reparación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="data:image/png;base64, <?php echo base64_encode($reparation->getImage()); ?>" alt="Imagen de reparación"
                        class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Reparaciones</h1>
            <a href="../../public/index.php" class="btn btn-secondary">Volver a Inicio</a>
        </div>

        <form action="view.php" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="searchId" class="form-label">ID de Reparación</label>
                <input type="number" name="searchId" id="searchId" class="form-control" placeholder="ID de reparación" required>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <section class="my-5">
            <h2 class="text-center">Reparaciones Encontradas</h2>
            <?php if (!empty($reparations)): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($reparations as $key => $reparation): ?>
                        <?php render($reparation, $key); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    No se encontró ninguna reparación registrada.
                </div>
            <?php endif; ?>
        </section>

        <?php if ($_SESSION["userType"] === "employee"): ?>
            <section class="my-5">
                <h2 class="text-center">Añadir Reparación</h2>
                <form action="view.php" class="row g-3" method="post" enctype="multipart/form-data">
                    <div class="col-md-6">
                        <label for="insertImage" class="form-label">Imagen</label>
                        <input type="file" name="insertImage" class="form-control" id="insertImage" required>
                    </div>
                    <div class="col-md-6">
                        <label for="insertName" class="form-label">Nombre</label>
                        <input type="text" name="insertName" class="form-control" id="insertName" maxlength="12" required>
                    </div>
                    <div class="col-md-6">
                        <label for="insertDate" class="form-label">Fecha de registro</label>
                        <input type="date" name="insertDate" class="form-control" id="insertDate" required>
                    </div>
                    <div class="col-md-6">
                        <label for="insertMatricula" class="form-label">Matrícula</label>
                        <input type="text" name="insertMatricula" class="form-control" id="insertMatricula" pattern="[0-9]{4}-[A-Z]{3}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="insertIdWorkshop" class="form-label">ID Workshop</label>
                        <input type="number" name="insertIdWorkshop" class="form-control" id="insertIdWorkshop" max="9999" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Añadir Reparación</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
