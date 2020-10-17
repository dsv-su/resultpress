@extends('layouts.loginmaster')

@section('content')
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <form method="POST" action="{{ route('partner-login') }}">
                        @csrf
                    <h5 class="card-title text-center">Partner Sign In</h5>
                    <form class="form-signin">
                        <div class="form-label-group">
                            <input type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                            <label for="inputEmail">Email address</label>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-label-group">
                            <input type="password" id="inputPassword" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required>
                            <label for="inputPassword">Password</label>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" name="remember" id="customCheck1" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customCheck1"> {{ __('Remember Me') }}</label>
                        </div>
                        <div class="d-flex justify-content-center">
                        <button class="btn btn-outline-primary btn-lg btn-block text-uppercase" type="button submit">{{ __('Login') }}</button>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                        <hr class="my-4">
                        <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-lg btn-block text-uppercase" type="submit"> TODO</button>
                        </div>
                        <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-lg btn-block text-uppercase" type="submit"> TODO</button>
                        </div>
                    </form>
                        <hr class="my-4">
                        <div class="d-flex justify-content-center">
                            <a href="{{route('login')}}" role="button" class="btn btn-outline-primary btn-lg btn-block text-uppercase" > SPIDER Sign In</a>
                        </div>
                </div>
            </div>
        </div>
    </div>


@endsection
