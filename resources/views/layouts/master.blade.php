<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.head')
</head>

<body>
<div id="container" class="wrapper">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="top-links">&nbsp;</div>
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
