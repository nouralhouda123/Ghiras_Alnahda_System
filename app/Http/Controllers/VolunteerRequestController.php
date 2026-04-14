<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class VolunteerRequestController extends Controller
{
//    public function store(Request $request)
//    {
//        // 1. التحقق من البيانات (Validation)
//        $validator = Validator::make($request->all(), [
//            'age' => 'required|integer|min:15',
//            'gender' => 'required|in:male,female',
//            'current_address' => 'required|string|max:255',
//            'cv' => 'required|file|mimes:pdf|max:2048', // الـ CV ملف PDF
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->errors()], 422);
//        }
//
//        // 2. رفع ملف الـ CV وتخزين مساره
//        $path = $request->file('cv')->store('volunteer_cvs', 'public');
//
//        // 3. إنشاء الطلب بربطه مع المستخدم المسجل (عن طريق الـ Token)
//        $joinRequest = JoinRequest::create([
//            'user_id' => auth()->id(),
//            'age' => $request->age,
//            'gender' => $request->gender,
//            'current_address' => $request->current_address,
//            'cv_path' => $path,
//            'status' => 'pending',
//        ]);
//
//        return response()->json([
//            'message' => 'Volunteer request submitted successfully!',
//            'data' => $joinRequest
//        ], 201);
//    }
    public function store(Request $request)
    {
        // سيقوم لارافل بالتحقق وإرجاع الأخطاء تلقائياً كـ JSON إذا فشل الـ Validation
        $validatedData = $request->validate([
            'age' => 'required|integer|min:15',
            'gender' => 'required|in:male,female',
            'current_address' => 'required|string|max:255',
            'cv' => 'required|file|mimes:pdf|max:2048',
        ]);

        // رفع الملف
        $path = $request->file('cv')->store('volunteer_cvs', 'public');

        // إنشاء الطلب
        $joinRequest = JoinRequest::create([
            'user_id' => auth()->id(),
            'age' => $validatedData['age'],
            'gender' => $validatedData['gender'],
            'current_address' => $validatedData['current_address'],
            'cv_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Volunteer request submitted successfully!',
            'data' => array_merge($joinRequest->toArray(), [
                'cv_path' => asset('storage/' . $joinRequest->cv_path)
            ])
        ], 201);
    }
}
