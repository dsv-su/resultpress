<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search($q, Request $request = null)
    {
        if ($q) {
            $projects = Project::search($q, null, true, true)->get();
        } else {
            $projects = Project::all();
        }
        foreach ($projects as $key=>$project) {
            if (!Auth::user()->hasPermissionTo("project-$project->id-update")) {
                unset($projects[$key]);
            }
        }
        $projectmanagers = $this->extractManagers($projects);
        $projectpartners = $this->extractPartners($projects);
        $programareas = $this->extractAreas($projects);
        $organisations = $this->extractOrgs($projects);
        $statuses = $this->extractStatuses($projects);
        return view('home.search', compact('projects', 'q', 'projectpartners', 'projectmanagers', 'programareas', 'organisations', 'statuses'));
    }

    public function filterSearch($q, Request $request)
    {
        $html = '';
        $projects = Project::search($q, null, true, true)->get();
        $managers = request('manager') ? explode(',', request('manager')) : null;
        $partners = request('partner') ? explode(',', request('partner')) : null;
        $areas = request('area') ? explode(',', request('area')) : null;
        $organisations = request('organisation') ? explode(',', request('organisation')) : null;
        $statuses =  request('status') ? explode(',', request('status')) : null;

        foreach ($projects as $key => $project) {
            if (!Auth::user()->hasPermissionTo("project-$project->id-update")) {
                continue;
            }
            $managerfound = $partnerfound = $areafound = $organisationfound = $statusfound = false;
            if ($managers) {
                foreach ($project->managers() as $manager) {
                    if (in_array($manager->id, $managers)) {
                        $managerfound = true;
                    }
                }
            } else {
                $managerfound = true;
            }
            if ($partners) {
                foreach ($project->partners() as $partner) {
                    if (in_array($partner->id, $partners)) {
                        $partnerfound = true;
                    }
                }
            } else {
                $partnerfound = true;
            }
            if ($areas) {
                foreach ($project->areas as $area) {
                    if (in_array($area->id, $areas)) {
                        $areafound = true;
                    }
                }
            } else {
                $areafound = true;
            }
            if ($organisations) {
                foreach ($project->partners() as $partner) {
                    foreach (User::find($partner->id)->organisations as $org) {
                        if (in_array($org->id, $organisations)) {
                            $organisationfound = true;
                        }
                    }
                }
            } else {
                $organisationfound = true;
            }
            if ($statuses) {
                if (in_array($project->status(), $statuses)) {
                    $statusfound = true;
                }
            } else {
                $statusfound = true;
            }
            if ($managerfound && $partnerfound && $areafound && $organisationfound && $statusfound) {
                $html .= '<div class="col my-3">' . view('project.project_list', ['project' => $project])->render() . '</div>';
            } else {
                unset($projects[$key]);
            }
        }

        if (!$html) {
            $html .= '<p class="col my-3 font-weight-light">No projects found</p>';
        }

        $projectmanagers = $this->extractManagers($projects);
        $projectpartners = $this->extractPartners($projects);
        $programareas = $this->extractAreas($projects);
        $organisations = $this->extractOrgs($projects);
        $statuses = $this->extractStatuses($projects);
        return ['html' => $html, 'managers' => $projectmanagers, 'partners' => $projectpartners, 'areas' => $programareas, 'organisations' => $organisations, 'statuses' => $statuses];
    }

    public function find(Request $request)
    {
        return Project::search($request->get('query'), null, true, true)->get();
    }

    public function extractManagers($projects): array
    {
        $managers = array();
        foreach ($projects as $project) {
            foreach ($project->managers() as $manager) {
                if (!key_exists($manager->id, $managers)) {
                    $managers[$manager->id] = $manager->name;
                }
            }
        }
        return $managers;
    }

    public function extractPartners($projects): array
    {
        $partners = array();
        foreach ($projects as $project) {
            foreach ($project->partners() as $partner) {
                if (!key_exists($partner->id, $partners)) {
                    $partners[$partner->id] = $partner->name;
                }
            }
        }
        return $partners;
    }

    public function extractAreas($projects): array
    {
        $areas = array();
        foreach ($projects as $project) {
            foreach ($project->areas as $area) {
                if (!key_exists($area->id, $areas)) {
                    $areas[$area->id] = $area->name;
                }
            }
        }
        return $areas;
    }

    public function extractOrgs($projects): array
    {
        $organisations = array();
        foreach ($this->extractPartners($projects) as $id => $name) {
            $orgs = User::find($id)->organisations;
            foreach ($orgs as $org) {
                if (!key_exists($org->id, $organisations)) {
                    $organisations[$org->id] = $org->org;
                }
            }
        }
        return $organisations;
    }

    public function extractStatuses($projects): array
    {
        $statuses = array();
        foreach ($projects as $project) {
            if (!in_array($project->status(), $statuses)) {
                $statuses[] = $project->status();
            }
        }
        return $statuses;
    }
}
