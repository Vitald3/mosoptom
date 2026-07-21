@extends('layouts.fullLayoutMaster')
@section('title','Регистрация')
@section('page-styles')
  <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/authentication.min.css')}}">
@endsection

@section('content')
<section class="row flexbox-container">
  <div class="col-xl-8 col-10">
    <div class="card bg-authentication mb-0">
      <div class="row m-0">
        <div class="col-md-6 col-12 px-0">
          <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
            <div class="card-header pb-1">
              <div class="card-title">
                <h4 class="text-center mb-2">Регистрация</h4>
              </div>
            </div>
            <div class="text-center">
              <p> <small> Пожалуйста, введите свои данные, чтобы зарегистрироваться</small>
              </p>
            </div>
            <div class="card-content">
              <div class="card-body">
                <form method="POST" action="{{ url('admin/register') }}?code={{ $code }}">
                  @csrf
                  <div class="form-group mb-50">
                    <label class="text-bold-600" for="name">Имя</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Имя">
                    @error('name')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-50">
                    <label class="text-bold-600" for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-2">
                    <label class="text-bold-600" for="password">Пароль</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Пароль">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                  <div class="form-group mb-2">
                    <label class="text-bold-600" for="password-confirm">Повторите пароль</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Повторите пароль">
                  </div>
                  <button type="submit" class="btn btn-primary glow position-relative w-100">Регистрировать<i
                    id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                </form>
                <hr>
                <div class="text-center"><small class="mr-25">Уже есть аккаунт?</small>
                  <a href="{{url('admin/login')}}"><small>Войти</small> </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
            <img class="img-fluid" src="{{asset('assets/admin/img/register.png')}}" alt="branding logo">
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
