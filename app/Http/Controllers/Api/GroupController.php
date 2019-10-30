<?php
namespace App\Http\Controllers\Api;

use App\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function getGroupList(Request $request)
    {

        $url = 'http://api.map.baidu.com/geosearch/v3/nearby?ak=CBM0h05b2zbdiZy9a1DsjgdnxNTr6hyl&geotable_id=202291&location='.$request->latitude.','.$request->longitude.'&coord_type=3&radius=10000&sortby=distance:1';
        $list = $this->curl_request($url);
        return $list;


        $calcScope = $this->searchByLatAndLng($request->latitude,$request->longitude,5000);

        return $calcScope;
    }


        //参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
    function curl_request($url,$post='',$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }


    /**
     * 根据经纬度和半径计算出范围
     * @param string $lat 纬度
     * @param String $lng 经度
     * @param float $radius 半径
     * @return Array 范围数组
     */
    private function calcScope($lat, $lng, $radius) {
        $degree = (24901*1609)/360.0;
        $dpmLat = 1/$degree;

        $radiusLat = $dpmLat*$radius;
        $minLat = $lat - $radiusLat;       // 最小纬度
        $maxLat = $lat + $radiusLat;       // 最大纬度

        $mpdLng = $degree*cos($lat * (pi()/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng*$radius;
        $minLng = $lng - $radiusLng;      // 最小经度
        $maxLng = $lng + $radiusLng;      // 最大经度

        /** 返回范围数组 */
        $scope = array(
            'minLat'    =>  $minLat,
            'maxLat'    =>  $maxLat,
            'minLng'    =>  $minLng,
            'maxLng'    =>  $maxLng
        );
        return $scope;
    }

    /**
     * 根据经纬度和半径查询在此范围内的所有的电站
     * @param  String $lat    纬度
     * @param  String $lng    经度
     * @param  float $radius 半径
     * @return Array         计算出来的结果
     */
    public function searchByLatAndLng($lat, $lng, $radius) {
        $scope = $this->calcScope($lat, $lng, $radius);     // 调用范围计算函数，获取最大最小经纬度

//        $list = Group::whereBetween('latitude',[$scope['minLat'],$scope['maxLat']])
//            ->whereBetween('longitude',[$scope['minLng'],$scope['maxLng']])
//            ->get();

        $list = Group::get();



        $list = $list->map(function ($v) use ($lat,$lng) {
           return [
               'id' => $v->id,
               'title' => $v->xqname,
               'address' => $v->address.'-'.$v->title,
               'distance' => round($this->calcDistance($lat,$lng,$v->latitude,$v->longitude),2)
           ];
        });
        return $this->response->array([
            'list' => $list->sortBy('distance')->values()->all()
        ]);
    }


    /**
     * 获取两个经纬度之间的距离
     * @param  string $lat1 纬一
     * @param  String $lng1 经一
     * @param  String $lat2 纬二
     * @param  String $lng2 经二
     * @return float  返回两点之间的距离
     */
    public function calcDistance($lat1, $lng1, $lat2, $lng2) {
        /** 转换数据类型为 double */
        $lat1 = doubleval($lat1);
        $lng1 = doubleval($lng1);
        $lat2 = doubleval($lat2);
        $lng2 = doubleval($lng2);
        /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344);
    }
}
