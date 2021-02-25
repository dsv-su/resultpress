@extends('layouts.master')
@section('content')

    <div class="form-row">
        <div class="col">
            <h4>Project administration: @empty($project->id) Add a new project @else
                    Update {{$project->name}} @endempty</h4>
        </div>
    </div>

    @empty($project->id)
        <form action="{{ route('update', $project) }}" method="POST">
            <input name="new_project" value="1" hidden>
            @else
                <form action="{{ route('project_update', $project) }}" method="POST">
                    @method('PUT')
                    @endempty
                    @csrf
                    <div class="form-group">
                        <label for="activities_list" class="form-group-header">Details</label>
                        <div class="col-lg-6 my-2 px-2" id="project_details" style="min-width: 16rem;">
                            <div class="form-row mb-1 row">
                                <label for="project_name" class="col-4 col-sm-3 col-form-label-sm">Name</label>
                                <div class="px-1 col">
                                    <input class="form-control form-control-sm @error('project_name') is-danger @enderror"
                                           type="text" name="project_name" id="project_name"
                                           placeholder="Project title"
                                           required
                                           value="{{ old('project_name', empty($project) ? '' : $project->name) }}">
                                    @error('project_name')
                                    <div class="text-danger">{{ $errors->first('project_name') }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row row">
                                <label for="project_description"
                                       class="col-4 col-sm-3 col-form-label-sm">Description</label>
                                <div class="col px-1">
                                <textarea rows="2"
                                          class="form-control form-control-sm @error('project_description') is-danger @enderror"
                                          name="project_description" id="project_description"
                                >{!! old('project_description', empty($project) ? '' : $project->description) !!}</textarea>
                                    @error('project_description')
                                    <div class="text-danger">
                                        {{ $errors->first('project_description') }}
                                    </div>@enderror
                                </div>
                            </div>
                            <div class="form-row row">
                                <label for="project_area" class="col-4 col-sm-3 col-form-label-sm">Area</label>
                                <div class="col px-1">
                                    <select name="project_area[]" id="project_area" class="custom-select-sm"
                                            multiple="multiple">
                                        @foreach($areas as $pa)
                                            <option value="{{$pa->id}}" {{ old('pa_id') == $pa->id || in_array($pa->id, $old_pa) ? 'selected':''}}>{{$pa->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <label for="project_start" class="col-3 col-form-label-sm">Start</label>
                                <div class="col-4">
                                    <input type="text" name="project_start" id="project_start"
                                           placeholder="Start date"
                                           value="{{ old('project_start', empty($project->start) ? '' : $project->start->format('d-m-Y'))}}"
                                           class="form-control form-control-sm datepicker" required>
                                </div>
                                <label for="project_end" class="col-1 col-form-label-sm text-right">End</label>
                                <div class="col-4">
                                    <input type="text" name="project_end" id="project_end" placeholder="End date"
                                           value="{{ old('project_end', empty($project->end) ? '' : $project->end->format('d-m-Y'))}}"
                                           class="form-control form-control-sm datepicker">
                                </div>
                            </div>
                            <div class="form-row row">
                                <label for="project_currency" class="col-3 col-form-label-sm">Currency</label>
                                <div class="col-2">
                                    <select name="project_currency" id="project_currency"
                                            class="form-control form-control-sm">
                                        <option value="SEK"
                                                @if ($project->currency == 'SEK' || !$project->currency) selected @endif>
                                            kr
                                        </option>
                                        <option value="EUR" @if ($project->currency == 'EUR') selected @endif>€
                                        </option>
                                        <option value="USD" @if ($project->currency == 'USD') selected @endif>$
                                        </option>
                                        <option value="GBP" @if ($project->currency == 'GBP') selected @endif>£
                                        </option>
                                    </select>
                                </div>
                                <label for="project_cumulative" class="col-5 col-form-label-sm text-right">Cumulative
                                    updates</label>
                                <div class="col-2">
                                    <select name="project_cumulative" class="form-control form-control-sm">
                                        <option value="1" @if($project->cumulative) selected="selected" @endif>Yes
                                        </option>
                                        <option value="0" @if(!$project->cumulative) selected="selected" @endif>No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="activities_list" class="form-group-header">Activities</label>
                            <div class="d-flex flex-wrap" id="activities_list">
                                @foreach ($activities as $activity)
                                    <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                        <div class="card bg-light m-auto">
                                            <div class="card-body pb-1">
                                                <div class="form-group mb-1 row">
                                                    <input type="hidden" name="activity_id[]" value="{{$activity->id}}">
                                                    <label for="activity_name[]"
                                                           class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
                                                    <div class="col-8 col-sm-9 px-1">
                                                        <input type="text" name="activity_name[]"
                                                               value="{{$activity->title}}" required
                                                               class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-2 row">
                                                    <label for="activity_name[]"
                                                           class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Description</label>
                                                    <div class="col-8 col-sm-9 px-1">
                                                        <textarea type="text" name="activity_description[]"
                                                                  id="activity_description" required
                                                                  class="form-control form-control-sm"
                                                                  placeholder="Description">{{$activity->description}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-1 row">
                                                    <label for="activity_start[]"
                                                           class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Start</label>
                                                    <div class="col-8 col-sm-4 px-1">
                                                        <input type="text" name="activity_start[]"
                                                               value="{{$activity->start->format('d-m-Y')}}" required
                                                               class="form-control form-control-sm datepicker">
                                                    </div>
                                                    <label for="activity_end[]"
                                                           class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">End</label>
                                                    <div class="col-8 col-sm-4 px-1">
                                                        <input type="text" name="activity_end[]"
                                                               value="{{$activity->end->format('d-m-Y')}}" required
                                                               class="form-control form-control-sm datepicker">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-2 row">
                                                    <label for="activity_reminder[]"
                                                           class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Email
                                                        reminder</label>
                                                    <div class="col-8 col-sm-9 px-1 form-inline">
                                                        <select name="activity_reminder[]"
                                                                class="form-control form-control-sm">
                                                            <option value="1"
                                                                    @if($activity->reminder) selected="selected" @endif>
                                                                Yes
                                                            </option>
                                                            <option value="0"
                                                                    @if(!$activity->reminder) selected="selected" @endif>
                                                                No
                                                            </option>
                                                        </select>

                                                        <input type="number" name="activity_reminder_due_days[]"
                                                               value="{{$activity->reminder_due_days}}"
                                                               class="form-control form-control-sm text-right mx-1"
                                                               style="width:60px;">

                                                        <label for="activity_reminder_due_days[]"
                                                               class="pl-0 pr-1 col-form-label-sm text-left">days
                                                            before end</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-2 row">
                                                    <label for="activity_budget[]"
                                                           class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label>
                                                    <div class="col col-sm-4 pl-1 pr-1">
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" name="activity_budget[]"
                                                                   placeholder="0"
                                                                   value="{{$activity->budget}}"
                                                                   required
                                                                   class="form-control text-right">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text currency"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label for="activity_template[]"
                                                           class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template
                                                        <i class="fas fa-chevron-right collapseEditor"></i></label>
                                                    <div class="col-8 col-sm-9 px-1">
                                                        <textarea name="activity_template[]" id="activity_template"
                                                                  placeholder="Activity description template"
                                                                  class="form-control form-control-sm mediumEditor collapsed">
                                                                    {{$activity->template}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <a name="copy"
                                                       class="btn btn-outline-secondary btn-sm copy ml-auto mt-1"><i
                                                                class="far fa-copy"></i></a>
                                                    <a name="remove"
                                                       class="btn btn-outline-danger btn-sm remove ml-1 mt-1"><i
                                                                class="far fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" name="add_activities"
                                    class="btn btn-outline-secondary btn-sm add-activities m-2">Add
                                Activity <i class="fas fa-plus"></i></button>

                        <!--
                            <div>
                                <table class="table table-sm" id="activities_table"
                                       @if($activities->isEmpty()) style="display:none;" @endif>
                                    <thead>
                                    <th scope="row">Activity Name</th>
                                    <th scope="row">Description</th>
                                    <th scope="row">Start</th>
                                    <th scope="row">End</th>
                                    <th scope="row">Email reminder</th>
                                    <th scope="row">Due days before</th>
                                    <th scope="row">Budget</th>
                                    <th></th>
                                    </thead>

                                    @foreach ($activities as $activity)
                            <tr>
                                <td><input type="hidden" name="activity_id[]" value="{{$activity->id}}">
                                                <input type="text" name="activity_name[]"
                                                       value="{{$activity->title}}" required
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td><input type="text" name="activity_description[]"
                                                       value="{{$activity->description}}" required
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td><input type="text" name="activity_start[]"
                                                       value="{{$activity->start->format('d-m-Y')}}" required
                                                       class="form-control form-control-sm datepicker">
                                            </td>
                                            <td><input type="text" name="activity_end[]"
                                                       value="{{$activity->end->format('d-m-Y')}}" required
                                                       class="form-control form-control-sm datepicker">
                                            </td>
                                            <td><select name="activity_reminder[]" class="form-control form-control-sm">
                                                    <option value="1"
                                                            @if($activity->reminder) selected="selected" @endif>Yes
                                                    </option>
                                                    <option value="0"
                                                            @if(!$activity->reminder) selected="selected" @endif>No
                                                    </option>
                                                </select>
                                            </td>
                                            <td><input type="number" name="activity_reminder_due_days[]"
                                                       value="{{$activity->reminder_due_days}}"
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td class="input-group">
                                                <input type="number" name="activity_budget[]" placeholder="0"
                                                       value="{{$activity->budget}}" required
                                                       class="form-control form-control-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text currency"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" name="remove"
                                                        class="btn btn-outline-danger btn-sm remove"><i
                                                            class="far fa-trash-alt"></i><span
                                                            class="glyphicon glyphicon-minus"></span></button>
                                            </td>
                                        </tr>
                                        <tr class="update activity_template">
                                            <td colspan=8>
                                                <table class="table mb-2">
                                                    <tr>
                                                        <td><textarea name="activity_template[]" id="activity_template"
                                                                      placeholder="Activity description template"
                                                                      class="form-control form-control-sm mediumEditor">
                                                                {{$activity->template}}</textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <button type="button" name="add_activities"
                                        class="btn btn-outline-secondary btn-sm add-activities">Add
                                    Activity <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        -->
                            <div class="form-group">
                                <label for="outputs_table" class="form-group-header">Outputs</label>
                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <table class="table table-sm" id="outputs_table"
                                           @if($outputs->isEmpty()) style="display: none;" @endif>
                                        <thead>
                                        <th scope="row">Output</th>
                                        <th scope="row">Target</th>
                                        <th></th>
                                        </thead>
                                        <!-- Here comes a foreach to show the outputs -->

                                        @foreach ($outputs as $output)
                                            <tr>
                                                <td class="w-75"><input type="hidden" name="output_id[]"
                                                                        value="{{$output->id}}">
                                                    <input type="text"
                                                           name="output_indicator[]"
                                                           value="{{$output->indicator}}"
                                                           placeholder="Output name" required
                                                           maxlength="255"
                                                           data-target="tooltip"
                                                           data-trigger="manual"
                                                           title="Maximum length is 255 chars"
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td class="w-25"><input type="text" name="output_target[]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="0"
                                                                        value="{{$output->target}}" required>
                                                </td>
                                                <td>
                                                    <button type="button" name="remove"
                                                            class="btn btn-outline-danger btn-sm remove"><i
                                                                class="far fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <button type="button" name="add_outputs"
                                        class="btn btn-outline-secondary btn-sm add-outputs mx-2">Add
                                    Output <i class="fas fa-plus"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="outcomes_table" class="form-group-header">Outcomes</label>
                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <table class="table table-sm" id="outcomes_table"
                                           @if($project->outcomes->isEmpty()) style="display: none;" @endif>
                                        <thead>
                                        <th scope="row">Outcome</th>
                                        <th></th>
                                        </thead>

                                        <!-- Here comes a foreach to show the outcomes -->
                                        @foreach ($project->outcomes as $outcome)
                                            <tr>
                                                <td class="w-100"><input type="hidden" name="outcome_id[]"
                                                                         value="{{$outcome->id}}">
                                                    <input type="text"
                                                           name="outcome_name[]"
                                                           value="{{$outcome->name}}"
                                                           placeholder="Outcome name" required
                                                           class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <button type="button" name="remove"
                                                            class="btn btn-outline-danger btn-sm remove"><i
                                                                class="far fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <button type="button" name="add_outcomes"
                                        class="btn btn-outline-secondary btn-sm add-outcomes mx-2">Add
                                    Outcome <i class="fas fa-plus"></i></button>
                            </div>

                            <div class="form-group">
                                <input class="btn btn-primary btn-lg" @empty($project->id) value="Save"
                                       @else value="Update"
                                       @endempty
                                       type="submit">
                            </div>
                        </div>
                </form>

                <script>
                    let editor = new MediumEditor('.mediumEditor#activity_template', {placeholder: {text: "Activity template"}});
                    $(function () {
                        $('.mediumEditor#activity_template').mediumInsert({
                            editor: editor,
                            addons: {
                                images: {
                                    fileUploadOptions: {
                                        url: '/images/upload',
                                        type: 'post',
                                        acceptFileTypes: /(.|\/)(gif|jpe?g|png)$/i
                                    }
                                }
                            }
                        });
                    });

                    $(document).ready(function () {
                        $('#project_area').multiselect({
                            templates: {
                                li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                            }
                        });

                        $('.currency').each(function () {
                            $(this).text($('#project_currency option:selected').text());
                        });
                        $(document).on('click', '.collapseEditor', function () {
                            $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                            $(this).toggleClass("fa-chevron-right fa-chevron-down");
                        });

                        $('input[name="output_indicator[]"]').focusout(function () {
                            $(this).tooltip('hide');
                        });
                        $('input[name="output_indicator[]"]').on('keyup', function () {
                            if (this.value.length > 250) {
                                $(this).tooltip('show');
                            } else {
                                $(this).tooltip('hide');
                            }
                        });

                        $(document).on('click', '.copy', function () {
                            let activity = $(this).closest('.card-body');
                            let currency = $('#project_currency option:selected').text();
                            let html = '';

                            html += '<div class="col-lg-6 my-2 px-2" data-type="copy" style="min-width: 16rem;"><div class="card bg-light m-auto"><div class="card-body pb-1"><div class="form-group mb-1 row"><input type="hidden" name="activity_id[]" value=0><label for="activity_name[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label><div class="col-8 col-sm-9 px-1"><input type="text" placeholder="Name" name="activity_name[]" required class="form-control form-control-sm" value="' + activity.find('input[name="activity_name[]"]').val() + '"></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_name[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Description</label><div class="col-8 col-sm-9 px-1"><textarea type="text" name="activity_description[]" id="activity_description" required class="form-control form-control-sm" rows="2" placeholder="Description">' + activity.find('textarea[name="activity_description[]"]').text() + '</textarea></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_start[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Start</label><div class="col-8 col-sm-4 px-1"><input type="text" name="activity_start[]" value=' + activity.find('input[name="activity_start[]"]').val() + ' placeholder="Start date" required class="form-control form-control-sm datepicker"></div>';
                            html += '<label for="activity_end[]" class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">End</label><div class="col-8 col-sm-4 px-1"><input type="text" name="activity_end[]" placeholder="End date" value=' + activity.find('input[name="activity_end[]"]').val() + ' required class="form-control form-control-sm datepicker"></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_reminder[]" class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Email reminder</label><div class="col-8 col-sm-9 px-1 form-inline"><select name="activity_reminder[]" class="form-control form-control-sm"><option value="1" ' + ((activity.find('select[name="activity_reminder[]"]').val() == 1) ? 'selected' : '') + '>Yes</option><option value="0" ' + ((activity.find('select[name="activity_reminder[]"]').val() == 0) ? 'selected' : '') + '>No</option></select>';
                            html += '<input type="number" placeholder="7" name="activity_reminder_due_days[]" value=' + activity.find('input[name="activity_reminder_due_days[]"]').val() + ' class="form-control form-control-sm text-right mx-1" style="width:60px;"><label for="activity_reminder_due_days[]" class="pl-0 pr-1 col-form-label-sm text-left">days before end</label></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_budget[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label><div class="col col-sm-4 pl-1 pr-1"><div class="input-group input-group-sm"><input type="number" name="activity_budget[]" placeholder="0"  value=' + activity.find('input[name="activity_budget[]"]').val() + ' required class="form-control text-right"><div class="input-group-append"><span class="input-group-text">' + currency + '</span></div></div></div></div>';
                            // html += '<div class="form-group mb-1 row"><label for="activity_budget[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label><div class="col col-sm-3 pl-1 pr-0"><input type="number" name="activity_budget[]" placeholder="0" value=0 required class="form-control form-control-sm text-right"></div><div class="input-group-append col col-sm-2 p-0 form-control-sm"><span class="input-group-text">' + currency + '</span></div></div>';
                            html += '<div class="form-group row mb-0"><label for="activity_template[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template<i class="fas fa-chevron-right collapseEditor"></i></label><div class="col-8 col-sm-9 px-1"><textarea name="activity_template[]" id="activity_template" placeholder="Activity description template" class="form-control form-control-sm mediumEditor collapsed"></textarea></div></div>';
                            html += '<div class="form-group row mt-2 mb-0"><a name="copy" class="btn btn-outline-secondary btn-sm copy ml-auto mt-1"><i class="far fa-copy"></i></a><a name="remove" class="btn btn-outline-danger btn-sm remove ml-1 mt-1"><i class="far fa-trash-alt"></i></a></div></div></div></div>';

                            $('#activities_list').append(html);

                            let template = $(activity).find('#activity_template').val();
                            $('#activities_list div[data-type="copy"] textarea[id="activity_template"]').html(template);

                            let editor = new MediumEditor('#activities_list #activity_template', {
                                placeholder: {text: "Activity template"}
                            });

                            $('#activities_list input.datepicker:last-child').datepicker({
                                format: 'dd-mm-yyyy',
                                weekStart: 1
                            });
                        });

                        $(document).on('click', '.add-activities', function () {

                            //$('#activities_table').show();
                            let currency = $('#project_currency option:selected').text();
                            let html = '';
                            /*
                            html += '<tr>';
                            html += '<input type="hidden" name="activity_id[]" value=0>';
                            html += '<td><input type="text" name="activity_name[]" class="form-control form-control-sm" placeholder="Activity name" required></td>';
                            html += '<td><input type="text" name="activity_description[]" class="form-control form-control-sm" placeholder="Description" required></td>';
                            html += '<td><input type="text" name="activity_start[]" class="form-control form-control-sm datepicker" placeholder="Startdate" size="1" required></td>';
                            html += '<td><input type="text" name="activity_end[]" class="form-control form-control-sm datepicker" placeholder="Enddate" size="1" required></td>';
                            html += '<td><select name="activity_reminder[]" class="form-control form-control-sm"><option value="1">Yes</option><option value="0">No</option></select></td>';
                            html += '<td><input type="number" name="activity_reminder_due_days[]" class="form-control form-control-sm" value="7" size="1" required></td>';
                            html += '<td class="input-group"><input type="number" name="activity_budget[]" class="form-control form-control-sm" placeholder="0" size="3" value="0" required><div class="input-group-append"><span class="input-group-text currency">' + currency + '</span></div></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="far fa-trash-alt"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                            html += '</tr>';
                            html += '<tr class="update activity_template"><td colspan=8><table class="table mb-2"><tr><td><textarea placeholder="Activity template" name="activity_template[]" ' +
                                'class="form-control form-control-sm mediumEditor"></textarea></td></tr></table></td></tr>';
                            */

                            html += '<div class="col-lg-6 my-2 px-2" style="min-width: 16rem;"><div class="card bg-light m-auto"><div class="card-body pb-1"><div class="form-group mb-1 row"><input type="hidden" name="activity_id[]" value=0><label for="activity_name[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label><div class="col-8 col-sm-9 px-1"><input type="text" placeholder="Name" name="activity_name[]" required class="form-control form-control-sm"></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_name[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Description</label><div class="col-8 col-sm-9 px-1"><textarea type="text" name="activity_description[]" id="activity_description" required class="form-control form-control-sm" rows="2" placeholder="Description"></textarea></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_start[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Start</label><div class="col-8 col-sm-4 px-1"><input type="text" name="activity_start[]" placeholder="Start date" required class="form-control form-control-sm datepicker"></div>';
                            html += '<label for="activity_end[]" class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">End</label><div class="col-8 col-sm-4 px-1"><input type="text" name="activity_end[]" placeholder="End date" required class="form-control form-control-sm datepicker"></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_reminder[]" class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Email reminder</label><div class="col-8 col-sm-9 px-1 form-inline"><select name="activity_reminder[]" class="form-control form-control-sm"><option value="1">Yes</option><option value="0">No</option></select>';
                            html += '<input type="number" placeholder="7" value="7" name="activity_reminder_due_days[]" class="form-control form-control-sm text-right mx-1" style="width:60px;"><label for="activity_reminder_due_days[]" class="pl-0 pr-1 col-form-label-sm text-left">days before end</label></div></div>';
                            html += '<div class="form-group mb-1 row"><label for="activity_budget[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label><div class="col col-sm-4 pl-1 pr-1"><div class="input-group input-group-sm"><input type="number" name="activity_budget[]" placeholder="0" value="0" required class="form-control text-right"><div class="input-group-append"><span class="input-group-text">' + currency + '</span></div></div></div></div>';
                            // html += '<div class="form-group mb-1 row"><label for="activity_budget[]" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label><div class="col col-sm-3 pl-1 pr-0"><input type="number" name="activity_budget[]" placeholder="0" value=0 required class="form-control form-control-sm text-right"></div><div class="input-group-append col col-sm-2 p-0 form-control-sm"><span class="input-group-text">' + currency + '</span></div></div>';
                            html += '<div class="form-group row mb-0"><label for="activity_template[]" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template<i class="fas fa-chevron-right collapseEditor"></i></label><div class="col-8 col-sm-9 px-1"><textarea name="activity_template[]" id="activity_template" placeholder="Activity description template" class="form-control form-control-sm mediumEditor collapsed"></textarea></div></div>';
                            html += '<div class="form-group row mt-2 mb-0"><a name="remove" class="btn btn-outline-danger btn-sm remove remove ml-auto mt-1"><i class="far fa-trash-alt"></i></a></div></div></div></div>';

                            $('#activities_list').append(html);

                            let editor = new MediumEditor('#activities_list #activity_template', {
                                placeholder: {text: "Activity template"}
                            });
                            $('#activities_list input.datepicker:last-child').datepicker({
                                format: 'dd-mm-yyyy',
                                weekStart: 1
                            });
                            $('#activities_list input.datepicker').eq(-2).datepicker("setDate", new Date());
                            $('input[name="activity_start[]"]').on('change', function () {
                                $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setStartDate", $(this).val());
                                $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setDate", $(this).val());
                            });
                        });
                        $(document).on('click', '.add-outputs', function () {
                            $('#outputs_table').show();
                            let html = '';
                            html += '<tr>';
                            html += '<input type="hidden" name="output_id[]" value=0>';
                            html += '<td class="w-75"><input type="text" name="output_indicator[]" class="form-control form-control-sm" placeholder="Output name" maxlength="255" data-trigger="manual" data-target="tooltip" title="Maximum length is 255 chars" required></td>';
                            html += '<td class="w-25"><input type="text" name="output_target[]" class="form-control form-control-sm" placeholder="0" size="3" value="0" required></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="far fa-trash-alt"></i></button></td></tr>';
                            $('#outputs_table').append(html);
                            $('input[name="output_indicator[]"]').focusout(function () {
                                $(this).tooltip('hide');
                            });
                            $('input[name="output_indicator[]"]').on('keyup', function () {
                                if (this.value.length > 250) {
                                    $(this).tooltip('show');
                                } else {
                                    $(this).tooltip('hide');
                                }
                            });
                        });
                        $(document).on('click', '#activities_list .remove', function () {
                            $(this).closest('.col-lg-6').remove();
                        });
                        $(document).on('click', '.remove', function () {
                            $(this).closest('tr').remove();
                            /*
                            if ($('tr', $('#activities_table')).length < 2) {
                                $('#activities_table').hide();
                            }*/
                            if ($('tr', $('#outputs_table')).length < 2) {
                                $('#outputs_table').hide();
                            }
                            if ($('tr', $('#outcomes_table')).length < 2) {
                                $('#outcomes_table').hide();
                            }
                        });
                        $(document).on('click', '.add-outcomes', function () {
                            $('#outcomes_table').show();
                            let html = '';
                            html += '<tr>';
                            html += '<input type="hidden" name="outcome_id[]" value=0>';
                            html += '<td class="w-75"><input type="text" name="outcome_name[]" class="form-control form-control-sm" placeholder="Outcome name" required></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="far fa-trash-alt"></i></button></td></tr>';
                            $('#outcomes_table').append(html);
                        });
                        $('input[name="activity_start[]"]').on('change', function () {
                            $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setStartDate", $(this).val());
                            $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setDate", $(this).val());
                        });
                        $('#project_currency').on('change', function () {
                            $('.currency').each(function () {
                                $(this).text($('#project_currency option:selected').text());
                            });
                        });
                        $("form").submit(function () {
                            if (!$('#project_area').val()) {
                                alert('Please select a project area');
                                return false;
                            }
                            let datealert = '';
                            $('input[name="activity_start[]"]').each(function (index) {
                                let startdate = $(this).val();
                                let enddate = $(this).closest('.card-body').find('input[name="activity_end[]"]').val();
                                if (enddate < startdate) {
                                    datealert += 'End date is earlier that start date for Activity ' + $(this).closest('.card-body').find('input[name="activity_name[]"]').val() +'\r\n';
                                }
                            });
                            if (datealert) {
                                alert(datealert);
                                return false;
                            }
                            // Add extra confirmation on empty fields
                            let confirmation = '';
                            if (!$('textarea[name=project_description]').text().trim()) {
                                confirmation += '\nDescription field is empty';
                            }
                            if (!$("#project_end").val()) {
                                confirmation += '\nProject end date field is empty';
                            }
                            $('#activities_list .medium-editor-element').each(function (index) {
                                if (!$(this).text().trim()) {
                                    let activity_name = $(this).closest('.card-body').find('input[name="activity_name[]"]').val();
                                    confirmation += '\nActivity template for ' + activity_name + ' is empty';
                                }
                            });
                            if (!confirmation) {
                                return true;
                            } else {
                                if (confirm('Please confirm the following empty fields:' + confirmation)) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                        });
                    });
                </script>

@endsection
