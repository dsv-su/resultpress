<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($q, Request $request = null)
    {
        if ($q) {
            $projects = Project::search($q, null, true, true)->get();
        } else {
            $projects = Project::all();
        }
        $projectmanagers = $this->extractManagers($projects);
        $projectpartners = $this->extractPartners($projects);
        $programareas = $this->extractAreas($projects);
        return view('home.search', compact('projects', 'q', 'projectpartners', 'projectmanagers', 'programareas'));
    }

    public function filterSearch($q, Request $request) {
        $html = '';
        $projects = Project::search($q, null, true, true)->get();
        $managers = request('manager') ? explode(',', request('manager')) : null;
        $partners = request('partner') ? explode(',', request('partner')) : null;
        $areas = request('area') ? explode(',', request('area')) : null;

        foreach ($projects as $key => $project) {
            $managerfound = $partnerfound = $areafound = false;
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
            if ($managerfound && $partnerfound && $areafound) {
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
        return ['html' => $html, 'managers' => $projectmanagers, 'partners' => $projectpartners, 'areas' => $programareas];
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

    public function extractAreas($projects): array {
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
}
