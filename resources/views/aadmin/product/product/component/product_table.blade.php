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
        @if (isset($productCategory) && $productCategory->count() > 0)
            @foreach ($productCategory as $product)
                <tr>
                    <td>
                        <div style="display: flex; align-items: flex-start;">
                            {{-- Ảnh bên trái --}}
                            <div style="margin-right: 10px;">
                                @if ($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="100">
                                @else
                                    <span>Chưa cập nhật</span>
                                @endif
                            </div>
                            <div>
                                <div style="color: rgb(72, 72, 225); font-weight: bold;">
                                    {{ $product->product_name }}
                                </div>
                                <span style="color: #e74c3c;">Danh mục: </span>{{ $product->category_name }}
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="js-switch status" name="status" data-id="{{ $product->id }}"
                            data-field="status" {{ $product->status === 1 ? 'checked' : '' }} />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-xs btn-success"
                            style="width: 30px; height: 30px; justify-content: center; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('product.destroy', $product->id) }}" method="POST"
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
