<?php
namespace App\Services;
use App\Helpers\StorageHelper;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\CampaingRequest;
use App\Http\Requests\SearchCampaignRequest;
use App\Http\Resources\CampaignDetailsResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\UserResource;
use App\Repositories\CampaingRepository;
use App\Repositories\userRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    public function __construct(CampaingRepository $CampaingRepository,userRepository $userRepository)
    {
        $this->CampaingRepository = $CampaingRepository;
        $this->userRepository=$userRepository;
    }
    public function create(CampaingRequest $request)
    {
        $storedFiles = [];
        try {
            return DB::transaction(function () use ($request, &$storedFiles) {
                $data = $request->validated();
                if ($request->hasFile('image')) {
                    $images = [];
                    foreach ($request->file('image') as $image) {
                        $path = StorageHelper::storeFile($image, 'campaigns/images');
                        $images[] = $path;
                        $storedFiles[] = $path;
                    }
                    $data['image'] = json_encode($images);
                }
                if ($request->hasFile('video')) {
                    $videos = [];
                    foreach ($request->file('video') as $video) {
                        $path = StorageHelper::storeFile($video, 'campaigns/videos');
                        $videos[] = $path;
                        $storedFiles[] = $path;
                    }
                    $data['video'] = json_encode($videos);
                }

                $campaign = $this->CampaingRepository->createCampaing($data);
                if ($request->has_evaluation) {
                    foreach ($request->kpis as $kpi) {
                        $this->CampaingRepository->createCampaing_Kpi([
                            'name' => $kpi['name'],
                            'target_value' => $kpi['target_value'],
                            'unit' => $kpi['unit'],
                            'campaign_id' => $campaign->id,
                        ]);
                    }
                }

                return [
                    'user' => $this->CampaingRepository->indexWithRelation($campaign->id),
                    'message' => 'success',
                    'code' => 201
                ];
            });

        } catch (\Throwable $e) {

            foreach ($storedFiles as $file) {
                StorageHelper::deleteFile($file);
            }

            Log::error('Campaign Creation Failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'message' => 'error',
                'error' => $e->getMessage(),
                'code' => 500
            ];
        }
    }
    public function show()
    {
        //ضبط صلاحيات والادوار
        //تزبيط رسائل
        $campanig=$this->CampaingRepository->index();
        return (['user'=>  CampaignResource::collection($campanig),
            'message' => 'Campaigns retrieved successfully',
            'code' =>200
        ]) ;

    }
    //ضبط صلاحيات والادوار
    public function indexDetail($id)
    {
        $campanig = $this->CampaingRepository->indexDetail($id);
        if ($campanig) {
            return [
                'user' => new CampaignDetailsResource($campanig),
                'message' => 'Campaign retrieved successfully',
                'code' => 200
            ];

        }

        return [
            'user' => null,
            'message' => 'the campaign_id  is not found',
            'code' => 404
        ];
    }
    public function SearchCampaign(SearchCampaignRequest $request)
    {
        $campanig = $this->CampaingRepository->Search($request);

            return [
                'user' =>  CampaignResource::collection($campanig),
                'message' => 'Campaign retrieved successfully',
                'code' => 200
            ];
        }

    public function assignTeamLeader($campaignId, $userId)
    {
        $campaign = $this->CampaingRepository->getById($campaignId);
        if (!$campaign) {
            return [
                'data' => null,
                'message' => 'Campaign not found',
                'code' => 404
            ];
        }

        $user = $this->userRepository->getById($userId);
        if (!$user) {
            return [
                'data' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        if (!$user->hasRole('Volunteer')) {
            return [
                'data' => null,
                'message' => 'User must be a volunteer',
                'code' => 403
            ];
        }

        if ($campaign->leader_id) {
            return [
                'data' => null,
                'message' => 'Campaign already has a leader',
                'code' => 400
            ];
        }

        $this->CampaingRepository->update([
            'leader_id' => $userId
        ], $campaign);

        return [
            'data' => [
                'campaign_id' => $campaignId,
                'leader_id' => $userId
            ],
            'message' => 'Leader assigned successfully',
            'code' => 200
        ];
    }}
