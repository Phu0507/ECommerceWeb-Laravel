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
            <th style="width: 50px" class="text-center">Vị trí</th>
            <th style="width: 100px" class="text-center">Tình trạng</th>
            <th style="width: 150px" class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($postCatalogues) && $postCatalogues->count() > 0)
            @foreach ($postCatalogues as $postCatalogue)
                <tr>
                    <td>
                        <div style="display: flex; align-items: flex-start;">
                            {{-- Ảnh bên trái --}}
                            <div style="margin-right: 10px;">
                                @if ($postCatalogue->image)
                                    <img src="{{ asset($postCatalogue->image) }}" alt="{{ $postCatalogue->name }}"
                                        width="100">
                                @else
                                    <span>Chưa cập nhật</span>
                                @endif
                            </div>
                            <div>
                                <div style="color: rgb(72, 72, 225); font-weight: bold;">
                                    {{ $postCatalogue->post_name }}
                                </div>
                                <span style="color: #e74c3c;">Danh mục: </span>{{ $postCatalogue->category_name }}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="number" name="order[{{ $postCatalogue->id }}]" value="{{ $postCatalogue->order }}"
                            class="form-control text-center" style="width: 70px; margin: auto;">
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status"
                            data-id="{{ $postCatalogue->id }}" data-field="status"
                            {{ $postCatalogue->publish === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('post.edit', $postCatalogue->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('post.destroy', $postCatalogue->id) }}" method="POST"
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
{{ $postCatalogues->links('pagination::bootstrap-5') }}
