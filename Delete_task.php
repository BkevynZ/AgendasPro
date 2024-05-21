<?php
require 'config.php'; // Incluir el archivo de configuración para la conexión a la base de datos

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Obtener el ID de la tarea desde la URL y convertirlo a entero

    // Verificar si la tarea existe
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La tarea existe, proceder a eliminarla
        $sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirigir de nuevo a la página de vista de tareas con un mensaje de éxito
            header("Location: view_tasks.php?message=Tarea eliminada con éxito");
            exit;
        } else {
            // Error al eliminar la tarea
            echo "Error al eliminar la tarea: " . $stmt->error;
        }
    } else {
        // La tarea no existe
        echo "La tarea con ID $id no existe.";
    }

    $stmt->close();
} else {
    // No se proporcionó ningún ID
    echo "ID de tarea no proporcionado.";
}

$conn->close(); // Cerrar la conexión a la base de datos
?>
