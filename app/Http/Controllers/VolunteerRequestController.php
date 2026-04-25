<?php

namespace App\Http\Controllers;
use App\Http\Requests\VolunteerStoreRequest;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\VolunteerRequestService;
class VolunteerRequestController extends Controller
{protected $service;

    public function __construct(VolunteerRequestService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $requests = $this->service->getPendingRequests();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }
    public function show($id)
    {
        $request = $this->service->getRequestDetails($id);
        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

   public function store(VolunteerStoreRequest $request)
{
    $path = $request->file('cv')->store('volunteer_cvs', 'public');

    $data = $request->validated();

    unset($data['cv']);

    $data['user_id'] = auth()->id();
    $data['cv_path'] = $path;
    $data['status']  = 'pending';

    $joinRequest = \App\Models\JoinRequest::create($data);

    return response()->json([
        'message' => 'Volunteer request submitted successfully!',
        'cv_url' => asset('storage/' . $path),
        'data' => $joinRequest
    ], 201);
}
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
