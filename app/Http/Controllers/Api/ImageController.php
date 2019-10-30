<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Img;

class ImageController extends Controller
{
    public function getLoginBg()
    {
        $img = env('APP_URL').'/uploads/'.Img::whereType(0)->orderBy('created_at', 'desc')->first()['image'];

        return $this->response->array([
            'code' => 200,
            'data' => $img
        ]);
    }
}
