<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.loginhead')
</head>

<body>
<div id="container" class="wrapper">
    <a class="accessibility-link" accesskey="s" href="#content-top" title="Skip navigation"></a>
    <div id="contents">
        <a class="accessibility-link"></a>
        @yield('content')
        <div class="clear">
        </div>
    </div>
    <div id="footer">
        <div id="footer-name">
            <div id="footer-dsv">
                Department of Computer and Systems Sciences
            </div>
            <div id="footer-su">
                Stockholm University
            </div>
        </div>
        <div id="footer-contact">
            <a id="footer-contact-link" href="http://dsv.su.se/en/about/contact" accesskey="7">Contact</a>
        </div>
        <div class="clear">
        </div>
    </div>
</div>
</body>

</html>
