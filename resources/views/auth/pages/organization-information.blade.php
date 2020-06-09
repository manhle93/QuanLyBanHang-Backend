@extends('auth.app')
@section('title','Thông tin tổ chức')
@section('header-link')
<div class="breadcrumb">Setup Account > <a href="">Chọn gói sử dụng</a> </div>
@endsection
@section('content')
<aside>
    <img src="{{ asset('images/auth/vector image 5.svg') }}" style="width:490px;margin-top:50px" alt="">
</aside>
<section class="organization-information">
    <form action="">
        <div class="form-title">
            Thông tin tổ chức
        </div>
        <div class="form-description">Nhập tên cơ quan, tổ chức bạn quản lý, nhấn nút Tiếp theo để chuyển sang
            phần chọn gói dịch vụ!
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Nhập tên cơ quan, tổ chức">
        </div>
        <div class="form-group">
            <button class="btn btn-action" type="submit">Tiếp theo</button>
        </div>
    </form>
</section>
@endsection