<!-- Flash Message section -->
@if(session()->has('message'))
    <div class="container align-self-center">
        <div class="alert {{session('alert') ?? 'alert-info'}}">
            {{ session('message') }}
        </div>
    </div>
@endif

<!-- Search box section -->
<div class="container align-self-center">
    <form class="form-inline form-main-search d-flex justify-content-between"
          id="header-main-search-form" name="header-main-search-form"
          action="/search" method="GET" data-search="/s%C3%B6k"
          role="search">
        @csrf
        <label for="header-main-search-text" class="sr-only">{{ __("Search") }}</label>
        <input class="form-control w-100 mx-auto" type="search"
               id="header-main-search-text" name="q" autocomplete="off"
               aria-haspopup="true"
               placeholder="{{ __("Search") }}"
               @if (isset($q)) value="{{$q}}" @endif
               aria-labelledby="header-main-search-form">
    </form>
</div>

<script>
    $('#header-main-search-form').on('submit', function(e){
        e.preventDefault();
        window.location.href = '/search/' + $('#header-main-search-text').val();
    });
</script>

<script>
    jQuery(document).ready(function ($) {
        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '/find?query=%QUERY%',
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('query'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $("#header-main-search-text").typeahead({
            classNames: {
                menu: 'search_autocomplete'
            },
            hint: false,
            autoselect: false,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),
            limit: 10,
            // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
            name: 'autocomplete-items',
            display: function (item) {
                if (item.type === 'project') {
                    return 'Project: ' + item.name;
                }
            },
            templates: {
                empty: [
                    ''
                ],
                header: [
                    ''
                ],
                suggestion: function (data) {
                    if (data.type === 'project') {
                        return '<li><a class="d-block w-100" href="/project/' + data.id + '">Project: ' + data.name + '</a></li>';
                    }
                }
            }
        }).on('keyup', function (e) {
            //$(".tt-suggestion:first-child").addClass('tt-cursor');
            let selected = $("#header-main-search-text").attr('aria-activedescendant');
            if (e.which === 13) {
                if (selected) {
                    window.location.href = $("#" + selected).find('a').prop('href');
                } else {
                    //      $(".tt-suggestion:first-child").addClass('tt-cursor');
                    //       window.location.href = $(".tt-suggestion:first-child").find('a').prop('href');
                }
            }
        });
    });
</script>