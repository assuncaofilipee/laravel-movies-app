<?php

namespace App\Http\Controllers;

use App\ViewModels\ActorsViewModel;
use App\ViewModels\ActorViewModel;
use Illuminate\Support\Facades\Http;

class ActorsController extends Controller
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
    public function index($page = 1)
    {
        abort_if($page > 500, 204);

        $popularActors = Http::withToken(config('services.tmdb.token'))
        ->get($this->tmdbUrl."person/popular?page=$page")
        ->json()['results'];

        $viewModel = new ActorsViewModel($popularActors, $page);
        
        return view('actors.index', $viewModel);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $actor = Http::withToken(config('services.tmdb.token'))
            ->get($this->tmdbUrl."person/$id")
            ->json();

        $social = Http::withToken(config('services.tmdb.token'))
            ->get($this->tmdbUrl."person/$id/external_ids")
            ->json();

        $credits = Http::withToken(config('services.tmdb.token'))
            ->get($this->tmdbUrl."person/$id/combined_credits")
            ->json();

        $viewModel = new ActorViewModel($actor, $social, $credits);
        
        return view('actors.show', $viewModel);
    }
}
