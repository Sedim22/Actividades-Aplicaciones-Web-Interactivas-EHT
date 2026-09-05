const id_button_todos = '#b-todos';
const id_button_nuevo = '#b-nuevo';
const id_button_oferta = '#b-oferta';
const id_button_eco = '#b-eco';
const id_carrito_modal = '#carritoModal';
const shoppingCart = '#shoppingCart';
const clear_shopping_cart = '#shopping-clear';

let products = [];
let carrito = [];
let product_index_id = -1;

let last_button_pressed = id_button_todos;

function addProductToShoppingCart(product_id, product_name, price) {
    product_index_id += 1;
    
    // IDs únicos para fila y botón de eliminar
    const row_id = `cart-item-${product_index_id}`;
    const delete_button_id = `b-${product_index_id}`;

    const content = `
        <tr id="${row_id}">
            <td scope="row">${product_name}</td>
            <td>$${price}</td>
            <td> 
                <button id="${delete_button_id}" class="btn p-0 border-0">
                    <i class="bi bi-trash text-danger"></i>
                </button>
            </td>
        </tr>
    `;

    $(shoppingCart).append(content);

    // Asignación limpia del evento eliminar
    $(`#${delete_button_id}`).click(function (e) { 
        e.preventDefault();
        $(`#${row_id}`).remove();
    });
}

