@extends('layouts.master')

@section('content')
    <form action="{{ route('project_save_update', $project) }}" method="POST">
        @method('PUT')
        @csrf

        <div class="row d-flex justify-content-between">
            <div class="col">
                <h4>{{ $project->name }}: @if (empty($project_update))
                        write an update
                    @else
                        edit draft
                        update
                        #{{ $project_update->index }}
                    @endif
                </h4>
            </div>
            <div class="col-sm-auto d-flex align-items-center">
                <span class="badge badge-info font-100">{{ Carbon\Carbon::now()->format('d-m-Y') }}</span>
            </div>
        </div>

        <p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>

        @if (!empty($project_update))
            <input type="hidden" value="{{ $project_update->id }}" id="project_update_id" name="project_update_id">
        @endif

        <!-- <span class="d-none" id="project_currency">{{ $project->getCurrencySymbol() }}</span> -->

        @if ($project->drafts($project_update ?? null) && $project->cumulative)
            <div class="alert alert-warning" role="alert">
                There are some drafts for this project. Please take this into account when covering activities (activity
                templates are based on the most recent update, including drafts).
            </div>
        @endif
        <div class="form-group">
            <label for="dates" class="form-group-header">Dates covered</label>
            <div class="col col-sm-5 col-md-3 px-1">
                <input type="text" name="dates" class="form-control form-control-sm" placeholder="Date" size="1"
                    @if (!empty($project_update)) value="{{ $project_update->start->format('d/m/Y') }} - {{ $project_update->end ? $project_update->end->format('d/m/Y') : $project_update->start->format('d/m/Y') }}"
                       @elseif ($project->project_updates->count()) value="{{ $project->getNextProjectUpdateDate() }} - {{ Carbon\Carbon::now()->format('d/m/Y') }}"
                       @else value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" @endif required>
            </div>
        </div>

        <div class="form-group">
            <label for="project_update_summary" class="form-group-header mt-4">Summary</label>
            <textarea rows="4" class="form-control form-control-sm mediumEditor @error('project_update_summary') is-danger @enderror" name="project_update_summary" id="project_update_summary">{{ old('project_description', empty($project_update) ? '' : $project_update->summary) }}</textarea>
            @error('project_description')
                <div class="text-danger">
                    {{ $errors->first('project_update_summary') }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="outcomes" class="form-group-header mt-4">Outcomes</label>
            @if (!$project->outcomes->isEmpty())
                <div id="outcomes">
                    @if (!empty($project_update->outcome_updates))
                        @foreach ($project_update->outcome_updates as $ou)
                            <div class="card mb-3">
                                @include('project.outcome_update', ['outcome' => $ou->outcome, 'outcome_update' => $ou])
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-8 col-lg my-2 px-0" style="min-width: 16rem;">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addOutcomes" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select outcomes to update
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @foreach ($project->outcomes as $outcome)
                                <a class="dropdown-item add-outcome" href="#" id="{{ $outcome->id }}" @if ((!empty($project_update->outcome_updates) && $project_update->outcome_updates->keyBy('outcome_id')->get($outcome->id)) || !$outcome->outcome_updates->isEmpty()) style="" @endif>{!! $outcome->name !!}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <p>The project has no outcomes added</p>
            @endif
        </div>

        <div class="form-group">
            <label for="outputs_table" class="form-group-header mt-4">Outputs</label>
            <div class="my-2 px-0" style="min-width: 16rem;">
                <div class="card bg-light m-auto" @if (empty($ous) || $ous->isEmpty()) style="display: none;" @endif>
                    <div class="card-body p-1">
                        <table class="table table-sm table-borderless mb-0" id="outputs_table">
                            <thead>
                                <th>Output</th>
                                <th>Target</th>
                                <th>Previously reported</th>
                                <th>Value</th>
                                <th></th>
                            </thead>
                            @if (!empty($ous))
                                @foreach ($ous as $ou)
                                    <tr>
                                        <input type="hidden" name="output_update_id[]" value="{{ $ou->id }}">
                                        <td class="w-50">
                                            <input type="hidden" id="output" name="output_id[]" value="{{ $ou->output_id }}">{{ $ou->indicator }}
                                        </td>
                                        <td class="">
                                            dddd-origin
                                        </td>
                                        <td class="">
                                            dddd-origin
                                        </td>
                                        <td class="">
                                            <input type="number" name="output_value[]" class="form-control form-control-sm" size="3" required value="{{ $ou->value }}">
                                        </td>
                                        <td>
                                            <button type="button" name="remove" id="{{ $ou->output_id }}" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">
                                        <textarea name="output_progress[]" class="form-control form-control-sm mediumEditor" placeholder="Enter output progress" rows="2"></textarea>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="dropdown mt-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addOutputs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select outputs to update
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($project->submitted_outputs() as $output)
                            <a class="dropdown-item add-output" href="#" id="{{ $output->id }}" data-valuesum="{{ $output->valuesumnew }}" data-target="{{ $output->target }}" @if ((!empty($ous) && $ous->keyBy('output_id')->get($output->id)) || $output->status == 'draft') style="display: none;" @endif>{!! $output->indicator !!}</a>
                        @endforeach
                        <a class="dropdown-item add-output" href="#" id="0">Add a new output</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="aus_list" class="form-group-header mt-4">Covered activities</label>
            @if (!$project->activities->isEmpty())
                <div class="d-flex flex-wrap" id="aus_list">
                    @if (!empty($aus))
                        @foreach ($aus as $au)
                            <div class="col-lg-6 my-2 px-0" style="min-width: 16rem; max-width: 40rem;">
                                @include('project.activity_update', ['a' => $au->activity, 'au' => $au])
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="col-lg-6 my-2 px-0 dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addActivities" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select activities to update
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($project->activities as $activity)
                            <p class="d-none">{{ $activity->getComment() }}</p>
                            <a class="dropdown-item add-activity" href="#" id="{{ $activity->id }}" @if (!empty($aus) && $aus->keyBy('activity_id')->get($activity->id)) style="display: none;" @endif>{{ $activity->title }}</a>
                        @endforeach
                    </div>
                </div>
            @else
                <p>The project has no activities added</p>
            @endif

            <div class="form-group">
                <label for="attachments" class="form-group-header mt-4">Attachments</label>
                <p>Note: attach SPIDER budget reporting template to all reports.</p>
                <div class="alert" id="message" style="display: none"></div>
                <div>
                    <div id="attachments">
                        @if (!empty($files))
                            @foreach ($files as $file)
                                <span id="uploaded_file" class="d-block">
                                    <input type="hidden" name="file_id[]" value="{{ $file->id }}">
                                    <a href="{{ $file->path }}" target="_blank">{{ $file->name }}</a>
                                    <button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="far fa-trash-alt"></i></button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <input type="file" id="files" name="attachments" placeholder="Choose file(s)" multiple>
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <button class="btn btn-secondary" id="laravel-ajax-file-upload" disabled>Upload</button>
                </div>
            </div>

            <div class="form-group">
                @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                    <label for="internal_comment" class="form-group-header mt-4">Spider's internal comment</label>
                    <textarea rows=4 placeholder="Spider's internal comment" class="form-control form-control-sm @error('internal_comment') is-danger @enderror" name="internal_comment">{{ old('internal_comment', empty($project_update) ? '' : $project_update->internal_comment) }}</textarea>
                    @error('internal_comment')
                        <div class="text-danger">{{ $errors->first('internal_comment') }}</div>
                    @enderror
                @endif
            </div>

            @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                <div class="form-group">
                    <label for="project_state" class="form-group-header mt-4">Project state</label>
                    <div class="col col-sm-6 p-0 col-md-4">
                        <select class="custom-select" name="project_state" id="project_state">
                            <option value="0" selected>Propose state change</option>
                            <option value="onhold">On hold</option>
                            <option value="terminated">Terminated</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
            @endif

            @if (empty($project_update) || $project_update->status == 'draft')
                <input class="btn btn-lg btn-secondary mt-5" role="button" name="draft" value="Save as draft" type="submit">
            @endif
            @if (!empty($project_update) && $project_update->status == 'draft')
                <input class="btn btn-lg btn-danger mt-5" role="button" name="delete" value="Delete this draft" type="submit">
            @endif
            <input class="btn btn-lg btn-success mt-5" role="button" name="submit" value="Submit" type="submit">
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $('.generalMediumEditor, .mediumEditor').css('min-height', '60px').css('height', 'auto !important');
            if ($('#project_state').val() == 0) {
                $("#project_update_summary").prop('required', false);
            } else {
                $("#project_update_summary").prop('required', true);
            }

            $('input[name="dates"]').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    daysOfWeek: [
                        "Mo",
                        "Tu",
                        "We",
                        "Th",
                        "Fr",
                        "Sa",
                        "Su"
                    ]
                }
            });

            var editor = new MediumEditor('.mediumEditor#project_update_summary', {
                placeholder: {
                    text: "Summary"
                }
            });

            $(document).on('click', '.collapseEditor', function() {
                $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                $(this).toggleClass("fa-chevron-right fa-chevron-down");
            });

            $(document).on('change', '#project_state', function() {
                if ($(this).val() != 0) {
                    $("#project_update_summary").prop('required', true);
                } else {
                    $("#project_update_summary").prop('required', false);
                }
            });

            $(document).on('click', '#laravel-ajax-file-upload', function(e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let formData = new FormData();
                formData.append("project_id", "{{ $project->id }}");

                $.each($('input[name="attachments"]').prop('files'), function(i, file) {
                    console.log(file);
                    formData.append('attachments[]', file);
                });

                for (let key of formData.entries()) {
                    // Debug output
                    console.log(key[0] + ': ' + key[1]);
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('store_file') }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        alert('File has been uploaded successfully');
                        $('#message').show();
                        $('#message').html(data.message);
                        $('#message').addClass(data.class_name);
                        $('#attachments').append(data.attachments);
                        /*
                        if (data.file_ids) {
                            let fileids = JSON.parse(data.file_ids);
                            $.each(fileids, function (index, id) {
                                $('#attachments').append('<input type="hidden" name="file_id[]" value="' + id + '">');
                            });
                        }*/
                        //console.log(data.file_ids);
                    },
                    error: function(data) {
                        alert('There was an error in uploading the file.');
                        console.log(data);
                    }
                });
            });

            $(document).on('click', '#attachments .remove', function() {
                $(this).closest('span').remove();
                $("#attachments input[value='" + value + "']").remove();
            });

            $('input[type=file]').change(function() {
                if ($('input[type=file]').val() == '') {
                    $('#laravel-ajax-file-upload').attr('disabled', true)
                } else {
                    $('#laravel-ajax-file-upload').attr('disabled', false);
                }
            })

            $(document).on('click', '.add-activity', function(e) {
                e.preventDefault();
                let id = $(this).attr('id');
                $('#aus_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;" id="au-' + id + '"></div>');
                $('#au-' + id).load('/au/' + id + '/0');
                $('#' + id + '.add-activity').hide();
                if ($('#addActivities').next().children('.add-activity:visible').length == 0) {
                    $('#addActivities').addClass('disabled');
                }
                let editor = new MediumEditor('.mediumEditor', {
                    placeholder: {
                        text: "Comment",
                        hideOnClick: true
                    }
                });
            });

            $(document).on('click', '.add-outcome', function(e) {
                e.preventDefault();
                let id = $(this).attr('id');
                $('#outcomes').append('<div class="card mb-3" id="ou-' + id + '"></div>');
                $('#ou-' + id).load('/outcome_update/' + id + '/0');
                $('#' + id + '.add-outcome').hide();
                if ($('#addOutcomes').next().children('.add-outcome:visible').length == 0) {
                    $('#addOutcomes').addClass('disabled');
                }
            });

            $(document).on('click', '.add-output', function(e) {
                e.preventDefault();
                $('#outputs_table').closest('.card').show();
                console.log(this);
                let id = $(this).attr('id');
                let target = parseInt($(this).attr('data-target') || 0);
                let valuesum = $(this).attr('data-valuesum') || '';
                let output = $(this).text(); // TODO: should get the text from the exact field id, not entire block text.
                let html = '<tr>';
                html += '<input type="hidden" name="output_update_id[]" value=0>';
                if (id > 0) {
                    html += '<td class="w-50"><input type="hidden" id="output" name="output_id[]" value="' + id + '">' + output + '</td>';
                } else {
                    html += '<td class="w-50"><input type="text" id="output" name="output_id[]" placeholder="Enter output name" data-trigger="manual" maxlength="255" data-target="tooltip" title="Maximum length is 255 chars" required class="w-100"></td>';
                }
                html += '<td class="">' + target + '</td>';
                html += '<td class="">' + valuesum + '</td>';
                if(target == 1) {
                    html += '<td class=""><label class="checkbox-inline"><input type="checkbox" name="output_value[]" value="1" class="" > Completed</label></td>';
                } else {
                    html += '<td class=""><input type="number" name="output_value[]"  class="form-control form-control-sm" placeholder="0" value="0" size="3" required></td>';
                }
                html += '<td><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-trash-alt"></i></button></td>'
                html += '</tr>';
                html += '<tr>';
                html += '<td colspan="5"><textarea name="output_progress[]" class="form-control form-control-sm mediumEditor" placeholder="Enter output progress" rows="2"></textarea></td>';
                html += '</tr>';
                if (id > 0) {
                    $('#' + id + '.add-output').hide();
                }

                $('#outputs_table').append(html).ready(function() {
                    let editor = new MediumEditor('.mediumEditor', {
                        placeholder: {
                            text: "Enter output progress",
                            hideOnClick: true
                        }
                    });
                });

                $('input[name="output_id[]"]').focusout(function() {
                    $(this).tooltip('hide');
                });
                $('input[name="output_id[]"]').on('keyup', function() {
                    if (this.value.length > 250) {
                        $(this).tooltip('show');
                    } else {
                        $(this).tooltip('hide');
                    }
                });
            });

            $(document).on('click', '#outputs_table .remove', function() {
                let id = $(this).attr('id');
                $('#' + id + '.add-output').show();
                let closest = $(this).closest('tr');
                closest.next().remove();
                closest.remove();
                if ($('tr', $('#outputs_table')).length < 2) {
                    $('#outputs_table').closest('.card').hide();
                }
            });

            $(document).on('click', '#aus_list .remove', function() {
                let id = $(this).attr('id');
                $('#' + id + '.add-activity').show();
                $(this).closest('.col-lg-6').remove();
                if ($('#addActivities').next().children('.add-activity[style*="display: none"]').length > 0) {
                    $('#addActivities').removeClass('disabled');
                }
            });
            $(document).on('click', '#outcomes .remove', function() {
                let id = $(this).attr('id');
                $('#' + id + '.add-outcome').show();
                $(this).closest('.card').remove();
                if ($('#addOutcomes').next().children('.add-outcome[style*="display: none"]').length > 0) {
                    $('#addOutcomes').removeClass('disabled');
                }
            });
            $("form").submit(function() {
                let activity_template = '';
                $('#aus_list .medium-editor-element').each(function(index) {
                    if (!$(this).text().trim()) {
                        let activity_name = $(this).closest('.card-body').find('#activity_label').text();
                        activity_template += '\nActivity template for ' + activity_name + ' is empty';
                    }
                });
                if (activity_template) {
                    alert(activity_template);
                    return false;
                }
                // Add extra confirmation on empty activity & output
                let confirmation = '';
                if (!$('#project_update_summary').val()) {
                    confirmation += '\nSummary is empty';
                }
                if ($('#aus_list').children().length < 1 && $('#outputs_table tr').length < 2) {
                    confirmation += '\nThe update does not cover neither activities nor outputs';
                }
                if (!confirmation) {
                    return true;
                } else {
                    if (confirm('Please confirm the following:' + confirmation)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            });
        });
    </script>

@endsection
