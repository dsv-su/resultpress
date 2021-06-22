@extends('layouts.master')
@section('content')

    @include('layouts.partials.searchbox')

    <h3 class="">Search results</h3>
    <div class="container px-0">
        @if(count($projects) > 0)
            @if (isset($projectmanagers) || isset($projectpartners) || isset($programareas))
                <form class="form-inline mx-3">
                    <label class="col-form-label mr-1 font-weight-light">Filter by: </label>
                    <select name="manager" @if (empty($projectmanagers)) disabled
                            @endif class="form-control mx-1 selectpicker"
                            data-none-selected-text="Manager" multiple style="width: 400px">
                        @foreach($projectmanagers as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                    <select name="partner" @if (empty($projectpartners)) disabled
                            @endif class="form-control mx-1 selectpicker"
                            data-none-selected-text="Partner" multiple style="width: 400px">
                        @foreach($projectpartners as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                    <select name="area" @if (empty($programareas)) disabled
                            @endif class="form-control mx-1 selectpicker"
                            data-none-selected-text="Area" multiple style="width: 400px">
                        @foreach($programareas as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
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