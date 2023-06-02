<!-- Start Top Bar -->

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    {{-- <a class="navbar-brand" href="{{ route('home') }}">ResultPress</a> --}}
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item">
                @if (Auth::user()->setting == null)
                    <a class="nav-link" href="{{ route('profile') }}">Dashboard<span class="badge badge-primary" style="z-index:15;position:relative; left: 0; top:-10px">1</span></a>
                @else
                    <a class="nav-link" href="{{ route('profile') }}">Dashboard</a>
                @endif
            </li>
            @can('project-create')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('project_create') }}">Add a project</a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="{{ route('search') }}">Filter projects</a>
            </li>
            @can('view-areas')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('programareas') }}">Program Areas</a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="{{ route('partner-logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('partner-logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
            @if (Auth::user()->hasRole('Administrator'))
                <li><a class="nav-link" href="{{ route('admin') }}">Admin</a></li>
            @endif
            @php
                $helpPage = App\Settings::where('name', 'help')->first();
            @endphp
            @if ($helpPage && $helpPage->value && !empty($helpPage->value))
                <li><a class="nav-link" href="{{ route('pages.show', 'help') }}">Help</a></li>
            @endif
        </ul>
    </div>
</nav>
