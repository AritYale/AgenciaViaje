<?php
// data.php

// Habilitar la visualización de todos los errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'FiltroBusqueda.php'; 

// Establecer la zona horaria a Arica, Chile
date_default_timezone_set('America/Santiago');

/**
 * Clase para representar un Paquete Turístico
 */
class PaqueteTuristico {
    public $id;
    public $type;
    public $destination;
    public $date; // Formato YYYY-MM-DD del PHP
    public $price;
    public $details;
    public $status;
    // --- NUEVAS PROPIEDADES ---
    public $city;     // Ciudad específica del paquete
    public $country; // País del paquete
    public $duration; // Duración en días

    public function __construct($id, $type, $destination, $date, $price, $details, $city = '', $country = '', $duration = null) {
        $this->id = $id;
        $this->type = $type;
        $this->destination = $destination;
        $this->date = $date;
        $this->price = $price;
        $this->details = $details;
        $this->status = 'disponible'; // Estado inicial por defecto
        // --- ASIGNAR NUEVAS PROPIEDADES ---
        $this->city = $city;
        $this->country = $country;
        $this->duration = $duration;
    }

    public function applyDiscount($percentage) {
        if ($percentage > 0 && $percentage <= 100) {
            $discountAmount = $this->price * ($percentage / 100);
            $this->price -= $discountAmount;
            $this->status = 'oferta'; // Marcar como oferta al aplicar descuento
            return true;
        }
        return false;
    }

    public function updateStatus($newStatus) {
        $validStatuses = ['disponible', 'reservado', 'cancelado', 'oferta'];
        if (in_array($newStatus, $validStatuses)) {
            $this->status = $newStatus;
            return true;
        }
        return false;
    }
}

/**
 * Clase para representar una Notificación en Tiempo Real
 */
class RealTimeNotification {
    public $type;
    public $message;
    public $expires; // Formato YYYY-MM-DD

    public function __construct($type, $message, $expires) {
        $this->type = $type;
        $this->message = $message;
        $this->expires = $expires;
    }

    // Método para verificar si la notificación sigue activa
    public function isActive() {
        // Asegurar que las fechas tienen la misma zona horaria para la comparación
        $expirationDate = new DateTime($this->expires, new DateTimeZone('America/Santiago')); 
        $now = new DateTime('now', new DateTimeZone('America/Santiago')); 
        return $expirationDate >= $now;
    }
}

// --- Simulación de datos de la agencia ---
// En una aplicación real, estos datos vendrían de una base de datos o un archivo de persistencia.
$paquetes_turísticos = [
    new PaqueteTuristico(1, 'vuelo', 'Madrid', '2025-08-10', 550000, 'Vuelo directo con Iberia', 'Madrid', 'España', 5),
    new PaqueteTuristico(2, 'hotel', 'Madrid', '2025-08-10', 120000, 'Hotel céntrico, 3 noches, Hotel Plaza', 'Madrid', 'España', 3),
    new PaqueteTuristico(3, 'vuelo', 'París', '2025-09-01', 480000, 'Vuelo con escala', 'París', 'Francia', 7),
    new PaqueteTuristico(4, 'hotel', 'París', '2025-09-01', 90000, 'Alojamiento económico, 4 noches, Hotel Eiffel', 'París', 'Francia', 4),
    new PaqueteTuristico(5, 'vuelo', 'Roma', '2025-07-20', 600000, 'Oferta especial, pocos asientos', 'Roma', 'Italia', 6),
    new PaqueteTuristico(6, 'hotel', 'Roma', '2025-07-20', 150000, 'Hotel con desayuno incluido, Hotel Coliseo', 'Roma', 'Italia', 3),
    new PaqueteTuristico(7, 'vuelo', 'Londres', '2025-10-15', 400000, 'Vuelo low-cost', 'Londres', 'Reino Unido', 4),
    new PaqueteTuristico(8, 'hotel', 'Londres', '2025-10-15', 110000, 'Cerca de atracciones principales, Hotel Big Ben', 'Londres', 'Reino Unido', 2)
];

// Aplicar un descuento a un paquete para simular una oferta
$paquetes_turísticos[4]->applyDiscount(10); // Roma vuelo ahora con 10% de descuento

