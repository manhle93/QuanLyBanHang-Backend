@extends('auth.app')
@section('title','Đăng ký')
@section('content')
<aside>
    <img src="{{ asset('images/auth/vector image 1.svg') }}" alt="">
    <p>Linh hoạt trên mọi trường hợp chấm công!</p>
</aside>
<section class="register">
    <form method="POST" action="/register">
        @csrf
        <div class="form-title">
            Đăng ký
        </div>
        <div class="form-description">Miễn phí trải nghiệm 05 ngày cho việc quản lý nhân sự trở nên dễ dàng hơn
        </div>
        <div class="form-group">
            <label for="">Email <span class="required-field">(*)</span></label>
            <input type="email" name="email" class="form-control" placeholder="Nhập Email của bạn">
        </div>
        <div class="form-group">
            <label for="">
                Mật khẩu <span class="required-field">(*)</span>
            </label>
            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
        </div>
        <div class="form-group">
            <label for="">Họ và tên</label>
            <input type="text" name="name" class="form-control" placeholder="Nhập Email của bạn">
        </div>
        <div class="form-group">
            <label for="">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" placeholder="Nhập Email của bạn">
        </div>
        <div class="form-group">
            <p>Bằng việc nhấn vào nút <b>Tạo tài khoản </b>dưới đây, bạn đã đồng ý các chính sách và điều
                khoản sử dụng hệ thống của chúng tôi.</p>
        </div>
        <div class="form-group">
            <button class="btn btn-action" type="submit">Tạo tài khoản</button>
        </div>
        <div class="divider">
            <hr>
            <span>Hoặc</span>
        </div>
        <div class="form-group">
            <button class="btn btn-social btn-fb">
                <img src="./images/icon-fb.png" alt="">
                <span>Đăng kí bằng tài khoản Facebook</span>
            </button>
        </div>
        <div class="form-group">
            <button class="btn btn-social btn-gg">
                <img src="./images/icon-gg.png" alt="">
                <span>Đăng kí bằng tài khoản Google</span>
            </button>
        </div>
        <div class="register-link">Bạn không có tài khoản? <a href="">Đăng kí tài khoản</a></div>
    </form>
</section>
@endsection