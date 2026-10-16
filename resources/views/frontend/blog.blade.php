@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Tin tức</h2>
                        <div class="breadcrumb__option">
                            <a href="./index.html">Trang chủ</a>
                            <span>Tin tức</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Blog Section Begin -->
    <section class="blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5">
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
                            <h4>Tin tức nổi bật</h4>
                            <div class="blog__sidebar__recent">
                                <a href="#" class="blog__sidebar__recent__item">
                                    <div class="blog__sidebar__recent__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="blog__sidebar__recent__item__text">
                                        <h6>Nổi bật 1</h6>
                                        <span>11 THÁNG 9, 2025</span>
                                    </div>
                                </a>
                                <a href="#" class="blog__sidebar__recent__item">
                                    <div class="blog__sidebar__recent__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="blog__sidebar__recent__item__text">
                                        <h6>Nổi bật 2</h6>
                                        <span>11 THÁNG 9, 2025</span>
                                    </div>
                                </a>
                                <a href="#" class="blog__sidebar__recent__item">
                                    <div class="blog__sidebar__recent__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="blog__sidebar__recent__item__text">
                                        <h6>Nổi bật 3</h6>
                                        <span>11 THÁNG 9, 2025</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="blog__sidebar__item">
                            <h4>Từ khóa tìm kiếm phổ biến</h4>
                            <div class="blog__sidebar__item__tags">
                                {{-- <a href="#">Xe</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="row">
                        @foreach ($posts as $post)
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="blog__item">
                                    <div class="blog__item__pic">
                                        <img src="{{ $post->image }}" alt="{{ $post->name }}">
                                    </div>
                                    <div class="blog__item__text">
                                        <ul>
                                            <li>
                                                <i class="fa fa-calendar-o"></i>
                                                {{ $post->created_at->locale('vi')->translatedFormat('j F, Y') }}
                                            </li>
                                            <li>
                                                <i class="fa fa-comment-o"></i>
                                                {{ $post->comments_count ?? 0 }}
                                            </li>
                                        </ul>
                                        <h5>
                                            <a
                                                href="{{ route('blog.details', [
                                                    'category_canonical' => $post->category_canonical,
                                                    'post_canonical' => $post->post_canonical,
                                                ]) }}">
                                                {{ $post->post_name }}
                                            </a>
                                        </h5>
                                        <p>
                                            {{ $post->post_description }}
                                        </p>
                                        <a href="{{ route('blog.details', [
                                            'category_canonical' => $post->category_canonical,
                                            'post_canonical' => $post->post_canonical,
                                        ]) }}"
                                            class="blog__btn">
                                            Đọc thêm <span class="arrow_right"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{ $posts->links('frontend.component.paginationCustom') }}
                </div>

            </div>
        </div>
    </section>
    <!-- Blog Section End -->
@endsection
