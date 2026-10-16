<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPermissionStoreRequest;
use App\Http\Requests\UserPermissionUpdateRequest;
use App\Services\UserPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserPermissionController extends Controller
{
    protected $permission;

    public function __construct(UserPermissionService $permission)
    {
        $this->permission = $permission;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.language');

        $permissions = $this->permission->getAllPage();
        return view('aadmin.user_permission.index', compact('config', 'permissions'));
    }

    public function create()
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        // Logic to show the form for creating a new user
        return view('aadmin.user_permission.create', compact('config'));
    }

    public function store( UserPermissionStoreRequest $request)
    {
        
        $data = $request->validated();
        if ($data) {
            $this->permission->create($data);
            return redirect()->route('user.permission.index')->with('success', 'Language created successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create language.']);
    }

    public function edit($id)
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        $permissions = $this->permission->getById($id);

        if (!$permissions) {
            return redirect()->route('aadmin.user_permission.index')->withErrors(['error' => 'Language not found.']);
        }
        return view('aadmin.user_permission.update', compact('id', 'config', 'permissions'));
    }

    public function update(UserPermissionUpdateRequest $request, $id)
    {
        $data = $request->validated();

        $this->permission->update($id, $data);

        return redirect()->route('user.permission.index')->with('success', 'Language updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a user
        $this->permission->delete($id);
        return redirect()->route('user.permission.index')->with('success', 'Language deleted successfully.');
    }

    private function config()
    {
        return [
            'js'=>['css2/js/plugins/switchery/switchery.js'],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }
}