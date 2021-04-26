@foreach($project->outcomes as $outcome)
    <div class="card">
        <div class="card-header bg-white" id="heading-outcome-{{$outcome->id}}">
            <div class="row">
                <div class="col-auto pl-1">
                    <h5 class="mb-0">
                        <span class="px-0 btn cursor-default">
                            {{$outcome->name}}
                        </span>
                    </h5>
                </div>
                <div class="col-auto d-flex py-2 px-1 align-items-center">
                    @if($outcome->completed)
                        <a href="#" class="badge badge-success font-100" data-toggle="collapse"
                           data-target="#collapse-outcome-{{$outcome->id}}"
                           aria-expanded="false"
                           aria-controls="collapse-outcome-{{$outcome->id}}">Completed on {{$outcome->completed_on}}</a>
                    @else
                        <a href="#" class="badge badge-primary font-100" data-toggle="collapse"
                           data-target="#collapse-outcome-{{$outcome->id}}"
                           aria-expanded="false"
                           aria-controls="collapse-outcome-{{$outcome->id}}">Mark as complete</a>
                    @endif
                </div>
            </div>
        </div>
        <div id="collapse-outcome-{{$outcome->id}}" class="collapse"
             aria-labelledby="heading-outcome-{{$outcome->id}}"
             data-parent="#outcomes">
            <div class="card-body">
                @if ($outcome->completed)
                    <div class="p-2">
                        <label for="summary" class="col-form-label">Completion
                            description:</label>
                        {{$outcome->summary}}
                        <br>
                        <label for="summary" class="col-form-label">Outputs status:</label>
                        @foreach(json_decode($outcome->outputs, true) as $output_id => $output)
                            <div class="row my-1">
                                <div class="col">
                                <span>{{$output['indicator']}} <span
                                            class="badge ml-2 badge-light font-100">{{$output['value']}}</span></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
            <!--        <form action="{{ route('outcome_update', $outcome) }}" method="POST">
                        @method('PUT')
                        @csrf-->
                        <div class="p-2">
                            <div class="form-group row">
                                <div class="col-auto col-sm-4 col-md-3">
                                    <!--<input name="project" value="{{$project->id}}" hidden>-->
                                        <input name="outcome_id[]" value="{{$outcome->id}}" hidden>
                                    <label for="project_area" class="col-form-labe">Outputs covered:</label>
                                </div>
                                <div class="col-sm">
                                    <select name="outcome_outputs[]" id="outcome_outputs"
                                            class="custom-select"
                                            multiple="multiple" required>
                                        @foreach($project->outputs as $output)
                                            <option value="{{$output->id}}" {{ old('output_id') == $output->id || ($outcome->outputs && in_array($output->id, json_decode($outcome->outputs, true))) ? 'selected':''}}>{{$output->indicator}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-auto col-sm-4 col-md-3">
                                    <label for="summary" class="col-form-label">Completion
                                        description:</label>
                                </div>
                                <div class="col-sm">
                                <textarea class="form-control" id="summary" name="outcome_summary[]"
                                          placeholder="Describe the outcome completion summary"></textarea>
                                </div>
                            </div>
                            <input class="btn btn-primary" value="Save" type="submit">
                        </div>
               <!--     </form>-->
                @endif
            </div>
        </div>
    </div>
@endforeach

<script>
    $(document).ready(function () {
        $('#outcome_outputs').multiselect({
            templates: {
                li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
            }
        });
    });
</script>