<div class="card-header">
    <div class="row">
        <div class="col-auto pl-1">
            <h5 class="mb-0">
                <input type="hidden" name="outcome_update_id[]" value="@if ($outcome_update) {{ $outcome_update->id }} @else 0 @endif">
                <span class="px-0 btn cursor-default text-left">{!! $outcome->name !!}</span>
                @if (isset($show) && $show)

                    <span data-toggle="collapse" data-target="#collapse-outcome-{{ $outcome->id ?? 0 }}" aria-expanded="false" role="button" aria-controls="collapseoutcome-{{ $outcome->id ?? 0 }}" class="badge badge-light font-50">{{ count($outcome->outcome_updates ?? []) }} @if (count($outcome->outcome_updates ?? []) > 1)
                            updates
                        @else
                            update
                        @endif </span>

                    @if ($outcome->completed())
                        <span class="badge badge-success">Completed on {{ $outcome->completed()->format('d/m/Y') }}</span>
                    @else
                        <span class="badge badge-danger">Not completed</span>
                    @endif
                @endif
            </h5>
            @if ($outcome->outcome_updates)
                <div id="collapse-outcome-{{ $outcome->id }}" class="collapse" aria-labelledby="headin-outcome-{{ $outcome->id }}" data-parent="#outcomes">
                    <div class="card-body">
                        @foreach ($outcome->outcome_updates as $puindex => $arr)
                            <div class="@if ($puindex > 0) mt-5 @endif">
                                <b><a class="mb-2" href="/project/update/{{ $arr['project_update_id'] }}">Update {{ $puindex + 1 }}</a></b>:
                                <h6 class="mt-2">Description:</h6>
                                {!! $arr['summary'] !!}
                                <h6>Connected outputs:</h6>
                                @foreach (json_decode($arr['outputs'], true) as $output)
                                    @foreach ($outcome->project->outputs as $out)
                                        @if ($out->id == $output)
                                            <ul>
                                                <li>{!! $out->indicator !!}</li>
                                            </ul>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if (isset($show) && $show)
    @if ($outcome_update && $outcome_update->outputs)
        <div class="card-body">
            <div>
                <label for="summary" class="col-form-label font-weight-bold">Latest update:</label>
                @if ($outcome_update)
                    @php
                        $short_summary = \Illuminate\Support\Str::words($outcome_update->summary, 60, $end = '');
                        $long_summary = str_replace($short_summary, '', $outcome_update->summary);
                    @endphp
                    <div data-toggle="collapse" data-target="#testid{!! $outcome_update->id !!}" aria-controls="#testid{!! $outcome_update->id !!}">{!! $short_summary !!} <b>more...</b></div>
                    <div class="collapse" id="testid{!! $outcome_update->id !!}">{!! $long_summary !!}</div>
                @endif
                <br />
                <label for="summary" class="col-form-label font-weight-bold font-italic">Connected outputs:</label>
                @foreach (json_decode($outcome_update->outputs, true) as $output)
                    <div class="row my-1">
                        <div class="col">
                            <span class="d-flex">{!! \App\Output::find($output)->indicator !!}
                                <span class="badge ml-2 badge-light font-100">{{ $outcome_update->calculateOutputValue($output) }}</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@elseif (!isset($show))
    <div class="card-body p-1">
        <div class="p-2">
            <div class="form-group row">
                <div class="col-auto col-sm-4 col-md-3">
                    <input name="outcome_id[]" value="{{ $outcome->id }}" hidden>
                    <label for="project_area" class="col-form-labe">Connected outputs:</label>
                </div>
                <div class="col-sm">
                    <input type="hidden" name="outcome_outputs[]" @if ($outcome_update) value="{{ $outcome_update->outputs }}" @endif>
                    <select id="outcome_outputs_{{ $outcome->id }}" class="custom-select" multiple="multiple" required>
                        @foreach ($outcome->project->outputs as $output)
                            <option value="{{ $output->id }}" {{ $outcome_update && $outcome_update->outputs && in_array($output->id, json_decode($outcome_update->outputs, true)) ? 'selected' : '' }}>{!! $output->indicator !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-auto col-sm-4 col-md-3">
                    <label for="outcome_summary[]" class="col-form-label">Description:</label>
                </div>
                <div class="col-sm">
                    <textarea class="form-control collapsed mediumEditor" id="outcome_summary[]" name="outcome_summary[]" placeholder="Describe the outcome completion summary">
@if ($outcome_update)
{!! $outcome_update->summary !!}
@endif
</textarea>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-auto col-sm-4 col-md-3">
                <label for="outcome_completion[]" class="col-form-label">Completed?</label>
            </div>
            <div class="col-sm">
                <input type="radio" name="outcome_completion[]" value="1"> Yes
                <input type="radio" name="outcome_completion[]" value="0" checked> No
            </div>
        </div>
        <div class="form-group row mb-0 mx-0">
            <a name="remove" id="{{ $outcome->id }}" class="btn btn-outline-danger btn-sm remove ml-auto mt-1"><i class="far fa-trash-alt"></i></a>
        </div>
    </div>
@endif

@if (!isset($show))
    <script>
        $(document).ready(function() {
            $('#outcome_outputs_{{ $outcome->id }}').multiselect({
                templates: {
                    li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                }
            });
            $('#outcome_outputs_{{ $outcome->id }}').on('change', function() {
                $(this).closest('div').find('input[name="outcome_outputs[]"]').val(JSON.stringify($(this).val()));
            });

        });
    </script>
@endif
<script>
    var editor = new MediumEditor('.mediumEditor', {
        placeholder: {
            text: "Summary",
            hideOnClick: true
        }
    });
</script>
