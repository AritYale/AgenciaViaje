<?php
// procesar_hotel.php

require_once 'conexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recopilar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $habitaciones_disponibles = filter_var($_POST['habitaciones_disponibles'] ?? '', FILTER_VALIDATE_INT);
    $tarifa_noche = filter_var($_POST['tarifa_noche'] ?? '', FILTER_VALIDATE_FLOAT);

    // 2. Validar datos
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre del hotel es obligatorio.";
    }
    if (empty($ubicacion)) {
        $errores[] = "La ubicación es obligatoria.";
    }
    if ($habitaciones_disponibles === false || $habitaciones_disponibles < 0) {
        $errores[] = "Las habitaciones disponibles son obligatorias y deben ser un número entero no negativo.";
    }
    if ($tarifa_noche === false || $tarifa_noche <= 0) {
        $errores[] = "La tarifa por noche es obligatoria y debe ser un número positivo.";
    }

    if (!empty($errores)) {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Registro</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Errores en el formulario:</h4><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul><a href='form_hotel.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
        exit();
    }

    // 3. Preparar y ejecutar la consulta SQL para insertar los datos
    $sql = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) VALUES (:nombre, :ubicacion, :habitaciones_disponibles, :tarifa_noche)";

    try {
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ubicacion', $ubicacion);
        $stmt->bindParam(':habitaciones_disponibles', $habitaciones_disponibles);
        $stmt->bindParam(':tarifa_noche', $tarifa_noche);

        if ($stmt->execute()) {
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Hotel Registrado</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-success'><h4>¡Hotel registrado exitosamente!</h4><p>Nombre: " . htmlspecialchars($nombre) . "</p><p>Ubicación: " . htmlspecialchars($ubicacion) . "</p><p>Habitaciones Disponibles: " . htmlspecialchars($habitaciones_disponibles) . "</p><p>Tarifa por Noche: $" . number_format($tarifa_noche, 0, ',', '.') . "</p><a href='form_hotel.html' class='btn btn-primary'>Registrar otro hotel</a> <a href='mostrar_datos.php' class='btn btn-info'>Ver todos los datos</a> <a href='index.php' class='btn btn-secondary'>Volver al inicio</a></div></div></body></html>";
        } else {
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Registro</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Error al registrar el hotel:</h4><p>Hubo un problema al intentar guardar los datos.</p><a href='form_hotel.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
        }
    } catch (PDOException $e) {
        error_log("Error al insertar hotel: " . $e->getMessage());
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error de Base de Datos</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container mt-5'><div class='alert alert-danger'><h4>Error de base de datos:</h4><p>Ocurrió un error al intentar registrar el hotel. Por favor, inténtalo de nuevo más tarde.</p><a href='form_hotel.html' class='btn btn-danger'>Volver al formulario</a></div></div></body></html>";
    }
} else {
    header("Location: form_hotel.html");
    exit();
}
?>
