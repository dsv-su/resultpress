<div class="card bg-light m-auto">
    <div class="card-body pb-1">
        <div class="form-group mb-1 row">
            <input type="hidden" name="activity_update_id[]" value="@if ($au) {{$au->id}} @else 0 @endif">
            <label for="activity_name[]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
            <div class="col-8 col-sm-9 px-1">
                <input type="hidden" id="activity" name="activity_id[]"
                       value="@if ($au) {{$au->activity_id}} @else {{$a->id}} @endif"><label
                        class="col-form-label-sm font-weight-bold">{{$a->title}}</label>
            </div>
        </div>
        <div class="form-group mb-1 row">
            <label for="activity_status[]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Status</label>
            <div class="col-8 col-sm-3 px-1">
                @if (isset($show) && $show)
                    <div class="form-control-sm px-0">
                        @if($au->status == 1)
                            <span class="badge badge-info font-100">Started</span>
                        @elseif($au->status == 2)
                            <span class="badge badge-warning font-100">Delayed</span>
                        @elseif($au->status == 3)
                            <span class="badge badge-success font-100">Done</span>
                        @endif
                    </div>
                @else
                    <select id="status" name="activity_status[]"
                            class="form-control-sm form-control">
                        <option value="1" @if ($au && $au->status == 1) selected @endif >In progress
                        </option>
                        <option value="2" @if ($au && $au->status == 2) selected @endif>Delayed
                        </option>
                        <option value="3" @if ($au && $au->status == 3) selected @endif>Done</option>
                    </select>
                @endif
            </div>
        </div>

        <div class="form-group mb-1 row">
            <label for="activity_money[]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Money
                spent</label>
            @if (isset($show) && $show)
                <label class="font-weight-bold pl-1 col-8 col-sm-9 col-form-label-sm">{{ceil($au->money)}} {{$a->project->getCurrencySymbol()}}</label>
            @else
                <div class="col-8 col-sm-auto px-1">
                    <div class="input-group input-group-sm">
                        <input type="number" name="activity_money[]" placeholder="0"
                               value="@if ($au){{ceil($au->money)}}@else 0 @endif" required
                               class="form-control text-right">
                        <div class="input-group-append">
                            <span class="input-group-text">{{$a->project->getCurrencySymbol()}}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="form-group mb-1 row">
            <label for="activity_date[]"
                   class="col-4 col-sm-3 pl-1 pr-1 col-form-label-sm text-right">Date</label>
            <div class="col-8 col-sm-3 px-1">
                @if (isset($show) && $show)
                    <label class="font-weight-bold pl-1 col-form-label-sm">{{$au->date->toDateString()}}</label>
                @else
                    <input type="text" name="activity_date[]"
                           class="form-control form-control-sm datepicker"
                           placeholder="Date" size="1" value="@if ($au) {{$au->date->toDateString()}} @endif"
                           required>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <label for="activity_template[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template
                <i class="fas fa-chevron-right collapseEditor"></i></label>
            <div class="col-8 col-sm-9 px-1">
                @if (isset($show) && $show)
                    <textarea name="activity_comment[]"
                              class="form-control form-control-sm collapsed mediumEditor">@if ($au){!! $au->comment !!}@endif</textarea>
                @else
                    <textarea name="activity_comment[]"
                              class="form-control form-control-sm collapsed mediumEditor"
                              required>@if ($au){!! $au->comment !!} @else {!!$a->template!!} @endif</textarea>
                @endif
            </div>
        </div>

        @if(isset($review) && $review)
            <div class="form-group row mb-0">
                <label for="activity_info[]"
                       class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Info</label>
                <div class="col-8 col-sm-9 px-1">
                    <label class="pl-1 col-form-label-sm">{{$au->budgetstring}}<br/>{{$au->deadlinestring}}</label>
                </div>
            </div>
        @endif

        @if (!isset($show))
            <div class="form-group row mb-0">
                <a name="remove" id="{{$a->id}}"
                   class="btn btn-outline-danger btn-sm remove remove ml-auto mt-1"><i
                            class="far fa-trash-alt"></i></a>
            </div>
        @endif
    </div>
</div>

<script>
    var editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Comment", hideOnClick: true}});
    $('input.datepicker:last-child').datepicker({
        format: 'dd-mm-yyyy',
        weekStart: 1
    });
</script>
