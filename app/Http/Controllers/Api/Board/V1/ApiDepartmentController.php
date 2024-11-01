<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Traits\DepartmentTrait;
use Illuminate\Http\Request;

class ApiDepartmentController extends Controller
{
    use DepartmentTrait;

    public function index(): ?\Illuminate\Http\JsonResponse
    {
        try {
            $departmentData = $this->department_trait();
            return response()->json([
                'departments' => $departmentData['departments_resource'],
                'all_departments' => $departmentData['all_departments_resource'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
