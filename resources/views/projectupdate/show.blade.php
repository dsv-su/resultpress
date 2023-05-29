@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('project_show', $project->id) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('projectupdate_index', $project->id) }}">Updates</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                @if (empty($project_update))
                    write an update
                @else
                    Review of update
                    #{{ $project_update->index }}
                @endif
            </li>
        </ol>
    </nav>
    <div class="row justify-content-between mb-5">
        <div class="col-6">
            @if ($project_update->status == 'draft')
                <a href="/project/update/{{ $project_update->id }}/edit" role="button" class="btn btn-primary">Edit the update</a>
            @endif
        </div>
        <div class="col-sm-auto d-flex align-items-center">
            @if ($project_update->status == 'draft')
                <span class="badge badge-warning font-100">Draft</span>
            @elseif($project_update->status == 'submitted')
                <span class="badge badge-info font-100">Submitted</span>
            @elseif($project_update->status == 'approved')
                <span class="badge badge-success font-100">Approved</span>
            @endif
            <span class="badge badge-info ml-2 font-100">{{ $project_update->created_at->format('d/m/Y') }}</span>
        </div>
    </div>

    <label for="dates" class="form-group-header">Dates covered</label>
    <div class="d-flex flex-wrap" id="dates">
        <label class="pl-1 col-form-label">{{ $project_update->start ? $project_update->start->format('d/m/Y') : 'not specified' }}
            - {{ $project_update->end ? $project_update->end->format('d/m/Y') : 'not specified' }}</label>
    </div>

    @if (!$activity_updates->isEmpty())
        <label for="aus_list" class="form-group-header">Covered activities</label>
        <div class="d-flex flex-wrap" id="aus_list">
            @foreach ($activity_updates as $au)
                <div class="col-lg-6 my-2 px-2" style="min-width: 16rem; max-width: 40rem;">
                    @include('project.activity_update', ['au' => $au, 'a' => $au->activity, 'show' => true, 'review' => $review])
                </div>
            @endforeach

        </div>
    @endif

    @if (!$output_updates->isEmpty())
        <label for="outputs_table" class="form-group-header mt-4">Outputs</label>
        <div class="col-md-12 my-2 px-0" style="min-width: 16rem;">
            <div class="card bg-light m-auto">
                <div class="card-body p-1">
                    <table class="table table-sm table-borderless mb-0" id="outputs_table">
                        <thead>
                            <th>Output</th>
                            <th class="text-right">Target</th>
                            <th class="text-right">This update added</th>
                            <th class="text-right">Total reported</th>
                        </thead>
                        @foreach ($output_updates as $ou)
                            @php
                                $completedStyle = $ou->output->target == 1 && $ou->output->target == $ou->output->valuesumnew ? 'style=display:none;' : '';
                            @endphp
                            <tr>
                                <td class="w-50">{!! $ou->indicator !!}</td>
                                <td class="text-right" {{ $completedStyle }}>{{ $ou->target }}</td>
                                <td class="text-right" {{ $completedStyle }}>{{ $ou->value }}</td>
                                <td class="text-right" {{ $completedStyle }}>{{ $ou->output->valuesumnew }}</td>
                                @if ($ou->output->target == 1 && $ou->output->target == $ou->output->valuesumnew)
                                    <td class="text-right" colspan="3">
                                        <span class="badge badge-success font-100 ml-1">Completed</span>
                                    </td>
                                @endif
                            </tr>
                            @foreach ($ou->aggregated as $a)
                                <tr class="update">
                                    <td colspan="4" class="px-1">
                                        <span class="badge badge-warning font-100 my-1">Contributes to {{ $a }} output</span>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($review && ($ou->contributionstring || $ou->totalstring) && Auth::user()->hasRole(['Spider', 'Administrator']))
                                <tr class="update">
                                    <td colspan="4" class="px-1">
                                        <span class="badge badge-info font-100 text-left my-1">
                                            @if ($ou->contributionstring)
                                                {{ $ou->contributionstring }}<br />
                                            @endif
                                            @if ($ou->totalstring)
                                                {{ $ou->totalstring }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            @if ($ou->progress)
                                <tr>
                                    <th colspan="4" class="px-1">
                                        Progress:
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-1 mt-2">
                                        {!! $ou->progress !!}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (!$project_update->outcome_updates->isEmpty())
        <label for="outcomes" class="form-group-header mt-4">Outcomes</label>
        <div id="outcomes">
            @foreach ($project_update->outcome_updates as $ou)
                <div class="card mb-3">
                    @include('project.outcome_update', ['outcome_update' => $ou, 'outcome' => $ou->outcome, 'show' => true])
                </div>
            @endforeach
        </div>
    @endif

    @if (!$files->isEmpty())
        <div class="my-1">
            <label for="attachments" class="form-group-header mt-4">Attachments</label>
            <div id="attachments">
                @foreach ($files as $file)
                    <span id="uploaded_file" class="d-block"><a href="{{ $file->path }}" target="_blank">{{ $file->name }}</a></span>
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
                    <span class="badge badge-info font-100">Closed</span>
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
                            {!! $project_update->summary !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TODO: Change the date before deploying to production --}}
    @if ($review)
        <form action="{{ route('projectupdate_update', $project_update) }}" method="POST">
            @method('PUT')
            @csrf
            @if ($project_update->project->created_at <= date('Y-m-d H:i:s', strtotime('2023-05-16')))
                <div class="form-group">
                    @if (Auth::user()->hasRole(['Partner']))
                        <label for="partner_comment" class="form-group-header mt-4">Partner's comment</label>
                        <textarea rows=4 placeholder="Partner's comment" class="form-control form-control-sm @error('partner_comment') is-danger @enderror" name="partner_comment">{{ old('partner_comment', empty($project_update) ? '' : $project_update->partner_comment) }}</textarea>
                        @error('partner_comment')
                            <div class="text-danger">{{ $errors->first('partner_comment') }}</div>
                        @enderror
                    @endif
                    @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                        @if ($project_update->partner_comment)
                            <label class="form-group-header mt-4">Partner's comment</label>
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <td>{{ $project_update->partner_comment }}</td>
                                </tr>
                            </table>
                        @endif

                        <label for="reviewer_comment" class="form-group-header mt-4">Reviewer comment</label>
                        <textarea rows=4 placeholder="Reviewer feedback to the project update author" required class="form-control form-control-sm @error('reviewer_comment') is-danger @enderror" name="reviewer_comment">{{ old('reviewer_comment', empty($project_update) ? '' : $project_update->reviewer_comment) }}</textarea>
                        @error('reviewer_comment')
                            <div class="text-danger">{{ $errors->first('reviewer_comment') }}</div>
                        @enderror

                        <label for="internal_comment" class="form-group-header mt-4">Spider's internal comment</label>
                        <textarea rows=4 placeholder="Spider's internal comment" class="form-control form-control-sm @error('internal_comment') is-danger @enderror" name="internal_comment">{{ old('internal_comment', empty($project_update) ? '' : $project_update->internal_comment) }}</textarea>
                        @error('internal_comment')
                            <div class="text-danger">{{ $errors->first('internal_comment') }}</div>
                        @enderror
                    @endif
            @endif
            @can("project-$project_update->project_id-update")
                <div class="my-3">
                    @if ($project_update->status == 'submitted' && Auth::user()->hasRole(['Spider', 'Administrator']))
                        <input class="btn btn-danger btn-lg mr-2" name="reject" value="Return for revision" type="submit">
                        <input class="btn btn-success btn-lg mr-2" name="approve" value="Approve" type="submit">
                    @endif
                    <input class="btn btn-primary btn-lg mr-2" value="Save" value="save" type="submit">
                </div>
            @endcan
            </div>
        </form>
    @else
        @if ($project_update->project->created_at <= date('Y-m-d H:i:s', strtotime('2023-05-16')) && ($project_update->partner_comment || $project_update->internal_comment))
            @if ($project_update->partner_comment)
                <label class="form-group-header mt-4">Partner's comment</label>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{ $project_update->partner_comment }}</td>
                    </tr>
                </table>
            @endif
            @if ($project_update->reviewer_comment && Auth::user()->hasRole(['Spider', 'Partner']))
                <label class="form-group-header mt-4">Reviewer comment</label>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{ $project_update->reviewer_comment }}</td>
                    </tr>
                </table>
            @endif
            @if ($project_update->internal_comment && Auth::user()->hasRole(['Spider']))
                <label class="form-group-header mt-4">Spider's internal comment</label>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{ $project_update->internal_comment }}</td>
                    </tr>
                </table>
            @endif
        @endif
    @endif
    @if ($project_update->project->created_at >= date('Y-m-d H:i:s', strtotime('2023-05-16')))
        @livewire('comments', ['projectUpdate' => $project_update, 'comments' => $project_update->comments, 'commentable_type' => 'App\ProjectUpdate', 'commentable_id' => $project_update->id])
    @endif
    <script>
        $(document).ready(function() {
            //var editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Description"}, toolbar: false, disableEditing: true});
            $(document).on('click', '.collapseEditor', function() {
                $(this).closest('.form-group').find('.medium-editor-element').toggleClass("collapsed expanded");
                $(this).toggleClass("fa-chevron-right fa-chevron-down");
            });
        });
    </script>

@endsection
