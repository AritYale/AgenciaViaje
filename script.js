// script.js

// === CLASE PaqueteTuristico ===
// Esta clase es útil en el frontend para manejar los datos recibidos del backend.
class PaqueteTuristico {
    // --- AGREGAR NUEVAS PROPIEDADES EN EL CONSTRUCTOR ---
    constructor(id, type, destination, date, price, details, status, city = '', country = '', duration = null) {
        this.id = id;
        this.type = type;
        this.destination = destination;
        this.date = date; // Formato YYYY-MM-DD del PHP
        this.price = price;
        this.details = details;
        this.status = status;
        this.city = city;
        this.country = country;
        this.duration = duration;
    }

    applyDiscount(percentage) {
        if (percentage > 0 && percentage <= 100) {
            const discountAmount = this.price * (percentage / 100);
            this.price -= discountAmount;
            console.log(`Descuento del ${percentage}% aplicado. Nuevo precio: $${this.price.toFixed(0)}`);
            return true;
        }
        return false;
    }

    updateStatus(newStatus) {
        const validStatuses = ['disponible', 'reservado', 'cancelado', 'oferta'];
        if (validStatuses.includes(newStatus)) {
            this.status = newStatus;
            console.log(`Estado actualizado a: ${newStatus}`);
            return true;
        }
        return false;
    }

    toHtmlCard() {
        console.log(`DEBUG: Generando tarjeta para paquete con ID: ${this.id}, Destino: ${this.destination}`);
        let statusBadge = '';
        if (this.status === 'oferta') {
            statusBadge = `<span class="badge bg-success position-absolute top-0 end-0 mt-2 me-2">¡Oferta!</span>`;
        } else if (this.status === 'reservado') {
            statusBadge = `<span class="badge bg-warning text-dark position-absolute top-0 end-0 mt-2 me-2">Reservado</span>`;
        } else if (this.status === 'cancelado') {
            statusBadge = `<span class="badge bg-danger position-absolute top-0 end-0 mt-2 me-2">Cancelado</span>`;
        }

        const formattedPrice = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(this.price);

        // Agregamos la información de ciudad, país y duración aquí para que se muestre en la tarjeta
        return `
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm position-relative">
                    ${statusBadge}
                    <div class="card-body">
                        <h5 class="card-title">${this.type === 'vuelo' ? '<i class="bi bi-airplane-fill"></i> Vuelo a ' : '<i class="bi bi-building"></i> Hotel en '}${this.destination}</h5>
                        <p class="card-text">
                            <strong>ID:</strong> ${this.id}<br>
                            <strong>Fecha:</strong> ${this.date}<br>
                            <strong>Precio:</strong> ${formattedPrice}<br>
                            <strong>Detalles:</strong> ${this.details}<br>
                            ${this.city ? `<strong>Ciudad:</strong> ${this.city}<br>` : ''}
                            ${this.country ? `<strong>País:</strong> ${this.country}<br>` : ''}
                            ${this.duration ? `<strong>Duración:</strong> ${this.duration} días<br>` : ''}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                        <h4 class="text-primary">${formattedPrice}</h4>
                        <button class="btn btn-outline-primary add-to-cart-btn" data-package-id="${this.id}">
                            <i class="bi bi-cart-plus"></i> Añadir
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

// --- Funciones para interactuar con PHP ---

/**
 * Obtiene los paquetes turísticos desde el servidor PHP.
 * @param {object} params - Objeto con parámetros de búsqueda.
 * @returns {Promise<Array<PaqueteTuristico>>}
 */
async function loadPaquetes(params = {}) {
    const resultsContainer = document.getElementById('results-container');
    resultsContainer.innerHTML = '<p class="text-center">Cargando paquetes...</p>';
    const queryString = new URLSearchParams(params).toString();
    const url = `data.php?action=${params.action || 'getPaquetes'}&${queryString}`;

    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        const paquetes = data.map(item => new PaqueteTuristico(
            item.id, item.type, item.destination, item.date, item.price,
            item.details, item.status, item.city, item.country, item.duration
        ));
        displayPaquetes(paquetes);
        return paquetes;
    } catch (error) {
        console.error('Error al cargar los paquetes:', error);
        resultsContainer.innerHTML = '<p class="text-center text-danger">Error al cargar los paquetes.</p>';
        return [];
    }
}

/**
 * Renderiza los resultados de la búsqueda en el contenedor HTML.
 * @param {Array<PaqueteTuristico>} paquetes 
 */
function displayPaquetes(paquetes) {
    const resultsContainer = document.getElementById('results-container');
    resultsContainer.innerHTML = ''; // Limpia resultados anteriores
    if (paquetes.length === 0) {
        resultsContainer.innerHTML = '<p class="text-center">No se encontraron paquetes con los criterios de búsqueda.</p>';
        return;
    }

    const row = document.createElement('div');
    row.className = 'row row-cols-1 row-cols-md-2 g-4';

    paquetes.forEach(paquete => {
        row.innerHTML += paquete.toHtmlCard(); // Usa el método toHtmlCard de la clase
    });
    resultsContainer.appendChild(row);

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const packageId = event.target.closest('.add-to-cart-btn').dataset.packageId;
            console.log('ID de paquete al hacer clic:', packageId); 
            addToCart(packageId, button); 
        });
    });
}

/**
 * Obtiene y muestra las notificaciones.
 */
async function loadNotifications() {
    const notificationsContainer = document.getElementById('notification-messages');
    notificationsContainer.innerHTML = '<p>Cargando notificaciones...</p>';
    try {
        const response = await fetch('data.php?action=getNotificaciones');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const notifications = await response.json();
        displayNotifications(notifications);
    } catch (error) {
        console.error('Error al cargar las notificaciones:', error);
        notificationsContainer.innerHTML = '<p class="text-danger">Error al cargar las notificaciones.</p>';
    }
}

/**
 * Muestra notificaciones en el DOM.
 * @param {Array<Object>} notifications - Array de objetos de notificación.
 */
function displayNotifications(notifications) {
    const notificationsContainer = document.getElementById('notification-messages');
    notificationsContainer.innerHTML = ''; // Limpia notificaciones anteriores
    if (notifications.length === 0) {
        notificationsContainer.innerHTML = '<p>No hay notificaciones activas en este momento.</p>';
        return;
    }
    notifications.forEach(notification => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${notification.type === 'oferta' ? 'warning' : 'info'} mb-2`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.textContent = notification.message;
        notificationsContainer.appendChild(alertDiv);
    });
}


