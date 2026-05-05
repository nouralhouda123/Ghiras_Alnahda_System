<?php
namespace App\Services;
use App\Models\PointTransaction;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\userRepository;

class PointTransactionService
{
    protected $userRepository;
    public function __construct(userRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index($user)
    {
        $PointTransaction=$user->receivedPoints;
        return [
            'user' =>$PointTransaction,
            'message' => 'success',
            'code' => 200
        ];
    }
    public function show($user_id)
    {
        $user=$this->userRepository->getById($user_id);
        if (!$user) {
            return ['message' => 'user not found', 'code' => 404];
        }
        $PointTransaction=$user->receivedPoints;
        return [
            'user' =>$PointTransaction,
            'message' => 'success',
            'code' => 200
        ];
    }
}
