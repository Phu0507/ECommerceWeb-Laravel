<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Services\ProvinceService as ProvinceService;

class UserController extends Controller
{
    protected $userService;
    protected $provinceService;

    public function __construct(UserService $userService, ProvinceService $provinceService)
    {
        $this->middleware('auth:web');
        $this->userService = $userService;
        $this->provinceService = $provinceService;
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.user');

        $perPage = (int) $request->input('perpage', 10);
        $role = $request->input('role');
        $search = $request->input('search');

        // Truyền cả điều kiện tìm kiếm xuống service
        $users = $this->userService->paginateWithFilters($perPage, $role, $search);

        return view('aadmin.user.index', compact('config', 'users', 'perPage'));
    }

    public function create()
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.user');
        $provinces = $this->provinceService->getAll();
        // Logic to show the form for creating a new user
        return view('aadmin.user.create', compact('config', 'provinces'));
    }

    public function store(UserRequest $request)
    {
        
        $data = $request->validated();
        //neu co du lieu thì lưu vào csdl
        if ($data) {
           $this->userService->createUser($data);
            return redirect()->route('user.index')->with('success', 'User created successfully.');
        }
        return redirect()->back()->withErrors(['error' => 'Failed to create user.']);
    }

    public function edit($id)
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.user');
        $provinces = $this->provinceService->getAll();
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            return redirect()->route('aadmin.user.index')->withErrors(['error' => 'User not found.']);
        }
        return view('aadmin.user.update', compact('id', 'config', 'provinces', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); // Lấy user hiện tại

        // Validate các field trừ email
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:0,1',
            'province_id' => 'required|exists:1_devvn_tinhthanhpho,matp',
            'district_id' => 'required|exists:2_devvn_quanhuyen,maqh',
            'ward_id' => 'required|exists:3_devvn_xaphuongthitran,xaid',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'phone' => 'required|string|max:15',
        ]);

        // Kiểm tra nếu email có thay đổi thì mới validate unique
        if ($request->input('email') !== $user->email) {
            $request->validate([
                'email' => 'required|email|max:255|unique:users,email',
            ]);
            $data['email'] = $request->input('email');
        } else {
            $data['email'] = $user->email;
        }

        // Nếu không nhập mật khẩu thì giữ nguyên mật khẩu cũ
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        // Gọi hàm update trong Service
        $this->userService->updateUser($id, $data);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a user
        $this->userService->deleteUser($id);
        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $userId = $request->input('id');
        $status = $request->input('status');


        //Update user status
        $this->userService->updateStatus($userId, ['status' => $status]);

        return response()->json(['success' => 'User status updated successfully']);
    }


    private function config()
    {
        return [
            'js'=>['css2/js/plugins/switchery/switchery.js'],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }
}