<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // --- تسجيل الدخول والتحقق (جاهزة كما هي) ---
    public function register(UserRequest $request) {
        $data = $this->userService->register($request);
        return ($data['code'] === 200) 
            ? ResponseHelper::Success($data['user'], $data['message'], 200) 
            : ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }

    public function verify(EmailVerificationRequest $request) {
        $data = $this->userService->Verify($request);
        return ($data['code'] === 200) 
            ? ResponseHelper::Success($data['user'], $data['message'], 200) 
            : ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }

    public function login(LoginRequest $request) {
        $data = $this->userService->login($request);
        return ($data['code'] === 200) 
            ? ResponseHelper::Success($data['user'], $data['message'], 200) 
            : ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }

    public function logout() {
        try {
            $data = $this->userService->logout();
            return ResponseHelper::Success([], $data['message'], 200);
        } catch (\Exception $e) {
            return ResponseHelper::Error(null, "Unexpected error: " . $e->getMessage(), 500);
        }
    }

    // --- الدوال التي كانت خارج القوس ---

    public function profile() {
        $user = auth()->user();
        $responseData = [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'profile_image_url' => $user->image ? asset('storage/' . $user->image) : null,
            'created_at'        => $user->created_at,
        ];
        return ResponseHelper::Success($responseData, 'Profile data retrieved successfully', 200);
    }

    public function updateProfile(Request $request) {
        $user = auth()->user();

        $request->validate([
            'name'  => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->has('name'))  $user->name = $request->name;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email')) $user->email = $request->email;

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }

        $user->save();

        // إرجاع الرابط الكامل في الرد
        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return ResponseHelper::Success($user, 'Profile updated successfully', 200);
    }

    public function card() {
        $user = auth()->user();

        // تأكد من وجود بروفايل متطوع أولاً لتجنب الـ Error
        $qrCode = ($user->volunteerProfile) 
                  ? asset('storage/' . $user->volunteerProfile->qr_code) 
                  : null;

        return ResponseHelper::Success([
            'fullName' => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'qr_code'  => $qrCode,
        ], 'User card data', 200);
    }
} // نهاية الكلاس الصحيحة