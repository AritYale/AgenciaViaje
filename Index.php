<?php
session_set_cookie_params([
    'lifetime' => 86400, // 1 día
    'path' => '/',
    'domain' => '', // Deja vacío para el dominio actual
    'secure' => false, 
    'httponly' => true, // Protege contra XSS
    'samesite' => 'Lax' // Buena práctica contra CSRF
]);

// Configura el tiempo de vida de la sesión en el servidor a 30 minutos (1800 segundos) de inactividad
// O si quieres que coincida con el lifetime de la cookie para una duración más larga:
ini_set('session.gc_maxlifetime', 86400); // Sesión en el servidor también dura 1 día de inactividad
ini_set('session.cookie_lifetime', 86400); // Asegura que la cookie del lado del cliente también dure 1 día si no se cierra el navegador

session_start(); // Inicia la sesión.
// --- FIN: Configuraciones de Sesión ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agencia de Viajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Agencia de Viajes</h1>

        <div class="card p-4 mb-4 shadow-sm">
            <h2 class="card-title text-center mb-3">Registrar Nuevo Paquete Turístico</h2>
            <form id="register-package-form" class="row g-3">
                <div class="col-md-6">
                    <label for="register-type" class="form-label">Tipo de Paquete:</label>
                    <select class="form-select" id="register-type" required>
                        <option value="">Selecciona un tipo</option>
                        <option value="vuelo">Vuelo</option>
                        <option value="hotel">Hotel</option>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona el tipo de paquete.</div>
                </div>

                <div class="col-md-6">
                    <label for="register-destination" class="form-label">Destino General:</label>
                    <input type="text" class="form-control" id="register-destination" placeholder="Ej: Madrid" required>
                    <div class="invalid-feedback">Por favor, ingresa un destino.</div>
                </div>

                <div class="col-md-6">
                    <label for="register-date" class="form-label">Fecha de Viaje:</label>
                    <input type="date" class="form-control" id="register-date" required>
                    <div class="invalid-feedback">Por favor, selecciona una fecha.</div>
                </div>

                <div class="col-md-6">
                    <label for="register-price" class="form-label">Precio (CLP):</label>
                    <input type="number" class="form-control" id="register-price" min="1" placeholder="Ej: 550000" required>
                    <div class="invalid-feedback">Por favor, ingresa un precio válido.</div>
                </div>

                <div class="col-12">
                    <label for="register-details" class="form-label">Detalles del Paquete:</label>
                    <textarea class="form-control" id="register-details" rows="3" placeholder="Ej: Vuelo directo con Iberia, Hotel céntrico 3 noches, Hotel Plaza" required></textarea>
                    <div class="invalid-feedback">Por favor, ingresa los detalles del paquete.</div>
                </div>

                <div class="col-md-6">
                    <label for="register-city" class="form-label">Ciudad Específica:</label>
                    <input type="text" class="form-control" id="register-city" placeholder="Ej: Santiago">
                </div>

                <div class="col-md-6">
                    <label for="register-country" class="form-label">País:</label>
                    <input type="text" class="form-control" id="register-country" placeholder="Ej: Chile">
                </div>

                <div class="col-md-6">
                    <label for="register-duration" class="form-label">Duración (días):</label>
                    <input type="number" class="form-control" id="register-duration" min="1" placeholder="Ej: 7">
                </div>

                <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Paquete
                    </button>
                </div>
            </form>
        </div>

        <h2 class="card-title text-center mb-3">Buscar Vuelos y Hoteles</h2>
        <form id="search-form" class="row g-3">
            <div class="col-md-6">
                <label for="destination" class="form-label">Destino (General):</label>
                <input type="text" class="form-control" id="destination" placeholder="Ej: París, Roma"
                value="<?php echo isset($_SESSION['last_search_criteria']['destination']) ? htmlspecialchars($_SESSION['last_search_criteria']['destination']) : ''; ?>">
                <div class="invalid-feedback">Por favor, ingresa un destino.</div>
            </div>

            <div class="col-md-6">
                <label for="travel-date" class="form-label">Fecha de Viaje:</label>
                <input type="date" class="form-control" id="travel-date"
                value="<?php echo isset($_SESSION['last_search_criteria']['travelDate']) ? htmlspecialchars($_SESSION['last_search_criteria']['travelDate']) : ''; ?>">
                <div class="invalid-feedback">Por favor, selecciona una fecha.</div>
            </div>

            <div class="col-md-6">
                <label for="hotel-name" class="form-label">Nombre del Hotel:</label>
                <input type="text" class="form-control" id="hotel-name" placeholder="Ej: Plaza, Coliseo"
                value="<?php echo isset($_SESSION['last_search_criteria']['hotelName']) ? htmlspecialchars($_SESSION['last_search_criteria']['hotelName']) : ''; ?>">
            </div>

            <div class="col-md-6">
                <label for="city" class="form-label">Ciudad Específica:</label>
                <input type="text" class="form-control" id="city" placeholder="Ej: Barcelona, Santiago"
                value="<?php echo isset($_SESSION['last_search_criteria']['city']) ? htmlspecialchars($_SESSION['last_search_criteria']['city']) : ''; ?>">
            </div>

            <div class="col-md-6">
                <label for="country" class="form-label">País:</label>
                <input type="text" class="form-control" id="country" placeholder="Ej: España, Francia"
                value="<?php echo isset($_SESSION['last_search_criteria']['country']) ? htmlspecialchars($_SESSION['last_search_criteria']['country']) : ''; ?>">
            </div>

            <div class="col-md-6">
                <label for="duration" class="form-label">Duración (días):</label>
                <input type="number" class="form-control" id="duration" min="1" placeholder="Ej: 7"
                value="<?php echo isset($_SESSION['last_search_criteria']['duration']) ? htmlspecialchars($_SESSION['last_search_criteria']['duration']) : ''; ?>">
            </div>

            <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-search me-2"></i>Buscar Paquetes
                </button>
                <button type="button" class="btn btn-primary btn-lg" id="new-search-btn">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Nueva Búsqueda
                </button>
            </div>
        </form>
        </div>

        <div id="notifications-container" class="alert alert-info fade show mb-4" role="alert">
            <h3 class="alert-heading">Ofertas y Actualizaciones en Vivo</h3>
            <div id="notification-messages"></div>
        </div>

        <div class="card p-4 mb-4 shadow-sm">
            <h2 class="card-title text-center mb-3">Tu Carrito de Compras</h2>

            <div id="empty-cart-message" class="text-center text-muted mb-3">
                <p>Tu carrito está vacío.</p>
            </div>

            <ul id="cart-items" class="list-group mb-3">
            </ul>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <h4>Total: $<span id="cart-total">0.00</span></h4>
            <div>
            <button id="clear-cart-btn" class="btn btn-danger me-2" style="display: none;">
                <i class="bi bi-trash"></i> Vaciar Carrito
            </button>
            <button id="checkout-btn" class="btn btn-success" style="display: none;">
                <i class="bi bi-bag"></i> Proceder al Pago
            </button>
            </div>
            </div>
        </div>

        <div id="results-container" class="mt-4">
            <h2 class="mb-3">Resultados de la Búsqueda</h2>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="script.js"></script>


    <?php
    // Incluye el archivo de notificaciones
    require_once 'notificaciones.php';

    // Llama a la función para generar la notificación emergente
    generarNotificacionOferta();
    ?>
</body>
</html>
