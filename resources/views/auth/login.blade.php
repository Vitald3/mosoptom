@extends('layouts.fullLayoutMaster')
@section('title','Вход в панель управления')
@section('page-styles')
<link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/authentication.min.css')}}">
@endsection

@section('content')
<section id="auth-login" class="row flexbox-container">
  <div class="col-xl-8 col-11">
    <div class="card bg-authentication mb-0">
      <div class="row m-0">
        <div class="col-md-6 col-12 px-0">
          <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
            <div class="card-header pb-1">
              <div class="card-title">
                <h4 class="text-center mb-2">Добро пожаловать</h4>
              </div>
            </div>
            <div class="card-content">
              <div class="card-body">
                <form method="POST" action="{{ url('admin/login') }}">
                  @csrf
                  <div class="form-group mb-50">
                    <label class="text-bold-600" for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" autofocus placeholder="Email">
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label class="text-bold-600" for="password">Пароль</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password" placeholder="Пароль">
                    @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                     @enderror
                  </div>
                  <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                    <div class="text-right">
                      <a href="{{ url('admin/forgot-password') }}" class="card-link"><small>Забыли пароль?</small></a>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary glow w-100 position-relative">Войти
                    <i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
          <div class="card-content">
            <img class="img-fluid" src="{{asset('assets/admin/img/login.png')}}" alt="branding logo">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
