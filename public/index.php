<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            background: #fff;
        }
        .card-body {
            padding: 2rem;
            color: #2d3436;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #0984e3;
            text-align: center;
        }
        .btn-primary, .btn-secondary {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            border-radius: 8px;
        }
        .btn-primary {
            background: #0984e3;
            color: #fff;
        }
        .btn-primary:hover {
            background: #74b9ff;
            color: #fff;
        }
        .btn-secondary {
            background: #2ecc71;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #55efc4;
            color: #fff;
        }
        .container {
            max-width: 500px;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        p {
            font-size: 1.2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h1>Car Workshop</h1>
                        <p>Por favor, selecciona tu rol:</p>
                        <form action="../src/view/view.php" method="POST">
                            <div class="btn-container">
                                <button type="submit" name="role" value="employee" class="btn btn-primary">Empleado</button>
                                <button type="submit" name="role" value="client" class="btn btn-secondary">Cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
