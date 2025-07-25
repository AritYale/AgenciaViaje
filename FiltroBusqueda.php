<?php
// FiltroBusqueda.php

// Habilitar la visualización de todos los errores para depuración (TEMPORAL)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Clase para representar un filtro de búsqueda de paquetes turísticos.
 * Encapsula los criterios de búsqueda (destino, fecha, nombre de hotel, ciudad, país, duración).
 */
class FiltroBusqueda {
    public $destination;
    public $travelDate;
    public $hotelName;
    public $city;
    public $country;
    public $duration;

    public function __construct($destination = '', $travelDate = '', $hotelName = '', $city = '',  $country = '', $duration = null) {
        $this->destination = strtolower(trim($destination));
        $this->travelDate = $travelDate;
        $this->hotelName = strtolower(trim($hotelName));
        $this->city = strtolower(trim($city));
        $this->country = strtolower(trim($country));
        $this->duration = is_numeric($duration) ? (int)$duration : null;
    }

    /**
     * Aplica este filtro a una lista de objetos PaqueteTuristico.
     *
     * @param array $paquetes Array de objetos PaqueteTuristico.
     * @return array Array de objetos PaqueteTuristico filtrados.
     */
    public function aplicarFiltro(array $paquetes) {
        return array_filter($paquetes, function($paquete) {
            // --- ¡IMPORTANTE! AÑADE ESTA LÍNEA DE DEPURACIÓN AQUÍ ---
            error_log("DEBUG (FiltroBusqueda.php): hotelName interno: " . $this->hotelName . 
                      " | City interno: " . $this->city . 
                      " | Country interno: " . $this->country . 
                      " | Duration interno: " . ($this->duration ?? 'null') . 
                      " | Paquete details: " . $paquete->details . 
                      " | Paquete City: " . $paquete->city . 
                      " | Paquete Country: " . $paquete->country . 
                      " | Paquete Duration: " . ($paquete->duration ?? 'null') . 
                      " | Tipo: " . $paquete->type);
            // --- FIN DEPURACIÓN ---

            // Convertimos a minúsculas para una búsqueda sin distinción de mayúsculas/minúsculas
            $paqueteDestinationLower = strtolower($paquete->destination);
            $paqueteDetailsLower = strtolower($paquete->details); // Para buscar nombre de hotel en detalles

            // --- NUEVOS CAMPOS EN MINÚSCULAS PARA COMPARACIÓN DIRECTA ---
            $paqueteCityLower = strtolower($paquete->city);
            $paqueteCountryLower = strtolower($paquete->country);
            // --- FIN NUEVOS CAMPOS ---

            // Criterios de filtrado
            $matchesDestination = empty($this->destination) || strpos($paqueteDestinationLower, $this->destination) !== false;
            $matchesDate = empty($this->travelDate) || $paquete->date === $this->travelDate;

            // Filtro de Nombre de Hotel: solo aplica si el paquete es de tipo 'hotel' Y el nombre coincide en los detalles
            $matchesHotelName = empty($this->hotelName) || (
                $paquete->type === 'hotel' && strpos($paqueteDetailsLower, $this->hotelName) !== false
            );
            
            // Filtro de Ciudad: si el filtro está vacío o la ciudad del paquete contiene el texto del filtro
            $matchesCity = empty($this->city) || strpos($paqueteCityLower, $this->city) !== false;
            
            // Filtro de País: si el filtro está vacío o el país del paquete contiene el texto del filtro
            $matchesCountry = empty($this->country) || strpos($paqueteCountryLower, $this->country) !== false;

            // Filtro de Duración: si el filtro está vacío o la duración del paquete es igual a la duración del filtro
            $matchesDuration = empty($this->duration) || ($paquete->duration === $this->duration);


            // Retorna verdadero si el paquete cumple con TODOS los criterios activos del filtro
            return $matchesDestination && $matchesDate && $matchesHotelName && $matchesCity && $matchesCountry && $matchesDuration;
        });
    }

    /**
     * Verifica si el filtro tiene algún criterio de búsqueda activo.
     * @return bool
     */
    public function estaActivo() {
        return !empty($this->destination) || !empty($this->travelDate) ||
               !empty($this->hotelName) || !empty($this->city) ||
               !empty($this->country) || !empty($this->duration);
    }
}
?>
