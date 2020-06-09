@extends('auth.app')
@section('title','Dịch vụ')
@section('content')
@section('header-link')
<div class="breadcrumb"><a href="">Setup Account</a> > Chọn gói sử dụng </div>
@endsection
<div class="service">
    <div class="service-img">
        <img src="{{ asset('images/auth/vector image 6.svg') }} " alt="">
    </div>
    <div class="service-name">Personal</div>
    <p class="service-description">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
        repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima</p>
    <div class="service-price">$19.00</div>
    <button class="btn btn-action">Choose plan</button>
</div>
<div class="service">
    <div class="service-img">
        <img src="{{  asset('images/auth/vector image 7.svg') }}" alt="">
    </div>
    <div class="service-name">Team</div>
    <p class="service-description">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
        repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima</p>
    <div class="service-price">$99.00</div>
    <button class="btn btn-action">Choose plan</button>
</div>
<div class="service">
    <div class="service-img">
        <img src="{{ asset('images/auth/vector image 8.svg') }}" alt="">
    </div>
    <div class="service-name">Corporate</div>
    <p class="service-description">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
        repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima</p>
    <div class="service-price">$199.00</div>
    <button class="btn btn-action">Choose plan</button>
</div>
@endsection