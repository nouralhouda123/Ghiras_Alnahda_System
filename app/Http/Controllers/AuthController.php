<?php

namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        $data = $this->userService->register($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}


    public function verify(EmailVerificationRequest $request)
    {
        $data = $this->userService->Verify($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}

    public function login(LoginRequest $request )
    {
        $data = $this->userService->login($request);

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}

///logout
    public function logout()
    {
        $data = [];
        try {
            $data = $this->userService->logout();
            if ($data['code'] === 200) {
                return ResponseHelper::Success([], $data['message'], $data['code']);
            } else {
                return ResponseHelper::Error([], $data['message'], $data['code']);
            }
        } catch (\Exception $e) {
            return ResponseHelper::Error(null, "Unexpected error: " . $e->getMessage(), 500);
        }


    }
///////
    // 1. تابع عرض بيانات البروفايل
    public function profile()
    {
        $user = auth()->user();

        // بناء الرابط الكامل للصورة: إذا كانت موجودة نضع الرابط، وإذا لا نضع null
        $imageUrl = $user->image ? asset('storage/' . $user->image) : null;

        // تحديد البيانات التي تريدين إرجاعها فقط ليكون الرد نظيفاً
        $responseData = [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'profile_image_url' => $imageUrl, // الرابط الكامل هنا
            'created_at'        => $user->created_at,
        ];

        return ResponseHelper::Success($responseData, 'Profile data retrieved successfully', 200);
    }

// 2. تابع تحديث بيانات البروفايل
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // التحقق من البيانات القادمة من Postman
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // التحقق من الصورة
        ]);

        // تحديث البيانات النصية
        if ($request->has('name'))  $user->name = $request->name;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email')) $user->email = $request->email;

        // التعامل مع رفع الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة (اختياري لتوفير المساحة)
            if ($user->getRawOriginal('image')) {
                Storage::disk('public')->delete($user->getRawOriginal('image'));
            }

            // تخزين الصورة الجديدة في مجلد profile_images داخل storage/app/public
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }

        $user->save();

        // إضافة الرابط الكامل للصورة قبل إرسال الرد
        if ($user->image) {
            $user->image = asset('storage/' . $user->image);
        }

        return ResponseHelper::Success($user, 'profile apdted successfuly', 200);
    }

    public function card()
    {
        $user = auth()->user();

        return ResponseHelper::Success([
            'fullName' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'qr_code' => asset('storage/' . $user->volunteerProfile->qr_code),
        ], 'User profile data', 200);
    }
}

