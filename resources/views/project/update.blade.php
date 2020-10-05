@extends('layouts.master')

@section('content')
    <form action="{{ route('project_save_update', $project) }}" method="POST">
        @method('PUT')
        @csrf

        <div class="row d-flex justify-content-between">
            <div class="col"><h4>{{ $project->name }}: @if (empty($project_update)) write an update @else edit draft
                    update
                    #{{$project_update->index}}@endif</h4></div>
            <div class="col-sm-auto field auto d-flex align-items-center">
                <span>{{Carbon\Carbon::now()->format('d-m-Y')}}</span>
            </div>
        </div>

        <p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>

        @if (!empty($project_update))
            <input type="hidden" value="{{$project_update->id}}" id="project_update_id" name="project_update_id">
        @endif

        <div class="form-group">
            <h4>Covered activities:</h4>
            <table class="table table-sm w-100" @if (empty($aus) || $aus->isEmpty()) style="display: none;"
                   @endif id="activities_table">
                <thead>
                <th>Activity</th>
                <th>Status</th>
                <th>Money spent</th>
                <th>Date(s)</th>
                <th></th>
                </thead>
                @if (!empty($aus))
                    @foreach($aus as $au)
                        <tr>
                            <input type="hidden" name="activity_update_id[]" value="{{$au->id}}">
                            <td class="auto"><input type="hidden" id="activity" name="activity_id[]"
                                                    value="{{$au->activity_id}}">{{$au->title}}</td>
                            <td class="editable">
                                <select id="status" name="activity_status[]">
                                    <option value="1" @if ($au->status == 1) selected @endif >In progress</option>
                                    <option value="2" @if ($au->status == 2) selected @endif>Delayed</option>
                                    <option value="3" @if ($au->status == 3) selected @endif>Done</option>
                                </select>
                            </td>
                            <td><input type="number" name="activity_money[]" class="form-control form-control-sm"
                                       placeholder="Money" size="3" required value="{{$au->money}}"></td>
                            <td><input type="date" name="activity_date[]" class="form-control form-control-sm"
                                       placeholder="Date" size="1" value="{{$au->date->toDateString()}}" required></td>
                            <td class="fit">
                                <button type="button" name="remove" id="{{$au->activity_id}}"
                                        class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span
                                            class="glyphicon glyphicon-minus"></span></button>
                            </td>
                        </tr>
                        <tr class="update">
                            <td colspan=5>
                                <table class="table mb-2 ">
                                    <tr>
                                        <td><textarea name="activity_comment[]"
                                                      class="form-control form-control-sm mediumEditor"
                                                      required>{!! $au->comment !!}</textarea></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addActivities"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Add an activity
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @foreach ($project->activity as $activity)
                        <p class="d-none">{{$activity->activity_updates->last()->comment ?? $activity->template}}</p>
                        <a class="dropdown-item add-activity" href="#"
                           id="{{$activity->id}}"
                           @if (!empty($aus) && $aus->keyBy('activity_id')->get($activity->id)) style="display: none;" @endif>{{$activity->title}}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-group">
            <h4>Affected outputs:</h4>
            <table class="table table-sm mw-400" @if (empty($ous) || $ous->isEmpty()) style="display: none;" @endif
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
                                        class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span
                                            class="glyphicon glyphicon-minus"></span></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="addOutputs"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Add an output
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @foreach ($project->output as $output)
                        <a class="dropdown-item add-output" href="#" id="{{$output->id}}"
                           @if (!empty($ous) && $ous->keyBy('output_id')->get($output->id)) style="display: none;" @endif>{{$output->indicator}}</a>
                    @endforeach
                    <a class="dropdown-item add-output" href="#" id="0">Add a new ouput</a>
                </div>
            </div>
        </div>

        <div class="form-group">
            <h5>Attachments:</h5>
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
                            <br/>
                        </span>
                        @endforeach
                    @endif
                </div>
                <input type="file" id="files" name="attachments" placeholder="Choose file(s)" multiple>
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <button class="btn btn-secondary" id="laravel-ajax-file-upload">Upload</button>
            </div>
        </div>

        <div class="form-group">
            <h5>Summary</h5>
            <textarea rows="4"
                      class="form-control form-control-sm @error('project_update_summary') is-danger @enderror"
                      name="project_update_summary" id="project_update_summary"
            >{{ old('project_description', empty($project_update) ? '' : $project_update->summary) }}</textarea>
            @error('project_description')
            <div class="text-danger">
                {{ $errors->first('project_update_summary') }}
            </div>@enderror
        </div>

        <input class="btn btn-lg" name="draft" value="Save as draft" type="submit">
        <input class="btn btn-lg" name="submit" value="Submit" type="submit">
    </form>

    <script>
        $(document).ready(function () {
            let editor = new MediumEditor('.mediumEditor');
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
                        if (data.file_ids) {
                            let fileids = JSON.parse(data.file_ids);
                            $.each(fileids, function (index, id) {
                                $('#attachments').append('<input type="hidden" name="file_id[]" value="' + id + '">');
                            });
                        }
                        console.log(data.file_ids);
                    },
                    error: function (data) {
                        alert('There was an error in uploading the file.');
                        console.log(data);
                    }
                });
            });

            $(document).on('click', '#attachments .remove', function () {
                $(this).closest('span').remove();
            });

            $(document).on('click', '.add-activity', function () {
                $('#activities_table').show();
                let id = $(this).attr('id');
                let activity = $(this).text();
                let template = $(this).prev('p').text();
                let html = '<tr>';
                html += '<input type="hidden" name="activity_update_id[]" value=0>';
                html += '<td class="auto"><input type="hidden" id="activity" name="activity_id[]" value="' + id + '">' + activity + '</td>';
                html += '<td class="editable"><select id="status" name="activity_status[]"><option value="1">In progress</option><option value="2">Delayed</option><option value="3">Done</option></select></td>'
                // html += '<td><input type="text" name="activity_comment[]" class="form-control form-control-sm" placeholder="Comment" required></td>';
                html += '<td><input type="number" name="activity_money[]"  class="form-control form-control-sm" placeholder="Money" size="3" required></td>';
                html += '<td><input type="date" name="activity_date[]" class="form-control form-control-sm" placeholder="Date" size="1" required></td>';
                html += '<td class="fit"><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                html += '</tr>';
                html += '<tr class="update"><td colspan=5><table class="table mb-2 "><tr><td><textarea name="activity_comment[]" ' +
                    'class="form-control form-control-sm mediumEditor" required>' + template + '</textarea></td></tr></table></td></tr>';
                $('#' + id + '.add-activity').hide();
                $('#activities_table').append(html);
                let editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Comment", hideOnClick: true}});
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
                    html += '<td class="w-75"><input type="text" id="output" name="output_id[]" placeholder="Enter output name"></td>';
                }
                html += '<td class="w-25"><input type="number" name="output_value[]"  class="form-control form-control-sm" placeholder="Value" size="3" required></td>';
                html += '<td><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                html += '</tr>';
                if (id > 0) {
                    $('#' + id + '.add-output').hide();
                }
                $('#outputs_table').append(html);
            });
            $(document).on('click', '#outputs_table .remove', function () {
                let id = $(this).attr('id');
                $('#' + id + '.add-output').show();
                $(this).closest('tr').remove();
                if ($('tr', $('#outputs_table')).length < 2) {
                    $('#outputs_table').hide();
                }
            });
            $(document).on('click', '#activities_table .remove', function () {
                let id = $(this).attr('id');
                $('#' + id + '.add-activity').show();
                $(this).closest('tr').next().remove();
                $(this).closest('tr').remove();
                if ($('tr', $('#activities_table')).length < 2) {
                    $('#activities_table').hide();
                }
            });
            $(document).on('change', '#status', function () {
                let value = this.options[this.selectedIndex].value;
                let status = '';
                switch (value) {
                    case '1':
                        status = 'inprogress';
                        break;
                    case '2':
                        status = 'delayed';
                        break;
                    case '3':
                        status = 'done';
                        break;
                }
                this.closest('td').classList.remove('inprogress', 'delayed', 'done');
                this.closest('td').classList.add('status', status);
            });
        });
    </script>

@endsection