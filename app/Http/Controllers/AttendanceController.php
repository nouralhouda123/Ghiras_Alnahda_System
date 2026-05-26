<?php


namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\CampaingRequest;
use App\Services\AttendanceService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class AttendanceController
{
    protected $attendanceService;
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    public function leaderCheckIn(AttendanceRequest $request,$id)
    {
        $data=$this->attendanceService->leaderCheckIn($request,$id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function leaderCheckOut($id)
    {
        $data=$this->attendanceService->leaderCheckOut($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//عرض حضور مستخدم
    public function volunteerAttendances()
    {
        $data=$this->attendanceService->index(Auth::user());
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//عرض حضور حملة
    public function campaignAttendances($Campanig_id)
    {
        $data=$this->attendanceService->show($Campanig_id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }

// داخل كلاس AttendanceController أضيفي هذا التابع:
public function scanVolunteerQr(ScanQrAttendanceRequest $request)
{
    // تأكيد أمني: التحقق من صلاحية قائد الفريق عبر الـ Seeder الخاص بكِ
    if (!auth()->user()->hasPermissionTo('scan.volunteer.qr')) {
        return ResponseHelper::Error(null, 'Unauthorized! Only Team Leaders can scan attendance QR codes.', 403);
    }

    $data = $this->attendanceService->scanVolunteerQr($request);

    if ($data['code'] === 200) {
        return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
    } else {
        return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
    }
}

}
