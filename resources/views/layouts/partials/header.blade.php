@php
    $logo = App\Settings::where('name', 'logo')->first();
    $logo = $logo->value ?? 'logo-2023.png';
@endphp
<div id="header">
    <a id="header-dsv" href="{{ route('home') }}" title="Home" accesskey="1">
        <img src="{{ asset('storage/' . $logo) }}" alt="SPIDER">
    </a>
    <div class="clear"></div>
</div>
