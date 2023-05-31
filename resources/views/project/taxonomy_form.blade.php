<div class="form-row mb-2 row">
    <label for="{{ $taxonomyType->slug }}" class="col-sm-3 col-form-label-sm">{{ $taxonomyType->name }}</label>
    <div class="col-sm-9 px-1">
        <select name="{{ $taxonomyType->slug }}[]" id="{{ $taxonomyType->slug }}" class="custom-select-sm form-control w-100"
            multiple="multiple"
            data-placeholder="Select {{ $taxonomyType->name }} / {{ Str::plural($taxonomyType->name, 2) }}"
        >
            {!! $taxonomyType->buildOptionsTree($taxonomyType->taxonomiesTree(), null, $taxonomies->pluck('id')->toArray()) !!}
        </select>
    </div>
</div>