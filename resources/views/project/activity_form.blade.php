<div class="card bg-light m-auto">
    <div class="card-body pb-1">
        <div class="form-group mb-1 row">
            <input type="hidden" name="activity_id[]" value="@if ($activity) {{$activity->id}} @else 0 @endif">
            <label for="activity_name[]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
            <div class="col-8 col-sm-9 px-1">
                <input type="text" name="activity_name[]" placeholder="Name"
                       @if ($activity) value="{{$activity->title}}" @endif required
                       class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="activity_name[]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Description</label>
            <div class="col-8 col-sm-9 px-1">
                                                        <textarea type="text" name="activity_description[]"
                                                                  id="activity_description" required
                                                                  class="form-control form-control-sm"
                                                                  placeholder="Description">@if ($activity) {{$activity->description}} @endif</textarea>
            </div>
        </div>
        <div class="form-group mb-1 row">
            <label for="activity_start[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Start</label>
            <div class="col-8 col-sm-4 px-1">
                <input type="text" name="activity_start[]" placeholder="Start date"
                       value="@if ($activity) {{$activity->start->format('d-m-Y')}} @endif" required
                       class="form-control form-control-sm datepicker">
            </div>
            <label for="activity_end[]"
                   class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">End</label>
            <div class="col-8 col-sm-4 px-1">
                <input type="text" name="activity_end[]" placeholder="End date"
                       value="@if ($activity) {{$activity->end->format('d-m-Y')}} @endif" required
                       class="form-control form-control-sm datepicker">
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="activity_reminder[]"
                   class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Email
                reminder</label>
            <div class="col-8 col-sm-9 px-1 form-inline">
                <select name="activity_reminder[]"
                        class="form-control form-control-sm">
                    <option value="1"
                            @if($activity && $activity->reminder) selected="selected" @endif>
                        Yes
                    </option>
                    <option value="0"
                            @if($activity && !$activity->reminder) selected="selected" @endif>
                        No
                    </option>
                </select>

                <input type="number" name="activity_reminder_due_days[]"
                       @if ($activity) value="{{$activity->reminder_due_days}}" @else value=7 @endif
                       class="form-control form-control-sm text-right mx-1"
                       style="width:60px;">

                <label for="activity_reminder_due_days[]"
                       class="pl-0 pr-1 col-form-label-sm text-left">days
                    before end</label>
            </div>
        </div>

        <div class="form-group mb-2 row">
            <label for="activity_priority[]"
                   class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Priority</label>
            <div class="col-8 col-sm-9 px-1 form-inline">
                <select name="activity_priority[]"
                        class="form-control form-control-sm">
                    <option value="normal"
                            @if(!$activity || $activity->priority=='normal') selected="selected" @endif>
                        Normal
                    </option>
                    <option value="high"
                            @if($activity && $activity->priority=='high') selected="selected" @endif>
                        High
                    </option>
                </select>
            </div>
        </div>

        <div class="form-group mb-2 row">
            <label for="activity_budget[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label>
            <div class="col col-sm-4 pl-1 pr-1">
                <div class="input-group input-group-sm">
                    <input type="number" name="activity_budget[]"
                           placeholder="0"
                           @if ($activity) value="{{$activity->budget}}" @else value="0" @endif
                           required
                           class="form-control text-right">
                    <div class="input-group-append">
                        <span class="input-group-text currency"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row mb-0">
            <label for="activity_template[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template
                <i class="fas fa-chevron-right collapseEditor"></i></label>
            <div class="col-8 col-sm-9 px-1">
                <textarea name="activity_template[]" id="activity_template"
                          placeholder="Activity description template"
                          class="form-control form-control-sm mediumEditor collapsed">
                            @if ($activity){{$activity->template}}@endif</textarea>
            </div>
        </div>
        <div class="form-group row mb-0">
            <a name="copy"
               class="btn btn-outline-secondary btn-sm copy ml-auto mt-1"><i
                        class="far fa-copy"></i></a>
            <a name="remove"
               class="btn btn-outline-danger btn-sm remove ml-1 mt-1"><i
                        class="far fa-trash-alt"></i></a>
        </div>
    </div>
</div>

@if (!$activity)
    <script>
        var currency = $('#project_currency option:selected').text();
        $('#activities_list div.col-lg-6:last-child').find('.currency').text(currency);
        $('#activities_list input.datepicker:last-child').datepicker({
            format: 'dd-mm-yyyy',
            weekStart: 1
        });
        $('#activities_list div.col-lg-6:last-child input.datepicker').datepicker("setDate", new Date());
        $('input[name="activity_start[]"]').on('change', function () {
            $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setStartDate", $(this).val());
            $(this).closest('.card-body').find('input[name="activity_end[]"]').datepicker("setDate", $(this).val());
        });
        @if ($index)
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_name[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_name[]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('textarea[name="activity_description[]"]').text($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('textarea[name="activity_description[]"]').text());
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_start[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_start[]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_end[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_end[]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('select[name="activity_reminder[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('select[name="activity_reminder[]"]').val()).change();
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_reminder_due_days[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_reminder_due_days[]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('select[name="activity_reminder[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('select[name="activity_reminder[]"]').val()).change();
        $('#activities_list div.col-lg-6:last-child').find('select[name="activity_priority[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('select[name="activity_priority[]"]').val()).change();
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_budget[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_budget[]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('input[name="activity_budget[]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activity_budget[]"]').val());
        $('#activities_list div.col-lg-6:last-child textarea[id="activity_template"]').html($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('#activity_template').val());
        @endif
        var editor = new MediumEditor('#activities_list #activity_template', {placeholder: {text: "Activity template"}});
    </script>
@endif