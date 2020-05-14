@extends('layouts.master')
@section('content')

<div class="form-row">
    <div class="col">
        <h4>@empty($project->id) Add a new project @else Update {{$project->name}} @endempty</h4>

    </div>
</div>
@empty($project->id)
<form action="{{ route('update', $project) }}" method="POST">
@else
<form action="{{ route('project_update', $project) }}" method="POST">
@method('PUT')
@endempty

    @csrf

    <div class="form-group  ">
        <label for="project" class="text-primary">Project administration</label>
        <div class=" ">
            <div class="form-row">
                <div class="col-5">
                    <label><strong>Name</strong></label>
                    <input class="form-control form-control-sm @error('project_name') is-danger @enderror" type="text"
                        name="project_name" value="{{ $project->name }}">
                    @error('project_name')<div class="text-danger">{{ $errors->first('project_name') }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>Description</label>
                    <textarea rows="4"
                        class="form-control form-control-sm @error('project_description') is-danger @enderror"
                        name="project_description" type="text">{{ $project->description }}</textarea>
                    @error('project_description')<div class="text-danger">{{ $errors->first('project_description') }}
                    </div>@enderror
                </div>
            </div>
        </div>

        <div class="form-group ">
            <label for="project" class="text-primary">Activities</label>
            <div class=" ">
                <button type="button" name="add" class="btn btn-outline-primary btn-sm add">Add Activities <i
                        class="fas fa-user-times"></i></button>

                <table class="table table-sm" id="item_table">
                    @if($activities)
                    <tr>
                        <th scope="row">Activity Name</th>
                        <th scope="row">Start</th>
                        <th scope="row">End</th>
                        <th scope="row">Budget</th>
                        <th></th>
                    </tr>
                    <!-- Here comes a foreach to show the activities -->

                    @foreach ($activities as $activity)
                    <tr>
                        <input type="hidden" name="activity_id[]" value="{{$activity->id}}">
                        <td><input type="text" name="activity_name[]" value="{{$activity->title}}"
                                class="form-control form-control-sm">
                        </td>
                        <td><input type="date" name="activity_start[]" value="{{$activity->start}}"
                                class="form-control form-control-sm">
                        </td>
                        <td><input type="date" name="activity_end[]" value="{{$activity->end}}"
                                class="form-control form-control-sm"></td>
                        <td><input type="number" name="activity_budget[]" value="{{$activity->budget}}"
                                class="form-control form-control-sm">
                        </td>
                        <td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i
                                    class="fas fa-user-times"></i><span
                                    class="glyphicon glyphicon-minus"></span></button></td>
                    </tr>
                    @endforeach
                    @endif
                </table>
            </div>
            <div class="col">
                <!-- Status change -->
                <label for="status">Project Status:</label>
                <br>
                <select id="status" name="project_status">
                    <option value="1">In progress</option>
                    <option value="2">Delayed</option>
                    <option value="3">Done</option>
                </select>

                <!-- end Status change -->
            </div>
            <div class="col">
                <br>
                <input class="btn btn-primary btn-lg" @empty($project->id) value="SAVE" @else value="UPDATE" @endempty
                type="submit">
            </div>

        </div>
</form>

<script>
    $(document).ready(function(){
            $(document).on('click', '.add', function(){
                var html = '';
                html += '<tr>';
                html += '<input type="hidden" name="activity_id[]" value=0>';
                html += '<td><input type="text" name="activity_name[]" class="form-control form-control-sm" placeholder="Activity Name"></td>';
                html += '<td><input type="date" name="activity_start[]" class="form-control form-control-sm"  placeholder="Startdate" size="1" required></td>';
                html += '<td><input type="date" name="activity_end[]"  class="form-control form-control-sm" placeholder="Enddate" size="1" required></td></td>';
                html += '<td><input type="number" name="activity_budget[]"  class="form-control form-control-sm" placeholder="Budget" size="3" required></td></td>';
                html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-user-times"></i><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
                $('#item_table').append(html);
            });
            $(document).on('click', '.remove', function(){
                $(this).closest('tr').remove();
            });
        });
</script>

@endsection
