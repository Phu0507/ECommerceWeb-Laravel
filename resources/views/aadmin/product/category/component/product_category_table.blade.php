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
            <th>Ảnh đại diện</th>
            <th style="width: 100px">Tình trạng</th>
            <th style="width: 150px" class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($productCategory) && $productCategory->count() > 0)
            @foreach ($productCategory as $category)
                <tr>
                    <td>{{ $category->display_name }}</td>

                    <td>
                        @if ($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" width="100">
                        @else
                            Chưa cập nhật
                        @endif
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="publish" data-id="{{ $category->id }}"
                            data-field="publish" {{ $category->status === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('product.category.edit', $category->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('product.category.destroy', $category->id) }}" method="POST"
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
{{ $productCategory->links('pagination::bootstrap-5') }}
