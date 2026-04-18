<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //اضافة قسم
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $department = Department::create($request->all());
        return ResponseHelper::Success($department, 'the department created successfully', 201);
    }
    //عرض الاقسام

}
