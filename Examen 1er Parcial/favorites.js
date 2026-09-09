import {get_favorite_movies,clear_favorite_movies, delete_movie_from_favorites, select_current_movie} from "./backend.js";

const ID_FAV_CONTAINER = "#modal-fav";
const FAV_COUNTER_CLASS = ".fav-count";

function add_favorite_movie_component(movie) {
    const year = new Date(movie.releaseDate).getFullYear();
    const imageSrc = movie.primaryImage || (movie.thumbnails && movie.thumbnails[0]?.url) || '';


    const element = `
        <div id="fav-${movie.id}" class="col">
            <div class="card h-100 shadow-sm border rounded-3 overflow-hidden p-2">
                <img src="${imageSrc}" class="card-img-top rounded-2 img-fluid" alt="${movie.primaryTitle}" style="height: 180px; object-fit: cover;">
                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="card-title text-truncate mb-1 fw-bold" title="${movie.primaryTitle}">${movie.primaryTitle}</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">${year}</span>
                            <span class="text-muted small">${(movie.metascore / 10.0).toFixed(1)} <i class="bi bi-star-fill text-warning"></i></span>
                        </div>
                    </div>
                    <div class="row g-1 align-items-center">
                        <div class="col-9 d-grid">
                            <button id="review-fav-${movie.id}"  class="btn btn-primary btn-sm rounded-3"><i class="bi bi-eye"></i> Ver</button>
                        </div>
                        <div class="col-3 text-center">
                            <button id="hb-fav-${movie.id}" class="btn btn-outline-danger btn-sm w-100 rounded-circle p-1 d-flex align-items-center justify-content-center" style="height: 31px; width: 31px; margin: 0 auto;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $(ID_FAV_CONTAINER).append(element);
    $(`#hb-fav-${movie.id}`).click(function (e) { 
        e.preventDefault();
        $(`fav-${movie.id}`).remove();
        const id_heart = `#heart-${movie.id}`;
        $(id_heart).removeClass('bi-heart-fill');
        $(id_heart).addClass('bi-heart');
        delete_movie_from_favorites(movie);
        update_favorite_list();
    });

    $(`#review-fav-${movie.id}`).click(function (e) { 
        e.preventDefault();
        select_current_movie(movie);
        window.location.href = "review.html";
    });
}

export async function update_favorite_list() {
    const movies = get_favorite_movies();
    $(FAV_COUNTER_CLASS).text(`${movies.count}`);
    $(ID_FAV_CONTAINER).empty();
    
    $(ID_FAV_CONTAINER).addClass('justify-content-center');
    if (movies.results && movies.results.length > 0) {
        $(ID_FAV_CONTAINER).removeClass('justify-content-center');
        movies.results.forEach(movie => {
            add_favorite_movie_component(movie);
        });
    } else {
        
        $(ID_FAV_CONTAINER).html('<div class="col-12 text-center text-muted py-4"><p>No hay películas agregadas a favoritos.</p></div>');
    }
}

export function add_favorite_modal() {
    const modal = `
        <div
            class="modal fade"
            id="modalFav"
            tabindex="-1"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            role="dialog"
            aria-labelledby="modalTitleId"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
                role="document"
            >
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title d-flex align-items-center gap-2 fw-bold" id="modalTitleId">
                            <i class="bi bi-heart-fill text-danger"></i> Mis Favoritos 
                            <span class="badge rounded-pill text-bg-danger fav-count">0</span>
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body container p-3">
                        <div id="modal-fav" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                            <!-- Tarjetas de favoritos -->
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-secondary rounded-3"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                        <button id="del-favorites" type="button" class="btn btn-danger rounded-3">
                            <i class="bi bi-trash"></i> Eliminar todos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('body').append(modal);
    $('#del-favorites').click(function (e) { 
        e.preventDefault();
        clear_favorite_movies();
        update_favorite_list();
        $('.heart-class').removeClass('bi-heart-fill');
        $('.heart-class').addClass('bi-heart');
    });
}