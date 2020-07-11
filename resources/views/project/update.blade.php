@extends('layouts.master')

@section('content')
<form action="{{ route('project_save_update', $project) }}" method="POST">
    @method('PUT')
    @csrf
    <h4>Write a project update</h4>

    <h5 class="field auto">Date: {{Carbon\Carbon::now()->format('d-m-Y')}}</h5>
    <h5>Project name: {{ $project->name }}</h5>

    <h5>Covered activities:</h5>
    <button type="button" name="add_activities" class="btn btn-outline-primary btn-sm add-activities">Add
        Activities <i class="fas fa-user-times"></i></button>

    <table class="table table-sm table-striped table-bordered" style="width:100%; display: none;" id="activities_table">
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
    <button type="button" name="add_outputs" class="btn btn-outline-primary btn-sm add-outputs">Add
        Outputs <i class="fas fa-user-times"></i></button>

    <table class="table table-sm table-striped table-bordered" style="width:100%; display: none;" id="outputs_table">
        <thead>
            <th>Output</th>
            <th>Value</th>
            <th></th>
        </thead>
    </table>

    <div><textarea name="project_update_summary" placeholder="Update summary"></textarea></div>

    <input class="btn btn-primary btn-lg" value="UPDATE" type="submit">
</form>

<script>
    $(document).ready(function(){
        $(document).on('click', '.add-activities', function(){
            var activities = @json($project->activity);
            $('#activities_table').show();
            var html = '';
            html += '<tr>';
            html += '<input type="hidden" name="activity_update_id[]" value=0>';
            html += '<td class="field auto"><select id="activity" name="activity_id[]">';
            $.each(activities, function(key, activity){
                html += '<option value="' + activity.id + '">' + activity.title + '</option>';
            })
            html += '</select></td>';
            html += '<td class="field editable"><select id="status" name="activity_status[]"><option value="1">In progress</option><option value="2">Delayed</option><option value="3">Done</option></select></td>'
            html += '<td><input type="text" name="activity_comment[]" class="form-control form-control-sm" placeholder="Comment" required></td>';
            html += '<td><input type="number" name="activity_money[]"  class="form-control form-control-sm" placeholder="Money" size="3" required></td></td>';
            html += '<td><input type="date" name="activity_date[]" class="form-control form-control-sm" placeholder="Date" size="1" required></td>';
            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-user-times"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
            html += '</tr>';
            $('#activities_table').append(html);
        });
        $(document).on('click', '.add-outputs', function(){
            var outputs = @json($project->output);
            $('#outputs_table').show();
            var html = '';
            html += '<tr>';
            html += '<input type="hidden" name="output_update_id[]" value=0>';
            html += '<td class="field auto"><select id="output" name="output_id[]">';
            $.each(outputs, function(key, output){
                html += '<option value="' + output.id + '">' + output.indicator + '</option>';
            })
            html += '</select></td>';
            html += '<td><input type="number" name="output_value[]"  class="form-control form-control-sm" placeholder="Value" size="3" required></td></td>';
            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-user-times"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
            html += '</tr>';
            $('#outputs_table').append(html);
        });
        $(document).on('click', '.remove', function(){
            $(this).closest('tr').remove();
            if($('tr', $('#activities_table')).length < 2) {
                $('#activities_table').hide();
            }
            if($('tr', $('#outputs_table')).length < 2) {
                $('#outputs_table').hide();
            }
        });
        $(document).on('change', '#status', function() {
            value = this.options[this.selectedIndex].value;
            var status = '';
            switch(value){
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