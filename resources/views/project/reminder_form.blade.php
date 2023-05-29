@php
    $key = $reminder->id ?? Str::random(10);
@endphp
<div class="card bg-light m-auto">
    <div class="card-body pb-1">
        <div class="form-group mb-2 row">
            @if ($reminder)
            <input type="hidden" name="reminders[{{$key}}][id]" value="{{$reminder->id}}">
            <input type="hidden" name="reminders[{{$key}}][type]" value="{{$reminder->type}}">
            <input type="hidden" name="reminders[{{$key}}][slug]" value="{{$reminder->slug}}">
            @endif
            <label for="reminders[{{$key}}][name]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
            <div class="col-8 col-sm-9 px-1">
                <textarea class="form-control form-control-sm w-100 generalMediumEditor" data-disable-toolbar="true" name="reminders[{{$key}}][name]" id="reminders[{{$key}}][name]" placeholder="Name">
                    {{ old('reminders[$key][name]', empty($reminder->name) ? '' : $reminder->name) }}
                </textarea>
            </div>
            <label for="reminders[{{$key}}][name]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right"></label>
            <div class="col-8 col-sm-9 px-1">
                @if (isset($project))
                    <span my-2>{{ $project->getSuggestedChanges('reminders', $reminder->slug, 'name') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="reminders[{{$key}}][reminder]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Email
                reminder</label>
            <div class="col-8 col-sm-2 px-1">
                <select name="reminders[{{$key}}][reminder]"
                        class="form-control form-control-sm">
                        <option value="1" @if ($reminder && $reminder->reminder) selected="selected" @endif>Yes</option>
                        <option value="0" @if (!$reminder || !$reminder->reminder) selected="selected" @endif>No</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="reminders[{{$key}}][set]"
                   class="col-4 col-sm-3 col-form-label-sm text-right">Deadline</label>
            <div class="col-8 col-sm-auto px-1">
                <input type="text" name="reminders[{{$key}}][set]"
                       id="reminders[{{$key}}][set]"
                       placeholder="Deadline Date"
                       value="{{ old('reminders[$key][set]', empty($reminder->set) ? '' : $reminder->set->format('d-m-Y')) }}"
                       class="form-control form-control-sm datepicker {{$reminder->type ?? ''}}-date" required>
                        @if (isset($project))
                            <span my-2>{{ $project->getSuggestedChanges('reminders', $reminder->slug, 'set') }}</span>
                        @endif
            </div>
        </div>

        <div class="form-group mb-2 row">
            <label for="reminders[{{$key}}][reminder_due_days]"
                   class="col-4 col-sm-3 col-form-label-sm text-right">Days before</label>
            <div class="col-8 col-sm-1 px-1">
                <input type="number" name="reminders[{{$key}}][reminder_due_days]"
                       @if ($reminder) value="{{ $reminder->reminder_due_days ?? 0 }}" @else value="0" @endif
                        placeholder="0"
                       class="form-control form-control-sm form-inline"
                       style="width:50px;"
                       >
            </div>
        </div>

        <div class="form-group row mb-0">
            <button type="button" name="reminder-remove"
                    class="btn btn-outline-danger btn-sm remove ml-auto mt-1"
                    data-toggle="tooltip" title="Remove this reminder">
                <i class="far fa-trash-alt"></i></button>
        </div>
    </div>
</div>

@if (!$reminder)
    <script>
        $('#reminders_list input.datepicker:last-child').datepicker({
            format: 'dd-mm-yyyy',
            weekStart: 1
        });
        $('#reminders_list div.col-lg-6:last-child input.datepicker').datepicker("setDate", new Date());
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        new MediumEditor('.generalMediumEditor');
    </script>
@endif