$notificaciones_en_tiempo_real = [
    new RealTimeNotification('oferta', '¡Oferta! Vuelo a Roma por solo '. number_format($paquetes_turísticos[4]->price, 0, ',', '.') .'. ¡Cupos limitados!', '2025-07-25'),
    new RealTimeNotification('disponibilidad', 'Últimos 2 paquetes turísticos a París disponibles para septiembre.', '2200-01-01'), // Fecha muy lejana
    new RealTimeNotification('bajada_precio', '¡Bajada de precio! Hotel en Madrid a $110.000 la noche.', '2200-01-01')
];

// --- Procesar la solicitud ---
header('Content-Type: application/json'); // Indicar que la respuesta es JSON

if (isset($_GET['action'])) { // Se usa $_GET para la acción, independientemente del método de datos
    $action = $_GET['action'];

    switch ($action) {
        case 'getPaquetes':
            // Si no hay parámetros de búsqueda adicionales, simplemente devuelve todos los paquetes
            $results = $paquetes_turísticos;
            break;

        case 'getNotificaciones':
            $activeNotifications = [];
            foreach ($notificaciones_en_tiempo_real as $notification) {
                if ($notification->isActive()) {
                    $activeNotifications[] = [
                        'type' => $notification->type,
                        'message' => $notification->message,
                        'expires' => $notification->expires
                    ];
                }
            }
            echo json_encode($activeNotifications);
            exit(); // Terminar el script aquí para notificaciones
            break;

        case 'searchPaquetes':
            // **Recupera los parámetros enviados por GET utilizando $_GET**
            $destination = isset($_GET['destination']) ? $_GET['destination'] : '';
            $travelDate = isset($_GET['travelDate']) ? $_GET['travelDate'] : '';
            $hotelName = isset($_GET['hotelName']) ? $_GET['hotelName'] : '';
            $city = isset($_GET['city']) ? $_GET['city'] : '';
            $country = isset($_GET['country']) ? $_GET['country'] : '';
            $duration = isset($_GET['duration']) ? $_GET['duration'] : null;

            error_log("DEBUG (data.php - searchPaquetes): Parámetros GET recibidos: " . print_r($_GET, true));

            // --- Guardar criterios de búsqueda en la sesión (Medida 2 de Pregunta 2) ---
            $_SESSION['last_search_criteria'] = [
                'destination' => $destination,
                'travelDate' => $travelDate,
                'hotelName' => $hotelName,
                'city' => $city,
                'country' => $country,
                'duration' => $duration
            ];
            error_log("DEBUG (data.php - searchPaquetes): Criterios de búsqueda guardados en sesión: " . print_r($_SESSION['last_search_criteria'], true));
            // --- FIN: Guardar criterios de búsqueda en la sesión ---

            // Instanciar la clase FiltroBusqueda y aplicar el filtro
            $filtro = new FiltroBusqueda($destination, $travelDate, $hotelName, $city, $country, $duration);
            $results = $filtro->aplicarFiltro($paquetes_turísticos);
            break;

        // --- INICIO: NUEVO CASE PARA REGISTRAR UN PAQUETE TURÍSTICO (MÉTODO POST) ---
        case 'addPaquete':
            // Asegurarse de que la solicitud sea POST y el tipo de contenido sea JSON
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty(file_get_contents('php://input'))) {
                http_response_code(400); 
                echo json_encode(['success' => false, 'message' => 'Solicitud inválida. Se esperaba POST con JSON.']);
                exit();
            }

            // Recuperar el JSON enviado en el cuerpo de la solicitud (para POST)
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true); 

            // DEPURACIÓN: Ver los datos recibidos por POST
            error_log("DEBUG (data.php - addPaquete): Datos POST recibidos: " . print_r($data, true));

            // Validar que se recibieron todos los datos necesarios
            $required_fields = ['type', 'destination', 'date', 'price', 'details', 'city', 'country', 'duration'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400); 
                    echo json_encode(['success' => false, 'message' => "Falta el campo requerido: $field."]);
                    exit();
                }
            }

            // Generar un nuevo ID para el paquete (simple, para simulación)
            $new_id = 0;
            if (!empty($paquetes_turísticos)) {
                $last_package = end($paquetes_turísticos); 
                $new_id = $last_package->id + 1; 
            } else {
                $new_id = 1; // Si el array está vacío, empezamos con ID 1
            }

            // Crear una nueva instancia de PaqueteTuristico con los datos del POST
            $nuevo_paquete = new PaqueteTuristico(
                $new_id,
                $data['type'],
                $data['destination'],
                $data['date'],
                (float)$data['price'], 
                $data['details'],
                $data['city'],
                $data['country'],
                (int)$data['duration'] 
            );

            // Agregar el nuevo paquete a la lista
            // En una aplicación real, lo insertarías en una base de datos o lo guardarías en un archivo.
            $paquetes_turísticos[] = $nuevo_paquete;

            // DEPURACIÓN: Confirma que el paquete fue agregado
            error_log("DEBUG (data.php - addPaquete): Nuevo paquete agregado. Total de paquetes: " . count($paquetes_turísticos));

            // Responder con éxito
            echo json_encode(['success' => true, 'message' => 'Paquete registrado exitosamente.', 'newPackageId' => $new_id]);
            exit(); // Terminar el script aquí para la acción 'addPaquete'
            break;
        // --- FIN: NUEVO CASE PARA REGISTRAR UN PAQUETE TURÍSTICO ---

        // --- INICIO: LÓGICA DEL CARRITO DE COMPRAS ---
        case 'addToCart':
            $packageId = isset($_POST['packageId']) ? (int)$_POST['packageId'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($packageId > 0 && $quantity > 0) {
                // Inicializa el carrito si no existe
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }

                // Agrega o actualiza la cantidad en el carrito
                if (isset($_SESSION['carrito'][$packageId])) {
                    $_SESSION['carrito'][$packageId] += $quantity;
                } else {
                    $_SESSION['carrito'][$packageId] = $quantity;
                }
                echo json_encode(['success' => true, 'message' => 'Paquete agregado al carrito.', 'carrito' => $_SESSION['carrito']]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de paquete o cantidad inválida.']);
            }
            exit();
            break;

        case 'getCart':
            // Devuelve el contenido actual del carrito
            $cartDetails = [];
            if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as $packageId => $quantity) {
                    // Busca el paquete en la lista de paquetes disponibles para obtener sus detalles
                    $foundPackage = null;
                    foreach ($paquetes_turísticos as $p) {
                        if ($p->id === $packageId) {
                            $foundPackage = $p;
                            break;
                        }
                    }
                    if ($foundPackage) {
                        $cartDetails[] = [
                            'id' => $foundPackage->id,
                            'type' => $foundPackage->type,
                            'destination' => $foundPackage->destination,
                            'price' => $foundPackage->price,
                            'quantity' => $quantity,
                            'subtotal' => $foundPackage->price * $quantity
                        ];
                    }
                }
            }
            echo json_encode(['success' => true, 'carrito' => $cartDetails]);
            exit();
            break;

        case 'clearCart':
            // Vacía el carrito
            unset($_SESSION['carrito']);
            echo json_encode(['success' => true, 'message' => 'Carrito vaciado exitosamente.']);
            exit();
            break;
        // --- FIN: LÓGICA DEL CARRITO DE COMPRAS ---

        default:
            echo json_encode(['error' => 'Acción no válida.']);
            exit();
            break;
    }

    // Código común para serializar y enviar los resultados de paquetes/búsqueda
    // Esto se ejecuta para 'getPaquetes' y 'searchPaquetes'
    $data_output = [];
    foreach ($results as $paquete) {
        $data_output[] = [
            'id' => $paquete->id,
            'type' => $paquete->type,
            'destination' => $paquete->destination,
            'date' => $paquete->date,
            'price' => $paquete->price,
            'details' => $paquete->details,
            'status' => $paquete->status,
            'city' => $paquete->city,
            'country' => $paquete->country,
            'duration' => $paquete->duration
        ];
    }
    echo json_encode($data_output);
} else {
    echo json_encode(['error' => 'No se especificó ninguna acción.']);
}
?>
