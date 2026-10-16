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
            <th>Bài viết</th>
            <th style="width: 100px" class="text-center">Tình trạng</th>
            <th style="width: 150px" class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($attributeCategory) && $attributeCategory->count() > 0)
            @foreach ($attributeCategory as $attribute)
                <tr>
                    <td>
                        <div style="display: flex; align-items: flex-start;">
                            <div>
                                <div style="color: rgb(72, 72, 225); font-weight: bold;">
                                    {{ $attribute->attribute_name }}
                                </div>
                                <span style="color: #e74c3c;">Danh mục: </span>{{ $attribute->category_name }}
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status" data-id="{{ $attribute->id }}"
                            data-field="status" {{ $attribute->status === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('attribute.edit', $attribute->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('attribute.destroy', $attribute->id) }}" method="POST"
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
