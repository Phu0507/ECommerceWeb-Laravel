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
            <th>Tên</th>
            <th>Canoncial</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($permissions) && $permissions->count() > 0)
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->canonical }}</td>
                    <td class="text-center">
                        <a href="{{ route('user.permission.edit', $permission->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('user.permission.destroy', $permission->id) }}" method="POST"
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
{{ $permissions->links('pagination::bootstrap-5') }}
