@extends('auth.app')
@section('title','Đăng nhập')
@section('content')
<aside>
    <img src="{{ asset('images/auth/vector image 1.svg') }}" alt="">
    <p>Linh hoạt trên mọi trường hợp chấm công!</p>
</aside>
<section class=" login">
    <form action="">
        <div class="form-title">
            Đăng nhập
        </div>
        <div class="form-description">Chúc mừng! Vui lòng đăng nhập để sử dụng dịch vụ</div>
        <div class="form-group">
            <label for="">Email</label>
            <input type="email" class="form-control" placeholder="Nhập Email của bạn">
        </div>
        <div class="form-group">
            <label for="">
                <div>Mật khẩu <a href="">Quên mật khẩu?</a></div>
            </label>
            <input type="email" class="form-control" placeholder="Nhập mật khẩu">
        </div>
        <div class="form-group">
            <button class="btn btn-action" type="submit">Đăng nhập</button>
        </div>
        <div class="divider">
            <hr>
            <span>Hoặc</span>
        </div>
        <div class="form-group">
            <button class="btn btn-social btn-fb">
                <img src="{{  asset('images/auth/icon-fb.png')}}" alt="">
                <span>Đăng kí bằng tài khoản Facebook</span>
            </button>
        </div>
        <div class=" form-group">
            <button class="btn btn-social btn-gg">
                <img src="{{  asset('images/auth/icon-gg.png')}}" alt="">
                <span>Đăng kí bằng tài khoản Google</span>
            </button>
        </div>
        <div class="register-link">Bạn không có tài khoản? <a href="">Đăng kí tài khoản</a></div>
    </form>
</section>
</aside>
@endsection