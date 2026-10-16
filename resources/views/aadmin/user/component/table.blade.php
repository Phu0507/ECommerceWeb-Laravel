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
            <th class="text-center">
                <input type="checkbox" class="check-all">
            </th>
            <th>Họ tên</th>
            <th>Email</th>
            <th width="120">Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Trạng thái</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($users) && $users->count() > 0)
            @foreach ($users as $user)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="check-all">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                    <td>
                        <div class="address-item">
                            @php
                                $fullAddress = $user->address ?? '';
                                if ($user->ward) {
                                    $fullAddress .= ', ' . $user->ward->name;
                                }
                                if ($user->district) {
                                    $fullAddress .= ', ' . $user->district->name;
                                }
                                if ($user->province) {
                                    $fullAddress .= ', ' . $user->province->name;
                                }
                            @endphp
                            {{ $fullAddress ?: 'Chưa cập nhật' }}
                        </div>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status" data-id="{{ $user->id }}"
                            data-field="status" {{ $user->status === 'active' ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;">
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
{{ $users->links('pagination::bootstrap-5') }}
