<div id="rating">
    <div class="rating-avg">
        <span>{{ $avgRating }}</span>
        <div class="rating-stars">
            @for ($i = 1; $i <= 5; $i++)
                <i
                    class="fa fa-star{{ $i <= floor($avgRating) ? '' : ($i - $avgRating <= 0.5 ? '-half-o' : '-o') }}"></i>
            @endfor
        </div>
    </div>
    @php $totalRatings = array_sum($starsCount); @endphp
    <ul class="rating">
        @for ($i = 5; $i >= 1; $i--)
            <li>
                <div class="rating-stars">
                    @for ($j = 1; $j <= 5; $j++)
                        <i class="fa fa-star{{ $j <= $i ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <div class="rating-progress">
                    <div style="width: {{ $totalRatings ? round(($starsCount[$i] / $totalRatings) * 100) : 0 }}%;"></div>
                </div>
                <span class="sum">{{ $starsCount[$i] }}</span>
            </li>
        @endfor
    </ul>
</div>
