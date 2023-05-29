@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit taxonomy</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('taxonomies.index') }}"> Back</a>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
        <br>
        <div class="alert alert-danger">
            There are some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('taxonomies.update', $taxonomy->id) }}" method="POST">
        @method('PATCH')
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control" name="name" type="text" placeholder="Name" value="{{ old('name', empty($taxonomy) ? '' : $taxonomy->name) }}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <table class="table table-bordered">
                    <tr>
                        <th>Term</th>
                        <th width="50px">Action</th>
                    </tr>
                    @foreach ($taxonomy->terms as $term)
                        <tr>
                            <td>
                                <input class="form-control" name="terms[{{ $term->id }}]" type="text" placeholder="Term" value="{{ $term->name }}">
                            </td>
                            <td>
                                <a class="btn btn-outline-danger deleteTerm" name="delete[{{ $term->id }}]" value="1"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <input class="form-control" name="terms[]" type="text" placeholder="New Term" value="">
                        </td>
                    </tr>
                </table>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <script>
        $('.deleteTerm').click(function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this term?')) {
                $(this).closest('tr').remove();
            }
        });
    </script>
@endsection

