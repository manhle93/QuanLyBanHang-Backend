<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\LichSuDangNhap;
use App\LichSuHoatDong;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mews\Captcha\Facades\Captcha;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'loginMobile', 'register', 'verifyEmail', 'resendVerifyEmail', 'getTokenByCheckingCode', 'refreshCaptcha', 'checkUser', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $info = $request->all();
        $info['username'] = $info['email'];
        $info['password'] = Hash::make($info['password']);
        $info['role_id'] = 2;
        $info['email_token'] = substr(md5(mt_rand()), 0, 15);
        $user = User::create($info);

        if ($user) {
            Mail::to($user)->send(new VerifyEmail($user->email_token));
        }
        return response(['user_id' => $user->id], Response::HTTP_CREATED);
    }

    public function verifyEmail($email_token)
    {
        $user = User::where('email_token', $email_token)->first();
        if ($user) {
            if (!$user->active) {
                $user->update(['active' => true]);
                return view('auth.verify_email');
            } else abort(404);
        } else abort(404);
    }
    public function resendVerifyEmail(User $user)
    {
        Mail::to($user)->send(new VerifyEmail($user->email_token));
        return response('ok', Response::HTTP_OK);
    }


    public function checkUser(Request $request)
    {
        $username = $request->get('username');
        $user = User::where('username', $username)->first();
        $cap = false;
        if ($user && $user->so_lan_nhap_sai >= 5) {
            $cap = true;
        }
        return response()->json(['captcha' => $cap], 200);
    }

    public function login(Request $request)
    {
        $checkCaptcha = true;
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password'  => 'required',

        ]);

        $user = User::where('username', $request->get('username'))->first();
        if ($user && $user->so_lan_nhap_sai >= 5) {
            $checkCaptcha = Captcha::check_api($request->get('captcha'), $request->get('captchaKey'));
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password'  => 'required',
                'captcha' => 'required'

            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể đăng nhập'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        if (!$checkCaptcha) {
            return response()->json([
                'code' => 400,
                'message' => __('Captcha không chính xác'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        if ($user && $user->so_lan_nhap_sai == 6 && Carbon::parse($user->thoi_gian_bat_dau_khoa)->addMinutes(10)->isPast()) {
            User::where('id', $user->id)->update(['trang_thai_khoa' => false]);
            $user = User::where('username', $request->get('username'))->first();
        }
        if ($user && $user->so_lan_nhap_sai == 7 && Carbon::parse($user->thoi_gian_bat_dau_khoa)->addMinutes(30)->isPast()) {
            User::where('id', $user->id)->update(['trang_thai_khoa' => false]);
            $user = User::where('username', $request->get('username'))->first();
        }
        if ($user && $user->trang_thai_khoa && $user->so_lan_nhap_sai == 6) {
            $now = Carbon::now();
            $thoi_gian_mo_khoa = Carbon::parse($user->thoi_gian_bat_dau_khoa)->addMinutes(10);
            if ($thoi_gian_mo_khoa->diffInMinutes($now) > 1) {
                return response()->json([
                    'code' => 400,
                    'message' => __('Vui lòng đăng nhập lại sau ' . $thoi_gian_mo_khoa->diffInMinutes($now) . ' phút'),
                ], 400);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => __('Vui lòng đăng nhập lại sau ' . $thoi_gian_mo_khoa->diffInSeconds($now) . ' giây'),
                ], 400);
            }
        }
        if ($user && $user->trang_thai_khoa && $user->so_lan_nhap_sai == 7) {
            $now = Carbon::now();
            $thoi_gian_mo_khoa = Carbon::parse($user->thoi_gian_bat_dau_khoa)->addMinutes(30);

            if ($thoi_gian_mo_khoa->diffInMinutes($now) > 1) {
                return response()->json([
                    'code' => 400,
                    'message' => __('Vui lòng đăng nhập lại sau ' . $thoi_gian_mo_khoa->diffInMinutes($now) . ' phút'),
                ], 400);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => __('Vui lòng đăng nhập lại sau ' . $thoi_gian_mo_khoa->diffInSeconds($now) . ' giây'),
                ], 400);
            }
        }
        if ($user && $user->trang_thai_khoa && $user->so_lan_nhap_sai > 7) {
            return response()->json(['message' => 'Tài khoản ' . $user->username . ' đã bị khóa'], 401);
        }

        $credentials = ['username' => $request->username, 'password' => $request->password];
        if (!$token = auth()->attempt($credentials)) {
            if ($user) {
                $so_lan_nhap_sai = $user->so_lan_nhap_sai;
                User::where('username', $request->username)->update(['so_lan_nhap_sai' => $so_lan_nhap_sai + 1]);

                if ($so_lan_nhap_sai == 5) {
                    $now = Carbon::now();
                    User::where('username', $request->username)->update(['thoi_gian_bat_dau_khoa' => $now, 'trang_thai_khoa' => true]);
                    LichSuHoatDong::create([
                        'reference_id' => $user->id,
                        'type' => 'user',
                        'hanh_dong' => 'login_fail',
                        'user_id' => $user->id,
                        'noi_dung' => 'Tài khoản người dùng '.$user->name.' đã bị khóa trong 10 phút, do đăng nhập sai nhiều lần'
                    ]);
                    LichSuDangNhap::create([
                        'user_id' => $user->id,
                        'type' => 'login',
                        'thong_tin' => "Tài khoản bị khóa trong 10 phút, do đăng nhập sai nhiều lần"
                    ]);
                    return response()->json(['message' => 'Tài khoản ' . $user->username . ' đã bị khóa trong 10 phút'], 401);
                }
                if ($so_lan_nhap_sai == 6) {
                    $now = Carbon::now();
                    User::where('username', $request->username)->update(['thoi_gian_bat_dau_khoa' => $now, 'trang_thai_khoa' => true]);
                    LichSuHoatDong::create([
                        'reference_id' => $user->id,
                        'type' => 'user',
                        'hanh_dong' => 'login_fail',
                        'user_id' => $user->id,
                        'noi_dung' => 'Tài khoản người dùng '.$user->name.' đã bị khóa trong 30 phút do đăng nhập sai nhiều lần'
                    ]);
                    LichSuDangNhap::create([
                        'user_id' => $user->id,
                        'type' => 'login',
                        'thong_tin' => "Tài khoản bị khóa trong 30 phút do đăng nhập sai nhiều lần"
                    ]);
                    return response()->json(['message' => 'Tài khoản ' . $user->username . ' đã bị khóa trong 30 phút'], 401);
                }
                if ($so_lan_nhap_sai >= 7) {
                    $now = Carbon::now();
                    User::where('username', $request->username)->update(['thoi_gian_bat_dau_khoa' => $now, 'trang_thai_khoa' => true]);
                    LichSuHoatDong::create([
                        'reference_id' => $user->id,
                        'type' => 'user',
                        'hanh_dong' => 'login_fail',
                        'user_id' => $user->id,
                        'noi_dung' => 'Tài khoản người dùng '.$user->name.' đã bị khóa do đăng nhập sai quá nhiều lần'
                    ]);
                    LichSuDangNhap::create([
                        'user_id' => $user->id,
                        'type' => 'login',
                        'thong_tin' => "Tài khoản bị khóa do đăng nhập sai quá nhiều lần"
                    ]);
                    return response()->json(['message' => 'Tài khoản ' . $user->username . ' đã bị khóa'], 401);
                }
            }
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }

        if (auth()->user()->role_id == 3) {
            return response()->json(['message' => 'Quản lý tòa nhà không thể đăng nhập'], 401);
        }
        if (!auth()->user()->active) return response(['message' => 'Tài khoản chưa kích hoạt', 'user_id' => auth()->user()->id], Response::HTTP_NOT_ACCEPTABLE);

        $user = User::where('username', $request->username)->first();
        User::where('username', $request->username)->update(['so_lan_nhap_sai' => 0, 'thoi_gian_bat_dau_khoa' => null]);

        LichSuDangNhap::create([
            'user_id' => $user->id,
            'type' => 'login',
            'thong_tin' => $_SERVER['HTTP_USER_AGENT']
        ]);
        return $this->respondWithToken($token);
    }

    public function loginMobile(LoginRequest $request)
    {

        $credentials = ['username' => $request->username, 'password' => $request->password];
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }

        if (!auth()->user()->active) return response(['message' => 'Tài khoản chưa kích hoạt', 'user_id' => auth()->user()->id], Response::HTTP_NOT_ACCEPTABLE);

        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->toa_nha_id == null) {
            return response()->json(['message' => 'Ứng dụng chỉ dành cho người dùng và quản lý tòa nhà'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function refreshCaptcha()
    {
        $res = Captcha::create('default', true);

        return response()->json(['captcha' => $res]);
    }
    
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        return response(['data' => [
            'name' => $user->name,
            'roles' => [$user->role->code],
            'avatar' => $user->avatar_url,
        ]]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->user();
        auth()->logout();
        LichSuDangNhap::create([
            'user_id' => $user->id,
            'type' => 'logout',
            'thong_tin' => $_SERVER['HTTP_USER_AGENT']
        ]);
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function setup(Request $request)
    {
        $company = Company::create($request->all());
        auth()->user()->update(['company_id' => $company->id]);
        $company->errorConfigs()->createMany([
            [
                'error_id' => 1,
                'max_acceptable_value' => 5,
                'fine' => 0
            ],
            [
                'error_id' => 2,
                'max_acceptable_value' => 50,
                'fine' => 0
            ],
            [
                'error_id' => 3,
                'max_acceptable_value' => 0,
                'fine' => 0
            ],
            [
                'error_id' => 4,
                'max_acceptable_value' => 5,
                'fine' => 0
            ],
            [
                'error_id' => 5,
                'max_acceptable_value' => 50,
                'fine' => 0
            ],
        ]);
        return response('created', Response::HTTP_CREATED);
    }
    public function checkExistCompanyCode(Request $request)
    {
        return Company::where('code', $request->code)->first() != null ? response(['message' => 'Mã công ty này đã tồn tại'], 400) : 'ok';
    }
    public function getTokenByCheckingCode(Request $request)
    {
        $employee = Employee::where([['checking_code', $request->checking_code], ['company_id', $request->company_id]])->first();
        if (!$employee) return response(['message' => 'Mã chấm công không tồn tại'], Response::HTTP_BAD_REQUEST);
        return response(['access_token' => auth()->tokenById($employee->user->id)], 200);
    }
    public function getEmployeeInfo()
    {
        return auth()->user()->employee;
    }
    public function getLichSuDangNhap(Request $request)
    {
        $user = auth()->user();
        $per_page = $request->get('per_page');
        $date = $request->get('date');
        $user_id = $request->get('user_id');
        $page = $request->get('page');
        $query = LichSuDangNhap::query()->with('user', 'user.role', 'user.tinhThanh');
        $data = [];
        if (!$user) {
            return response('Chưa đăng nhập', 500);
        }
        if (isset($user_id)) {
            $query->where('user_id', $user_id);
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->tinh_thanh_id && $user->role_id == 2) {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('tinh_thanh_id', $user->tinh_thanh_id);
            });
            $query->orderBy('created_at', 'desc');
            $data = $query->paginate($per_page, ['*'], 'page', $page);
        }
        if ($user->role_id == 1) {
            $query->orderBy('created_at', 'desc')->get();
            $data = $query->paginate($per_page, ['*'], 'page', $page);
        }
        return response($data, 200);
    }

    public function getLichSuHoatDong(Request $request)
    {
        $user = auth()->user();
        $per_page = $request->get('per_page');
        $date = $request->get('date');
        $user_id = $request->get('user_id');
        $doi_tuong = $request->get('doi_tuong');
        $hanh_vi = $request->get('hanh_vi');
        $page = $request->get('page');
        $query = LichSuHoatDong::query()->with('user', 'user.role', 'user.tinhThanh');
        $data = [];
        if (!$user) {
            return response('Chưa đăng nhập', 500);
        }
        if (isset($user_id)) {
            $query->where('user_id', $user_id);
        }
        if (isset($doi_tuong)) {
            $query->where('type', $doi_tuong);
        }
        if (isset($hanh_vi)) {
            $query->where('hanh_dong', $hanh_vi);
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->tinh_thanh_id && $user->role_id == 2) {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('tinh_thanh_id', $user->tinh_thanh_id);
            });
            $query->orderBy('created_at', 'desc');
            $data = $query->paginate($per_page, ['*'], 'page', $page);
        }
        if ($user->role_id == 1) {
            $query->orderBy('created_at', 'desc')->get();
            $data = $query->paginate($per_page, ['*'], 'page', $page);
        }
        return response($data, 200);
    }
}
