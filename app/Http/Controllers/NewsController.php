<?php
namespace App\Http\Controllers;
use App\News;
class NewsController extends Controller
{
    public function data(News $news)
    {
       return view('home.news',compact('news'));
    }


    public function cjwt()
    {
        return view('home.cjwt');
    }

    public function qrcode()
    {
        return view('home.qrcode');
    }
}
