<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('幸福到家')
            ->description('商品部后台管理')
            ->body('<img src="https://xfdj.luckhome.xyz/uploads/images/b8012f0f065e5940347f006908998a1d.jpg" style="width: 95%;border-radius: 5px" />');
    }
}
