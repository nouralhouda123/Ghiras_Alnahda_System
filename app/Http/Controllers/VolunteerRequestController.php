<?php

namespace App\Http\Controllers;
use App\Http\Requests\VolunteerStoreRequest;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\VolunteerRequestService;
class VolunteerRequestController extends Controller
{protected $service;

    // حقن الخدمة في الكنترولر
    public function __construct(VolunteerRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * 1. عرض جميع الطلبات المعلقة (للمسؤول)
     */
    public function index()
    {
        $requests = $this->service->getPendingRequests();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * 2. عرض تفاصيل طلب واحد
     */
    public function show($id)
    {
        $request = $this->service->getRequestDetails($id);
        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    /**
     * 3. تخزين طلب تطوع جديد (للمستخدم)
     */
   public function store(VolunteerStoreRequest $request)
{
    // 1. ارفعي ملف الـ CV وخزني المسار في متغير
    $path = $request->file('cv')->store('volunteer_cvs', 'public');

    // 2. خذي البيانات التي تم التحقق منها (age, gender, etc.)
    $data = $request->validated();

    // 3. الخطوة السحرية: احذفي 'cv' من المصفوفة لأننا لا نريد تخزين "الملف" بل "المسار"
    unset($data['cv']);

    // 4. أضيفي الحقول التي يحتاجها الجدول يدوياً
    $data['user_id'] = auth()->id(); // معرف المستخدم الحالي
    $data['cv_path'] = $path;        // المسار الذي حصلنا عليه في الخطوة 1
    $data['status']  = 'pending';    // الحالة الافتراضية

    // 5. الآن احفظي البيانات في قاعدة البيانات
    $joinRequest = \App\Models\JoinRequest::create($data); 

    return response()->json([
        'message' => 'Volunteer request submitted successfully!',
        'cv_url' => asset('storage/' . $path),
        'data' => $joinRequest
    ], 201);
}

    /**
     * 4. قبول أو رفض الطلب (للمسؤول)
     */
    public function updateStatus(Request $request, $id)
    {
        // نتحقق من أن الحالة المرسلة إما approved أو rejected
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $result = $this->service->processStatus($id, $request->status);

        return response()->json([
            'message' => "Request status updated to {$request->status} successfully.",
            'data' => $result
        ]);
    }
}