async function showProductList(list, animated) {
    const cartModal = new bootstrap.Modal(document.getElementById('carritoModal'));
    const id_product_container = '#lista-productos'; 

    if (animated === true) {
        $(id_product_container).stop(true, true);
        await $(id_product_container).fadeOut(600).promise();
    }

    $(id_product_container).empty();

    list.forEach(product => {
        let badge_class = '';
        let badge_text = '';
        if (product.etiqueta === 'nuevo') {
            badge_class = 'badge bg-primary mb-2';
            badge_text = 'Nuevo';
        } else if (product.etiqueta === 'oferta') {
            badge_class = 'badge bg-warning text-dark mb-2';
            badge_text = 'Oferta';
        } else if (product.etiqueta === 'eco') {
            badge_class = 'badge bg-success mb-2';
            badge_text = 'Eco';
        }

        // Corregido: eliminación de llave extra en ${badge_class}
        const product_element = `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <img src="${product.imagen}" class="card-img-top" alt="${product.nombre}">
                    <div class="card-body">
                        <span class="${badge_class}">${badge_text}</span>
                        <h5 class="card-title">${product.nombre}</h5>
                        <p class="card-text text-muted">${product.descripcion}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">$${product.precio}</span>
                            <button class="btn btn-sm btn-outline-primary btn-add-to-cart" 
                                    data-id="${product.id}"
                                    data-nombre="${product.nombre}"
                                    data-precio="${product.precio}">
                                Añadir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $(id_product_container).append(product_element);
    });

    $(id_product_container).off('click', '.btn-add-to-cart').on('click', '.btn-add-to-cart', function (e) {
        e.preventDefault();

        const pName = $(this).data('nombre');
        const pPrice = $(this).data('precio');
        const pId = $(this).data('id');

        $('#modalProductName').text(pName);
        $('#modalProductPrice').text('$' + pPrice);

        addProductToShoppingCart(pId, pName, pPrice);

        cartModal.show();
    });

    if (animated === true) {
        await $(id_product_container).fadeIn(600).promise();
    }
}

$(document).ready( async function () {

    $.getJSON("./productos.json",
            async function (data) {
                 await showProductList(data, false);
            }
        );

    console.log('Landing page de tienda cargada (jQuery)');

    // Instancias de los modales Bootstrap
    const cartModal = new bootstrap.Modal(document.getElementById('carritoModal'));
    const subModal = new bootstrap.Modal(document.getElementById('suscripcionModal'));
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    // BOTONES DE AÑADIR AL CARRITO =====
    $('.btn-add-to-cart').on('click', function (e) {
        e.preventDefault();

        // Obtener datos del producto
        const product = $(this).data('product');
        const price = $(this).data('price');

        // Actualizar modal
        $('#modalProductName').text(product);
        $('#modalProductPrice').text('$' + price);

        // Mostrar modal
        cartModal.show();
    });

    // BOTÓN DE SUSCRIPCIÓN =====
    $('#btnSubscribe').on('click', function () {
        const email = $('#emailInput').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && emailRegex.test(email)) {
            $('#modalEmailSubscription').text(email);
            subModal.show();
            $('#emailInput').val('');
        } else {
            errorModal.show();
        }
    });

    // SCROLL SUAVE PARA ENLACES DEL NAVBAR =====
    $('a[href^="#"]').on('click', function (e) {
        const target = $($(this).attr('href'));

        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 600);
        }
    });

    // EVENTOS DE CIERRE DE MODALES =====
    $('#carritoModal').on('hidden.bs.modal', function () {
        console.log('Modal de carrito cerrado (jQuery)');
    });

    $('#suscripcionModal').on('hidden.bs.modal', function () {
        console.log('Modal de suscripción cerrado (jQuery)');
    });

    console.log('Todos los modales están listos (jQuery)');


    $(id_button_todos).on('click',async function() {
         if(last_button_pressed === id_button_todos)
            return;
        $(last_button_pressed).removeClass('active');
        $(id_button_todos).addClass('active');
        last_button_pressed = id_button_todos;

        $.getJSON("./productos.json",
            async function (data) {
                console.log('Todos los products:\n'+data);
                await showProductList(data, true);
            }
        );


        console.log('mostrar todos');
    });

    //Evento de barra de busqueda
    $('#search').keyup(function (e) { 
        $(last_button_pressed).removeClass('active');
        const texto = $(this).val();
        $.getJSON("./productos.json",
            async function (data) {
                const products = data.filter( element => element.nombre.toLowerCase().includes(texto.toLowerCase()));
                if(products.length!==0){
                    $('#product-alert').addClass('d-none');
                }
                else{
                    $('#product-alert').removeClass('d-none');
                }
                await showProductList(products,false);
            }
        );
    });

    //Evento de botones
    $(id_button_nuevo).on('click',async function() {
        $('#search').text('');
        if(last_button_pressed === id_button_nuevo)
            return;
        $(last_button_pressed).removeClass('active');
        $(id_button_nuevo).addClass('active');
        last_button_pressed = id_button_nuevo;
        console.log('mostrar nuevos');
        $.getJSON("./productos.json",
            async function (data) {
                const new_products = data.filter( element => element.etiqueta === 'nuevo' );
                console.log('Todos los products:\n'+new_products);
                 await showProductList(new_products, true);
            }
        );
    });

    $(id_button_oferta).on('click',function() {
        $('#search').text('');
        $(last_button_pressed).removeClass('active');
        $(id_button_oferta).addClass('active');
        last_button_pressed = id_button_oferta;
        console.log('mostrar ofertas');
        $.getJSON("./productos.json",
            async function (data) {
                const discount_products = data.filter( element => element.etiqueta === 'oferta' );
                console.log('Todos los products:\n'+discount_products);
                await showProductList(discount_products, true);
            }
        );
    });

    $(id_button_eco).on('click',function() {
        $('#search').text('');
        $(last_button_pressed).removeClass('active');
        $(id_button_eco).addClass('active');
        last_button_pressed = id_button_eco;
        console.log('mostrar eco');
        $.getJSON("./productos.json",
            async function (data) {
                const eco_products = data.filter( element => element.etiqueta === 'eco' );
                console.log('Todos los products:\n'+eco_products);
                await showProductList(eco_products, true);
            }
        );
    });

    $('#search').text('');

    $(clear_shopping_cart).on('click',function(){
        $(shoppingCart).empty();
    });

});




