<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VolunteerRequestController extends Controller
{
    public function store(Request $request)
    {
        // 1. التحقق من البيانات (Validation)
        $validatedData = $request->validate([
            'age' => 'requi 
            red|integer|min:15',
            'gender' => 'required|in:male,female',
            'current_address' => 'required|string|max:255',
            'cv' => 'required|file|mimes:pdf|max:2048',

            // الحقول الجديدة بناءً على تعديلات الـ enum
            'preferred_sector' => 'required|in:relief,educational,medical,administrative',
            'preferred_field' => 'required|in:food_distribution,psychological_support,teaching,data_entry,media_marketing,logistics,first_aid',
            'weekly_hours_capacity' => 'required|integer|min:1|max:168',
            'message_title' => 'nullable|string|max:255',
            'message_content' => 'nullable|string',
        ]);

        // 2. رفع ملف الـ CV
        $path = $request->file('cv')->store('volunteer_cvs', 'public');

        // 3. إنشاء الطلب
        $joinRequest = JoinRequest::create([
            'user_id' => auth()->id(),
            'age' => $validatedData['age'],
            'gender' => $validatedData['gender'],
            'current_address' => $validatedData['current_address'],
            'cv_path' => $path,
            'preferred_sector' => $validatedData['preferred_sector'],
            'preferred_field' => $validatedData['preferred_field'],
            'weekly_hours_capacity' => $validatedData['weekly_hours_capacity'],
            'message_title' => $validatedData['message_title'] ?? null,
            'message_content' => $validatedData['message_content'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Volunteer request submitted successfully!',
            'data' => array_merge($joinRequest->toArray(), [
                'cv_url' => asset('storage/' . $path)
            ])
        ], 201);
    }
}
