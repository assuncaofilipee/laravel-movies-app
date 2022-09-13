<?php

namespace App\Http\Controllers;

use App\ViewModels\MoviesViewModel;
use App\ViewModels\MovieViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MoviesController extends Controller
{
    public $tmdbUrl;

    public function __construct()
    {
        $this->tmdbUrl = getenv('TMDB_API_URL');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $popularMovies = Http::withToken(config('services.tmdb.token'))
        ->get($this->tmdbUrl.'movie/popular')
        ->json()['results'];

        $genres = Http::withToken(config('services.tmdb.token'))
        ->get($this->tmdbUrl.'genre/movie/list')
        ->json()['genres'];

        $nowPlayingMovies = Http::withToken(config('services.tmdb.token'))
        ->get($this->tmdbUrl.'movie/now_playing')
        ->json()['results'];

        $viewModel = new MoviesViewModel(
            $popularMovies,
            $nowPlayingMovies,
            $genres
        );

        return view('movies.index', $viewModel);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Http::withToken(config('services.tmdb.token'))
        ->get($this->tmdbUrl."movie/$id?append_to_response=credits,videos,images")
        ->json();

        $viewModel = new MovieViewModel($movie);
        
        return view('movies.show', $viewModel);
    }
}
