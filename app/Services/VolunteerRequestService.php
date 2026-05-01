<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\userRepository;
use App\Repositories\VolunteerRequestRepository;
use Illuminate\Support\Facades\DB;

class VolunteerRequestService
{
    protected $repository;
    protected $userRepository;

    public function __construct(VolunteerRequestRepository $repository,userRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;

    }

    /**
     * جلب كافة الطلبات مع الاسم والايميل ورابط الـ CV الكامل
     */
    public function getPendingRequests()
    {
        $requests = $this->repository->getAllPending();

        return $requests->map(function ($request) {
            return [
                'id'                    => $request->id,
                'user_name'             => $request->user->name ?? 'N/A', // جلب الاسم من العلاقة
                'user_email'            => $request->user->email ?? 'N/A', // جلب الإيميل من العلاقة
                'age'                   => $request->age,
                'gender'                => $request->gender,
                'current_address'       => $request->current_address,
                'cv_url'                => asset('storage/' . $request->cv_path), // الرابط الكامل هنا
                'preferred_sector'      => $request->preferred_sector,
                'preferred_field'       => $request->preferred_field,
                'weekly_hours_capacity' => $request->weekly_hours_capacity,
                'message_title'         => $request->message_title,
                'message_content'       => $request->message_content,
                'status'                => $request->status,
                'created_at'            => $request->created_at,
            ];
        });
    }

    /**
     * جلب تفاصيل طلب واحد بشكل منظم
     */
    public function getRequestDetails($id)
    {
        $request = $this->repository->findById($id);

        if (!$request) return null;

        // إضافة الحقول المطلوبة للكائن قبل إرجاعه
        $request->cv_url = asset('storage/' . $request->cv_path);
        $request->user_name = $request->user->name ?? 'N/A';
        $request->user_email = $request->user->email ?? 'N/A';

        return $request;
    }
    public function processStatus($id, $status)
    {
        return DB::transaction(function () use ($id, $status) {
            $joinRequest = $this->repository->updateStatus($id, $status);
            if ($status === 'approved') {
                $this->repository->createVolunteerProfile([
                    'user_id'               => $joinRequest->user_id,
                    'age'                   => $joinRequest->age,
                    'gender'                => $joinRequest->gender,
                    'current_address'       => $joinRequest->current_address,
                    'cv_path'               => $joinRequest->cv_path,
                    'preferred_sector'      => $joinRequest->preferred_sector,
                    'preferred_field'       => $joinRequest->preferred_field,
                    'weekly_hours_capacity' => $joinRequest->weekly_hours_capacity,
                ]);
            }
$user=$this->userRepository->getById($joinRequest->user_id);
            $user->assignRole('Volunteer');
            return $joinRequest;
        });
    }
}