/**
 * Maneja el envío del formulario de búsqueda.
 */
async function handleSearch(event) {
    event.preventDefault();
    const destination = document.getElementById('destination').value;
    const travelDate = document.getElementById('travel-date').value;
    const hotelName = document.getElementById('hotel-name').value;
    const city = document.getElementById('city').value;
    const country = document.getElementById('country').value;
    const duration = document.getElementById('duration').value;

    const searchParams = {
        action: 'searchPaquetes',
        destination,
        travelDate,
        hotelName,
        city,
        country,
        duration
    };
    await loadPaquetes(searchParams);
}

/**
 * Resetea el formulario de búsqueda y vuelve a cargar todos los paquetes.
 */
async function resetSearch() {
    document.getElementById('search-form').reset(); // Reinicia el formulario
    document.getElementById('search-form').classList.remove('was-validated'); // Limpia la validación visual
    await loadPaquetes({}); // Carga todos los paquetes nuevamente
}

/**
 * Maneja el envío del formulario de registro de paquete.
 */
async function handleRegisterPackage(event) {
    event.preventDefault();
    const registerPackageForm = document.getElementById('register-package-form');

    if (!registerPackageForm.checkValidity()) {
        event.stopPropagation();
        registerPackageForm.classList.add('was-validated');
        return;
    }

    const newPackage = {
        type: document.getElementById('register-type').value,
        destination: document.getElementById('register-destination').value,
        date: document.getElementById('register-date').value,
        price: parseFloat(document.getElementById('register-price').value),
        details: document.getElementById('register-details').value,
        city: document.getElementById('register-city').value,
        country: document.getElementById('register-country').value,
        duration: parseInt(document.getElementById('register-duration').value)
    };

    try {
        const response = await fetch('data.php?action=addPaquete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(newPackage)
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            registerPackageForm.reset();
            registerPackageForm.classList.remove('was-validated');
            await loadPaquetes({}); // Recarga la lista de paquetes para incluir el nuevo
        } else {
            alert('Error al registrar paquete: ' + result.message);
        }
    } catch (error) {
        console.error('Error en la solicitud para registrar paquete:', error);
        alert('Ocurrió un error al intentar registrar el paquete.');
    }
}

