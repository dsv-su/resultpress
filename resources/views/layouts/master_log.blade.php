<!DOCTYPE html>
<html>

<head>
    @include('layouts.partials.head_log')
</head>

<body>
<div id="container-md" class="wrapper">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="top-links">&nbsp;</div>
    @include('layouts.partials.header')
    @include('layouts.partials.nav')
    <div id="contents">
        <a class="accessibility-link"></a>
        @yield('content')
        <div class="clear">
        </div>
    </div>
    @include('layouts.partials.footer-scripts_log')
</div>

</body>

</html>
