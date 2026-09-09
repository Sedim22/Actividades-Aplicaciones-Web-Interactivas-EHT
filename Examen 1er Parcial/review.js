import { 
    get_movie_by_id, 
    add_movie_to_favorites, 
    delete_movie_from_favorites, 
    movie_is_in_favorites,
    get_current_movie_id
} from "./backend.js";
import { add_favorite_modal, update_favorite_list } from "./favorites.js";

$(document).ready(async function () {
    add_favorite_modal();
    update_favorite_list();

    const movie_id = get_current_movie_id();

    const movie = await get_movie_by_id(movie_id);

    $('#loading-spinner').remove();

    if (!movie) {
        showError("La película solicitada no pudo ser encontrada.");
        return;
    }

    $('title').text(`${movie.primaryTitle} - Review`);
    renderMovieDetail(movie);
    $('#movie-detail').fadeIn(400);
});

function showError(message) {
    $('#loading-spinner').remove();
    $('#movie-detail').html(`
        <div class="alert alert-danger text-center shadow-sm my-5 py-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill display-4 text-danger d-block mb-3"></i>
            <h4 class="alert-heading fw-bold">Error</h4>
            <p class="mb-3">${message}</p>
            <a href="index.html" class="btn btn-outline-danger"><i class="bi bi-arrow-left"></i> Volver al Inicio</a>
        </div>
    `).fadeIn(300);
}

