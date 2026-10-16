@extends('frontend.index')

@section('content')
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Đăng ký</h2>
                        <div class="breadcrumb__option">
                            <a href="{{ route('home.index') }}">Trang chủ</a>
                            <span>Đăng ký</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="register-section spad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-sm p-4">
                        <h4 class="card-title mb-4 text-center">Tạo tài khoản mới</h4>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('customer.postRegister') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Họ và tên</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="Nhập họ tên"
                                    required value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email"
                                    required value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Mật khẩu</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Nhập lại mật khẩu" required>
                            </div>

                            <button type="submit" class="btn btn-danger btn-block">Đăng ký</button>

                            <div class="mt-3 text-center">
                                <span>Đã có tài khoản?
                                    <a href="{{ route('customer.index') }}"
                                        style="color:#007bff; text-decoration:none;">Đăng
                                        nhập</a>
                                </span>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="" style="color:#007bff; text-decoration:none;">Quên
                                    mật khẩu?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
