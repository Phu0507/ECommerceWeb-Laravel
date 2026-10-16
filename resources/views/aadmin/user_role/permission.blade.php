@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
    <style>
        .ibox-content .form-control {
            margin-bottom: 20px;
            /* thêm khoảng cách dưới input */
        }
    </style>
    <form action="{{ route('user.role.UpdatePermission') }}" method="post" enctype="multipart/form-data">
        @csrf
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
                <tr>
                    <th>Tên quyền</th>
                    @foreach ($roles as $role)
                        <th class="text-center">{{ $role->name }} </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if (isset($permissions) && $permissions->count() > 0)
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="text-primary">{{ $permission->name }}</span>
                                    <span class="text-danger">{{ $permission->canonical }}</span>
                                </div>
                            </td>
                            @php
                                // Tạo 1 mảng role-permission cho dễ check
                                $checked = [];
                                foreach ($rolePermissions as $rp) {
                                    $checked[$rp->user_role_id][$rp->permission_id] = true;
                                }
                            @endphp
                            @foreach ($roles as $role)
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][]"
                                        value="{{ $permission->id }}"
                                        {{ isset($checked[$role->id][$permission->id]) ? 'checked' : '' }}>
                                </td>
                            @endforeach

                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="row">
            <div class="col-lg-5"></div>
            <div class="col-lg-7">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="#" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </form>
    {{ $permissions->links('pagination::bootstrap-5') }}
@endsection
