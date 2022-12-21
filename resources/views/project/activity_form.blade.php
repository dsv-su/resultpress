@php
    $key = Str::random(10);
@endphp
<div class="card bg-light m-auto">
    <div class="card-body pb-1">
        <div class="form-group mb-1 row">
            @if ($activity)
            <input type="hidden" name="activities[{{$key}}][id]" value="{{$activity->id}}">
            @endif
            <label for="activities[{{$key}}][title]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
            <div class="col-8 col-sm-9 px-1">
                <input type="text" name="activities[{{$key}}][title]" placeholder="Name"
                       @if ($activity) value="{{$activity->title}}" @endif required
                       class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="activities[{{$key}}][description]"
                   class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Description</label>
            <div class="col-8 col-sm-9 px-1">
                                                        <textarea type="text" name="activities[{{$key}}][description]"
                                                                  id="activities[{{$key}}][description]" required
                                                                  class="form-control form-control-sm"
                                                                  placeholder="Description">@if ($activity) {{$activity->description}} @endif</textarea>
            </div>
        </div>
        <div class="form-group mb-1 row">
            <label for="activities[{{$key}}][start]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Start</label>
            <div class="col-8 col-sm-4 px-1">
                <input type="text" name="activities[{{$key}}][start]" placeholder="Start date"
                       @if ($activity) value="{{$activity->start->format('d-m-Y')}}" @endif required
                       class="form-control form-control-sm datepicker">
            </div>
            <label for="activities[{{$key}}][end]"
                   class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">End</label>
            <div class="col-8 col-sm-4 px-1">
                <input type="text" name="activities[{{$key}}][end]" placeholder="End date"
                       @if ($activity) value="{{$activity->end->format('d-m-Y')}}" @endif required
                       class="form-control form-control-sm datepicker">
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="activities[{{$key}}][priority]"
                   class="col-4 col-sm-3 mb-0 pl-0 pr-1 col-form-label-sm text-right">Priority</label>
            <div class="col-8 col-sm-9 px-1 form-inline">
                <select name="activities[{{$key}}][priority]"
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
            <label for="activities[{{$key}}][budget]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Budget</label>
            <div class="col col-sm-4 pl-1 pr-1">
                <div class="input-group input-group-sm">
                    <input type="number" name="activities[{{$key}}][budget]"
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
            <label for="activities[{{$key}}][template]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Template
                <i class="fas fa-chevron-right collapseEditor"></i></label>
            <div class="col-8 col-sm-9 px-1">
                <textarea name="activities[{{$key}}][template]" id="activities[{{$key}}][template]"
                          placeholder="Activity description template"
                          class="form-control form-control-sm mediumEditor collapsed">
                            @if ($activity){{$activity->template}}@endif</textarea>
            </div>
        </div>
        <div class="form-group row mb-0">
            <a name="copy"
               class="btn btn-outline-secondary btn-sm copy ml-auto mt-1" data-toggle="tooltip"
               title="Copy this activity"><i
                        class="far fa-copy"></i></a>
            <a name="remove"
               class="btn btn-outline-danger btn-sm remove ml-1 mt-1" data-toggle="tooltip"
               title="Delete this activity"><i
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
        $('input[name="activities[{{$key}}][start]"]').on('change', function () {
            $(this).closest('.card-body').find('input[name="activities[{{$key}}][end]"]').datepicker("setStartDate", $(this).val());
            var end = new Date($(this).closest('.card-body').find('input[name="activities[{{$key}}][end]"]').datepicker("getDate"));
            var start = new Date($(this).datepicker("getDate"));
            if (new Date($(this).datepicker("getDate")) > end) {
                $(this).closest('.card-body').find('input[name="activities[{{$key}}][end]"]').datepicker("setDate", $(this).val());
            }
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        @if ($index)
        $('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][name]"]').val($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('input[name="activities[{{$key}}][name]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('textarea[name="activities[{{$key}}][description]"]').text($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('textarea[name="activities[{{$key}}][description]"]').text());
        $('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][start]"]').datepicker("setDate", $('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('input[name="activities[{{$key}}][start]"]').datepicker("getDate"));
        $('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][end]"]').datepicker("setDate", $('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('input[name="activities[{{$key}}][end]"]').datepicker("getDate"));
        //$('#activities_list div.col-lg-6:last-child').find('select[name="activities[{{$key}}][reminder]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('select[name="activities[{{$key}}][reminder]"]').val()).change();
        //$('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][reminder_due_days]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('input[name="activities[{{$key}}][reminder_due_days]"]').val());
        //$('#activities_list div.col-lg-6:last-child').find('select[name="activities[{{$key}}][reminder]"]').val($('#activities_list div.col-lg-6:nth-child('+{{$index}}+')').find('select[name="activities[{{$key}}][reminder]"]').val()).change();
        $('#activities_list div.col-lg-6:last-child').find('select[name="activities[{{$key}}][priority]"]').val($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('select[name="activities[{{$key}}][priority]"]').val()).change();
        $('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][budget]"]').val($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('input[name="activities[{{$key}}][budget]"]').val());
        $('#activities_list div.col-lg-6:last-child').find('input[name="activities[{{$key}}][budget]"]').val($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('input[name="activities[{{$key}}][budget]"]').val());
        $('#activities_list div.col-lg-6:last-child textarea[id="activities[{{$key}}][template]"]').html($('#activities_list div.col-lg-6:nth-child(' + {{$index}} + ')').find('#activities[{{$key}}][template]').val());
        @endif
        var editor = new MediumEditor('#activities_list #activities[{{$key}}][template]', {placeholder: {text: "Activity template"}});
    </script>
@endif
