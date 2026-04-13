<?php

namespace App\Services;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\userRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\VolunteerProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserService
{
    protected $userRepository;
    protected $emailRepository;

    public function __construct(userRepository $userRepository, EmailVerficationRepository $emailRepository)
    {
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
    }

    protected function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function register(UserRequest $request): array
    {
        $user = $this->userRepository->create($request->validated());
        $code = $this->generateVerificationCode();
        $this->emailRepository->deleteByEmail($request->email);
        $verification = $this->emailRepository->create($request->email, $code);
        // Mail::to($request->email)->send(new SendEmailVerification($code));
        return [
            'user' => $user,
            'verification' => $verification,
            'message' => 'Registration success. Please check your email to verify your account',
            'code' => 201
        ];
    }


//
//    public function register(UserRequest $request): array
//    {
//        // 1. إنشاء المستخدم
//        $user = $this->userRepository->create($request->validated());
//
//        // 2. إنشاء المتطوع + token
//        $volunteer = VolunteerProfile::create([
//            'user_id' => $user->id,
//            'qr_token' => 'VOL-' . Str::random(10),
//        ]);
//
//        // 3. توليد QR Code
//        $qr = QrCode::format('png')
//            ->size(300)
//            ->generate($volunteer->qr_token);
//
//        // 4. حفظ الصورة
//        $path = 'qrcodes/' . $volunteer->qr_token . '.png';
//        Storage::put('public/' . $path, $qr);
//
//        // 5. حفظ مسار الصورة
//        $volunteer->update([
//            'qr_code' => $path
//        ]);
//
//        // 6. كود التحقق (كما عندك)
//        $code = $this->generateVerificationCode();
//        $this->emailRepository->deleteByEmail($request->email);
//        $verification = $this->emailRepository->create($request->email, $code);
//
//        return [
//            'user' => $user,
//            'verification' => $verification,
//            'message' => 'Registration success. Please check your email to verify your account',
//            'code' => 201
//        ];
//    }
    public function Verify(EmailVerificationRequest $request){
        $emailverfication = $this->emailRepository->exists($request);
        if (!$emailverfication) {
            return response()->json(['message' => 'Invalid or expired Verification code'], 400);
        }
        $user = $this->userRepository->getByEmail($request->email);
        $user->email_verified_at = Carbon::now();
        $user->save();
       $emailverfication->delete();
        return [
            'user' => $emailverfication,
            'message' => 'تم تاكيد حسابك بنجاح يمكنك الان تسجيل الدخول',
            'code' => 201,
        ];

    }
    public function login(LoginRequest $request)
    {
        $user = $this->userRepository->getByEmail($request->email);
        if ($user)
            if (!$user || !Auth::attempt($request->only(['email', 'password']))) {
                return [
                    'user' => null,
                    'message' => 'Invalid credentials',
                    'code' => 401
                ];
            }
        if (is_null($user->email_verified_at)) {
            return [
                'user' => null,
                'message' => 'البريد الإلكتروني غير مُفعل. يرجى إتمام عملية التحقق.',
                'code' => 403
            ];
        }

        $user['token'] = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'message' => 'Login successful',
            'code' => 200
        ];
    }
    public function logout()
    {
        $user = Auth::user();
        if (!is_null($user)) {
            $user->delete();
            $message = 'user Logged out  Successfully';
            $code = 200;
        } else {
            $message = 'invaild token';
            $code = 404;
        }
        return (['user' => $user, 'message' => $message, 'code' => $code]);
    }


}