// --- LÓGICA DEL CARRITO DE COMPRAS ---

// Función para agregar un paquete al carrito
async function addToCart(packageId, button) { 
    if (button) {
        button.disabled = true; 
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Añadiendo...'; // Opcional: spinner
    }
    try {
        const response = await fetch('data.php?action=addToCart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `packageId=${packageId}&quantity=1`
        });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            // Vuelve a cargar el carrito pasando las referencias a los elementos DOM
            await loadCart(
                document.getElementById('cart-items'),
                document.getElementById('empty-cart-message'),
                document.getElementById('cart-total'),
                document.getElementById('clear-cart-btn'),
                document.getElementById('checkout-btn')
            );
        } else {
            alert('Error al añadir al carrito: ' + result.message);
        }
    } catch (error) {
        console.error('Error al añadir al carrito:', error);
        alert('Ocurrió un error al intentar añadir el paquete al carrito.');
    } finally {
        if (button) {
            button.disabled = false; // Rehabilita el botón
            button.innerHTML = '<i class="bi bi-cart-plus"></i> Añadir';
        }
    }
}

// Función para cargar y mostrar el contenido del carrito
// Ahora recibe los elementos del DOM como parámetros
async function loadCart(cartItemsContainer, emptyCartMessage, cartTotalSpan, clearCartBtn, checkoutBtn) {
    // === DEBUG: Verifica si los elementos se encuentran ===
    console.log('DEBUG: Elementos del carrito encontrados:', {
        cartItemsContainer: cartItemsContainer ? 'Sí' : 'No',
        clearCartBtn: clearCartBtn ? 'Sí' : 'No',
        checkoutBtn: checkoutBtn ? 'Sí' : 'No',
        emptyCartMessage: emptyCartMessage ? 'Sí' : 'No',
        cartTotalSpan: cartTotalSpan ? 'Sí' : 'No' 
    });

    // Valida si los elementos existen antes de manipularlos
    if (!cartItemsContainer || !emptyCartMessage || !cartTotalSpan || !clearCartBtn || !checkoutBtn) {
        console.error('ERROR: No se encontraron todos los elementos del carrito en el DOM. Revisa los IDs.');
        return; // Detiene la ejecución si faltan elementos
    }
    
    cartItemsContainer.innerHTML = '';
    console.log('DEBUG: Iniciando carga del carrito...');

    try {
        const response = await fetch('data.php?action=getCart');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        const cartItems = result.carrito;

        console.log('DEBUG: Carrito recibido del backend:', cartItems);

        if (cartItems && cartItems.length > 0) {
            console.log('DEBUG: Carrito NO vacío. Mostrando ítems y botones.');
            emptyCartMessage.style.display = 'none'; // Oculta el mensaje de vacío
            cartItemsContainer.style.display = 'block'; 
            clearCartBtn.style.display = 'inline-block'; 
            checkoutBtn.style.display = 'inline-block';

            let total = 0;
            cartItems.forEach(item => {
                const itemElement = document.createElement('li');
                itemElement.className = 'list-group-item d-flex justify-content-between align-items-center';
                itemElement.innerHTML = `
                    <div>
                        <strong>${item.package_name} (${item.type.charAt(0).toUpperCase() + item.type.slice(1)})</strong><br>
                        <small>${item.quantity} x $${item.price.toLocaleString('es-CL')}</small>
                    </div>
                    <div>
                        <strong>$${item.subtotal.toLocaleString('es-CL')}</strong>
                        <button class="btn btn-sm btn-outline-danger ms-2 remove-from-cart-btn" data-package-id="${item.package_id}">X</button>
                    </div>
                `;
                cartItemsContainer.appendChild(itemElement);
                total += item.subtotal;
            });
            cartTotalSpan.textContent = total.toLocaleString('es-CL');

            // Adjuntar event listeners para eliminar del carrito
            document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const packageId = event.target.closest('.remove-from-cart-btn').dataset.packageId;
                    console.log('Intentando eliminar paquete con ID:', packageId);
                    removeFromCart(packageId);
                });
            });


        } else {
            console.log('DEBUG: Carrito vacío. Ocultando ítems y mostrando mensaje.');
            emptyCartMessage.style.display = 'block'; // Muestra el mensaje de vacío
            cartItemsContainer.style.display = 'none'; // Oculta la lista <ul> si el carrito está vacío
            clearCartBtn.style.display = 'none';
            checkoutBtn.style.display = 'none';
            cartTotalSpan.textContent = '0.00'; // Resetea el total
        }
    } catch (error) {
        console.error('Error al cargar el carrito:', error);
        cartItemsContainer.innerHTML = '<p class="text-center text-danger">Error al cargar el carrito.</p>';
        emptyCartMessage.style.display = 'none'; 
        clearCartBtn.style.display = 'none';
        checkoutBtn.style.display = 'none';
        cartTotalSpan.textContent = '0.00'; // Resetea el total
    }
}

