<?php
session_start();
require 'config.php'; // Incluir el archivo de configuración para la conexión a la base de datos

// Verificar si el usuario no está autenticado
if (!isset($_SESSION['user_id'])) {
    // Si el usuario no está autenticado, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Consultar las tareas del usuario actual
$sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date, due_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .card-title {
            font-size: 1.25rem;
        }
        .card-body img {
            max-width: 100px;
            height: auto;
        }
        .priority-high {
            border-left: 5px solid #dc3545;
        }
        .priority-medium {
            border-left: 5px solid #ffc107;
        }
        .priority-low {
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Gestión de Tareas</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="view_tasks.php">Ver Tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_task.php">Agregar Tarea</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Lista de Tareas</h2>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    // Determinar la clase de prioridad
                    $priorityClass = '';
                    if ($row['priority'] == 'alta') {
                        $priorityClass = 'priority-high';
                    } elseif ($row['priority'] == 'media') {
                        $priorityClass = 'priority-medium';
                    } elseif ($row['priority'] == 'baja') {
                        $priorityClass = 'priority-low';
                    }
                    ?>
                    <div class="col-md-4">
                        <div class="card <?php echo $priorityClass; ?>">
                            <div class="card-header">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['category']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <?php if (!empty($row['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Imagen de Tarea" class="img-fluid mb-3">
                                <?php endif; ?>
                                <p class="card-text"><small class="text-muted">Fecha de Entrega: <?php echo htmlspecialchars($row['due_date']); ?></small></p>
                                <p class="card-text"><small class="text-muted">Hora de Entrega: <?php echo htmlspecialchars($row['due_time']); ?></small></p>
                                <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta tarea?');">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No hay tareas registradas.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close(); // Cerrar la conexión a la base de datos
?>
