<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if user is logged in and has the role of Administrator and has permission to edit the project.
        $permission_check  = $this->route('project') && Auth::user()->hasPermissionTo('project-' . $this->route('project')->id . '-edit');
        $new_project_check = ( $this->request->has('new_project') && Auth::user()->hasRole(['Partner']) ) || Auth::user()->hasRole(['Administrator']);

        return Auth::check() && ($new_project_check || $permission_check);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|min:10',
            'description' => 'required|min:15',
            'activities.*.title' => 'required|min:4',
            // 'activities.*.description' => 'required|min:4',
            'activities.*.start' => 'required|date',
            'activities.*.end' => 'required|date',
            'activities.*.budget' => 'required|numeric',
            'outcomes.*.name' => 'required|min:10|max:370',
            'outputs.*.indicator' => 'required|min:10|max:370',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Project name is required',
            'name.min' => 'Project name must be at least 25 characters',
            'description.required' => 'Project description is required',
            // 'description.min' => 'Project description must be at least 25 characters',
            'activities.*.title.required' => 'Activity title is required',
            'activities.*.title.min' => 'Activity title must be at least 25 characters',
            'activities.*.description.required' => 'Activity description is required',
            // 'activities.*.description.min' => 'Activity description must be at least 25 characters',
            'activities.*.start.required' => 'Activity start date is required',
            'activities.*.start.date' => 'Activity start date must be a valid date',
            'activities.*.end.required' => 'Activity end date is required',
            'activities.*.end.date' => 'Activity end date must be a valid date',
            'activities.*.budget.required' => 'Activity budget is required',
            'activities.*.budget.numeric' => 'Activity budget must be a number',
            'outcomes.*.name.required' => 'Outcome name is required',
            'outcomes.*.name.min' => 'Outcome name must be at least 10 characters',
            'outcomes.*.name.max' => 'Outcome name must be less than 370 characters',
            'outputs.*.indicator.required' => 'Output indicator is required',
            'outputs.*.indicator.min' => 'Output indicator must be at least 10 characters',
            'outputs.*.indicator.max' => 'Output indicator must be less than 370 characters',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     */
    protected function failedAuthorization()
    {
        return redirect()->route('project_edit', $this->route('project'))->withErrors(['You are not authorized to edit this project.']);
    }

}
