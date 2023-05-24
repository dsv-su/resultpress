@extends('layouts.loginmaster')

@section('content')
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            @if ($message = Session::get('success'))
                <div class="alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            @endif
        </div>
    </div>
    @if (App\Settings::where('name', 'system-message-login')->first()->value)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ App\Settings::where('name', 'system-message-login')->first()->value }}
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>
    @endif
    <div class="row justify-content-end mx-4">
        <div class="float-lg-end">
            <form>
                <a href="{{route('login')}}"
                   role="button"
                   class="btn btn-outline-primary btn-lg btn-block e">SPIDER login
                </a>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto mt-5">
            <h1 id="main-h1">Welcome to ResultPress</h1>
            <p class="">
                Result Press is a project reporting tool. Here you as a Partner of
                SPIDER can:
            </p>
            <ul class="">
                <li>Report on Activites, Outputs and Outcomes.</li>
                <li>Keep track of deadlines</li>
                <li>Update progress</li>
                <li>Receive feedback from SPIDER</li>
            </ul>

            <a href="https://spidercenter.org/" target="_blank">
                <img src={{ asset('images/purple.png') }} alt="Spider" class="spider-logo" />
            </a>
        </div>
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <form method="POST" action="{{ route('partner-login') }}">
                        @csrf
                        <h5 class="card-title text-center">Partner Log in</h5>
                        <hr class="my-4" />

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

                        <hr class="my-4" />

                        <label>Or log in with a social network account:</label>
                        <div class="d-flex justify-content-center">
                            <a href="/partner-login/github" role="button" class="btn btn-outline-primary btn-lg btn-block text-uppercase"><i class="fab fa-github"></i> GitHub</a>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <a href="/partner-login/facebook" role="button" class="btn btn-outline-primary btn-lg btn-block text-uppercase"><i class="fab fa-facebook"></i> Facebook</a>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <a href="/partner-login/linkedin" role="button" class="btn btn-outline-primary btn-lg btn-block text-uppercase"><i class="fab fa-linkedin"></i> Linkedin</a>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <a href="/partner-login/google" role="button" class="btn btn-outline-primary btn-lg btn-block text-uppercase"><i class="fab fa-google"></i> Google</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
