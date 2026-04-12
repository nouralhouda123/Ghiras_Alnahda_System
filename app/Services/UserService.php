<?php

namespace App\Services;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Mail\EmailVerificationMail;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\userRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($user->email)->send(new EmailVerificationMail($code));
        return [
            'user' => $user,
            'verification' => $verification,
            'message' => 'Registration success. Please check your email to verify your account',
            'code' => 201
        ];
    }

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
