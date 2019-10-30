<?php


namespace App\Http\Controllers;


class MapController extends Controller
{
    public function index()
    {
        if ($this->is_weixin()) {
            return view('ps.wx_login');
        } else {
            return view('map.index');
        }
    }

    function is_weixin()
    {

        if (strpos($_SERVER['HTTP_USER_AGENT'],

                'MicroMessenger') !== false) {

            return true;

        }

        return false;

    }

}
