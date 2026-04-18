<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CampaingRequest;
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
