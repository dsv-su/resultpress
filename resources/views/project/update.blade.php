@extends('layouts.master')

@section('content')
    <form action="{{ route('project_save_update', $project) }}" method="POST">
        @method('PUT')
        @csrf
        <h4>Write a project update</h4>
        <p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>
        <h5 class="field auto">Date: {{Carbon\Carbon::now()->format('d-m-Y')}}</h5>
        <h5>Project name: {{ $project->name }}</h5>

        <h5>Covered activities:</h5>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="addActivities" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                Add an activity
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @foreach ($project->activity as $activity)
                    <a class="dropdown-item add-activity" href="#" id="{{$activity->id}}">{{$activity->title}}</a>
                @endforeach
            </div>
        </div>
        <table class="table table-sm table-striped table-bordered" style="width:100%; display: none;"
               id="activities_table">
            <thead>
            <th>Activity</th>
            <th>Status</th>
            <th>Summary</th>
            <th>Money spent</th>
            <th>Date(s)</th>
            <th></th>
            </thead>

        </table>
        <h5>Affected outputs:</h5>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="addOutputs" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                Add an output
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @foreach ($project->output as $output)
                    <a class="dropdown-item add-output" href="#" id="{{$output->id}}">{{$output->indicator}}</a>
                @endforeach
            </div>
        </div>

        <table class="table table-sm table-striped table-bordered" style="width:100%; display: none;"
               id="outputs_table">
            <thead>
            <th>Output</th>
            <th>Value</th>
            <th></th>
            </thead>
        </table>

        <h5>Attachments:</h5>
        <div class="alert" id="message" style="display: none"></div>
        <div><span id="uploaded_file"></span></div>
        <input type="file" name="file" placeholder="Choose File" id="file">
        <input type="hidden" name="file_id" id="file_id" value=0>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <button class="btn btn-primary" id="laravel-ajax-file-upload">Upload</button>

        <div><textarea name="project_update_summary" placeholder="Update summary"></textarea></div>

        <input class="btn btn-primary btn-lg" value="UPDATE" type="submit">
    </form>

    <script>
        $(document).ready(function () {
            $(document).on('click', '#laravel-ajax-file-upload', function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let formData = new FormData();
                formData.append("project_id", "{{$project->id}}");
                formData.append('file', $('#file').prop('files')[0]);
                for (let key of formData.entries()) {
                    console.log(key[0] + ', ' + key[1]);
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
                        $('#uploaded_file').html(data.uploaded_file);
                        $('#file_id').val(data.file_id);
                        console.log(data.file_id);
                    },
                    error: function (data) {
                        alert('NOOO');
                        console.log(data);
                    }
                });
            });

            $(document).on('click', '.add-activity', function () {
                $('#activities_table').show();
                let id = $(this).attr('id');
                let activity = $(this).text();
                let html = '<tr>';
                html += '<input type="hidden" name="activity_update_id[]" value=0>';
                html += '<td class="auto"><input type="hidden" id="activity" name="activity_id[]" value="' + id + '">' + activity + '</td>';
                html += '<td class="editable"><select id="status" name="activity_status[]"><option value="1">In progress</option><option value="2">Delayed</option><option value="3">Done</option></select></td>'
                html += '<td><input type="text" name="activity_comment[]" class="form-control form-control-sm" placeholder="Comment" required></td>';
                html += '<td><input type="number" name="activity_money[]"  class="form-control form-control-sm" placeholder="Money" size="3" required></td></td>';
                html += '<td><input type="date" name="activity_date[]" class="form-control form-control-sm" placeholder="Date" size="1"></td>';
                html += '<td><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-user-times"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                html += '</tr>';
                $('#' + id + '.add-activity').hide();
                $('#activities_table').append(html);
            });
            $(document).on('click', '.add-output', function () {
                $('#outputs_table').show();
                let id = $(this).attr('id');
                let output = $(this).text();
                let html = '<tr>';
                html += '<input type="hidden" name="output_update_id[]" value=0>';
                html += '<td class="auto"><input type="hidden" id="output" name="output_id[]" value="' + id + '">' + output + '</td>';
                html += '<td><input type="number" name="output_value[]"  class="form-control form-control-sm" placeholder="Value" size="3" required></td></td>';
                html += '<td><button type="button" name="remove" id="' + id + '" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-user-times"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                html += '</tr>';
                $('#' + id + '.add-output').hide();
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