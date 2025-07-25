<?php
// procesar_vuelo.php

// Incluimos el archivo de conexión a la base de datos
require_once 'conexion.php'; 

// Habilitar la visualización de todos los errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si la solicitud es POST y si se han enviado datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recopilar datos del formulario
    $origen = trim($_POST['origen'] ?? '');
    $destino = trim($_POST['destino'] ?? '');
    $fecha_salida = trim($_POST['fecha_salida'] ?? '');
    $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT); // Asegura que es un número
    $plazas_disponibles = filter_var($_POST['plazas_disponibles'] ?? '', FILTER_VALIDATE_INT); // Nuevo campo

    // 2. Validar datos
    $errores = [];

    if (empty($origen)) {
        $errores[] = "El origen es obligatorio.";
    }
    if (empty($destino)) {
        $errores[] = "El destino es obligatorio.";
    }
    if (empty($fecha_salida) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_salida)) {
        $errores[] = "La fecha de salida es obligatoria y debe tener formato YYYY-MM-DD.";
    }
    if ($precio === false || $precio <= 0) {
        $errores[] = "El precio es obligatorio y debe ser un número positivo.";
    }
    // Validación para plazas_disponibles
    if ($plazas_disponibles === false || $plazas_disponibles < 0) {
        $errores[] = "Las plazas disponibles son obligatorias y deben ser un número entero no negativo.";
    }

    // Si hay errores, mostrar un mensaje y detener el proceso
    if (!empty($errores)) {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Registro</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Errores en el formulario:</h4><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul><a href='form_vuelo.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
        exit(); // Detener la ejecución del script
    }

    // 3. Preparar y ejecutar la consulta SQL para insertar los datos
    $sql = "INSERT INTO VUELO (origen, destino, fecha_salida, precio, plazas_disponibles) VALUES (:origen, :destino, :fecha_salida, :precio, :plazas_disponibles)";

    try {
        // Preparamos la sentencia
        $stmt = $pdo->prepare($sql);

        // Vinculamos los parámetros
        $stmt->bindParam(':origen', $origen);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':plazas_disponibles', $plazas_disponibles);

        // Ejecutamos la sentencia
        if ($stmt->execute()) {
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Vuelo Registrado</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-success'><h4>¡Vuelo registrado exitosamente!</h4><p>Origen: " . htmlspecialchars($origen) . "</p><p>Destino: " . htmlspecialchars($destino) . "</p><p>Fecha de Salida: " . htmlspecialchars($fecha_salida) . "</p><p>Precio: $" . number_format($precio, 0, ',', '.') . "</p><p>Plazas Disponibles: " . htmlspecialchars($plazas_disponibles) . "</p><a href='form_vuelo.html' class='btn btn-primary'>Registrar otro vuelo</a> <a href='mostrar_datos.php' class='btn btn-info'>Ver todos los datos</a> <a href='index.php' class='btn btn-secondary'>Volver al inicio</a></div></div></body></html>";
        } else {
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Registro</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Error al registrar el vuelo:</h4><p>Hubo un problema al intentar guardar los datos.</p><a href='form_vuelo.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
        }
    } catch (PDOException $e) {
        // En caso de error de PDO
        error_log("Error al insertar vuelo: " . $e->getMessage()); // Registrar el error
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Base de Datos</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Error de base de datos:</h4><p>Ocurrió un error al intentar registrar el vuelo. Por favor, inténtalo de nuevo más tarde.</p><a href='form_vuelo.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
    }
} else {
    // Si se accede al script directamente sin un POST
    header("Location: form_vuelo.html"); // Redirigir al formulario
    exit();
}
?>
