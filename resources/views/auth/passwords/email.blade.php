@extends('layouts.fullLayoutMaster')
@section('title','Восстановить пароль')

@section('page-styles')
  <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/authentication.min.css')}}">
@endsection

@section('content')
<section class="row flexbox-container">
  <div class="col-xl-7 col-md-9 col-10  px-0">
    <div class="card bg-authentication mb-0">
      <div class="row m-0">
        <div class="col-md-6 col-12 px-0">
          <div class="card disable-rounded-right mb-0 p-2">
            <div class="card-header pb-1">
              <div class="card-title">
                <h4 class="text-center mb-2">Восстановить пароль?</h4>
              </div>
            </div>
            <div class="card-content">
              <div class="card-body">
                @if (\Session::has('success'))
                  <div class="text-muted text-center mb-2" style="background: green;color: #fff !important;border-radius: 4px">
                    <small>{!! \Session::get('success') !!}</small>
                  </div>
                @else
                  <div class="text-muted text-center mb-2">
                    <small>Введите адрес электронной почты, который вы использовали при регистрации, и мы вышлем вам временный пароль</small>
                  </div>
                @endif
                <form class="mb-2" method="POST" action="{{ url('admin/forgot-password') }}">
                  @csrf
                  <div class="form-group mb-2">
                    <label class="text-bold-600" for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email">
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <button type="submit" class="btn btn-primary glow position-relative w-100">Отправить пароль
                    <i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
                  </button>
                </form>

                <div class="text-center mb-2">
                  <a href="{{url('admin/login')}}">
                    <small class="text-muted">Войти</small>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 d-md-block d-none text-center align-self-center">
          <img class="img-fluid" src="{{asset('assets/admin/img/forgot-password.png')}}" alt="branding logo" width="300">
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
