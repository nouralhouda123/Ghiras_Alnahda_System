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
    public function create_instructor(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
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
    public function getAll()
    {
        return User::all();
    }

    public function searchUser($request)
    {
        $query = User::query();

        $query->when($request->filled('role'), function ($q) use ($request) {
            $q->whereHas('roles', function ($r) use ($request) {
                $r->where('name', $request->role);
            });
        });

        $query->when($request->filled('name'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        });

        return $query->get();
    }    public function UpdateEmployee($data, $id)
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
