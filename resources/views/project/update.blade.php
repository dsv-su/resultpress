@extends('layouts.master')

@section('content')
    <form action="{{ route('project_save_update', $project) }}" method="POST">
        @method('PUT')
        @csrf

        <div class="row d-flex justify-content-between">
            <div class="col"><h4>{{ $project->name }}: @if (empty($project_update)) write an update @else edit draft
                    update
                    #{{$project_update->index}}@endif</h4></div>
            <div class="col-sm-auto d-flex align-items-center">
                <span class="badge badge-info font-100">{{Carbon\Carbon::now()->format('d-m-Y')}}</span>
            </div>
        </div>

        <p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>

        @if (!empty($project_update))
            <input type="hidden" value="{{$project_update->id}}" id="project_update_id" name="project_update_id">
        @endif

    <!-- <span class="d-none" id="project_currency">{{$project->getCurrencySymbol()}}</span> -->

        @if ($project->hasDraft() && $project->cumulative)
            <div class="alert alert-warning" role="alert">
                There are some drafts for this project. Please take this into account when covering activities (activity
                templates are based on the most recent update, including drafts).
            </div>
        @endif

        <div class="form-group">
            <label for="aus_list" class="form-group-header">Covered activities</label>
            <div class="d-flex flex-wrap" id="aus_list">
                @if (!empty($aus))
                    @foreach($aus as $au)
                        <div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;">
                            @include('project.activity_update', ['a' => $au->activity, 'au' => $au])
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col-lg-6 my-2 px-2 dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addActivities"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Add Activity
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @foreach ($project->activities as $activity)
                        <p class="d-none">{{$activity->getComment()}}</p>
                        <a class="dropdown-item add-activity" href="#"
                           id="{{$activity->id}}"
                           @if (!empty($aus) && $aus->keyBy('activity_id')->get($activity->id)) style="display: none;" @endif>{{$activity->title}}</a>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="outputs_table" class="form-group-header mt-4">Affected outputs</label>
                <div class="col-md-8 col-lg-6 my-2 px-2" style="min-width: 16rem;">
                    <table class="table table-sm" @if (empty($ous) || $ous->isEmpty()) style="display: none;"
                           @endif
                           id="outputs_table">
                        <thead>
                        <th>Output</th>
                        <th>Value</th>
                        <th></th>
                        </thead>
                        @if (!empty($ous))
                            @foreach($ous as $ou)
                                <tr>
                                    <input type="hidden" name="output_update_id[]" value="{{$ou->id}}">
                                    <td class="w-75"><input type="hidden" id="output" name="output_id[]"
                                                            value="{{$ou->output_id}}">{{$ou->indicator}}</td>
                                    <td class="w-25"><input type="number" name="output_value[]"
                                                            class="form-control form-control-sm"
                                                            size="3" required value="{{$ou->value}}"></td>
                                    <td>
                                        <button type="button" name="remove" id="{{$ou->output_id}}"
                                                class="btn btn-outline-danger btn-sm remove"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addOutputs"
                                data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            Add Output
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @foreach ($project->outputs as $output)
                                <a class="dropdown-item add-output" href="#" id="{{$output->id}}"
                                   @if ((!empty($ous) && $ous->keyBy('output_id')->get($output->id)) || $output->status == 'draft') style="display: none;" @endif>{{$output->indicator}}</a>
                            @endforeach
                            <a class="dropdown-item add-output" href="#" id="0">Add a new output</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="attachments" class="form-group-header mt-4">Attachments</label>
                <div class="alert" id="message" style="display: none"></div>
                <div>
                    <div id="attachments">
                        @if (!empty($files))
                            @foreach($files as $file)
                                <span id="uploaded_file" class="d-block">
                            <input type="hidden" name="file_id[]" value="{{$file->id}}">
                            <a href="{{$file->path}}" target="_blank">{{$file->name}}</a>
                            <button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i
                                        class="far fa-trash-alt"></i></button>
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
                <label for="project_update_summary" class="form-group-header mt-4">Summary</label>
                <textarea rows="4"
                          class="form-control form-control-sm @error('project_update_summary') is-danger @enderror"
                          name="project_update_summary" id="project_update_summary"
                >{{ old('project_description', empty($project_update) ? '' : $project_update->summary) }}</textarea>
                @error('project_description')
                <div class="text-danger">
                    {{ $errors->first('project_update_summary') }}
                </div>@enderror
            </div>

            @if (empty($project_update) || $project_update->status == 'draft')
                <input class="btn btn-lg btn-secondary" role="button" name="draft" value="Save as draft" type="submit">
            @endif
            @if (!empty($project_update) && $project_update->status == 'draft')
                <input class="btn btn-lg btn-danger" role="button" name="delete" value="Delete this draft"
                       type="submit">
            @endif
            <input class="btn btn-lg btn-success" role="button" name="submit" value="Submit" type="submit">
    </form>

    <script>
        $(document).ready(function () {
            let editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Description"}});
            $(document).on('click', '.collapseEditor', function () {
                $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                $(this).toggleClass("fa-chevron-right fa-chevron-down");
            });

            $(document).on('click', '#laravel-ajax-file-upload', function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let formData = new FormData();
                formData.append("project_id", "{{$project->id}}");

                $.each($('input[name="attachments"]').prop('files'), function (i, file) {
                    console.log(file);
                    formData.append('attachments[]', file);
                });

                for (let key of formData.entries()) {
                    // Debug output
                    console.log(key[0] + ': ' + key[1]);
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('store_file')}}",
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
                    error: function (data) {
                        alert('There was an error in uploading the file.');
                        console.log(data);
                    }
                });
            });

            $(document).on('click', '#attachments .remove', function () {
                $(this).closest('span').remove();
                $("#attachments input[value='" + value + "']").remove();
            });

            $('input[type=file]').change(function () {
                if ($('input[type=file]').val() == '') {
                    $('#laravel-ajax-file-upload').attr('disabled', true)
                } else {
                    $('#laravel-ajax-file-upload').attr('disabled', false);
                }
            })

            $(document).on('click', '.add-activity', function () {
                let id = $(this).attr('id');
                $('#aus_list').append('<div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;" id="au-' + id + '"></div>');
                $('#au-' + id).load('/au/' + id + '/0');
                $('#' + id + '.add-activity').hide();
                if ($('#addActivities').next().children('.add-activity:visible').length == 0) {
                    $('#addActivities').addClass('disabled');
                }
                ;
                let editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Comment", hideOnClick: true}});
                // $('#activities_table').append(html);
            });
            $(document).on('click', '.add-output', function () {
                $('#outputs_table').show();
                let id = $(this).attr('id');
                let output = $(this).text();
                let html = '<tr>';
                html += '<input type="hidden" name="output_update_id[]" value=0>';
                if (id > 0) {
                    html += '<td class="w-75"><input type="hidden" id="output" name="output_id[]" value="' + id + '">' + output + '</td>';
                } else {
                    html += '<td class="w-75"><input type="text" id="output" name="output_id[]" placeholder="Enter output name" data-trigger="manual" maxlength="255" data-target="tooltip" title="Maximum length is 255 chars" required></td>';
                }
                html += '<td class="w-25"><input type="number" name="output_value[]"  class="form-control form-control-sm" placeholder="0" value="0" size="3" required></td>';
                html += '<td><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-trash-alt"></i></button></td>'
                html += '</tr>';
                if (id > 0) {
                    $('#' + id + '.add-output').hide();
                }

                $('#outputs_table').append(html);

                $('input[name="output_id[]"]').focusout(function () {
                    $(this).tooltip('hide');
                });
                $('input[name="output_id[]"]').on('keyup', function() {
                    if (this.value.length > 250) {
                        $(this).tooltip('show');
                    } else {
                        $(this).tooltip('hide');
                    };
                });
            });

            $(document).on('click', '#outputs_table .remove', function () {
                let id = $(this).attr('id');
                $('#' + id + '.add-output').show();
                $(this).closest('tr').remove();
                if ($('tr', $('#outputs_table')).length < 2) {
                    $('#outputs_table').hide();
                }
            });
            $(document).on('click', '#aus_list .remove', function () {
                let id = $(this).attr('id');
                $('#' + id + '.add-activity').show();
                $(this).closest('.col-lg-6').remove();
                if ($('#addActivities').next().children('.add-activity[style*="display: none"]').length > 0) {
                    $('#addActivities').removeClass('disabled');
                }
                ;
            });
            $("form").submit(function () {
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
