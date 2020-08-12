<!-- Start Top Bar -->
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="{{ route('home') }}">ResultPress</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('project_create') }}" role="button">Add</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Projects</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Program Areas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('home') }}">Ghana</a>
                        <a class="dropdown-item" href="{{ route('home') }}">Kenya</a>
                        <a class="dropdown-item" href="{{ route('home') }}">Nigeria</a>
                        <a class="dropdown-item" href="{{ route('home') }}">Senegal</a>
                        <a class="dropdown-item" href="{{ route('home') }}">Uganda</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Statistics</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">| UserName |</a>
                </li>

            </ul>
        </div>
    </nav>
</div>
