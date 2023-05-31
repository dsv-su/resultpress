@php
    $user = auth()->user() ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.head')
</head>

<body>
<div id="container" class="wrapper">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="top-links">
        @if ($user)
            <div class="mt-3 btn btn-sm btn-icon btn-outline-light" id="user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="btn-inner--icon mr-2" style="position: relative;">
                    <i class="fas fa-envelope"></i>
                    <span class="badge badge-pill badge-dark" style="position: absolute; top: -7px; right: -7px;">{{ $user->unreadNotifications->count() }}</span>
                </span>
                <span class="btn-inner--text">{{ $user->name }}</span>
            </div>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow mt-1" aria-labelledby="user-dropdown" data-toggle="user-dropdown" data-offset="" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; max-width:50%;">
                {{-- <div class="dropdown-header">
                    <h6 class="text-overflow m-0"></h6>
                </div> --}}
                <div class="">
                    @livewire('notifications')
                </div>

            </div>
            
        @endif
    </div>
    @include('layouts.partials.header')
    @include('layouts.partials.nav')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div id="contents">
        <a class="accessibility-link"></a>
        @yield('content')
        <div class="clear">
        </div>
    </div>
    @include('layouts.partials.footer-scripts')
</div>
<!-- Livewire -->
@livewireScripts
</body>
</html>
