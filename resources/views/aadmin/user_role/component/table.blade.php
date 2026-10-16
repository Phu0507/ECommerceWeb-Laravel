<style>
    .switchery-small {
        width: 33px;
        height: 20px;
        border-radius: 20px;
    }

    .switchery-small>small {
        width: 20px;
        height: 20px;

    }
</style>

<table class="table table-striped table-bordered table-hover dataTables-example">
    <thead>
        <tr>
            <th>Tên vai trò</th>
            <th class="text-center" width='100px'>Số lượng</th>
            <th>Mô tả</th>
            <th class="text-center" width='100px'>Trạng thái</th>
            <th class="text-center" width='200px'>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($roles) && $roles->count() > 0)
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td class="text-center">{{ $role->users_count }}</td>
                    <td>{{ $role->description }}</td>
                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status" data-id="{{ $role->id }}"
                            data-field="status" {{ $role->is_active === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('user.role.edit', $role->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('user.role.destroy', $role->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger"
                                style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $roles->links('pagination::bootstrap-5') }}
