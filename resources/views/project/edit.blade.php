@extends('layouts.master')
@section('content')
    <div class="form-row">
        <div class="col">
            <h4>Update {{$project->name}}</h4>
        </div>
        <div class="col">
            <h3><span class="badge badge-secondary">Status</span></h3>
        </div>
    </div>
    <form action="/project/{{ $project->id }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group border border p-5">
            <label for="project" class="text-primary">Project administration</label>
            <div class="border border p-5">
                <div class="form-row">
                    <div class="col-5">
                        <label><strong>Name</strong></label>
                        <input class="form-control form-control-sm @error('project_name') is-danger @enderror" type="text" name="project_name" value="{{ $project->name }}">
                        @error('project_name')<div class="text-danger">{{ $errors->first('project_name') }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label>Description</label>
                        <textarea  rows="4" class="form-control form-control-sm @error('project_description') is-danger @enderror" name="project_description" type="text">{{ $project->description }}</textarea>
                        @error('project_description')<div class="text-danger">{{ $errors->first('project_description') }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group border border p-5">
                <label for="project" class="text-primary">Activities</label>
                <div class="border border p-5">
                    <button type="button" name="add" class="btn btn-outline-primary btn-sm add">Add Activities <i class="fas fa-hands"></i></button>

                    <table class="table table-sm" id="item_table">
                        <tr>
                            <th scope="row">Activity Name</th>
                            <th scope="row">Start</th>
                            <th scope="row">End</th>
                            <th scope="row">Budget</th>
                            <th></th>
                        </tr>
                        <input type="hidden" name="status" value=1>


                        <tr>
                            <td><input type="text" name="activity_name[]" value="" class="form-control form-control-sm" ></td>
                            <td><input type="date" name="activity_start[]" value="" class="form-control form-control-sm" ></td>
                            <td><input type="date" name="activity_end[]" value="" class="form-control form-control-sm" ></td>
                            <td><input type="number" name="activity_budget[]" value="" class="form-control form-control-sm"></td>
                            <td>
                                <a class="btn btn-danger btn-sm" href=""><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>

                    </table>
                </div>




                <div class="col">
                    <br>
                    <input class="btn btn-primary btn-lg" value="UPDATE"  type="submit">
                </div>


            </div>
    </form>
@endsection
