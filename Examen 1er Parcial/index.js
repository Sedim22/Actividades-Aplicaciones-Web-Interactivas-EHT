import {get_movies, add_movie_to_favorites, delete_movie_from_favorites, movie_is_in_favorites, select_current_movie} from './backend.js'
import {update_favorite_list, add_favorite_modal} from './favorites.js'

let search_filter = null;
let searchTimer = null;   // Temporizador para el buscador
let resizeTimer = null;   // Temporizador para el resize de la ventana


const button_all = {
    id: "b-all",
    range: null
};

const button_nineties = {
    id: "b-1990",
    range: {min: 1990, max: 1999}
};

const button_two_thousands = {
    id: "b-2000",
    range: {min: 2000, max: 2009}
};

const button_two_ten = {
    id: "b-2010",
    range: {min: 2010, max: 2019}
};

const button_twenties = {
    id: "b-2020",
    range: {min: 2020, max: 2026}
};

let current_button = button_all;



function getColumns() {
    const ancho = window.innerWidth;
    if (ancho >= 1024) {
        return 4; // Escritorio
    } else if (ancho >= 768) {
        return 2; // Tablet
    } else {
        return 1; // Móvil
    }
}

function add_movie_component(movie,row_id){
    const row_element = $(`#${row_id}`);
    const movie_release_date = new Date(movie.releaseDate);
    const id_rating = `rating-${movie.id}`;
    const id_fav_button = `fav-button-${movie.id}`;
    const id_heart = `heart-${movie.id}`;
    const fav_movie_id = `movie-fav-${movie.id}`;
    const ratingNum = movie.averageRating ? Math.trunc(movie.metascore/10.0) : 0;
    const heart_class = (movie_is_in_favorites(movie)) ? 'bi-heart-fill' : 'bi-heart';
    const movie_component = `
        <div id="${movie.id}" class="col border border-2 rounded p-2 mx-1 my-1 position-relative" style="width:270px;">
            <img class="rounded img-fluid" src="${movie.primaryImage}" alt="${movie.primaryTitle}">
            <div class="container mt-1">
                <div class="row">
                    <div class="col-9 text-truncate"> <strong>${movie.primaryTitle}</strong> </div>
                    <div id="${id_fav_button}" class="col-3 text-end"> <button class="btn"><i id="${id_heart}" class="bi ${heart_class} heart-class text-danger"></i></button> </div>
                </div>
                <div class="row">
                    <div class="col-8"> <p class="text-muted"> ${ movie_release_date.getFullYear()}</p> </div>
                    <div class="col-4 text-end"><span class="text-muted" style="font-size:12pt">${(movie.metascore/10.0).toFixed(1)} <i class="bi bi-star-fill text-warning"></i> </span> </div>
                </div>
                <div class="row">
                    <div id="${id_rating}" class="col-12 d-flex text-warning"></div>
                </div>
                <div class="row align-items-end">
                    <div id="review-${movie.id}" class="col-12 d-grid">
                        <button class="btn btn-primary"><i class="bi bi-eye"></i> Ver Reseña</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    row_element.append(movie_component);
    for (let index = 0; index < 10; index++) {
        const star = (index < ratingNum) ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
        $(`#${id_rating}`).append(star);
    }
    //Evento de boton review
    $(`#review-${movie.id}`).click(function (e) { 
        e.preventDefault();
        select_current_movie(movie);
        window.location.href = "review.html";
    });
    //Evento de boton favorito
    $(`#${id_fav_button}`).click(function (e) { 
        e.preventDefault();
        $(`#${id_heart}`).toggleClass('bi-heart bi-heart-fill');
        if (  $(`#${id_heart}`).hasClass('bi-heart-fill') ){
            //console.log('Agregar pelicula a favoritos');
            add_movie_to_favorites(movie);
            update_favorite_list();
        }
        else if($(`#${id_heart}`).hasClass('bi-heart')){
            //console.log('Eliminar pelicula de favoritos');
            delete_movie_from_favorites(movie);
            update_favorite_list();
        }
    });
    //agrega efectos y animaciones
    $(`#${movie.id}`).hover(
        function(){
        $(`#${movie.id}`).animate({top:'-10px'},200);
        },
        function(){
            $(`#${movie.id}`).animate({top:'0px'},200);
    });
}

