@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Setting</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('settings.index') }}"> Back</a>
            </div>
        </div>
    </div>

    <form action="{{ route('settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <input type="hidden" name="id" value="{{ $setting->id }}">
        <div class="row mt-5">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control" name="name" type="text" placeholder="Name" value="{{ old('name', empty($setting) ? '' : $setting->name) }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Type: {{ $setting->type }}</strong>
                    <select name="type" id="type" class="form-control">
                        @foreach ( $fieldTypes as $fieldTypeKey => $fieldType )
                            <option value="{{ $fieldTypeKey }}" {{ old('type', empty($setting) ? '' : $setting->type) == $fieldTypeKey ? 'selected' : '' }}>{{ $fieldType }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    {{-- <strong>Value:</strong> --}}
                    @switch($setting->type)
                        @case('text')
                            <input class="form-control" name="value" type="text" placeholder="Value" value="{{ old('value', empty($setting) ? '' : $setting->value) }}">
                            @break
                        @case('textarea')
                        @case('wysiwyg')
                        @case('json')
                        @case('html')
                        @case('code')
                            <textarea class="form-control" name="value" placeholder="Value" rows="7">{{ old('value', empty($setting) ? '' : $setting->value) }}</textarea>
                            @break
                        @case('file')
                        @case('image')
                            <span>Current file: <a href="{{ asset('storage/' . $setting->value) }}" target="_blank">{{ $setting->value }}</a></span>
                            <input type="file" name="value" id="value" class="form-control">
                            @break
                        @case('date')
                            <input class="form-control datepicker" name="value" type="text" placeholder="Value" value="{{ old('value', empty($setting) ? '' : $setting->value) }}">
                            @break
                        @case('datetime')
                            <input class="form-control datetimepicker" name="value" type="text" placeholder="Value" value="{{ old('value', empty($setting) ? '' : $setting->value) }}">
                            @break
                        @case('time')
                            <input class="form-control timepicker" name="value" type="text" placeholder="Value" value="{{ old('value', empty($setting) ? '' : $setting->value) }}">
                            @break
                            
                        @break
                    
                        @default
                            <input class="form-control" name="value" type="text" placeholder="Value" value="{{ old('value', empty($setting) ? '' : $setting->value) }}">
                            
                    @endswitch
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $('#type').change(function() {
            $('button[type="submit"]').click();
        });
    </script>
    @if ($setting->type == 'wysiwyg')
        <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace( 'value', {
                height: '600px'
            });
        </script>
    @endif
@endsection

