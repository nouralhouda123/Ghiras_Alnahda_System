<?php


namespace App\Services;


use App\Repositories\EmailVerficationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\userRepository;

class RoleService
{
    protected $roleRepository;
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getRoleNames()
    {
        $roles = $this->roleRepository->getNames();

        if ($roles->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No roles found',
                'code' => 200
            ];
        }

        return [
            'data' => $roles,
            'message' => 'Roles fetched successfully',
            'code' => 200
        ];
    }
}
