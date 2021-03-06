@extends('layouts.master')
@section('content')

    @include('layouts.partials.searchbox')

    <h3 class="container">Projects</h3>
    <div class="container px-0">
        @if(count($projects) > 0)
            @if (isset($projectmanagers) || isset($projectpartners) || isset($programareas) || isset($organisations) || isset($statuses))
                <form class="form-inline mx-3">
                    <label class="mb-2 col-form-label mr-1 font-weight-light">Filter by: </label>
                    <select name="my" class="mb-2 form-control mx-1 selectpicker"
                            data-none-selected-text="All Projects" multiple style="width: 400px">
                        <option value="owned" selected>My projects</option>
                        @if (Auth::user()->hasRole(['Administrator', 'Program administrator', 'Spider']))
                            <option value="followed">Followed projects</option>
                        @endif
                    </select>
                    <select name="manager" @if (empty($projectmanagers)) disabled
                            @endif class="mb-2 form-control mx-1 selectpicker"
                            data-none-selected-text="Manager" multiple style="width: 400px">
                        @foreach($projectmanagers as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                    <select name="partner" @if (empty($projectpartners)) disabled
                            @endif class="mb-2 form-control mx-1 selectpicker"
                            data-none-selected-text="Partner" multiple style="width: 400px">
                        @foreach($projectpartners as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                    <select name="area" @if (empty($programareas)) disabled
                            @endif class="mb-2 form-control mx-1 selectpicker"
                            data-none-selected-text="Area" multiple style="width: 400px">
                        @foreach($programareas as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                    <select name="organisation" @if (empty($organisations)) disabled
                            @endif class="mb-2 form-control mx-1 selectpicker"
                            data-none-selected-text="Organisation" multiple style="width: 400px">
                        @foreach($organisations as $id => $org)
                            <option value="{{$id}}">{{$org}}</option>
                        @endforeach
                    </select>
                    @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                        <select name="status" @if (empty($statuses)) disabled
                                @endif class="mb-2 form-control mx-1 selectpicker"
                                data-none-selected-text="Status" multiple style="width: 400px">
                            @foreach($statuses as $status)
                                <option value="{{$status}}">
                                    @if($status == 'planned')
                                        Planned
                                    @elseif($status == 'inprogress')
                                        In progress
                                    @elseif($status == 'delayedhigh')
                                        Delayed
                                    @elseif($status == 'delayednormal')
                                        Delayed
                                    @elseif($status == 'pendingreview')
                                        Pending review
                                    @elseif($status == 'completed')
                                        Completed
                                    @elseif($status == 'archived')
                                        Archived
                                    @elseif($status == 'onhold')
                                        On hold
                                    @elseif($status == 'terminated')
                                        Terminated
                                    @endif</option>
                            @endforeach
                        </select>
                    @endif
                    <button class="mb-2 btn btn-outline-secondary" onclick="$('.selectpicker').selectpicker('deselectAll'); $('.selectpicker').selectpicker('refresh');
">Clear selection
                    </button>
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                </form>
            @endif

            <div id="projects">
                @foreach ($projects as $key => $project)
                    <div class="col my-3">
                        @include('project.project_list', ['project' => $project])
                    </div>
                @endforeach
            </div>
        @endif
    </div><!-- /.container -->

    <script>
        $(document).on('change', 'select', function (e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let formData = new FormData();
            formData.append("partner", $('select[name="partner"]').val());
            formData.append("manager", $('select[name="manager"]').val());
            formData.append("area", $('select[name="area"]').val());
            formData.append("organisation", $('select[name="organisation"]').val());
            if ($('select[name="status"]').length) {
                formData.append("status", $('select[name="status"]').val());
            }
            formData.append("my", $('select[name="my"]').val());
            $.ajax({
                type: 'POST',
                url: "/{{ Request::path()}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    $('#projects').html(data['html']);
                    $('select[name="partner"] option').each(function () {
                        if (data['partners'][$(this).val()]) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('select[name="manager"] option').each(function () {
                        if (data['managers'][$(this).val()]) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('select[name="area"] option').each(function () {
                        if (data['areas'][$(this).val()]) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('select[name="organisation"] option').each(function () {
                        if (data['organisations'][$(this).val()]) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('select[name="status"] option').each(function () {
                        if (data['statuses'].includes($(this).val())) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('.selectpicker').selectpicker('refresh');
                },
                error: function (data) {
                    alert('There was an error.');
                    console.log(data);
                }
            });
        });
    </script>

@endsection
