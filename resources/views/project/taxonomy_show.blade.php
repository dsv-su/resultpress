<div class="row my-1">
    <div class="col-sm col-md-3 font-weight-bold">{{ Str::plural($taxonomyType->name, $taxonomies->count()) }}</div>
    <div class="col-sm">
        {{ $taxonomies->implode('title', ', ') }}
    </div>
</div>