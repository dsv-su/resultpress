@extends('layouts.master')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            @if (!empty($project->id))
                <li class="breadcrumb-item"><a href="{{ route('project_show', $project->id) }}">{{ $project->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
                @if (empty($project->id))
                    Add a new project
                @else
                    Edit
                @endif
            </li>
        </ol>
    </nav>

    @empty($project->id)
        <form action="{{ route('update', $project) }}" method="POST">
            <input name="new_project" value="1" hidden>
            @else
                <form action="{{ route('project_update', $project) }}" method="POST">
                    @method('PUT')
                    @endempty
                    @csrf
                    <div class="form-group">
                        <div class="col-lg-6 my-2 px-2" id="project_details" style="min-width: 16rem;">
                            <label for="activities_list" class="form-group-header">Details</label>
                            <span data-toggle="tooltip"
                                  title="Here you add basic information about the project"><i
                                        class="fas fa-info-circle fa-1x"></i></span>
                            <div class="form-row mb-2 row">
                                <label for="name" class="col-sm-3 col-form-label-sm">Name</label>
                                <div class="px-1 col-sm-9">
                                    <input class="form-control form-control-sm @error('name') is-danger @enderror"
                                           type="text" name="name" id="name"
                                           placeholder="Project title"
                                           required
                                           value="{{ old('name', empty($project) ? '' : $project->name) }}">
                                           <span>{{ $project->getHistory('name') }}</span>
                                           <span>{{ $project->getSuggestedChanges('name') }}</span>
                                    @error('name')
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row mb-2 row">
                                <label for="summary" class="col-sm-3 col-form-label-sm">Summary</label>
                                <div class="col-sm-9 px-1">
                                <textarea rows="2" class="form-control text-limit-1000 generalMediumEditor form-control-sm @error('summary') is-danger @enderror" name="summary" id="summary" maxlength="1000" data-maxlength="1000">{!! old('summary', empty($project) ? '' : $project->summary) !!}</textarea>
                                <div><span class="text-limit-1000-count float-right">1000</span> Remaining characters</div>
                                    @error('summary')
                                    <div class="text-danger">
                                        {{ $errors->first('summary') }}
                                    </div>@enderror
                                </div>
                                <label class="col-sm-3 col-form-label-sm"></label>
                                <div class="col-sm-9 px-1">{{ strip_tags($project->getHistory('summary')) }}</div>
                                <div class="col-sm-9 px-1">{{ strip_tags($project->getSuggestedChanges('summary')) }}</div>
                            </div>
                            <div class="form-row mb-2 row">
                                <label for="description"
                                       class="col-sm-3 col-form-label-sm">Description</label>
                                <div class="col-sm-9 px-1">
                                <textarea rows="2"
                                          class="form-control mediumEditor form-control-sm @error('description') is-danger @enderror"
                                          name="description" id="description"
                                >{!! old('description', empty($project) ? '' : $project->description) !!}</textarea>
                                    @error('description')
                                    <div class="text-danger">
                                        {{ $errors->first('description') }}
                                    </div>@enderror
                                </div>
                                <label class="col-sm-3 col-form-label-sm"></label>
                                <div class="col-sm-9 px-1">{{ strip_tags($project->getHistory('description')) }}</div>
                                <div class="col-sm-9 px-1">{{ strip_tags($project->getSuggestedChanges('description')) }}</div>
                            </div>
                            <div class="form-row mb-2 row">
                                <label for="project_area" class="col-sm-3 col-form-label-sm">Area</label>
                                <div class="col-sm-9 px-1">
                                    <select name="project_area[]" id="project_area" class="custom-select-sm"
                                            multiple="multiple">
                                        @foreach($areas as $pa)
                                            <option value="{{$pa->id}}" {{ old('pa_id') == $pa->id || in_array($pa->id, $old_pa) ? 'selected':''}}>{{$pa->name}}</option>
                                        @endforeach
                                    </select>

                                    <span>{{ $project->getHistory('areas') }}</span>
                                </div>
                            </div>

                            <div class="form-row mb-2 row">
                                <label for="project_category" class="col-sm-3 col-form-label-sm">Project Category</label>
                                <div class="col-sm-9 px-1">
                                    <select name="project_category[]" id="project_category" class="custom-select-sm"
                                            multiple="multiple">

                                        @foreach($categories as $pc)
                                            <option value="{{$pc->id}}" {{ old('pc_id') == $pc->id || $project_categories->has($pc->slug) ? 'selected':''}}>{{$pc->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row mb-2 row">
                                <label for="project_region" class="col-sm-3 col-form-label-sm">Project Region</label>
                                <div class="col-sm-9 px-1">
                                    <select name="project_region[]" id="project_region" class="custom-select-sm"
                                            multiple="multiple">

                                        @foreach($regions as $pr)
                                            <option value="{{$pr->id}}" {{ old('pr_id') == $pr->id || $project_regions->has($pr->slug) ? 'selected':''}}>{{$pr->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row mb-2 row">
                                <label for="start" class="col-auto col-sm-3 col-form-label-sm">Start</label>
                                <div class="col-auto">
                                    <input type="text" name="start" id="start"
                                           placeholder="Start date"
                                           value="{{ old('start', empty($project->start) ? '' : $project->start->format('d-m-Y'))}}"
                                           class="form-control form-control-sm datepicker" required>
                                </div>
                            </div>
                            <div class="form-row mb-2 row">
                                <label for="end" class="col-auto col-sm-3 col-form-label-sm">End</label>
                                <div class="col-auto">
                                    <input type="text" name="end" id="end" placeholder="End date"
                                           value="{{ old('end', empty($project->end) ? '' : $project->end->format('d-m-Y'))}}"
                                           class="form-control form-control-sm datepicker">
                                </div>
                            </div>

                            <div class="form-row mb-2 row">
                                <label for="currency"
                                       class="col-auto col-sm-3 col-form-label-sm">Currency</label>
                                <div class="col-auto">
                                    <select name="currency" id="currency"
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
                            </div>
                            <div class="form-row mb-2 row">
                                <label for="cumulative" class="col-auto col-sm-3 col-form-label-sm">Cumulative
                                    updates <span data-toggle="tooltip"
                                                  title="Each activity update will build on the previous one"><i
                                                class="fas fa-info-circle fa-1x"></i></span></label>
                                <div class="col-auto">
                                    <select name="cumulative" class="form-control form-control-sm">
                                        <option value="1" @if($project->cumulative) selected="selected" @endif>Yes
                                        </option>
                                        <option value="0" @if(!$project->cumulative) selected="selected" @endif>No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Email reminders for project deadlines -->
                        <div class="form-group">
                            <label for="deadlines" class="form-group-header pl-2 pr-0">Deadlines</label>
                            <span data-toggle="tooltip"
                                  title="Each deadline will trigger an email reminder when the date is reached"><i
                                        class="fas fa-info-circle fa-1x"></i></span>
                            <div class="d-flex flex-wrap" id="reminders_list">
                                @if ($impact_reminder && ! in_array($impact_reminder->id, $project_reminders->pluck('id')->toArray()))
                                    <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                        @include('project.reminder_form', ['reminder' => $impact_reminder])
                                    </div>
                                @endif
                                @foreach($project_reminders as $thisproject)
                                    <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                        @include('project.reminder_form', ['reminder' => $thisproject])
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add_reminder" name="add_reminder"
                                    class="btn btn-outline-secondary btn-sm add-reminder m-2">Add Reminder <i
                                        class="fas fa-plus"></i></button>
                        </div>
                        <!-- end email reminders -->

                        <div class="form-group">
                            <label for="activities_list" class="form-group-header pl-2 pr-0">Activities</label>
                            <span data-toggle="tooltip"
                                  title="Here you add project activities information and reporting templates"><i
                                        class="fas fa-info-circle fa-1x"></i></span>
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
                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <label for="outputs_table" class="form-group-header">Outputs</label>
                                    <span data-toggle="tooltip"
                                          title="Here you add project outputs and targets"><i
                                                class="fas fa-info-circle fa-1x"></i></span>
                                    <div class="card bg-light m-auto"
                                         @if($outputs->isEmpty()) style="display: none;" @endif>
                                        <div class="card-body p-1">
                                            <table class="table table-sm table-borderless mb-0" id="outputs_table">
                                                <thead>
                                                <th scope="row">Output</th>
                                                <th scope="row">Target</th>
                                                <th></th>
                                                </thead>
                                                <!-- Here comes a foreach to show the outputs -->

                                                @foreach ($outputs as $output)
                                                    <tr>
                                                        <td class="w-75">
                                                            <input type="hidden" name="outputs[{{$output->id}}][id]" value="{{$output->id}}">
                                                            <input type="hidden" name="outputs[{{$output->id}}][slug]" value="{{$output->slug}}">
                                                            <input type="hidden" name="outputs[{{$output->id}}][status]" value="{{$output->status}}">
                                                            <textarea name="outputs[{{$output->id}}][indicator]"
                                                                      data-placeholder="Output name" required
                                                                      class="generalMediumEditor form-control form-control-sm">{{$output->indicator}}</textarea>
                                                        </td>
                                                        <td class="w-25"><input type="text" name="outputs[{{$output->id}}][target]"
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
                                    </div>
                                </div>
                                <button type="button" name="add_outputs"
                                        class="btn btn-outline-secondary btn-sm add-outputs mx-2">Add
                                    Output <i class="fas fa-plus"></i></button>
                            </div>

                            <div class="form-group">
                                <div class="col my-2 px-2" style="min-width: 16rem;">
                                    <label for="aggregated_outputs_table" class="form-group-header">Aggregated
                                        outputs</label>
                                    <span data-toggle="tooltip"
                                          title="Here you add aggregated outputs and targets. Notice that in case you added new outputs, you need to save the project first in order to be able to aggregate them."><i
                                                class="fas fa-info-circle fa-1x"></i></span>
                                    <div class="card bg-light m-auto"
                                         @if($aggregated_outputs->isEmpty()) style="display: none;" @endif>
                                        <div class="card-body p-1">
                                            <table class="table table-sm table-borderless mb-0"
                                                   id="aggregated_outputs_table">
                                                <thead>
                                                <th scope="row">Output</th>
                                                <th scope="row">Aggregation</th>
                                                <th></th>
                                                </thead>
                                                <!-- Here comes a foreach to show the outputs -->

                                                @foreach ($aggregated_outputs as $aggregated_output)
                                                    <tr>
                                                        <td class="w-25 align-middle">
                                                            <input type="hidden" name="outputs[{{$aggregated_output->id}}][id]" value="{{$aggregated_output->id}}">
                                                            <input type="hidden" name="outputs[{{$aggregated_output->id}}][slug]" value="{{$aggregated_output->slug}}">
                                                            <input type="hidden" name="outputs[{{$aggregated_output->id}}][status]" value="aggregated">
                                                            <input type="text"
                                                                   name="outputs[{{$aggregated_output->id}}][indicator]"
                                                                   value="{{$aggregated_output->indicator}}"
                                                                   placeholder="Output name" required
                                                                   maxlength="255"
                                                                   data-target="tooltip"
                                                                   data-trigger="manual"
                                                                   title="Maximum length is 255 chars"
                                                                   class="form-control form-control-sm">
                                                        </td>
                                                        <td class="w-75">
                                                            <select name="outputs[{{$aggregated_output->id}}][target]"
                                                                    class="output_multiselect custom-select form-control-sm"
                                                                    multiple="multiple" required>
                                                                @foreach($outputs as $output)
                                                                    <option value="{{$output->id}}" {{in_array($output->id, json_decode($aggregated_output->target)) ? 'selected':''}}>{{$output->indicator}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="align-middle">
                                                            <button type="button" name="remove"
                                                                    class="btn btn-outline-danger btn-sm remove"><i
                                                                        class="far fa-trash-alt"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" name="add_aggregated_outputs"
                                        class="btn btn-outline-secondary btn-sm add-aggregated-outputs mx-2">Add
                                    Aggregated
                                    Output <i class="fas fa-plus"></i></button>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <label for="outcomes_table" class="form-group-header">Outcomes</label>
                                    <span data-toggle="tooltip"
                                          title="Here you add project outcomes"><i
                                                class="fas fa-info-circle fa-1x"></i></span>
                                    <div class="card bg-light m-auto"
                                         @if($project->outcomes->isEmpty()) style="display: none;" @endif>
                                        <div class="card-body p-1">
                                            <table class="table table-sm table-borderless mb-0" id="outcomes_table">
                                                <thead>
                                                <th scope="row">Outcome</th>
                                                <th></th>
                                                </thead>

                                                <!-- Here comes a foreach to show the outcomes -->
                                                @foreach ($project->outcomes as $outcome)
                                                    <tr>
                                                        <td class="w-100">
                                                            <input type="hidden" name="outcomes[{{$outcome->id}}][id]" value="{{$outcome->id}}">
                                                            <input type="hidden" name="outcomes[{{$outcome->id}}][slug]" value="{{$outcome->slug}}">
                                                            <textarea type="text"
                                                                   name="outcomes[{{$outcome->id}}][name]"
                                                                   data-placeholder="Outcome name" required
                                                                   class="generalMediumEditor form-control">{{$outcome->name}}</textarea>
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
                                    </div>
                                </div>
                                <button type="button" name="add_outcomes"
                                        class="btn btn-outline-secondary btn-sm add-outcomes mx-2">Add
                                    Outcome <i class="fas fa-plus"></i></button>
                            </div>

                            <!-- Project managers and partners -->
                            @if (!Auth::user()->hasRole('Partner'))
                                <div class="form-group">
                                    <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                        <label for="users" class="form-group-header">Users</label>
                                        <span data-toggle="tooltip"
                                            title="Users and permissions associated with this project"><i
                                                    class="fas fa-info-circle fa-1x"></i></span>
                                        <div class="card bg-light m-auto">
                                            <div class="form-row row mx-1">
                                                <label class="col-form-label">Managers:</label>
                                                <div class="col px-1 d-flex align-items-center">
                                                    <select name="user_id[]" class="custom-select" id="managers"
                                                            multiple="multiple" required>
                                                        @foreach($users as $user)
                                                            <option value="{{$user->id}}" {{ old('user_id') == $user->id || in_array($user->id, $old_users) ? 'selected':''}}>{{$user->fullViewName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-row row mx-1">
                                                <label class="col-form-label">Partners:</label>
                                                <div class="col px-1 d-flex align-items-center">
                                                    <select name="partner_id[]" class="custom-select" id="partners"
                                                            multiple="multiple">
                                                        @foreach($partnerusers as $user)
                                                            <option value="{{$user->id}}" {{ old('partner_id') == $user->id || in_array($user->id, $partners) ? 'selected':''}}>{{$user->fullViewName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            

                                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem;">
                                    <!-- Invites list -->
                                    <div class="form-group">
                                        <label for="invites" class="form-group-header">Active Invites</label>
                                        <span data-toggle="tooltip" title="Sent invites are listed here"><i
                                                    class="fas fa-info-circle fa-1x"></i></span>
                                        @include('project.invite_form')
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="col-lg-6 my-2 px-2">
                                    <input class="btn btn-primary btn-lg" @empty($project->id) value="Save"
                                           @else value="Update"
                                           @endempty
                                           data-toggle="tooltip" title="Save the project"
                                           type="submit">
                                </div>
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
                    $('.output_multiselect').multiselect({
                        templates: {
                            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                        }
                    });
                    $('.generalMediumEditor, .mediumEditor').css('min-height', '60px').css('height', 'auto');

                    var editor = new MediumEditor('.mediumEditor[id*="activities"]', {placeholder: {text: "Activity template"}});
                    var editor = new MediumEditor('.mediumEditor#description', {placeholder: {text: "Describe the project"}});
                    var generalEditor = new MediumEditor('.generalMediumEditor');

                    $(document).ready(function () {
                        $(document).on('click', '.collapseEditor', function () {
                            $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                            $(this).toggleClass("fa-chevron-right fa-chevron-down");
                        });

                        $('.text-limit-1000').on('change keyup keypress input', function (e) {
                            var tlength = $(this).text().length;
                            var remain = 1000 - parseInt(tlength);
                            $('.text-limit-1000-count').text(remain);
                            if (remain <= 0) {
                                $('.text-limit-1000-count').css('color', 'red');
                                e.preventDefault();
                                e.stopPropagation();
                            } else {
                                $('.text-limit-1000-count').css('color', 'black');
                            }
                        });
                        $('.text-limit-1000').trigger('change');
                    });

                    $(document).ready(function () {
                        $('#project_area, #project_category, #project_region').multiselect({
                            buttonWidth: '100%',
                            templates: {
                                li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                            }
                        });

                        $('.currency').each(function () {
                            $(this).text($('#currency option:selected').text());
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

                        $(document).on('click', '.add-reminder', function () {
                            $('#reminders_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;"></div>');
                            $('#reminders_list div.col-lg-6:last-child').load('/reminder/0/');
                        });

                        $(document).on('click', '#reminders_list .remove', function () {
                            console.log('ddd');
                            $(this).tooltip('hide');
                            $(this).closest('.col-lg-6').remove();
                        });

                        $(document).on('click', '.add-activities', function () {
                            $('#activities_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;"></div>');
                            $('#activities_list div.col-lg-6:last-child').load('/a/0/0', function(){
                                var generalEditor = new MediumEditor('.mediumEditor');
                            });
                        });

                        $(document).on('click', '.add-outputs', function () {
                            $('#outputs_table').closest('.card').show();
                            let html = '';
                            let timestamp = Date.now();
                            html += '<tr>';
                            html += '<input type="hidden" data-id="'+timestamp+'" name="outputs['+timestamp+'][status]" value="default">';
                            html += '<td class="w-75"><textarea name="outputs['+timestamp+'][indicator]" data-id="'+timestamp+'" class="MediumEditor form-control form-control-sm" placeholder="Output name" maxlength="255" data-trigger="manual" data-target="tooltip" title="Maximum length is 255 chars" required></textarea></td>';
                            html += '<td class="w-25"><input type="text" name="outputs['+timestamp+'][target]" class="form-control form-control-sm" placeholder="0" size="3" value="0" required></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove" data-toggle="tooltip" title="Delete this output"><i class="far fa-trash-alt"></i></button></td></tr>';
                            $('#outputs_table').append(html).ready(function (e) {
                                var generalEditor = new MediumEditor('.MediumEditor');
                            });
                            $(function () {
                                $('[data-toggle="tooltip"]').tooltip();
                            })
                            $('input[name="outputs['+timestamp+'][indicator]"]').focusout(function () {
                                $(this).tooltip('hide');
                            });
                            $('input[name="outputs['+timestamp+'][indicator]"]').on('keyup', function () {
                                if (this.value.length > 250) {
                                    $(this).tooltip('show');
                                } else {
                                    $(this).tooltip('hide');
                                }
                            });
                        });

                        $(document).on('click', '.add-aggregated-outputs', function () {
                            $('#aggregated_outputs_table').closest('.card').show();
                            let html = '';
                            let timestamp = Date.now();
                            let outputs = [];

                            $('textarea[name*="outputs"]').each(function(){
                                let output = {};
                                output.id = $(this).data('id');
                                output.indicator = $(this).val();
                                outputs.push(output);
                            });


                            html += '<tr>';
                            html += '<input type="hidden" name="outputs['+timestamp+'][status]" value="aggregated">';
                            html += '<td class="w-75"><textarea type="text" name="outputs['+timestamp+'][indicator]" class="form-control form-control-sm" placeholder="Output name" maxlength="255" data-trigger="manual" data-target="tooltip" title="Maximum length is 255 chars" required></textarea></td>';
                            html += '<td class="w-75"><select name="outputs['+timestamp+'][target]" class="custom-select form-control-sm output_multiselect" multiple="multiple" required> @foreach($outputs as $output) <option value="{{$output->id}}">{!!$output->indicator!!}</option>@endforeach </select></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove" data-toggle="tooltip" title="Delete this output"><i class="far fa-trash-alt"></i></button></td></tr>';
                            $('#aggregated_outputs_table').append(html);

                            @if (empty($project->id))
                                outputs.forEach(output => {
                                    let outputsSelect = $('select[name="outputs['+timestamp+'][target]"]');
                                    let option = '<option value="'+output.id+'">'+output.indicator+'</option>';
                                    outputsSelect.append(option);
                                });
                            @endif

                            $(function () {
                                $('[data-toggle="tooltip"]').tooltip()
                            })
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
                            $('.output_multiselect').multiselect({
                                templates: {
                                    li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                                }
                            });
                        });

                        $(document).on('click', '#activities_list .remove', function () {
                            $(this).tooltip('hide');
                            $(this).closest('.col-lg-6').remove();
                        });
                        $(document).on('click', '.remove', function () {
                            $(this).tooltip('hide');
                            $(this).closest('tr').remove();
                            /*
                            if ($('tr', $('#activities_table')).length < 2) {
                                $('#activities_table').hide();
                            }*/
                            if ($('tr', $('#outputs_table')).length < 2) {
                                $('#outputs_table').closest('.card').hide();
                            }
                            if ($('tr', $('#aggregated_outputs_table')).length < 2) {
                                $('#aggregated_outputs_table').closest('.card').hide();
                            }
                            if ($('tr', $('#outcomes_table')).length < 2) {
                                $('#outcomes_table').closest('.card').hide();
                            }
                        });
                        $(document).on('click', '.add-outcomes', function () {
                            $('#outcomes_table').closest('.card').show();
                            let html = '';
                            let timestamp = Date.now();
                            html += '<tr>';
                            html += '<td class="w-100"><textarea type="text" name="outcomes['+timestamp+'][name]" class="form-control form-control-sm outcomesMediumEditor" placeholder="Outcome name" required></textarea></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove" data-toggle="tooltip" title="Delete this outcome"><i class="far fa-trash-alt"></i></button></td></tr>';
                            $('#outcomes_table').append(html);
                            $(function () {
                                var outcomesMediumEditor = new MediumEditor('.outcomesMediumEditor');
                                $('[data-toggle="tooltip"]').tooltip();
                            })
                        });

                        $('input[name="activity_start[]"]').on('change', function () {
                            $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setStartDate", $(this).val());
                            var end = new Date($(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("getDate"));
                            var start = new Date($(this).datepicker("getDate"));
                            if (new Date($(this).datepicker("getDate")) > end) {
                                $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setDate", $(this).val());
                            }
                        });

                        $('#currency').on('change', function () {
                            $('.currency').each(function () {
                                $(this).text($('#currency option:selected').text());
                            });
                        });

                        $('#end').on('change', function (e) {
                            let end = new Date($(this).datepicker("getDate"));
                            end.setMonth(end.getMonth() + 24);
                            $('.impact-date').each(function () {
                                $(this).datepicker("setDate", end);
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
                            if (!$('textarea[name=description]').text().trim()) {
                                confirmation += '\nDescription field is empty';
                            }
                            if (!$("#end").val()) {
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
