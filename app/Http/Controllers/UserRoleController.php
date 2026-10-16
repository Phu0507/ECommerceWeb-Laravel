<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleStoreRequest;
use App\Http\Requests\UserRoleUpdateRequest;
use App\Services\UserPermissionService;
use App\Services\UserRoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    protected $roleService;
    protected $permissionService;

    public function __construct(UserRoleService $roleService, UserPermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.language');
        $roles = $this->roleService->getAllPage();
        return view('aadmin.user_role.index', compact('config', 'roles'));
    }

    public function create()
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        // Logic to show the form for creating a new user
        return view('aadmin.user_role.create', compact('config'));
    }

    public function store( UserRoleStoreRequest $request)
    {
        
        $data = $request->validated();

        if ($data) {
            $this->roleService->create($data);
            return redirect()->route('user.role.index')->with('success', 'Language created successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create language.']);
    }

    public function edit($id)
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        $role = $this->roleService->getById($id);

        if (!$role) {
            return redirect()->route('aadmin.user_role.index')->withErrors(['error' => 'Language not found.']);
        }
        return view('aadmin.user_role.update', compact('id', 'config', 'role'));
    }

    public function update(UserRoleUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $this->roleService->update($id, $data);

        return redirect()->route('user.role.index')->with('success', 'Language updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a user
        $this->roleService->delete($id);
        return redirect()->route('user.role.index')->with('success', 'Language deleted successfully.');
    }

    //ajax
    public function getRoles(Request $request)
    {
        $roles = $this->roleService->all()
        ->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        });
        return response()->json($roles);
    }

    private function config()
    {
        return [
            'js'=>['css2/js/plugins/switchery/switchery.js'],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }

    public function permission()
    {

        $config['seo'] = config('apps.language');
        $roles = $this->roleService->all();
        $permissions = $this->permissionService->getAllPage();
        // lấy tất cả role kèm theo permission
        $rolePermissions = DB::table('user_role_permission')->get();
        return view('aadmin.user_role.permission', compact('config', 'roles', 'permissions', 'rolePermissions'));
    }

    public function UpdatePermission(Request $request)
    {
        $data = $request->all();
        $this->roleService->syncPermissions($data);
        return redirect()->back()->with('success', 'Cập nhật phân quyền thành công');
    }
}