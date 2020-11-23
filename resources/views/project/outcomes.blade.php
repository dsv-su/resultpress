@foreach($project->outcomes as $outcome)
    @if ($outcome->completed)
        <li class="list-group-item">{{$outcome->name}} <a href="#" class="badge badge-success"
                                                          data-toggle="modal"
                                                          data-target="#outcome_completed"
                                                          data-outcome="{{$outcome->name}}"
                                                          data-id="{{ $outcome->id }}">Completed
                on {{$outcome->completed_on}}</a></li>

        <div class="modal fade" id="outcome_completed" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Outcome
                            completion: {{$outcome->name}}</h5>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <p>Completed on {{$outcome->completed_on}}</p>
                        </div>
                        <div class="form-group">
                            <label for="summary" class="col-form-label">Completion
                                description:</label>
                            <p class="form-control" id="summary"
                               name="summary">{{$outcome->summary}}</p>
                        </div>
                        <div class="form-group">
                            <label for="summary" class="col-form-label">Outputs status:</label>
                            @foreach(json_decode($outcome->outputs, true) as $output_id => $output)
                                <p>Output: {{$output['indicator']}} Value: {{$output['value']}}</p>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <li class="list-group-item">{{$outcome->name}} <a href="#" class="badge badge-primary"
                                                          data-toggle="modal"
                                                          data-target="#outcome_completion"
                                                          data-outcome="{{$outcome->name}}"
                                                          data-id="{{ $outcome->id }}">Mark as
                complete</a>
        </li>
        <div class="modal fade" id="outcome_completion" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('outcome_update', $outcome) }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Outcome
                                completion: {{$outcome->name}}</h5>
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <!--
                                <div class="form-group">
                                    <label for="completion_date"
                                           class="col-form-label">Completed on:</label>
                                    <input type="date" class="form-control" id="completion-date">
                                </div>
                                -->
                                <input name="project" value="{{$project->id}}" hidden>
                                <div class="form-group col-md-3 px-0">
                                    <label for="project_area">Outputs covered</label><br>
                                    <div class="col-md-4 py-2">
                                        <select name="outcome_outputs[]" id="outcome_outputs"
                                                class="custom-select"
                                                multiple="multiple" required>
                                            @foreach($outputs as $output)
                                                <option value="{{$output->id}}" {{ old('output_id') == $output->id || in_array($output->id, json_decode($outcome->outputs, true)) ? 'selected':''}}>{{$output->indicator}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="summary" class="col-form-label">Completion
                                        description:</label>
                                    <textarea class="form-control" id="summary" name="summary"
                                              placeholder="Describe the outcome completion summary"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                            <input class="btn btn-primary" value="Save" type="submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
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