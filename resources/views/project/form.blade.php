@extends('layouts.master')
@section('content')

    <div class="form-row">
        <div class="col">
            <h4>@empty($project->id) Add a new project @else Update {{$project->name}} @endempty</h4>
        </div>
    </div>

    @empty($project->id)
        <form action="{{ route('update', $project) }}" method="POST">
            @else
                <form action="{{ route('project_update', $project) }}" method="POST">
                    @method('PUT')
                    @endempty
                    @csrf
                    <div class="form-group">
                        <label for="project" class="form-group-header">Project administration</label>
                        <div class="form-group">
                            <label for="project_name">Name</label>
                            <input class="form-control form-control-sm @error('project_name') is-danger @enderror"
                                   type="text" name="project_name" id="project_name" placeholder="Project title"
                                   required
                                   value="{{ old('project_name', empty($project) ? '' : $project->name) }}">
                            @error('project_name')
                            <div class="text-danger">{{ $errors->first('project_name') }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="project_description">Description</label>
                            <textarea rows="4"
                                      class="form-control form-control-sm mediumEditor @error('project_description') is-danger @enderror"
                                      name="project_description" id="project_description"
                            >{!! old('project_description', empty($project) ? '' : $project->description) !!}</textarea>
                            @error('project_description')
                            <div class="text-danger">
                                {{ $errors->first('project_description') }}
                            </div>@enderror
                        </div>
                        <div class="form-group col-md-3 px-0">
                            <label for="project_start">Project start</label>
                            <input type="text" name="project_start" id="project_start" placeholder="Start date"
                                   value="{{ old('project_start', empty($project->start) ? '' : $project->start->format('d-m-Y'))}}"
                                   class="form-control form-control-sm datepicker" required>
                        </div>
                        <div class="form-group col-md-3 px-0">
                            <label for="project_end">Project end</label>
                            <input type="text" name="project_end" id="project_end" placeholder="End date"
                                   value="{{ old('project_end', empty($project->end) ? '' : $project->end->format('d-m-Y'))}}"
                                   class="form-control form-control-sm datepicker">
                        </div>
                        <div class="form-group col-md-3 px-0">
                            <label for="project_currency">Project currency</label>
                            <select name="project_currency" id="project_currency" class="form-control">
                                <option value="SEK"
                                        @if ($project->currency == 'SEK' || !$project->currency) selected @endif>kr
                                </option>
                                <option value="EUR" @if ($project->currency == 'EUR') selected @endif>€</option>
                                <option value="USD" @if ($project->currency == 'USD') selected @endif>$</option>
                                <option value="GBP" @if ($project->currency == 'GBP') selected @endif>£</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="activities_table" class="form-group-header">Activities</label>
                            <div>
                                <table class="table table-sm" id="activities_table"
                                       @if($activities->isEmpty()) style="display:none;" @endif>
                                    <thead>
                                    <th scope="row">Activity Name</th>
                                    <th scope="row">Description</th>
                                    <th scope="row">Start</th>
                                    <th scope="row">End</th>
                                    <th scope="row">Email reminder</th>
                                    <th scope="row">Due days before</th>
                                    <th scope="row">Budget</th>
                                    <th></th>
                                    </thead>
                                    <!-- Here comes a foreach to show the activities -->

                                    @foreach ($activities as $activity)
                                        <tr>
                                            <td><input type="hidden" name="activity_id[]" value="{{$activity->id}}">
                                                <input type="text" name="activity_name[]"
                                                       value="{{$activity->title}}" required
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td><input type="text" name="activity_description[]"
                                                       value="{{$activity->description}}" required
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td><input type="text" name="activity_start[]"
                                                       value="{{$activity->start->format('d-m-Y')}}" required
                                                       class="form-control form-control-sm datepicker">
                                            </td>
                                            <td><input type="text" name="activity_end[]"
                                                       value="{{$activity->end->format('d-m-Y')}}" required
                                                       class="form-control form-control-sm datepicker">
                                            </td>
                                            <td><select name="activity_reminder[]">
                                                    @if($activity->reminder == true)
                                                        <option value="1" selected="selected">Yes</option>
                                                        <option value="0">No</option>
                                                    @else
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected="selected">No</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td><input type="number" name="activity_reminder_due_days[]"
                                                       value="{{$activity->reminder_due_days}}"
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td class="input-group">
                                                <input type="number" name="activity_budget[]" placeholder="0"
                                                       value="{{$activity->budget}}" required
                                                       class="form-control form-control-sm">
                                                <div class="input-group-append">
                                                    <span class="input-group-text currency"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" name="remove"
                                                        class="btn btn-outline-danger btn-sm remove"><i
                                                            class="fas fa-minus"></i><span
                                                            class="glyphicon glyphicon-minus"></span></button>
                                            </td>
                                        </tr>
                                        <tr class="update activity_template">
                                            <td colspan=8>
                                                <table class="table mb-2">
                                                    <tr>
                                                        <td><textarea name="activity_template[]" id="activity_template"
                                                                      placeholder="Activity description template"
                                                                      class="form-control form-control-sm mediumEditor">
                                                                {{$activity->template}}</textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <button type="button" name="add_activities"
                                        class="btn btn-outline-secondary btn-sm add-activities">Add
                                    Activities <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="project" class="form-group-header">Outputs</label>
                            <div>
                                <table class="table table-sm mw-400" id="outputs_table"
                                       @if($outputs->isEmpty()) style="display: none;" @endif>
                                    <thead>
                                    <th scope="row">Indicator</th>
                                    <th scope="row">Target</th>
                                    <th></th>
                                    </thead>
                                    <!-- Here comes a foreach to show the outputs -->

                                    @foreach ($outputs as $output)
                                        <tr>
                                            <td class="w-75"><input type="hidden" name="output_id[]"
                                                                    value="{{$output->id}}">
                                                <input type="text"
                                                       name="output_indicator[]"
                                                       value="{{$output->indicator}}"
                                                       placeholder="Output name" required
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td class="w-25"><input type="text" name="output_target[]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="0"
                                                                    value="{{$output->target}}" required></td>
                                            <td>
                                                <button type="button" name="remove"
                                                        class="btn btn-outline-danger btn-sm remove"><i
                                                            class="fas fa-minus"></i><span
                                                            class="glyphicon glyphicon-minus"></span></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <button type="button" name="add_outputs"
                                        class="btn btn-outline-secondary btn-sm add-outputs">Add
                                    Outputs <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-lg" @empty($project->id) value="Save"
                                   @else value="Update"
                                   @endempty
                                   type="submit">
                        </div>
                    </div>
                </form>

                <script>
                    let editor = new MediumEditor('.mediumEditor[name=project_description]', {placeholder: {text: "Description"}});
                    let editor2 = new MediumEditor('.mediumEditor#activity_template', {placeholder: {text: "Template"}});
                    $(document).ready(function () {
                        $('.currency').each(function () {
                            $(this).text($('#project_currency option:selected').text());
                        });
                        $(document).on('click', '.add-activities', function () {
                            $('#activities_table').show();
                            let currency = $('#project_currency option:selected').text();
                            let html = '';
                            html += '<tr>';
                            html += '<input type="hidden" name="activity_id[]" value=0>';
                            html += '<td><input type="text" name="activity_name[]" class="form-control form-control-sm" placeholder="Activity name" required></td>';
                            html += '<td><input type="text" name="activity_description[]" class="form-control form-control-sm" placeholder="Description" required></td>';
                            html += '<td><input type="text" name="activity_start[]" class="form-control form-control-sm datepicker" placeholder="Startdate" size="1" required></td>';
                            html += '<td><input type="text" name="activity_end[]"  class="form-control form-control-sm datepicker" placeholder="Enddate" size="1" required></td>';
                            html += '<td><select name="activity_reminder[]"  class="form-control form-control-sm"><option value="1">Yes</option><option value="0">No</option></select></td>';
                            html += '<td><input type="number" name="activity_reminder_due_days[]" class="form-control form-control-sm"  value="7" size="1" required></td>';
                            html += '<td class="input-group"><input type="number" name="activity_budget[]" class="form-control form-control-sm" placeholder="0" size="3" required><div class="input-group-append"><span class="input-group-text currency">' + currency + '</span></div></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span class="glyphicon glyphicon-minus"></span></button></td>'
                            html += '</tr>';
                            html += '<tr class="update activity_template"><td colspan=8><table class="table mb-2"><tr><td><textarea placeholder="Activity template" name="activity_template[]" ' +
                                'class="form-control form-control-sm mediumEditor"></textarea></td></tr></table></td></tr>';
                            $('#activities_table').append(html);
                            let editor = new MediumEditor('#activities_table .mediumEditor', {
                                placeholder: {text: "Template"}
                            });
                            $('input.datepicker').datepicker({
                                format: 'dd-mm-yyyy'
                            });
                        });
                        $(document).on('click', '.add-outputs', function () {
                            $('#outputs_table').show();
                            let html = '';
                            html += '<tr>';
                            html += '<input type="hidden" name="output_id[]" value=0>';
                            html += '<td class="w-75"><input type="text" name="output_indicator[]" class="form-control form-control-sm" placeholder="Output name" required></td>';
                            html += '<td class="w-25"><input type="text" name="output_target[]" class="form-control form-control-sm" placeholder="0" size="3" required></td>';
                            html += '<td><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i class="fas fa-minus"></i><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
                            $('#outputs_table').append(html);
                        });
                        $(document).on('click', '.remove', function () {
                            $(this).closest('tr').next().remove();
                            $(this).closest('tr').remove();
                            if ($('tr', $('#activities_table')).length < 2) {
                                $('#activities_table').hide();
                            }
                            if ($('tr', $('#outputs_table')).length < 2) {
                                $('#outputs_table').hide();
                            }
                        });
                        $('#project_currency').on('change', function () {
                            $('.currency').each(function () {
                                $(this).text($('#project_currency option:selected').text());
                            });
                        });
                        $("form").submit(function () {
                            // Add extra confirmation on empty fields
                            let confirmation = '';
                            if (!$('.medium-editor-element[name=project_description]').text().trim()) {
                                confirmation += '\nDescription field is empty';
                            }
                            if (!$("#project_end").val()) {
                                confirmation += '\nProject end date field is empty';
                            }
                            $('#activities_table .medium-editor-element').each(function (index) {
                                if (!$(this).text().trim()) {
                                    let activity_name = $(this).closest('.activity_template').prev().children('td').first().children('input[type=text]').val();
                                    confirmation += '\nActivity template for ' + activity_name + ' is empty';
                                }
                            });
                            if (!confirmation) {
                                return true;
                            } else {
                                if (confirm('Please confirm the following empty fields:' + confirmation)) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                        });
                    });
                </script>

@endsection