// Función para eliminar un paquete del carrito
async function removeFromCart(packageId) {
    if (confirm('¿Estás seguro de que quieres eliminar este paquete del carrito?')) {
        try {
            const response = await fetch('data.php?action=removeFromCart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `packageId=${packageId}`
            });
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                // Vuelve a cargar el carrito pasando las referencias a los elementos DOM
                await loadCart(
                    document.getElementById('cart-items'),
                    document.getElementById('empty-cart-message'),
                    document.getElementById('cart-total'),
                    document.getElementById('clear-cart-btn'),
                    document.getElementById('checkout-btn')
                );
            } else {
                alert('Error al eliminar del carrito: ' + result.message);
            }
        } catch (error) {
            console.error('Error al eliminar del carrito:', error);
            alert('Ocurrió un error al intentar eliminar el paquete del carrito.');
        }
    }
}

// Maneja el botón "Vaciar Carrito"
async function clearCartBtnHandler() {
    if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
        try {
            const response = await fetch('data.php?action=clearCart');
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                // Vuelve a cargar el carrito pasando las referencias a los elementos DOM
                await loadCart(
                    document.getElementById('cart-items'),
                    document.getElementById('empty-cart-message'),
                    document.getElementById('cart-total'),
                    document.getElementById('clear-cart-btn'),
                    document.getElementById('checkout-btn')
                );
            } else {
                alert('Error al vaciar el carrito: ' + result.message);
            }
        } catch (error) {
            console.error('Error al vaciar el carrito:', error);
            alert('Ocurrió un error al intentar vaciar el carrito.');
        }
    }
}


// === Event Listeners ===
document.addEventListener('DOMContentLoaded', async () => {
    console.log("DEBUG: DOMContentLoaded disparado.");

    // Referencias a elementos del DOM de formularios y búsqueda
    const searchForm = document.getElementById('search-form');
    const newSearchBtn = document.getElementById('new-search-btn');
    const registerPackageForm = document.getElementById('register-package-form');

    // === REFERENCIAS CLAVE DEL CARRITO ===
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartTotalSpan = document.getElementById('cart-total');
    const clearCartBtn = document.getElementById('clear-cart-btn');
    const checkoutBtn = document.getElementById('checkout-btn');
    // ===================================================================

    // Adjuntar Event Listeners
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    }
    if (newSearchBtn) {
        newSearchBtn.addEventListener('click', resetSearch);
    }
    if (registerPackageForm) {
        registerPackageForm.addEventListener('submit', handleRegisterPackage);
    }
    // Adjunta el event listener a clearCartBtn aquí
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCartBtnHandler);
    }
   
    if (checkoutBtn) {

    }

    // Carga datos iniciales
    await loadPaquetes({}); // Carga todos los paquetes al inicio
    await loadNotifications(); // Carga notificaciones al inicio

    // Llama a loadCart pasando las referencias del DOM que ya están disponibles
    await loadCart(cartItemsContainer, emptyCartMessage, cartTotalSpan, clearCartBtn, checkoutBtn);

    // Actualizar notificaciones cada 10 segundos
    setInterval(loadNotifications, 10000);
});
