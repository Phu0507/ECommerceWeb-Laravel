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
            <th style="width: 600px">Tên nhóm</th>
            <th class="text-center" style="width: 100px">Tình trạng</th>
            <th style="width: 150px" class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($attributeCategory) && $attributeCategory->count() > 0)
            @foreach ($attributeCategory as $category)
                <tr>
                    <td>{{ $category->display_name }}</td>
                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="publish" data-id="{{ $category->id }}"
                            data-field="publish" {{ $category->status === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('attribute.category.edit', $category->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('attribute.category.destroy', $category->id) }}" method="POST"
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
{{ $attributeCategory->links('pagination::bootstrap-5') }}
