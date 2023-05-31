<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaxonomyType;

class TaxonomiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaxonomyType  $taxonomyType
     * @param  string  $type
     * @param  string  $parent
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TaxonomyType $taxonomyType, $type = null, $parent = null)
    {
        $taxonomyType = TaxonomyType::findOrfail($request->get('type'));
        $terms = $request->get('parent') ? $taxonomyType->taxonomies()->where('parent_id', $request->get('parent'))->get() : $taxonomyType->taxonomies()->whereNull('parent_id')->get();
        $parentTax = $taxonomyType->taxonomies()->find($request->get('parent')) ?? null;
        $parent = $parentTax && $parentTax->parent_id ? $parentTax->parent_id : null;
        $requestParent = $request->get('parent');
        return view('taxonomies.index', compact('terms', 'taxonomyType', 'parent', 'parentTax', 'requestParent'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, TaxonomyType $taxonomyType)
    {
        $parent = $request->get('parent');
        $taxonomyType = TaxonomyType::find($request->get('type'));
        $parentTax = $taxonomyType->taxonomies()->find($parent) ?? null;
        if(!$taxonomyType) return redirect()->route('types.index')->withErrors(['type' => 'Type not found.']);

        $rootTaxonomies = $taxonomyType->taxonomiesHtmlSelect('parent_id', null, null, $parent);
        $types = TaxonomyType::all();
        return view('taxonomies.create', compact('taxonomyType', 'rootTaxonomies', 'types', 'parent', 'parentTax'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TaxonomyType $taxonomyType)
    {
        $data = request()->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|min:3|max:255',
            'parent_id' => 'nullable',
            'type' => 'required',
        ]);

        $data['parent_id'] = $data['parent_id'] ? $data['parent_id'] : null;

        $taxonomyType = TaxonomyType::where('slug', $data['type'])->first();
        if(!$taxonomyType) return redirect()->route('types.index')->withErrors(['type' => 'Type not found.']);
        
        $taxonomyType->taxonomies()->create($data);

        return redirect()->route('terms.index', ['type' => $taxonomyType->id])->with('success', 'Term created successfully.');
    }

    /**
     * Edit the specified resource.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, TaxonomyType $taxonomyType, $id)
    {
        $taxonomy = config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class)::find($id);
        if(!$taxonomy) return redirect()->back()->withErrors(['taxonomy' => 'Taxonomy not found.']);
        
        $taxonomyType = TaxonomyType::where('slug', $taxonomy->type)->first();
        if(!$taxonomyType) return redirect()->back()->withErrors(['type' => 'Type not found.']);

        $parentTax = $taxonomyType->taxonomies()->find($taxonomy->parent_id) ?? null;
        $rootTaxonomies = $taxonomyType->taxonomiesHtmlSelect('parent_id', $taxonomy, $taxonomy->id);

        return view('taxonomies.edit', compact('taxonomy', 'taxonomyType', 'rootTaxonomies', 'parentTax'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxonomyType $taxonomyType, $id)
    {
        $data = request()->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|min:3|max:255',
            'parent_id' => 'nullable',
        ]);

        $data['parent_id'] = $data['parent_id'] ? $data['parent_id'] : null;

        $taxonomy = config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class)::find($id);
        if(!$taxonomy) return redirect()->back()->withErrors(['taxonomy' => 'Taxonomy not found.']);
        
        $taxonomyType = TaxonomyType::where('slug', $taxonomy->type)->first();
        if(!$taxonomyType) return redirect()->back()->withErrors(['type' => 'Type not found.']);
        
        $taxonomy->update($data);

        return redirect()->route('terms.index', ['type' => $taxonomyType->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TaxonomyType  $taxonomyType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, TaxonomyType $taxonomyType, $id)
    {
        $taxonomy = config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class)::find($id);
        if(!$taxonomy) return redirect()->back()->withErrors(['taxonomy' => 'Taxonomy not found.']);
        
        $taxonomyType = TaxonomyType::where('slug', $taxonomy->type)->first();
        if(!$taxonomyType) return redirect()->back()->withErrors(['type' => 'Type not found.']);
        
        $taxonomy->delete();

        return redirect()->route('terms.index', ['type' => $taxonomyType->id, 'parent' => $taxonomy->parent_id])->with('success', 'Term deleted successfully.');
    }
}
