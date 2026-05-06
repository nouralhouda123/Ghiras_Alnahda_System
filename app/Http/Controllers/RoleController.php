<?php


namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Services\RoleService;

class RoleController
{
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function getRoleNames(): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->getRoleNames();
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
}}
