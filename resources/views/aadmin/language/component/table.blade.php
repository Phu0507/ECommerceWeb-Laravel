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
            <th>Ảnh đại diện</th>
            <th>Trạng thái</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($languages) && $languages->count() > 0)
            @foreach ($languages as $language)
                <tr>
                    <td>{{ $language->name }}</td>
                    <td>{{ $language->canonical }}</td>
                    <td>
                        @if ($language->image)
                            <img src="{{ asset($language->image) }}" alt="{{ $language->name }}" width="100">
                        @else
                            Chưa cập nhật
                        @endif
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status" data-id="{{ $language->id }}"
                            data-field="status" {{ $language->status === 'active' ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('language.edit', $language->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('language.destroy', $language->id) }}" method="POST"
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
{{ $languages->links('pagination::bootstrap-5') }}
