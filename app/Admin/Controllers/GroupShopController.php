<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Product;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class GroupShopController extends Controller
{
    public function index(Content $content)
    {
        $list = Product::orderBy('created_at','desc')->get();
        return $content
            ->header('团购产品设置')
            ->body(view('admin.groupshop',compact('list')));
    }
}
