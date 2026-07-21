@extends('layouts.fullLayoutMaster')
{{-- page title --}}
@section('title','Доступ запрещен!')

@section('content')
<!-- not authorized start -->
<section class="row flexbox-container">
  <div class="col-xl-7 col-md-8 col-12">
    <div class="card bg-transparent shadow-none">
      <div class="card-content">
        <div class="card-body text-center">
          <img src="{{asset('images/pages/not-authorized.png')}}" class="img-fluid" alt="not authorized" width="400">
          <h1 class="my-2 error-title">Доступ запрещен!</h1>
          <p>
              Доступ к данному разделу запрещен администратором.
          </p>
          <a href="{{asset('/admin')}}" class="btn btn-primary round glow mt-2">Продолжить</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- not authorized end -->
@endsection