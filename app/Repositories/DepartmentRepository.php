<?php


namespace App\Repositories;


use App\Models\Department;

class DepartmentRepository
{

    public function getAll()
    {
        return  Department::all();

    }

    public function create($data)
    {
        return Department::create($data);
    }}
