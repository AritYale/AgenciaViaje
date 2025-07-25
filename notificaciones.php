<?php

/**
 * Función que genera una notificación emergente con una oferta especial.
 * Se ejecuta al cargar la página.
 */
function generarNotificacionOferta() {
    $ofertasDisponibles = true; // Establece a 'false' si no se quiere que la alerta aparezca al cargar.

    if ($ofertasDisponibles) {
        // Establecer la zona horaria a Arica (Chile).
        $timezone = new DateTimeZone('America/Santiago');
        $now = new DateTime('now', $timezone);

        // Obtener el nombre del mes en español utilizando IntlDateFormatter.
        // Esto es la forma moderna y recomendada para internacionalización en PHP.
        $formatter = new IntlDateFormatter(
            'es_CL', // Locale para español de Chile
            IntlDateFormatter::FULL, // Estilo completo para la fecha
            IntlDateFormatter::FULL, // Estilo completo para la hora
            $timezone, // Zona horaria
            IntlDateFormatter::GREGORIAN, // Calendario Gregoriano
            'MMMM' // Patrón para obtener solo el nombre completo del mes
        );
        $currentMonth = ucfirst($formatter->format($now)); // Obtener el mes y capitalizar la primera letra.

        $mensajeOferta = "¡Oferta especial de Verano! Disfruta de un 30% de descuento en paquetes a la Patagonia en " . $currentMonth . ". ¡Reserva antes de que acabe el mes!";

        // Generar el script de JavaScript para mostrar la alerta.
        echo "<script type='text/javascript'>";
        echo "window.onload = function() {";
        echo "    alert('" . addslashes($mensajeOferta) . "');";
        echo "};";
        echo "</script>";
    }
}
?>