function renderMovieDetail(movie) {
    // Formateadores y auxilires
    const formatCurrency = (val) => val ? new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(val) : 'N/A';
    const placeholderImg = "https://via.placeholder.com/500x750?text=Sin+Imagen";
    const imageSrc = movie.primaryImage || (movie.thumbnails && movie.thumbnails[0]?.url) || placeholderImg;
    const isFav = movie_is_in_favorites(movie);

    // Color del Metascore (≥70 verde, ≥50 amarillo, <50 rojo)
    let metascoreBg = "bg-danger";
    if (movie.metascore >= 70) metascoreBg = "bg-success";
    else if (movie.metascore >= 50) metascoreBg = "bg-warning text-dark";

    // Generar Badges
    const genresBadges = (movie.genres || []).map(g => `<span class="badge text-bg-primary me-1">${g}</span>`).join('') || 'N/A';
    const interestsBadges = (movie.interests || []).map(i => `<span class="badge text-bg-secondary me-1 mb-1">${i}</span>`).join('') || 'N/A';
    const spokenLangs = (movie.spokenLanguages || []).join(', ').toUpperCase() || 'N/A';
    const countries = (movie.countriesOfOrigin || []).join(', ') || 'N/A';
    const prodCompanies = (movie.productionCompanies || []).map(c => c.name).join(', ') || 'N/A';

    // Enlaces externos
    const externalLinksList = (movie.externalLinks && movie.externalLinks.length > 0)
        ? movie.externalLinks.map(link => `<a href="${link}" target="_blank" class="text-truncate d-inline-block mw-100 me-3 mb-1"><i class="bi bi-box-arrow-up-right"></i> ${new URL(link).hostname}</a>`).join('')
        : 'N/A';

    const template = `
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden p-3 p-md-4">
            <div class="row g-4 align-items-start">
                
                <!-- Poster de la Película -->
                <div class="col-12 col-md-4 text-center">
                    <img src="${imageSrc}" class="img-fluid rounded-3 shadow w-100" alt="${movie.primaryTitle}" style="max-height: 550px; object-fit: cover;">
                </div>

                <!-- Detalle Principal -->
                <div class="col-12 col-md-8">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h1 class="fw-bold mb-1 display-6">${movie.primaryTitle}</h1>
                            <p class="text-muted mb-2 fs-6">
                                <span>${movie.startYear || 'N/A'}</span> • 
                                <span>${movie.runtimeMinutes ? movie.runtimeMinutes + ' min' : 'N/A'}</span> • 
                                <span class="badge border border-secondary text-body">${movie.contentRating || 'NR'}</span>
                            </p>
                        </div>
                        
                        <!-- Botón Favorito en Detalle (Esquina Superior Derecha) -->
                        <button id="btn-detail-fav" class="btn ${isFav ? 'btn-danger' : 'btn-outline-danger'} rounded-pill px-3 d-flex align-items-center gap-2">
                            <i id="detail-fav-icon" class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i>
                            <span id="detail-fav-text">${isFav ? 'Favorito' : 'Agregar a Favoritos'}</span>
                        </button>
                    </div>

                    <!-- Géneros -->
                    <div class="mb-3">${genresBadges}</div>

                    <!-- Calificaciones: Rating + Metascore -->
                    <div class="row g-3 mb-4 align-items-center bg-body-tertiary p-3 rounded-3 mx-0">
                        <div class="col-6 col-sm-4 text-center border-end">
                            <div class="fs-4 fw-bold text-warning">
                                <i class="bi bi-star-fill"></i> ${movie.averageRating || 'N/A'}<span class="fs-6 text-muted">/10</span>
                            </div>
                            <small class="text-muted d-block">${movie.numVotes ? movie.numVotes.toLocaleString() + ' votos' : ''}</small>
                        </div>
                        <div class="col-6 col-sm-8">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small">Metascore:</span>
                                <span class="fw-bold">${movie.metascore ?? 'N/A'}</span>
                            </div>
                            <div class="progress" style="height: 10px;" role="progressbar" aria-valuenow="${movie.metascore || 0}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar ${metascoreBg}" style="width: ${movie.metascore || 0}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sinopsis -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Sinopsis</h5>
                        <p class="text-secondary lead fs-6">${movie.description || 'Sin descripción disponible.'}</p>
                    </div>

                    <!-- Botón Tráiler -->
                    ${movie.trailer ? `
                        <div class="mb-4">
                            <a href="${movie.trailer}" target="_blank" class="btn btn-red btn-danger rounded-3 px-4 py-2">
                                <i class="bi bi-play-circle-fill me-2"></i> Ver Tráiler
                            </a>
                        </div>
                    ` : ''}

                    <hr>

                    <!-- Lista de Metadatos -->
                    <div class="row g-3 fs-6">
                        <div class="col-12 col-sm-6">
                            <strong>Idiomas:</strong> <span class="text-muted">${spokenLangs}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <strong>Países de origen:</strong> <span class="text-muted">${countries}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <strong>Presupuesto:</strong> <span class="text-muted">${formatCurrency(movie.budget)}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <strong>Recaudación mundial:</strong> <span class="text-muted">${formatCurrency(movie.grossWorldwide)}</span>
                        </div>
                        <div class="col-12">
                            <strong>Productoras:</strong> <span class="text-muted">${prodCompanies}</span>
                        </div>
                        <div class="col-12">
                            <strong class="d-block mb-1">Intereses:</strong>
                            <div>${interestsBadges}</div>
                        </div>
                        <div class="col-12">
                            <strong class="d-block mb-1">Enlaces externos:</strong>
                            <div>${externalLinksList}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    `;

    $('#movie-detail').html(template);

    // Configuración de evento para el botón de Favorito
    $('#btn-detail-fav').click(function () {
        const currentlyFav = movie_is_in_favorites(movie);

        if (currentlyFav) {
            delete_movie_from_favorites(movie);
            $('#detail-fav-icon').removeClass('bi-heart-fill').addClass('bi-heart');
            $('#detail-fav-text').text('Agregar a Favoritos');
            $(this).removeClass('btn-danger').addClass('btn-outline-danger');
        } else {
            add_movie_to_favorites(movie);
            $('#detail-fav-icon').removeClass('bi-heart').addClass('bi-heart-fill');
            $('#detail-fav-text').text('Favorito');
            $(this).removeClass('btn-outline-danger').addClass('btn-danger');
        }

        // Sincronizar el modal y los contadores en la Navbar
        update_favorite_list();
    });
}