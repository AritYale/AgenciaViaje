<?php

// Se definen las credenciales de la base de datos
define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'root');    
define('DB_PASSWORD', '');        
define('DB_NAME', 'AGENCIA');    

try {
    // Se crea una nueva instancia de PDO
    // La cadena DSN (Data Source Name) especifica el tipo de base de datos, host y nombre de la DB
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);

    // Se configura el modo de error de PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Se puede establecer un mensaje para confirmar la conexión
    // Pero en un entorno de producción, este mensaje no debería mostrarse por seguridad
    echo "Conexión exitosa a la base de datos AGENCIA.";

} catch (PDOException $e) {
    // Si la conexión falla, se captura la excepción PDOException
    // y se muestra un mensaje de error.
    // En producción, es mejor registrar el error y mostrar un mensaje genérico al usuario.
    die("ERROR: No se pudo conectar a la base de datos. " . $e->getMessage());
}

?>
