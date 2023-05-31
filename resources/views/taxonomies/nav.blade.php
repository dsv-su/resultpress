<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('types.index') }}">Taxonomies</a></li>
        @if (!empty($taxonomyType))
            <li class="breadcrumb-item"><a href="{{ route('types.terms', ['type' => $taxonomyType->id ?? '']) }}">{{ Str::plural($taxonomyType->name ?? '', 2) }}</a></li>
        @endif

        @if (!empty($parentTax))
            @if (!empty($parentTax->parent_id) && $upperTax = $taxonomyType->taxonomies()->where('id', $parentTax->parent_id)->first())
                @if (!empty($upperTax->parent_id) && $upperTax1 = $taxonomyType->taxonomies()->where('id', $upperTax->parent_id)->first())
                    <li class="breadcrumb-item"><a href="{{ route('terms.index', ['type' => $taxonomyType->id ?? '', 'parent' => $upperTax->parent_id]) }}">{{ $upperTax1->title }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('terms.index', ['type' => $taxonomyType->id ?? '', 'parent' => $parentTax->parent_id]) }}">{{ $upperTax->title }}</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('terms.index', ['type' => $taxonomyType->id ?? '', 'parent' => $parentTax->id ?? null]) }}">{{ $parentTax->title }}</a></li>
        @endif

        @if (strpos(Route::currentRouteName(), 'create') !== false)
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
        @endif
        @if (strpos(Route::currentRouteName(), 'edit') !== false)
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        @endif
    </ol>
</nav>