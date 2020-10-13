@extends('layouts.master_log')

@section('content')
    <table id="logTable" class="table table-sm table-striped table-bordered" style="width:100%" data-order='[[ 0, "desc" ]]'
           data-page-length='10'>
        <thead>
        <tr>
            <th>LogId</th>
            <th>LogName</th>
            <th>Description</th>
            <th>SubjectId</th>
            <th>Subject</th>
            <th>CauserId</th>
            <th>CauserType</th>
            <th>Properties</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $activities as $activity )
            <tr>
                <td>{{ $activity->id }}</td>
                <td>{{ $activity->log_name }}</td>
                <td>{{ $activity->description }}</td>
                <td>{{ $activity->subject_id }}</td>
                <td>{{ $activity->subject }}</td>
                <td>{{ $activity->causer_id }}</td>
                <td>{{ $activity->causer_type }}</td>
                <td>{{ $activity->properties }}</td>
                <td>{{ $activity->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
