<?php

namespace App\Http\Controllers;

use App\Area;
use App\Project;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile settings
     *
     *
     */
    public function index()
    {
        if ($user = Auth::user()) {
            //Summary of project/programareas
            $data['programareas'] = DB::table('areas')
                ->join('project_areas', 'areas.id', '=', 'project_areas.area_id')
                ->join('projects', 'project_areas.project_id', '=', 'projects.id')
                ->select('areas.name', DB::raw('project_areas.area_id, count(*) as count'))
                ->where('projects.object_type', '=', 'project')
                ->when($user->isRegulatorAdmin || $user->isRegulator, function ($query) {
                    return $query->join('taxables', 'areas.id', '=', 'taxable_id')
                        ->join('taxonomies', 'taxables.taxonomy_id', '=', 'taxonomies.id')
                        ->where('taxonomies.slug', '=', 'regulator-area');
                })
                ->groupby('project_areas.area_id', 'areas.name')
                ->get();

            $data['user'] = User::find(auth()->user()->id);

            $data['areas'] = Area::with('projects.project_owner.user')->get();

            $data['otherprojects'] = Project::doesntHave('project_area')->get();

            return view('home.home', $data);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if(!$request->projects == null) {
            //Cast array to int
            $follow_projects = array_map('intval', $request->projects);
        }
        else {
            $follow_projects = '[]';
        }
        $user->follow_projects = $follow_projects;

        //Store profile picture
        if ($request->hasfile('profile')) {
            $path = Storage::disk('public')->putFile('/images/profiles', $request->file('profile'));
            $user->avatar = $path;
        }

        if($request->setting == 'true') {
            $user->setting = true;
        }
        else {
            $user->setting = false;
        }
        $user->save();

        return redirect()->route('home')->with('status', 'Profile settings updated');
    }
}
