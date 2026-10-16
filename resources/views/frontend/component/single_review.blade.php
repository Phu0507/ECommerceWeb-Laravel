<li>
    <div class="review-heading">
        <h5 class="name">{{ $rating->customer->name ?? 'Khách' }}</h5>
        <p class="date">
            {{ $rating->created_at->locale('vi')->translatedFormat('d F Y, H:i') }}
        </p>
        <div class="review-rating">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fa fa-star{{ $i <= $rating->rating ? '' : '-o' }}"></i>
            @endfor
        </div>
    </div>
    <div class="review-body">
        <p>{{ $rating->comment }}</p>
    </div>
</li>
