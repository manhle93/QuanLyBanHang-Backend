@extends('auth.app')
@section('title','Xác nhận email')
@section('content')
<aside>
    <img src="{{  asset('images/auth/vector image 4.svg') }}" style="width:410px" alt="">
    <p>Xác nhận địa chỉ email của bạn!</p>
</aside>
<section class="email-confirmation">
    <form action="">
        <div class="form-title">
            Xác nhận email
        </div>
        <div class="form-description">Vui lòng kiểm tra email và click vào link để xác nhận tài khoản của bạn!
        </div>

        <div class="form-group">
            <button class="btn btn-action">Gửi lại email</button>
        </div>
        <div class="form-group">
            <button class="btn btn-social">
                <span>Liên hệ hỗ trợ</span>
            </button>
        </div>
    </form>
</section>
@endsection