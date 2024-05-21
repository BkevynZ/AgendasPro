<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];
    $priority = $_POST['priority'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id']; // Obtener el user_id de la sesión

    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = $target_file;
    }

    $sql = "INSERT INTO tasks (title, description, due_date, due_time, priority, category, user_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssis", $title, $description, $due_date, $due_time, $priority, $category, $user_id, $image_path);

    if ($stmt->execute()) {
        header("Location: view_tasks.php?message=Tarea añadida exitosamente.");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tarea</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Tarea</h2>
        <form action="create_task.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título:</label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción:</label>
                <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Subir Imagen (opcional):</label>
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="form-group">
                <label for="due_date">Fecha de Entrega:</label>
                <input type="date" class="form-control" name="due_date" id="due_date" required>
            </div>
            <div class="form-group">
                <label for="due_time">Hora de Entrega:</label>
                <input type="time" class="form-control" name="due_time" id="due_time" required>
            </div>
            <div class="form-group">
                <label for="priority">Prioridad:</label>
                <select class="form-control" name="priority" id="priority" required>
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Categoría:</label>
                <input type="text" class="form-control" name="category" id="category" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Tarea</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>