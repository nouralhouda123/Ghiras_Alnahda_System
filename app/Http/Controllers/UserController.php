<?php

namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    // 1. تابع عرض بيانات البروفايل
    public function profile()
    {
        $user = auth()->user();
        $imageUrl = $user->image ? asset('storage/' . $user->image) : null;
        $responseData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_image_url' => $imageUrl,
            'created_at' => $user->created_at,
        ];
        return ResponseHelper::Success($responseData, 'Profile data retrieved successfully', 200);
    }

// 2. تابع تحديث بيانات البروفايل
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // التحقق من الصورة
        ]);
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->hasFile('image')) {
            if ($user->getRawOriginal('image')) {
                Storage::disk('public')->delete($user->getRawOriginal('image'));
            }
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }
        $user->save();
        if ($user->image) {
            $user->image = asset('storage/' . $user->image);
        }
        return ResponseHelper::Success($user, 'تم تحديث البروفايل بنجاح', 200);
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
//اضافة User
    public function addUser(addUserRequest $request ){
        $data=$this->userService->createUser($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}
    public function getRoleNames()
    {
        $roles = Role::pluck('name');
        return ResponseHelper::Success($roles, 'Roles fetched successfully', 200);
    }













}
