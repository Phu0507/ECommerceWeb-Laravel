<?php

namespace App\Services;

use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserPermissionService
 * @package App\Services
 */
class UserPermissionService
{
    public function getAllPage()
    {
        return UserPermission::paginate(25);
    }
    public function all()
    {
        return UserPermission::all();
    }
    public function getById($id)
    {
        return UserPermission::find($id);
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id(); // thêm trước khi create
        return UserPermission::create($data);
    }

    public function update($id, array $data)
    {
        $language = UserPermission::findOrFail($id);
        $language->update($data);
        return $language;
    
    }

    public function delete($id)
    {
        $language = UserPermission::findOrFail($id);
        return $language->delete();
    }
}