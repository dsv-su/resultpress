<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Myerscode\Laravel\Taxonomies\Taxonomy;
use \Myerscode\Laravel\Taxonomies\Term;

class TaxonomiesController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $taxonomies = Taxonomy::all();
        return view('taxonomies.index', compact('taxonomies'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Myerscode\Laravel\Taxonomies\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    function edit(Request $request, $id)
    {
        $taxonomy = Taxonomy::find($id);
        $terms = $taxonomy->terms;
        return view('taxonomies.edit', compact('taxonomy', 'terms'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Myerscode\Laravel\Taxonomies\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    function update(Request $request, $id)
    {
        $taxonomy = Taxonomy::findOrFail($id);
        // dd($request->all(), $taxonomy);
        $taxonomy->name = $request->name;
        $terms = collect($request->terms)->filter(function ($term) {
            return !empty($term);
        })->map(function ($term, $key) use ($taxonomy) {
            return Term::updateOrCreate(['id' => $key], ['name' => $term, 'taxonomy_id' => $taxonomy->id]);
        });
        $taxonomy->terms->filter(function ($term) use ($terms) {
            return !$terms->pluck('id')->contains($term->id);
        })->each(function ($term) {
            $term->delete();
        });
        $taxonomy->attachTerms($terms);
        $taxonomy->save();

        return redirect()->route('taxonomies.edit', $taxonomy->id);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Myerscode\Laravel\Taxonomies\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('taxonomies.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Myerscode\Laravel\Taxonomies\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $taxonomy = Taxonomy::create(['name' => $request->name]);
        return redirect()->route('taxonomies.edit', $taxonomy->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Taxonomy  $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $taxonomy = Taxonomy::findOrFail($id);
        $taxonomy->delete();
        return redirect()->route('taxonomies.index');
    }
}
