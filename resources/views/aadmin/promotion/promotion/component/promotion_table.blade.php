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
            <th>Chương trình giảm giá</th>
            <th class="text-center">Thời gian áp dụng</th>
            <th class="text-center">Chi tiết</th>
            <th class="text-center">Chiết khấu</th>
            <th style="width: 100px" class="text-center">Tình trạng</th>
            <th style="width: 150px" class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($promotionCategory) && $promotionCategory->count() > 0)
            @foreach ($promotionCategory as $promotion)
                @php
                    // đảm bảo các key luôn tồn tại
                    $promotion = array_merge(
                        [
                            'discounts' => [],
                            'promotion_type' => 'order',
                            'discount_type' => 'percentage',
                            'status' => 'inactive',
                        ],
                        $promotion,
                    );

                    $discounts = collect($promotion['discounts']);
                    $rows = $discounts->count() > 0 ? $discounts : [null];
                @endphp

                @foreach ($rows as $discount)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $discounts->count() ?: 1 }}">
                                <div style="color: rgb(72, 72, 225); font-weight: bold;">
                                    {{ $promotion['name'] ?? '-' }}
                                </div>
                            </td>
                            <td rowspan="{{ $discounts->count() ?: 1 }}" class="text-center">
                                {{ \Carbon\Carbon::parse($promotion['start_date'] ?? now())->format('d/m/Y H:i') }} -
                                {{ \Carbon\Carbon::parse($promotion['end_date'] ?? now())->format('d/m/Y H:i') }}
                            </td>
                        @endif

                        <td class="text-center">
                            @if (($promotion['promotion_type'] ?? null) === 'order' && $discount)
                                {{ $discount['order_amount_from'] ?? '-' }} - {{ $discount['order_amount_to'] ?? '-' }}
                            @elseif(($promotion['promotion_type'] ?? null) === 'product' && $discount)
                                Min: {{ $discount['min_quantity'] ?? '-' }}, Max: {{ $discount['max_quantity'] ?? '-' }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-center">
                            @if ($discount)
                                {{ $discount['discount_value'] ?? '-' }}
                                {{ ($promotion['discount_type'] ?? null) === 'percentage' ? '%' : 'đ' }}
                            @else
                                -
                            @endif
                        </td>

                        @if ($loop->first)
                            <td rowspan="{{ $discounts->count() ?: 1 }}" class="text-center"
                                style="vertical-align: middle;">
                                <input type="checkbox" class="js-switch status" name="status"
                                    data-id="{{ $promotion['id'] ?? 0 }}" data-field="status"
                                    {{ ($promotion['status'] ?? '') === 'active' ? 'checked' : '' }} />
                            </td>

                            <td rowspan="{{ $discounts->count() ?: 1 }}" class="text-center"
                                style="vertical-align: middle;">
                                <a style="width: 30px; height: 30px;"
                                    href="{{ route('promotion.edit', $promotion['id'] ?? 0) }}"
                                    class="btn btn-xs btn-success">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('promotion.destroy', $promotion['id'] ?? 0) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button style="width: 30px; height: 30px;" type="submit"
                                        class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        @endif
    </tbody>
</table>

{{ $promotionCategory->links('pagination::bootstrap-5') }}
