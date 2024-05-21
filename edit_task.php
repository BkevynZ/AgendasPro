<?php
session_start();
require 'config.php'; // Incluir el archivo de configuración para la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Verificar si la tarea pertenece al usuario actual
    $sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        // La tarea no pertenece al usuario actual
        header("Location: view_tasks.php?message=No tienes permiso para editar esta tarea.");
        exit;
    }
} else {
    header("Location: view_tasks.php?message=ID de tarea no proporcionado.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];
    $priority = $_POST['priority'];
    $category = $_POST['category'];
    $image_path = null;

    // Manejo de subida de imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Actualizar la tarea en la base de datos
    $sql = "UPDATE tasks SET title = ?, description = ?, due_date = ?, due_time = ?, priority = ?, category = ?, image_path = IFNULL(?, image_path) WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $title, $description, $due_date, $due_time, $priority, $category, $image_path, $task_id, $user_id);
    if ($stmt->execute()) {
        header("Location: view_tasks.php?message=Tarea actualizada con éxito.");
        exit;
    } else {
        $error = "Error al actualizar la tarea: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Editar Tarea</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="edit_task.php?id=<?php echo htmlspecialchars($task['id']); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Fecha de Entrega</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="due_time">Hora de Entrega</label>
                <input type="time" class="form-control" id="due_time" name="due_time" value="<?php echo htmlspecialchars($task['due_time']); ?>" required>
            </div>
            <div class="form-group">
                <label for="priority">Prioridad</label>
                <select class="form-control" id="priority" name="priority" required>
                    <option value="alta" <?php if ($task['priority'] == 'alta') echo 'selected'; ?>>Alta</option>
                    <option value="media" <?php if ($task['priority'] == 'media') echo 'selected'; ?>>Media</option>
                    <option value="baja" <?php if ($task['priority'] == 'baja') echo 'selected'; ?>>Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Categoría</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($task['category']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Imagen (opcional)</label>
                <input type="file" class="form-control-file" id="image" name="image">
                <?php if (!empty($task['image_path'])): ?>
                    <p>Imagen actual:</p>
                    <img src="<?php echo htmlspecialchars($task['image_path']); ?>" alt="Imagen de Tarea" class="img-fluid mb-3" style="max-width: 100px;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
