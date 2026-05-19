<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Http\Requests\StoreComplaintRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    /**
     * 1. GET /api/complaints/meta-data
     * إرجاع الأمثلة الديناميكية للحساسيات للفرونت اند ليعرضها في الواجهة فوراً.
     */
    public function metaData(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => Complaint::getSensitivityMetaData()
        ], 200);
    }

    /**
     * 2. GET /api/complaints
     * جلب الشكاوى المفلترة تلقائياً بناءً على دور (Role) المستخدم الحالي باستخدام الـ Scope الأمني.
     */
    public function index(): JsonResponse
    {
        // استخدام الـ Local Scope للفلترة الذكية
        $complaints = Complaint::withControlPermission()
            ->with('user:id,name,email') // جلب بيانات المشتكي الأساسية فقط إن وجدت
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $complaints
        ], 200);
    }

    public function store(StoreComplaintRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // قانون غراس: إذا اختار التقديم المجهول، نلغي الـ user_id تماماً
            $data['user_id'] = $request->input('is_anonymous', false) ? null : auth()->id();

            // التوجيه التلقائي: جلب الـ target_role المناسب للحساسية المحددة من الـ Meta Data
            $metaData = Complaint::getSensitivityMetaData();
            $data['assigned_role'] = $metaData[$request->sensitivity_level]['target_role'];

            // معالجة رفع الملف المرفق بأمان (Attachment Management)
            if ($request->hasFile('attachment')) {
                $data['attachment_path'] = $request->file('attachment')->store('complaints/attachments', 'public');
            }

            // حذف ملف الـ Object المرفق من مصفوفة الإدخال لكي لا يذهب للاستعلام
            unset($data['attachment']);

            // إنشاء الشكوى في قاعدة البيانات بأمان الآن
            $complaint = Complaint::create($data);

            /*
            |--------------------------------------------------------------------------
            | 🔥 التعديل المطلوب هنا: تحويل المسار إلى رابط كامل للفرونت اند
            |--------------------------------------------------------------------------
            */
            if ($complaint->attachment_path) {
                $complaint->attachment_path = asset('storage/' . $complaint->attachment_path);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Your complaint has been submitted successfully.',
                'data'    => $complaint
            ], 201);

        } catch (\Throwable $e) {
            // تسجيل الخطأ في الـ Logs لحماية النظام
            Log::error('Complaint Submission Failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
