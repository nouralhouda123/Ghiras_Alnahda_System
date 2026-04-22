<?php


namespace App\Repositories;
use App\Models\JoinRequest;
use App\Models\VolunteerProfile;

class VolunteerRequestRepository
{
    

    // جلب طلب واحد بالتفصيل
    public function findById($id)
    {
        return JoinRequest::findOrFail($id);
    }

    // تحديث حالة الطلب
    public function updateStatus($id, $status)
    {
        $request = JoinRequest::findOrFail($id);
        $request->update(['status' => $status]);
        return $request;
    }

    // إنشاء بروفايل متطوع جديد
    public function createVolunteerProfile(array $data)
    {
        return VolunteerProfile::create($data);
    }




    public function getAllPending()
{
    // جلب الطلبات المعلقة مع بيانات المستخدم المرتبط بها
    return \App\Models\JoinRequest::with('user')
        ->where('status', 'pending')
        ->get();
}
}
