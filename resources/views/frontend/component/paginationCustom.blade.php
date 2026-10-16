@if ($paginator->hasPages())
    <div class="product__pagination">
        {{-- Previous Page Link --}}
        @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}"><i class="fa fa-long-arrow-left"></i></a>
        @endif

        @php
            $total = $paginator->lastPage(); // tổng số trang
            $current = $paginator->currentPage(); // trang hiện tại
        @endphp

        {{-- Nếu tổng số trang <= 6 thì in hết --}}
        @if ($total <= 6)
            @for ($i = 1; $i <= $total; $i++)
                <a href="{{ $paginator->url($i) }}" class="{{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
            @endfor
        @else
            {{-- Trang 1, 2, 3 --}}
            @if ($current <= 3)
                @for ($i = 1; $i <= $current + 1; $i++)
                    <a href="{{ $paginator->url($i) }}"
                        class="{{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
                @endfor
                <span>. . .</span>
                <a href="{{ $paginator->url($total) }}">{{ $total }}</a>

                {{-- Ở giữa --}}
            @elseif ($current > 3 && $current < $total - 2)
                <a href="{{ $paginator->url(1) }}">1</a>
                <span>. . .</span>
                @for ($i = $current - 1; $i <= $current + 1; $i++)
                    <a href="{{ $paginator->url($i) }}"
                        class="{{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
                @endfor
                <span>. . .</span>
                <a href="{{ $paginator->url($total) }}">{{ $total }}</a>

                {{-- Gần cuối --}}
            @else
                <a href="{{ $paginator->url(1) }}">1</a>
                <span>. . .</span>
                @for ($i = $total - 2; $i <= $total; $i++)
                    <a href="{{ $paginator->url($i) }}"
                        class="{{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
                @endfor
            @endif
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"><i class="fa fa-long-arrow-right"></i></a>
        @endif
    </div>
@endif
