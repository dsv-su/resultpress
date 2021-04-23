@extends('layouts.master')

@section('content')
    <div class="row justify-content-between">
        <div class="col-6"><h4>{{ $project->name }}: Update #{{$project_update->index}} </h4></div>
        <div class="col-sm-auto d-flex align-items-center">
            @if($project_update->status == 'draft') <span class="badge badge-warning font-100">Draft</span>
            @elseif($project_update->status == 'submitted') <span class="badge badge-info font-100">Submitted</span>
            @elseif($project_update->status == 'approved') <span class="badge badge-success font-100">Approved</span>
            @endif
            <span class="badge badge-info ml-2 font-100">{{$project_update->created_at->format('d/m/Y')}}</span>
        </div>
    </div>
    <p><a href="{{ route('projectupdate_index', $project_update->project_id) }}">Back to project updates list</a></p>

    @if(!$activity_updates->isEmpty())
        <label for="aus_list" class="form-group-header">Covered activities</label>
        <div class="d-flex flex-wrap" id="aus_list">
            @foreach($activity_updates as $au)
                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;">
                    @include('project.activity_update', ['au' => $au, 'a' => $au->activity, 'show' => true, 'review' => $review])
                </div>
            @endforeach

        </div>
    @endif

    @if(!$output_updates->isEmpty())
        <label for="outputs_table" class="form-group-header mt-4">Affected outputs</label>
        <table class="table mw-400" id="outputs_table">
            <thead>
            <th>Output</th>
            <th>Value</th>
            </thead>
            @foreach($output_updates as $ou)
                <tr>
                    <td class="w-75">{{$ou->indicator}}</td>
                    <td class="w-25">{{$ou->value}}</td>
                </tr>
                @if($review && ($ou->contributionstring || $ou->totalstring))
                    <tr class="update">
                        <td colspan=2>
                            <table class="table mb-2">
                                <tr>
                                    <td class="derived">
                                        @if ($ou->contributionstring){{$ou->contributionstring}}<br/>@endif
                                        @if ($ou->totalstring){{$ou->totalstring}}@endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif

    @can('project-create')
        <label for="outcomes" class="form-group-header mt-4">Outcomes</label>
        <div class="accordion" id="outcomes">
            @if (!$project_update->project->outcomes->isEmpty())
                @include('project.outcomes')
            @else
                The project has no outcomes.
            @endif
        </div>
    @endcan

    @if(!$files->isEmpty())
        <div class="my-1">
            <label for="attachments" class="form-group-header mt-4">Attachments</label>
            <div id="attachments">
                @foreach($files as $file)
                    <span id="uploaded_file" class="d-block"><a href="{{$file->path}}"
                                                                target="_blank">{{$file->name}}</a></span>
                @endforeach
            </div>
        </div>
    @endif

    @if (Auth::user()->hasRole(['Spider', 'Administrator']) && $project_update->state)
        <div class="form-group">
            <label for="project_status" class="form-group-header mt-4">Proposed project state</label>
            <div>
                @if ($project_update->state == 'onhold')
                    <span class="badge badge-info font-100">On hold</span>
                @elseif ($project_update->state == 'terminated')
                    <span class="badge badge-info font-100">Terminated</span>
                @elseif ($project_update->state == 'archived')
                    <span class="badge badge-info font-100">Archived</span>
                @endif
            </div>
        </div>
    @endif

    @if ($project_update->summary)
        <div class="accordion my-3" id="summary">
            <label for="outcomes" class="form-group-header mt-4">Summary</label>
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row">
                        <div class="col-auto pl-1">
                            {!!$project_update->summary!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($project_update->status == 'draft')
        <a href="/project/update/{{$project_update->id}}/edit" role="button" class="btn btn-primary">Edit</a>
    @endif

    @if($review)
        <form action="{{ route('projectupdate_update', $project_update) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="form-group">
                @if (Auth::user()->hasRole(['Partner']))
                    <div class="form-row my-2">
                        <label for="partner_comment" class="form-group-header mt-4">Partner's comment</label>
                        <textarea rows=4 placeholder="Partner's comment"
                                  class="form-control form-control-sm @error('partner_comment') is-danger @enderror"
                                  name="partner_comment">{{ old('partner_comment', empty($project_update) ? '' : $project_update->partner_comment) }}</textarea>
                        @error('partner_comment')
                        <div class="text-danger">{{ $errors->first('partner_comment') }}</div>
                        @enderror
                    </div>
                @endif
                @if (Auth::user()->hasRole(['Spider']))
                    <div class="form-row my-2">
                        <label for="internal_comment" class="form-group-header mt-4">Spider's internal comment</label>
                        <textarea rows=4 placeholder="Spider's internal comment"
                                  class="form-control form-control-sm @error('internal_comment') is-danger @enderror"
                                  name="internal_comment">{{ old('internal_comment', empty($project_update) ? '' : $project_update->internal_comment) }}</textarea>
                        @error('internal_comment')
                        <div class="text-danger">{{ $errors->first('internal_comment') }}</div>
                        @enderror
                    </div>
                @endif
                @can('project-create')
                    <div class="form-row my-2">
                        @if ($project_update->status == 'submitted' && Auth::user()->hasRole(['Spider', 'Administrator']))
                            <input class="btn btn-danger btn-lg mr-2" name="reject" value="Return for revision"
                                   type="submit">
                            <input class="btn btn-success btn-lg mr-2" name="approve" value="Approve" type="submit">
                        @endif
                        <input class="btn btn-primary btn-lg mr-2" value="Save" value="save" type="submit">
                    </div>
                @endcan
            </div>
        </form>
    @else
        @if ($project_update->partner_comment || $project_update->internal_comment)
            @if ($project_update->partner_comment)
                <label class="form-group-header mt-4">Partner's comment</label>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{$project_update->partner_comment}}</td>
                    </tr>
                </table>
            @endif
            @if ($project_update->internal_comment)
                <label class="form-group-header mt-4">Spider's internal comment</label>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{$project_update->internal_comment}}</td>
                    </tr>
                </table>
            @endif
        @endif
    @endif

    <script>
        $(document).ready(function () {
            //var editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Description"}, toolbar: false, disableEditing: true});
            $(document).on('click', '.collapseEditor', function () {
                $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                $(this).toggleClass("fa-chevron-right fa-chevron-down");
            });
        });
    </script>

@endsection
