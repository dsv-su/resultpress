@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Project Managers and Partners</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('projectadmin.index') }}"> Back</a>
            </div>
            <br>
            <p>This transfers all <strong>Roles and Permissions</strong> from one user to another.</p>
        </div>
    </div>
    @if (count($errors) > 0)
        <br>
        <div class="alert alert-danger">
            There are some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <!-- Multiple project owners -->
    <div class="form-row">
        <div class="col">
            <h4>{{$project->id}} {{ old('name', empty($project) ? '' : $project->name) }}</h4>
        </div>
    </div>
    <div class="form-row">
       <div class="form-group border p-2">
           <form action="{{ route('projectadmin.update', $project->id) }}" method="POST">
           @method('PATCH')
           @csrf
          <div class="form-row">
              <div class="col">
                  <label class="text-primary">Managers:</label>
                  <div class="col-md-12 py-2">
                  <select name="user_id[]" class="custom-select" id="managers" multiple="multiple">
                  @foreach($users as $user)
                     <option value="{{$user->id}}" {{ old('user_id') == $user->id || in_array($user->id, $old_users) ? 'selected':''}}>{{$user->name}}</option>
                  @endforeach
                  </select>
                  </div>
              </div>
          </div>
          <div class="form-row">
              <div class="col">
                  <label class="text-primary">Partners:</label>
                  <div class="col-md-12 py-2">
                   <select name="partner_id[]" class="custom-select" id="partners" multiple="multiple">
                   @foreach($users as $user)
                      <option value="{{$user->id}}" {{ old('partner_id') == $user->id || in_array($user->id, $partners) ? 'selected':''}}>{{$user->name}}</option>
                   @endforeach
                   </select>
                  </div>
              </div>
          </div>

              <div class="col-2 fa-pull-right">
                  <button type="button submit" class="btn btn-outline-primary">Submit</button>
              </div>

           </form>
       </div>
    </div>
<script>
    $('#managers').multiselect({
        templates: {
            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
        }
    });
    $('#partners').multiselect({
        templates: {
            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
        }
    });
</script>
@endsection
