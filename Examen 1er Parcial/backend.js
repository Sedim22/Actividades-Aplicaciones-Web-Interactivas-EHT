const inRange = (num, min, max) => num >= min && num <= max;
const FAV_MOVIES_KEY = 'favMovies';
const FAV_MOVIE_COUNT_KEY = 'favMovieCount';

async function load_from_api(search,filter) {
    const url = 'https://imdb236.p.rapidapi.com/api/imdb/search';
    
    const general_search_data = { type: 'movie', rows: 100, cursor:"" };
    
    const data = (search === null || search === "") 
        ? general_search_data 
        : { ...general_search_data, primaryTitle: search };

    return new Promise((resolve) => {
        $.ajax({
            type: "GET",
            url: url,
            data: data,
            headers: {
                'x-rapidapi-key': 'a0762e0fbamshf73b2c28d7bd7e4p16603fjsnff9a93eaaa3d',
                'x-rapidapi-host': 'imdb236.p.rapidapi.com'
            },
            success: function (response) {
                console.log('Successfully loaded movies from api');

                let movies = response.results;
                if(filter !== null){
                    movies = response.results.filter(movie => {
                    if (!movie.releaseDate) return false;
                    const anio = new Date(movie.releaseDate).getFullYear();
                    return inRange(anio, filter.min, filter.max);
                    });
                }

                resolve(movies);
            },
            error: function(xhr) {
                console.error('Could not get data from imdbAPI');
                resolve(null);
            }
        });
    });
}


async function load_from_json() {
    return new Promise((resolve) => {
        $.getJSON("PELICULAS.json", function (data) {
            console.log('Successfully loaded movies from json');
            resolve(data);
        }).fail(function() {
            console.error("Could not get data from JSON");
            resolve(null);
        });
    });
}

export function add_movie_to_favorites(movie) {
    const data = localStorage.getItem(FAV_MOVIES_KEY);
    let movies = data !== null ? JSON.parse(data) : [];

    const exists = movies.some(fav => fav.id === movie.id);
    if (exists) {
        console.warn(`La película with id ${movie.id} ya está en favoritos.`);
        return;
    }

    movies.push(movie);

    localStorage.setItem(FAV_MOVIES_KEY, JSON.stringify(movies));
    localStorage.setItem(FAV_MOVIE_COUNT_KEY, movies.length.toString());

    console.log(`Agregada película: ${movie.id} a favoritos`);
    console.log(movies);
}

export function delete_movie_from_favorites(movie) {
    const data = localStorage.getItem(FAV_MOVIES_KEY);
    
    if (data === null) {
        console.error('No hay películas en favoritos para eliminar.');
        return;
    }

    const currentMovies = JSON.parse(data);
    const updatedMovies = currentMovies.filter(fav => fav.id !== movie.id);

    if (currentMovies.length === updatedMovies.length) {
        console.warn(`La película con id ${movie.id} no se encontró en favoritos.`);
        return;
    }

    localStorage.setItem(FAV_MOVIES_KEY, JSON.stringify(updatedMovies));
    localStorage.setItem(FAV_MOVIE_COUNT_KEY, updatedMovies.length.toString());

    console.log(`Eliminada película: ${movie.id} de favoritos`);
    console.log(updatedMovies);
}

export function clear_favorite_movies(){
    const zero_length = 0;
    localStorage.setItem(FAV_MOVIES_KEY, JSON.stringify([]));
    localStorage.setItem(FAV_MOVIE_COUNT_KEY,zero_length.toString());
}

export function get_favorite_movies(){
    const data = localStorage.getItem(FAV_MOVIES_KEY);
    const movies = data !== null ? JSON.parse(data) : [];
    const count = movies.length;
    return {results:movies,count:count};
}

export function movie_is_in_favorites(movie){
    const data = get_favorite_movies();
    const filtered = data.results.filter(element => element.id !== movie.id);
    return data.results.length !== filtered.length;
}

export function select_current_movie(movie){
    localStorage.setItem("selected",movie.id);    
}

export function deselect_current_movie(movie){
    localStorage.setItem("selected","");    
}


export function get_current_movie_id(){
    const data = localStorage.getItem("selected");
    console.log(data);
    const movie_id =  data !== null ? data : "";
    return movie_id;
}

export async function get_movie_by_id(id) {
    if (!id) return null;
    const apiUrl = `https://imdb236.p.rapidapi.com/api/imdb/titles/${id}`;
    let movieData = await new Promise((resolve) => {
        $.ajax({
            type: "GET",
            url: apiUrl,
            headers: {
                'x-rapidapi-key': 'a0762e0fbamshf73b2c28d7bd7e4p16603fjsnff9a93eaaa3d',
                'x-rapidapi-host': 'imdb236.p.rapidapi.com'
            },
            success: function (response) {
                resolve(response || null);
            },
            error: function () {
                resolve(null);
            }
        });
    });
    if (!movieData) {
        console.log("Fallo al obtener película de la API. Buscando en PELICULAS.json...");
        const jsonMovies = await load_from_json();
        if (jsonMovies) {
            movieData = jsonMovies.find(m => m.id === id) || null;
        }
    }
    return movieData;
}

export async function get_movies({ search, filter }) {
    let movies = null;
    movies = await load_from_api(search, filter);
    
    if (movies === null) {
        console.log("Loading from json");
        const result = await load_from_json();
        
        if (result !== null) {
            if (search === null && filter === null) {
                movies = result;
            }
            else if (search !== null && filter === null) {
                movies = result.filter(movie => movie.primaryTitle.toLowerCase().includes(search.toLowerCase()));
            }
            else if (search === null && filter !== null) {
                console.log('Searching by decade...');
                movies = result.filter(movie => {
                    if (!movie.releaseDate) return false;
                    const anio = new Date(movie.releaseDate).getFullYear();
                    return inRange(anio, filter.min, filter.max);
                });
            }
            else if (search !== null && filter !== null) {
                const filtered_search = result.filter(movie => movie.primaryTitle.toLowerCase().includes(search.toLowerCase()));
                
                movies = filtered_search.filter(movie => {
                    if (!movie.releaseDate) return false;
                    const anio = new Date(movie.releaseDate).getFullYear();
                    return inRange(anio, filter.min, filter.max);
                });
            }
        } else {
            movies = null;
        }
    }
    
    if (movies !== null)
        console.log(movies);
    return movies;
}