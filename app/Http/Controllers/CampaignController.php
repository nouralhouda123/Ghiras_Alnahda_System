<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\CampaingRequest;
use App\Http\Requests\SearchCampaignRequest;
use App\Models\Campaign;
use App\Services\CampaignService;
use App\Services\UserService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CampaingRequest $request)
    {
      $data=$this->campaignService->create($request);
      if($data['code']===200){
          return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
      } else {
          return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
      }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $data=$this->campaignService->show();
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }

    public function indexDetail($id)
    {
        $data=$this->campaignService->indexDetail($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//بحث عن حملة
    public function SearchCampaign(SearchCampaignRequest  $request)
    {
        $data=$this->campaignService->SearchCampaign($request);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function assignCampaignLeader($campaignId, $userId)
    {
        $data=$this->campaignService->assignTeamLeader($campaignId, $userId);
        if($data['code']===200){
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
    public function update(Request $request, string $id)
    {
    }
    public function destroy(string $id)
    {
    }
}
