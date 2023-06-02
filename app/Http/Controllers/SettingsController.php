<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Settings::all();
        return view('settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get field type enum values
        $fieldTypes = Settings::getFieldTypes();
        return view('settings.create', compact('fieldTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSettingsRequest $request)
    {
        Settings::create($request->validated());
        return redirect()->route('settings.edit', ['setting' => $request->name])->with('success', 'Setting created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $setting
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit($setting, Settings $settings)
    {
        $setting = Settings::where('name', $setting)->firstOrFail();
        $fieldTypes = Settings::getFieldTypes();
        return view('settings.edit', compact('setting', 'fieldTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $request
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingsRequest $request, Settings $settings)
    {
        $setting = Settings::where('name', $request->name)->firstOrFail();
        // Upload file if it exists
        if($request->hasFile('value')){
            $fileName = $request->name . '-' . time() . '-' . $request->value->getClientOriginalName();
            $file = $request->file('value')->storeAs('settings', $fileName, 'public');
            $setting->value = $file;
        }
        else{
            $setting->value = $request->value;
        }
        $setting->type = $request->type;
        $setting->save();
        
        return redirect()->route('settings.edit', ['setting' => $request->name])->with('success', 'Setting updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Settings $settings)
    {
        $settings = Settings::where('name', $request->setting)->firstOrFail();
        $settings->delete();
        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully');
    }

    public function updateLogo(UpdateSettingsRequest $request)
    {
        if(!$request->hasFile('logo')){
            return redirect()->route('admin')->withErrors(['No file selected']);
        }
        $validated = $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if(empty($validated)){
            return redirect()->route('admin')->withErrors(['Invalid file']);
        }
        
        $fileName = 'logo-' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
        $logo = $request->file('logo')->storeAs('logos', $fileName, 'public');

        $settings = Settings::firstOrCreate(['name' => 'logo']);
        $settings->value = $logo;
        $settings->save();

        return redirect()->route('admin')->with('success', 'Logo updated successfully');
    }

    public function page(Request $request)
    {
        $page = $request->page;
        try {
            $settings = Settings::where('name', $page)->firstOrFail();
            if(!in_array($settings->type, ['html', 'wysiwyg'])){
                return redirect()->route('home')->withErrors(['Page not found']);
            }
        } catch (\Throwable $th) {
            return redirect()->route('home')->withErrors(['Page not found']);
        }
        return view('settings.page', compact('settings'));
    }
}
