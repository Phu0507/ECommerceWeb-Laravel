@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <section class="blog-details-hero set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="blog__details__hero__text">
                        <h2>{{ $post->post_name }}</h2>
                        <ul>
                            <li>{{ $post->author_name }}</li>
                            <li> {{ $post->created_at->locale('vi')->translatedFormat('j F, Y') }}</li>
                            <li>8 bình luận</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details Hero End -->

    <!-- Blog Details Section Begin -->
    <section class="blog-details spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5 order-md-1 order-2">
                    <div class="blog__sidebar">
                        <div class="blog__sidebar__search">
                            <form action="{{ route('blog.index') }}" method="GET">
                                <input type="text" name="keyword" value="{{ request('keyword') }}"
                                    placeholder="Tìm kiếm...">
                                @if (request()->filled('category'))
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                @endif
                                <button type="submit"><span class="icon_search"></span></button>
                            </form>
                        </div>
                        <div class="blog__sidebar__item">
                            <h4>Danh mục</h4>
                            <ul>
                                <li>
                                    <a href="{{ route('blog.index') }}">Tất cả</a>
                                </li>
                                @foreach ($postCategories as $category)
                                    <li>
                                        <a
                                            href="{{ route('blog.index', array_merge(request()->query(), ['category' => $category->id])) }}">
                                            {{ $category->languages->first()->pivot->name }}
                                            ({{ $category->posts_count }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="blog__sidebar__item">
                            <h4>Tin tức gần đây</h4>
                            <div class="blog__sidebar__recent">
                                @foreach ($recentPosts as $recent)
                                    <a href="{{ route('blog.details', [$recent->category_canonical, $recent->post_canonical]) }}"
                                        class="blog__sidebar__recent__item">
                                        <div class="blog__sidebar__recent__item__pic">
                                            <img src="{{ asset($recent->image ?? 'frontend/img/default.png') }}"
                                                alt="{{ $recent->post_name }}">
                                        </div>
                                        <div class="blog__sidebar__recent__item__text">
                                            <h6>{{ $recent->post_name }}</h6>
                                            <span>{{ $recent->created_at->locale('vi')->translatedFormat('j F, Y') }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="blog__sidebar__item">
                            <h4>Từ khóa tìm kiếm phổ biến</h4>
                            <div class="blog__sidebar__item__tags">
                                {{-- <a href="#"></a> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 order-md-1 order-1">
                    <div class="blog__details__text">
                        {!! $post->post_content !!}
                    </div>
                    <div class="blog__details__content">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="blog__details__author">
                                    <div class="blog__details__author__pic">
                                        <img src="{{ asset('frontend2/img/blog/details/details-author.jpg') }}"
                                            alt="">
                                    </div>
                                    <div class="blog__details__author__text">
                                        <h6>{{ $post->author_name }}</h6>
                                        <span>Admin</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="blog__details__widget">
                                    <ul>
                                        <li><span>Danh mục:</span> {{ $post->category_name }}
                                        </li>
                                        <li><span>Tags:</span> All, Trending</li>
                                    </ul>
                                    <div class="blog__details__social">
                                        <a href="#"><i class="fa fa-facebook"></i></a>
                                        <a href="#"><i class="fa fa-twitter"></i></a>
                                        <a href="#"><i class="fa fa-google-plus"></i></a>
                                        <a href="#"><i class="fa fa-linkedin"></i></a>
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details Section End -->

    <!-- Related Blog Section Begin -->
    <section class="related-blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title related-blog-title">
                        <h2>Tin tức có thể bạn sẽ thích</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-1.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> 11 THÁNG 9, 2025</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">Tin tức yêu thích 1</a></h5>
                            <p>Mô tả 1</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-2.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> 11 THÁNG 9, 2025</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">Tin tức yêu thích 2</a></h5>
                            <p>Mô tả 2</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-3.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> 11 THÁNG 9, 2025</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">Tin tức yêu thích 3</a></h5>
                            <p>Mô tả 3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Related Blog Section End -->
@endsection
