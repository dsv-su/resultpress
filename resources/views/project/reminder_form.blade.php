<div class="card bg-light m-auto">
    <div class="card-body pb-1">
        <div class="form-group mb-2 row">
            <label for="project_reminder[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Name</label>
            <div class="col-8 col-sm-9 px-1">
                <input class="form-control form-control-sm w-100"
                       name="project_reminder_name[]"
                       type="text" placeholder="Name"
                       value="{{ old('project_reminder_name', empty($reminder->name) ? '' : $reminder->name) }}">
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="project_reminder[]"
                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Email
                reminder</label>
            <div class="col-8 col-sm-2 px-1">
                <select name="project_reminder[]"
                        class="form-control form-control-sm">
                        <option value="1" @if ($reminder && $reminder->reminder) selected="selected" @endif>Yes</option>
                        <option value="0" @if (!$reminder || !$reminder->reminder) selected="selected" @endif>No</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-2 row">
            <label for="project_reminder_due_days[]"
                   class="col-4 col-sm-3 col-form-label-sm text-right">Deadline</label>
            <div class="col-8 col-sm-auto px-1">
                <input type="text" name="project_reminder_date[]"
                       id="project_reminder_date"
                       placeholder="Deadline Date"
                       value="{{ old('project_reminder_date', empty($reminder->set) ? '' : $reminder->set->format('d-m-Y')) }}"
                       class="form-control form-control-sm datepicker" required>

            </div>
            <label for="project_reminder_due_days[]"
                   class="col-4 col-sm-auto col-form-label-sm text-right pr-1">Days
                before</label>
            <div class="col-8 col-sm-1 px-1">
                <input type="number" name="project_reminder_due_days[]"
                       @if ($reminder) value="{{ $reminder->reminder_due_days}}" @else value="0" @endif
                        placeholder="0"
                       class="form-control form-control-sm form-inline"
                       style="width:50px;"
                       required>
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
        </script>
@endif
