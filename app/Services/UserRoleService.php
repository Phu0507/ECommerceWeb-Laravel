<?php

namespace App\Services;

use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRoleService
 * @package App\Services
 */
class UserRoleService
{
    public function all()
    {
        return UserRole::all();
    }

    public function getAllPage()
    {
        return UserRole::withCount('users')->paginate(10);
    }

    public function getById($id)
    {
        return UserRole::find($id);
    }

    public function create(array $data)
    {
        // $data['user_id'] = UserRole::id(); // thêm trước khi create
        return UserRole::create($data);
    }

    public function update($id, array $data)
    {
        $role = UserRole::findOrFail($id);
        $role->update($data);
        return $role;
    
    }

    public function delete($id)
    {
        $role = UserRole::findOrFail($id);
        return $role->delete();
    }

    public function syncPermissions(array $permissionsData)
    {
        $permissionsData = $permissionsData['permissions'] ?? [];

        DB::transaction(function () use ($permissionsData) {
            // Lấy tất cả role
            $roles = UserRole::all();

            foreach ($roles as $role) {
                // Nếu role không có trong request => mảng rỗng, sẽ xóa hết quyền cũ
                $permissionIds = $permissionsData[$role->id] ?? [];
                $role->permissions()->sync($permissionIds);
            }
        });
    }
    
}