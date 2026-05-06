<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function getAll()
    {
        return Role::all();
    }

    public function getNames()
    {
        return Role::pluck('name');
    }

    public function findById($id)
    {
        return Role::find($id);
    }

    public function create($data)
    {
        return Role::create($data);
    }
}
