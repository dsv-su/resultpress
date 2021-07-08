@extends('layouts.master_new')

@section('content')
    <h3 class="mx-3">Projects <span data-toggle="tooltip"
                                    title="These are the projects in which you have been registered as a project partner"><i
                    class="fas fa-info-circle fa-1x"></i></span></h3>
    <div class="col">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane show active" role="tabpanel" aria-labelledby="owned-tab" id="owned">
                @foreach($projects as $project)
                    @include('project.project_list', ['$project' => $project])
                @endforeach
            </div>
        </div><!--/tab-content-->
    </div>

    <script>
        $('#myTab a').on('click', function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        $('span[data-toggle=tooltip]').mouseover(function () {
            $(this).tooltip('show');
        });
    </script>
@endsection
