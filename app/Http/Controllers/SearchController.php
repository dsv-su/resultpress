<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TaxonomyType;

class SearchController extends Controller
{
    /**
     * Search for projects
     *
     * @param string $q
     * @param Request $request
     * @return void
     */
    public function search($q = null, Request $request = null)
    { 
        $projects = $q ? 
            Project::where('name', 'like', '%' . $q . '%')
            ->orWhere('description', 'like', '%' . $q . '%')
            ->get() :
            Project::all();

        foreach ($projects as $key => $project) {
            if (!Auth::user()->hasPermissionTo("project-$project->id-list") && !Auth::user()->hasPermissionTo('project-list')) {
                unset($projects[$key]);
            }
        }
        //$projects = $this->filter($projects, array('owned'));
        $projectmanagers = $this->extractManagers($projects);
        $projectpartners = $this->extractPartners($projects);
        $programareas = $this->extractAreas($projects);
        $organisations = $this->extractOrgs($projects);
        $statuses = $this->extractStatuses($projects);
        $years = $this->extractYears($projects);
        $taxonomyTypes = TaxonomyType::whereModel('Project')->get();
        return view('home.search', compact('projects', 'q', 'projectpartners', 'projectmanagers', 'programareas', 'organisations', 'statuses', 'years', 'taxonomyTypes'));
    }

    /**
     * Filter search results.
     * 
     * @param Request $request
     * @return array
     */
    public function filterSearch(Request $request): array
    {
        $q = $request->q ?? null;
        $html = '';
        $user = Auth::user();
        $projects = $q ? 
            Project::where('name', 'like', '%' . $q . '%')
                ->orWhere('description', 'like', '%' . $q . '%')
                ->get() : 
            Project::all();
        $managers = request('manager') ? explode(',', request('manager')) : null;
        $partners = request('partner') ? explode(',', request('partner')) : null;
        $areas = request('area') ? explode(',', request('area')) : null;
        $organisations = request('organisation') ? explode(',', request('organisation')) : null;
        $statuses = request('status') ? explode(',', request('status')) : null;
        $years = request('year') ? explode(',', request('year')) : null;
        $filter = request('my') ? explode(',', request('my')) : array();

        if (in_array('requested', $filter)) {
            $projects = Project::withoutGlobalScopes()->OfType('project_add_request')->get();
        }

        if (in_array('archived', $filter)) {
            $projects = Project::withoutGlobalScopes()->OfType('project_archive')->get();
        }

        $taxonomyTypes = TaxonomyType::whereModel('Project')->get();
        foreach ($taxonomyTypes as $taxonomyType) {
            $requestTaxonomy = request($taxonomyType->slug) ? explode(',', request($taxonomyType->slug)) : null;
            if (!empty($requestTaxonomy)) {
                $projects = $projects->filter(function ($project) use ($requestTaxonomy) {
                    foreach ($requestTaxonomy as $taxonomy) {
                        if ($project->taxonomies->contains('id', $taxonomy)) {
                            return true;
                        }
                    }
                    return false;
                });
            }
        }

        // Prefilter presets
        if ($filter) {
            $projects = $this->filter($projects, $filter);
        }

        foreach ($projects as $key => $project) {
            if (!$user->hasPermissionTo("project-$project->id-list")) {
                unset($projects[$key]);
                continue;
            }
            $managerfound = $partnerfound = $areafound = $organisationfound = $yearfound = $statusfound = false;
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
                if (in_array('noarea', $areas) && $project->areas->isEmpty()) {
                    $areafound = true;
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
            if ($years) {
                foreach ($years as $year) {
                    if ($project->start->year <= $year && $project->end->year >= $year) {
                        $yearfound = true;
                    }
                }
            } else {
                $yearfound = true;
            }
            if ($statuses) {
                if (in_array($project->status(), $statuses)) {
                    $statusfound = true;
                }
            } else {
                $statusfound = true;
            }
            if ($managerfound && $partnerfound && $areafound && $organisationfound && $statusfound && $yearfound) {
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
        $years = $this->extractYears($projects);
        return [
            'html' => $html,
            'managers' => $projectmanagers,
            'partners' => $projectpartners,
            'areas' => $programareas,
            'organisations' => $organisations,
            'statuses' => $statuses,
            'years' => $years
        ];
    }

    /**
     * Find projects by query
     */
    public function find(Request $request)
    {
        return Project::search($request->get('query'), null, true, true)->get();
    }

    /**
     * Filter projects.
     * 
     * @param array $projects
     * @param array $filter
     */
    public function filter($projects, $filter)
    {
        foreach ($projects as $key => $project) {
            $ok = false;
            $user = Auth::user();
            if (in_array('owned', $filter) || in_array('requested', $filter) || in_array('archived', $filter)) {
                if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                    $ok = true;
                } elseif ($user->hasRole(['Partner'])) {
                    foreach ($project->partners() as $partner) {
                        if ($partner->id == $user->id) {
                            $ok = true;
                        }
                    }
                }
            }
            if (in_array('followed', $filter) && in_array($project->id, json_decode($user->follow_projects))) {
                $ok = true;
            }
            if (!$ok) {
               unset($projects[$key]);
            }
        }
        return $projects;
    }

    /**
     * Extract Managers.
     * 
     * @param array $projects
     * @return array
     */
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

    /**
     * Extract Partners.
     * 
     * @param array $projects
     * @return array
     */
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

    /**
     * Extract Areas.
     * 
     * @param array $projects
     * @return array
     */
    public function extractAreas($projects): array
    {
        $areas = array('noarea' => 'Not specified');
        foreach ($projects as $project) {
            foreach ($project->areas as $area) {
                if (!key_exists($area->id, $areas)) {
                    $areas[$area->id] = $area->name;
                }
            }
        }
        return $areas;
    }

    /**
     * Extract Orgs.
     * 
     * @param array $projects
     * @return array
     */
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

    /**
     * Extract Statuses.
     * 
     * @param array $projects
     * @return array
     */
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

    /**
     * Extract Years.
     * 
     * @param array $projects
     * @return array
     */
    public function extractYears($projects): array
    {
        $end = 0;
        $start = 9999;
        foreach ($projects as $project) {
            if ($project->start->year < $start) {
                $start = $project->start->year;
            }
            if ($project->end && $project->end->year > $end) {
                $end = $project->end->year;
            }
        }
        return range($start, $end);
    }
}
