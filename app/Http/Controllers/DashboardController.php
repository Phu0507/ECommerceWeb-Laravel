<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $config = $this->getConfig();
        $data = $this->orderService->getDashboardData();
        return view('aadmin.dashboard.home.index', array_merge(compact('config'), $data));
    }

    private function getConfig()
    {
        return [
            'js' => [
                'css2/js/plugins/flot/jquery.flot.js',
                'css2/js/plugins/flot/jquery.flot.tooltip.min.js',
                'css2/js/plugins/flot/jquery.flot.spline.js',
                'css2/js/plugins/flot/jquery.flot.resize.js',
                'css2/js/plugins/flot/jquery.flot.pie.js',
                'css2/js/plugins/flot/jquery.flot.symbol.js',
                'css2/js/plugins/flot/jquery.flot.time.js',
                'css2/js/plugins/peity/jquery.peity.min.js',
                'css2/js/demo/peity-demo.js',
                'css2/js/inspinia.js',
                'css2/js/plugins/pace/pace.min.js',
                'css2/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js',
                'css2/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
                'css2/js/plugins/easypiechart/jquery.easypiechart.js',
                'css2/js/plugins/sparkline/jquery.sparkline.min.js',
                'css2/js/demo/sparkline-demo.js'
            ]
        ];
    }
}