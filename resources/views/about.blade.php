@extends('layouts.app')

@section('title', 'За мен')
@section('meta_description', 'Философията и подходът на психотерапевтичната практика MindBloom — цялостен, интегративен поглед към терапията.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">За мен</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>За мен</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row vr-size-1">
        <div class="col-lg-6">
          <img src="{{ asset('images/880x920_img1.jpg') }}" alt="MindBloom" class="w-100" />
        </div>
        <div class="col-lg-6">
          <h2 class="mad-title">Моят подход</h2>
          <p class="mad-text-medium content-element-4">
            Работя с убеждението, че истинската промяна идва от цялостен, интегративен поглед към човека — тялото,
            емоциите и историята, която носим, са свързани и си влияят взаимно. Затова съчетавам методи от различни
            терапевтични школи, вместо да се придържам към една единствена рамка.
          </p>
          <p class="mad-text-medium content-element-4">
            Болката и трудностите не са просто пречка, а посока — те показват къде е нужна работа, за да се стигне до
            корена на нещата, а не само до временно облекчение на симптомите. Всеки, който търси развитие, заслужава
            съпровождащ процес, а не единствено съвети.
          </p>
          <p class="mad-text-medium content-element-4">
            Психотерапията е доброволен и много по-безболезнен катализатор на растежа, който така или иначе би се
            случил в живота ви — само че с повече яснота и по-малко излишна болка по пътя.
          </p>
          <a href="{{ route('contact') }}" class="btn btn-big">Запишете безплатна консултация</a>
        </div>
      </div>
    </div>
  </div>
@endsection
