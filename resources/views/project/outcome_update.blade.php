<div class="card-header">
    <div class="row">
        <div class="col-auto pl-1">
            <h5 class="mb-0">
                <input type="hidden" name="outcome_update_id[]"
                       value="@if ($outcome_update) {{$outcome_update->id}} @else 0 @endif">
                <span class="px-0 btn cursor-default text-left">{{$outcome->name}}</span>
                @if (isset($show) && $show)
                    @if($outcome->completed())
                        <span class="badge badge-success">Completed on {{$outcome->completed()->format('d/m/Y')}}</span>
                    @else
                        <span class="badge badge-danger">Not completed</span>
                    @endif
                @endif
            </h5>
        </div>
    </div>
</div>

@if (isset($show) && $show)
    @if ($outcome_update && $outcome_update->outputs)
        <div class="card-body">
            <div class="p-2">
                <label for="summary" class="col-form-label">Completion description:</label>
                @if ($outcome_update) {{$outcome_update->summary}} @endif
                <br/>
                <!--<label for="summary" class="col-form-label">Outputs status:</label>-->
                @foreach(json_decode($outcome_update->outputs, true) as $output)
                    <div class="row my-1">
                        <div class="col">
                        <span>{{\App\Output::find($output)->indicator}}
                            <span class="badge ml-2 badge-light font-100">{{$outcome_update->calculateOutputValue($output)}}</span>
                        </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@elseif (!isset($show))
    <div class="card-body">
        <div class="p-2">
            <div class="form-group row">
                <div class="col-auto col-sm-4 col-md-3">
                    <input name="outcome_id[]" value="{{$outcome->id}}" hidden>
                    <label for="project_area" class="col-form-labe">Outputs covered:</label>
                </div>
                <div class="col-sm">
                    <input type="hidden" name="outcome_outputs[]"
                           @if ($outcome_update) value="{{$outcome_update->outputs}}" @endif>
                    <select id="outcome_outputs_{{$outcome->id}}"
                            class="custom-select"
                            multiple="multiple" required>
                        @foreach($outcome->project->outputs as $output)
                            <option value="{{$output->id}}" {{$outcome_update && $outcome_update->outputs && in_array($output->id, json_decode($outcome_update->outputs, true)) ? 'selected':''}}>{{$output->indicator}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-auto col-sm-4 col-md-3">
                    <label for="outcome_summary[]" class="col-form-label">Completion
                        description:</label>
                </div>
                <div class="col-sm">
                                <textarea class="form-control" id="outcome_summary[]" name="outcome_summary[]"
                                          placeholder="Describe the outcome completion summary">@if($outcome_update) {{$outcome_update->summary}} @endif</textarea>
                </div>
            </div>
        </div>
        <div class="form-group row mb-0">
            <a name="remove" id="{{$outcome->id}}"
               class="btn btn-outline-danger btn-sm remove ml-auto mt-1"><i
                        class="far fa-trash-alt"></i></a>
        </div>
    </div>
@endif

@if (!isset($show))
    <script>
        $(document).ready(function () {
            $('#outcome_outputs_{{$outcome->id}}').multiselect({
                templates: {
                    li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                }
            });
            $('#outcome_outputs_{{$outcome->id}}').on('change', function () {
                $(this).closest('div').find('input[name="outcome_outputs[]"]').val(JSON.stringify($(this).val()));
            });

        });
    </script>
@endif