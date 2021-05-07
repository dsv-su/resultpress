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
                                          class="form-control mediumEditor form-control-sm @error('project_description') is-danger @enderror"
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

                            <!-- Email reminders for project deadlines -->
                            <label for="deadlines" class="form-group-header">Deadlines</label>
                            <div class="form-row row" id="deadlines">
                                <label for="add_reminder" class="col-3 col-form-label-sm">Add deadline</label>
                                <div>
                                    <button type="button" id="add_reminder" name="add_reminder"
                                            class="btn btn-outline-secondary btn-sm add-reminder m-2">Add <i
                                                class="far fa-bell"></i><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            @foreach($project_reminders as $thisproject)
                                <div class="form-row border row">
                                    <label for="project_reminder[]" class="col-3 col-form-label-sm">Deadline</label>
                                    <div class="col-8 col-sm-9 px-0 form-inline">
                                        <input class="form-control form-control-sm w-100" name="project_reminder_name[]"
                                               type="text" placeholder="Name"
                                               value="{{ old('project_reminder_name', empty($thisproject->name) ? '' : $thisproject->name) }}">
                                    </div>
                                    <label for="project_reminder[]" class="col-3 col-form-label-sm">Email
                                        reminder</label>
                                    <div class="col-8 col-sm-9 px-0 form-inline">
                                        <select name="project_reminder[]" class="form-inline form-control-sm">
                                            @if($thisproject->reminder)
                                                <option value="1" selected="selected">Yes</option>
                                                <option value="0">No</option>
                                            @else
                                                <option value="0" selected="selected">No</option>
                                                <option value="1">Yes</option>
                                            @endif
                                        </select>

                                        <input type="number" name="project_reminder_due_days[]"
                                               value="{{ $thisproject->reminder_due_days}}"
                                               class="form-control form-control-sm" style="width:50px;" required>

                                        <label for="project_reminder_due_days[]"
                                               class="col-2 col-form-label-sm text-left">days before</label>
                                        <div class="col-4">
                                            <input type="text" name="project_reminder_date[]" id="project_reminder_date"
                                                   placeholder="Deadline Date"
                                                   value="{{ old('project_reminder_date', empty($thisproject->set) ? '' : $thisproject->set->format('d-m-Y')) }}"
                                                   class="form-control form-control-sm datepicker" required>

                                        </div>
                                        <div class="col-8 col-sm-9 px-0 form-inline">
                                            <button type="button" name="reminder-remove" class="btn btn-outline-danger btn-sm reminder-remove">
                                                <i class="far fa-trash-alt"></i></button>
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                            <div class="d-flex flex-wrap" id="reminders_list">
                            </div>
                            <!-- end email reminders -->

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
                                        @include('project.activity_form', ['activity' => $activity])
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" name="add_activities"
                                    class="btn btn-outline-secondary btn-sm add-activities m-2">Add
                                Activity <i class="fas fa-plus"></i></button>
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

                            <!-- Project managers and partners -->

                            <div class="form-group">
                                <label for="users" class="form-group-header">Users</label>
                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <div class="card p-2">
                                        <div class="form-row row">
                                            <label class="text-primary col-form-label">Managers:</label>
                                            <div class="col px-1">
                                                <select name="user_id[]" class="custom-select" id="managers"
                                                        multiple="multiple">
                                                    @foreach($users as $user)
                                                        <option value="{{$user->id}}" {{ old('user_id') == $user->id || in_array($user->id, $old_users) ? 'selected':''}}>{{$user->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row row">
                                            <label class="text-primary col-form-label">Partners:</label>
                                            <div class="col px-1">
                                                <select name="partner_id[]" class="custom-select" id="partners"
                                                        multiple="multiple">
                                                    @foreach($users as $user)
                                                        <option value="{{$user->id}}" {{ old('partner_id') == $user->id || in_array($user->id, $partners) ? 'selected':''}}>{{$user->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <input class="btn btn-primary btn-lg" @empty($project->id) value="Save"
                                       @else value="Update"
                                       @endempty
                                       type="submit">
                            </div>
                        </div>
                    </div>
                </form>

                <script>
                    $('#managers').multiselect({
                        templates: {
                            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                        }
                    });
                    $('#partners').multiselect({
                        templates: {
                            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                        }
                    });

                    var editor = new MediumEditor('.mediumEditor#activity_template', {placeholder: {text: "Activity template"}});
                    var editor = new MediumEditor('.mediumEditor#project_description', {placeholder: {text: "Describe the project"}});

                    $(document).ready(function () {
                        $(document).on('click', '.collapseEditor', function () {
                            $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                            $(this).toggleClass("fa-chevron-right fa-chevron-down");
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
                            let index = $(this).closest('.col-lg-6').index() + 1;
                            $('#activities_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;"></div>');
                            $('#activities_list div.col-lg-6:last-child').load('/a/0/' + index);
                        });

                        /* -- */
                        $(document).on('click', '.add-reminder', function () {
                            let html = '';
                            html += '<div class="form-row border row"><label for="project_reminder[]" class="col-3 col-form-label-sm">Deadline</label>';
                            html += '<div class="col-8 col-sm-9 px-0 form-inline">';
                            html += '<input class="form-control form-control-sm w-100" name="project_reminder_name[]" type="text" placeholder="Name" value="{{ old('project_reminder_name', empty($thisproject->name) ? '' : $thisproject->name) }}"></div>';
                            html += '<label for="project_reminder[]" class="col-3 col-form-label-sm">Email reminder</label>';
                            html += '<div class="col-8 col-sm-9 px-0 form-inline">';
                            html += '<select name="project_reminder[]" class="form-inline form-control-sm">' +
                                '<option value="1" @if($project->reminder) selected="selected" @endif>Yes</option><option value="0" @if(!$project->reminder) selected="selected" @endif>No</option></select>';
                            html += '<input type="number" name="project_reminder_due_days[]" value="{{$project->reminder_due_days}}" class="form-control form-control-sm" style="width:50px;" required>';
                            html += '<label for="project_reminder_due_days[]" class="col-2 col-form-label-sm text-left">days before</label>';
                            html += '<div class="col-4">' +
                                '<input type="text" name="project_reminder_date[]" id="project_reminder_date" placeholder="Deadline Date" value="{{ old('project_reminder_date', empty($project->reminder_date) ? '' : $project->reminder_date->format('d-m-Y'))}}" class="form-control form-control-sm datepicker" required>';
                            html += '</div>';
                            html += '<div class="col-8 col-sm-9 px-0 form-inline"><button type="button" name="reminder-remove" class="btn btn-outline-danger btn-sm reminder-remove"><i class="far fa-trash-alt"></i></button>';
                            html += '</div></div></div>';
                            $('#reminders_list').append(html);
                            $('#reminders_list input.datepicker:last-child').datepicker({
                                format: 'dd-mm-yyyy',
                                weekStart: 1
                            });
                        });
                        $(document).on('click', '.reminder-remove', function () {
                            $(this).closest('.form-row').remove();
                        });
                        /* -- */
                        $(document).on('click', '.add-activities', function () {
                            $('#activities_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;"></div>');
                            $('#activities_list div.col-lg-6:last-child').load('/a/0/0');
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
                            var end = new Date($(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("getDate"));
                            var start = new Date($(this).datepicker("getDate"));
                            if (new Date($(this).datepicker("getDate")) > end) {
                                $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setDate", $(this).val());
                            }
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
                                let startdate = new Date($(this).datepicker("getDate"));
                                let enddate = new Date($(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("getDate"));
                                if (enddate < startdate) {
                                    datealert += 'End date is earlier that start date for Activity ' + $(this).closest('.card-body').find('input[name="activity_name[]"]').val() + '\r\n';
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
                                return confirm('Please confirm the following empty fields:' + confirmation);
                            }
                        });
                    });
                </script>

@endsection
