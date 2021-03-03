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
        @if(isset($review) && $review)
            <div class="form-group mb-1 row">
                <label for="activity_status[]"
                       class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Status</label>
                <div class="col-8 col-sm-3 px-1">
                    <div class="form-control-sm px-0">
                        @if($au->activity->status() == 1)
                            <span class="badge badge-light font-100">Pending</span>
                        @elseif($au->activity->status() == 2)
                            <span class="badge badge-warning font-100">In progress</span>
                        @elseif($au->activity->status() == 3)
                            <span class="badge badge-danger font-100">Delayed</span>
                        @elseif($au->activity->status() == 4)
                            <span class="badge badge-info font-100">Pending review</span>
                        @elseif($au->activity->status() == 5)
                            <span class="badge badge-success font-100">Completed</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

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

        @if (isset($review) && $review && $au->activity->status() != 5)
            <div class="form-group row mb-0">
                <meta name="csrf-token" content="{{ csrf_token() }}">
                @if ($au->activity->completed == 1)
                    <a href="#" id="complete" data-completed="1"
                       class="badge badge-sm badge-success font-100 mt-1 ml-auto">Completed</a>
                @else
                    <a href="#" id="complete" data-completed="0"
                       class="badge badge-sm badge-primary font-100 mt-1 ml-auto">Mark as complete</a>
                @endif
            </div>
        @endif

        @if (!isset($show))
            <div class="form-group row mb-0">
                <a name="remove" id="{{$a->id}}"
                   class="btn btn-outline-danger btn-sm remove ml-auto mt-1"><i
                            class="far fa-trash-alt"></i></a>
            </div>
        @endif
    </div>
</div>

@if(isset($review) && $review)
    <script>
        var editor = new MediumEditor('.mediumEditor', {
            placeholder: {text: "Description"},
            toolbar: false,
            disableEditing: true
        });
        $('#complete').on('click', function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $(this).prev('meta[name="csrf-token"]').attr('content')
                }
            });
            let formData = new FormData();
            let completed = null;
            if ($(this).attr('data-completed') == 1) {
                completed = 0;
            } else {
                completed = 1;
            }
            formData.append("activity_id", "{{$au->activity->id}}");
            formData.append("activity_completed", completed);
            $.ajax({
                type: 'POST',
                url: "{{ url('complete_activity')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    alert('Complete');
                    $(this).text(data.text);
                    $(this).attr('data-completed', completed);
                    $(this).toggleClass('badge-primary badge-success');
                    if (completed) {
                        $(this).text('Completed');
                    } else {
                        $(this).text('Mark as complete');
                    }
                },
                error: function (data) {
                    alert('There was an error in updating completion status.');
                }
            });
        });
    </script>
@else
    <script>
        var editor = new MediumEditor('.mediumEditor', {placeholder: {text: "Comment", hideOnClick: true}});
    </script>
@endif
<script>
    $('input.datepicker:last-child').datepicker({
        format: 'dd-mm-yyyy',
        weekStart: 1
    });
</script>
