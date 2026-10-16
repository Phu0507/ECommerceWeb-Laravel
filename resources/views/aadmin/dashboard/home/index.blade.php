@extends('aadmin.dashboard.layout')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <!-- Doanh thu hôm nay -->
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Doanh thu hôm nay</h5>
                    </div>
                    <div class="ibox-content text-center">
                        <h1 class="no-margins text-success">{{ number_format($todayRevenue, 0, ',', '.') }}₫</h1>
                        <small>So với hôm qua: <span class="text-info">{{ $todayChange }}%</span></small>
                    </div>
                </div>
            </div>

            <!-- Doanh thu tháng -->
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Doanh thu tháng</h5>
                    </div>
                    <div class="ibox-content text-center">
                        <h1 class="no-margins text-primary">{{ number_format($monthRevenue, 0, ',', '.') }}₫</h1>
                        <small>So với tháng trước: <span class="text-info">{{ $monthChange }}%</span></small>
                    </div>
                </div>
            </div>

            <!-- Tổng đơn hàng -->
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Tổng đơn hàng</h5>
                    </div>
                    <div class="ibox-content text-center">
                        <h1 class="no-margins text-warning">{{ $totalOrders }}</h1>
                        <small>Trong năm nay</small>
                    </div>
                </div>
            </div>

            <!-- Số khách hàng -->
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Khách hàng</h5>
                    </div>
                    <div class="ibox-content text-center">
                        <h1 class="no-margins text-danger">{{ $totalCustomers }}</h1>
                        <small>Đã đăng ký</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu -->
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thống kê doanh thu theo tháng</h5>
                    </div>
                    <div class="ibox-content">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng gần đây -->
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Đơn hàng gần đây</h5>
                    </div>
                    <div class="ibox-content">
                        <ul class="list-group">
                            @foreach ($recentOrders as $order)
                                @php
                                    $statusClass = match ($order->order_status) {
                                        'pending' => 'badge-warning',
                                        'confirmed' => 'badge-success',
                                        'shipping' => 'badge-info',
                                        'delivered' => 'badge-primary',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary',
                                    };
                                @endphp

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    #{{ $order->id }} - {{ $order->customer_name }}
                                    <span class="badge badge-primary">
                                        {{ number_format($order->total, 0, ',', '.') }}₫
                                    </span>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        $(function() {
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: {!! json_encode($revenues) !!},
                        borderColor: '#1ab394',
                        backgroundColor: 'rgba(26, 179, 148, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: val => val.toLocaleString() + '₫'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