async function refresh_movie_list({search,filter}) {
    const warn_alert = $('#warn-alert');
    const error_alert = $('#error-alert');

    warn_alert.addClass('visually-hidden');
    error_alert.addClass('visually-hidden');

    $('#movie-grid').empty();
    console.log("Cargando pelis");
    $('#loading-status').removeClass('visually-hidden');
    const movies = await get_movies({search,filter});
    console.log("tarea de pelis hecha");
    $('#loading-status').addClass('visually-hidden');
    if(movies === null){
        error_alert.removeClass('visually-hidden');
        return;
    }
    if(movies.length===0){
        warn_alert.removeClass('visually-hidden');
        return;
    }
    const n_columns = getColumns();
    for (let index = 0; index < movies.length; index++) {
        const row = Math.trunc(index/n_columns);
        const row_id = `movie-row-${row}`;
        const movie = movies[index];
        if( $(`#${row_id}`).length === 0 ){
            const row_container = `
                <div id="${row_id}" class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-4 justify-content-center"></div>
            `;
            $('#movie-grid').append(row_container);
        }
        add_movie_component(movie,row_id);
    }
}

$(document).ready(async function () {
    console.log('JQ: Finished loading dom');
    

    add_favorite_modal();
    $('#page-title').hide().fadeIn(600);
    $('#lead-page-title').hide().fadeIn(600);
    


    await refresh_movie_list({search: search_filter, filter: current_button.range});

    $(`#${button_all.id}`).click(async function (e) { 
        console.log('all');
        e.preventDefault();
        $(`#${current_button.id}`).removeClass('active');
        current_button = button_all;
        $(`#${current_button.id}`).addClass('active');
        await refresh_movie_list({search:search_filter,filter: current_button.range});
    });

    $(`#${button_nineties.id}`).click(async function (e) { 
        console.log('1990');
        e.preventDefault();
        $(`#${current_button.id}`).removeClass('active');
        current_button = button_nineties;
        $(`#${current_button.id}`).addClass('active');
        await refresh_movie_list({search:search_filter,filter: current_button.range});
    });

    $(`#${button_two_thousands.id}`).click(async function (e) { 
        console.log('2000');
        e.preventDefault();
        $(`#${current_button.id}`).removeClass('active');
        current_button = button_two_thousands;
        $(`#${current_button.id}`).addClass('active');
        await refresh_movie_list({search:search_filter,filter: current_button.range});
    });

    $(`#${button_two_ten.id}`).click(async function (e) { 
        console.log('2010');
        e.preventDefault();
        $(`#${current_button.id}`).removeClass('active');
        current_button = button_two_ten;
        $(`#${current_button.id}`).addClass('active');
        await refresh_movie_list({search:search_filter,filter: current_button.range});
    });

    $(`#${button_twenties.id}`).click(async function (e) { 
        console.log('2020');
        e.preventDefault();
        $(`#${current_button.id}`).removeClass('active');
        current_button = button_twenties;
        $(`#${current_button.id}`).addClass('active');
        await refresh_movie_list({search:search_filter,filter: current_button.range});
    });

    $('#search-bar').on('keyup', function() {
        const text = $(this).val();
        search_filter = (text.length === 0) ? null : text;

        clearTimeout(searchTimer);
        searchTimer = setTimeout(async () => {
            console.log(`Buscando: ${search_filter}`);
            await refresh_movie_list({search: search_filter, filter: current_button.range});
        }, 300); 
    });

    $('#reload').click(async function (e) { 
        e.preventDefault();
        await refresh_movie_list({search: search_filter, filter: current_button.range});
    });

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(async () => {
            console.log("Ventana redimensionada, redibujando...");
            await refresh_movie_list({search: search_filter, filter: current_button.range});
        }, 250); 
    });

    update_favorite_list();

});