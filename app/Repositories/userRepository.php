<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class userRepository
{
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'email_verified_at' => now(),
        ]);
    }

    public function create_User(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'email_verified_at' => now(),
            'status' => 'active',
            'department_id' => $data['department_id'],
        ]);
    }

    public function getById($id)
    {
        return User::query()->find($id);
    }

    public function searchEmployee($request)
    {
        $query = User::query();
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name);

        }
    }
    public function UpdateEmployee($data, $id)
    {
        $user=User::query()->find($id);
         $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'department_id' => $data['department_id'] ?? $user->department_id,
            'status' => $data['status'] ?? $user->status,            ]);
        return $user;
    }
    public function getByRolesAndDepartment($roles, $departmentId)
    {
        return User::query()
            ->role($roles)
            ->where('department_id', $departmentId)
            ->get();
    }

}
