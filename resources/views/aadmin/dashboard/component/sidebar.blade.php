<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        <img alt="image" class="img-circle" src="img/profile_small.jpg" />
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs"> <strong
                                    class="font-bold">{{ auth()->user()->name }}</strong>
                            </span> <span class="text-muted text-xs block">Art Director <b class="caret"></b></span>
                        </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile.html">Profile</a></li>
                        <li><a href="contacts.html">Contacts</a></li>
                        <li><a href="mailbox.html">Mailbox</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            <li>
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label">Quản lý người dùng</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('user.role.index') }}">Nhóm người dùng</a></li>
                    <li><a href="{{ route('user.index') }}">DS người dùng</a></li>
                    <li><a href="{{ route('user.permission.index') }}">QL quyền</a></li>
                </ul>
            </li>
            <li>
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label">Quản lý bài viết</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('post.catalogue.index') }}">Nhóm bài viết</a></li>
                    <li><a href="{{ route('post.index') }}">DS bài viết</a></li>
                </ul>
            </li>
            <li>
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label">Quản lý sản phẩm</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('product.category.index') }}">Danh mục sản phẩm</a></li>
                    <li><a href="{{ route('product.index') }}">DS sản phẩm</a></li>
                    <li><a href="{{ route('attribute.category.index') }}">Loại thuộc tính</a></li>
                    <li><a href="{{ route('attribute.index') }}">DS thuộc tính</a></li>
                </ul>
            </li>
            <li>
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label">Quản lý đơn hàng</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('orders.index') }}">Danh sách đơn hàng</a></li>
                </ul>
            </li>
            <li>
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label">Quản lý Marketing</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('promotion.index') }}">Quản lý giảm giá</a></li>
                    {{-- <li><a href="{{ route('product.index') }}">Quản lý mã giảm giá</a></li> --}}
                </ul>
            </li>
            <li>
                <a href="{{ route('user.index') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Cấu hình
                        chung</span>
                    <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('language.index') }}">Quan lý ngôn ngữ</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav>
