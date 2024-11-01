<?php

namespace App\Traits;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;

trait DepartmentTrait
{
    public function department_trait($type = 'executive')
    {
        $all_departments = Department::where('status', 1)->with(['getBoardLists'])->get();
        if (auth()->user()->type === $type) {
            $user_department_ids = isset(auth()->user()->getDepartment) ? auth()->user()->getDepartment->pluck('id')->toArray() : [];
            $user_departments = Department::where('status', 1)->whereIn('id', $user_department_ids)->with(['getBoardLists'])->get();
        } else {
            $user_departments = $all_departments;
        }

        return [
            'departments' => $user_departments,
            'departments_resource' => DepartmentResource::collection($user_departments),
            'all_departments' => $all_departments,
            'all_departments_resource' => DepartmentResource::collection($all_departments),
        ];
    }
}
