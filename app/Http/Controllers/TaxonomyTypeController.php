<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hamedov\Taxonomies\Taxonomy;
use App\TaxonomyType;

class TaxonomyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = TaxonomyType::all();
        return view('taxonomies.types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function create(TaxonomyType $taxonomyType, $id = null)
    {
        $taxonomyType = taxonomyType::find($id) ?? null;
        return view('taxonomies.types.create', compact('taxonomyType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function store(TaxonomyType $taxonomyType)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);

        $taxonomyType->create($data);

        return redirect()->route('types.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxonomyType $taxonomyType)
    {
        return redirect()->route('types.index');
    }

    /**
     * Edit the specified resource.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxonomyType $taxonomyType, $id)
    {
        $taxonomyType = TaxonomyType::findOrfail($id);
        return view('taxonomies.types.edit', compact('taxonomyType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function update(TaxonomyType $taxonomyType, $id)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);
        $taxonomyType = TaxonomyType::findOrfail($id);
        $taxonomyType->update($data);

        return redirect()->route('types.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxonomyType $taxonomyType, $id)
    {
        $taxonomyType = TaxonomyType::findOrfail($id);
        $taxonomyType->delete();

        return redirect()->route('types.index');
    }

    /**
     * Display a listing of the terms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function terms(Request $request, TaxonomyType $taxonomyType, $id)
    {
        $taxonomyType = TaxonomyType::findOrfail($id);
        $terms = $taxonomyType->taxonomiesTree();
        $parent = null;
        return view('taxonomies.index', compact('terms', 'taxonomyType', 'parent'));
    }
}
