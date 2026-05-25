<?php
namespace App\Services;
use App\Helpers\StorageHelper;
use App\Http\Requests\ApprovalRequest;
use App\Http\Requests\CampaingRequest;
use App\Http\Requests\SearchForPermissionsAndRolesRequest;
use App\Http\Resources\CampaignDetailsResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\UserResource;
use App\Repositories\AttendanceRepository;
use App\Repositories\CampaingRepository;
use App\Repositories\PointTransactionRepository;
use App\Repositories\userRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    public function __construct(
        CampaingRepository $CampaignRepository,
        KPIBrain $KPIBrain,
        IndicatorMatchingService $indicatorService
    ) {
        $this->CampaignRepository = $CampaignRepository;
        $this->KPIBrain = $KPIBrain;
        $this->indicatorService = $indicatorService;
    }


    public function create(CampaingRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $data = $request->validated();
            unset($data['image'], $data['video'], $data['goals']);

            if ($request->hasFile('image')) {
                $images = [];

                foreach ($request->file('image') as $image) {
                    $images[] = StorageHelper::storeFile($image, 'campaigns/images');
                }

                $data['image'] = json_encode($images);
            }

            if ($request->hasFile('video')) {
                $videos = [];

                foreach ($request->file('video') as $video) {
                    $videos[] = StorageHelper::storeFile($video, 'campaigns/videos');
                }

                $data['video'] = json_encode($videos);
            }
            $campaign = $this->CampaignRepository->createCampaing($data);

            $results = [];

            if ($request->has_evaluation && !empty($request->goals)) {

                foreach ($request->goals as $goalText) {

                    $analysis = $this->KPIBrain->analyze($goalText);

                    $kpi = $this->CampaignRepository->createCampaing_Kpi([
                        'campaign_id' => $campaign->id,
                        'goal_text'   => $goalText,
                        'domain'      => $analysis['domain'],
                        'intent'      => $analysis['intent'],
                        'type'        => $analysis['type'],
                        'target_value'=> $analysis['target'] ?? null,
                    ]);

                    $indicators = $this->indicatorService->generate(
                        $analysis,
                        $goalText
                    );

                    $results[] = [
                        'goal' => $kpi,
                        'analysis' => $analysis,
                        'indicators' => $indicators
                    ];
                }
            }

            return [
                'user' => $results,
                    //new CampaignDetailsResource($campanig),

               // 'results' => $results,
                'message' => 'Campaign created successfully',
                'code' => 201
            ];
        });
    }        public function show()
    {
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
    public function SearchCampaign(SearchForPermissionsAndRolesRequest $request)
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
    }

    public function showMyCampanig()
    {
        $user = auth()->user();
        if (!$user->volunteerProfile) {
            return [
                'user' => null,
                'message' => 'No volunteer profile found for this user.',
                'code' => 404
            ];
        }
        $campaigns = $user->volunteerProfile
            ->campaigns()
            ->get();
        return [
            'user' => CampaignResource::collection($campaigns),
            'message' => 'Your campaigns retrieved successfully.',
            'code' => 200
        ];
    }

}
