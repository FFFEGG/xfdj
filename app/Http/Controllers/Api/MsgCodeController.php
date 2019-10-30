<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Msg;
use Illuminate\Http\Request;

class MsgCodeController extends Controller
{
    public function getCodeByTel(Request $request)
    {
        Msg::sendmsg(4,$request->tel);
    }
}
