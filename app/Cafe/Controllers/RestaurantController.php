<?php

namespace App\Cafe\Controllers;

use App\Group;
use App\Restaurant;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RestaurantController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('餐厅')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        // `row`是`body`方法的别名
        $content->row('<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">点击获取经纬度</a>');
        return $content
            ->header('餐厅')
            ->description('修改')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        // `row`是`body`方法的别名
        $content->row('<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">点击获取经纬度</a>');
        return $content
            ->header('餐厅')
            ->description('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Restaurant);

        $grid->id('Id');
        $grid->qsj('起送价');
        $grid->psf('配送费');
        $grid->title('餐厅名称');
        $grid->thumb('图片')->image(env('APP_URL').'/uploads/',100);
        $grid->created_at('创建时间');
        $grid->updated_at('h更新时间');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Restaurant::findOrFail($id));

        $show->id('Id');
        $show->qsj('Qsj');
        $show->psf('Psf');
        $show->longitude('Longitude');
        $show->latitude('Latitude');
        $show->title('Title');
        $show->thumb('Thumb');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Restaurant);
        $form->text('title', '餐厅名称');
        $form->text('address', '餐厅地址');
        $form->image('thumb', '餐厅图片')->uniqueName();
        $form->decimal('qsj', '起送价');
        $form->decimal('psf', '配送费');
        $form->latlong('latitude', 'longitude', '位置')->height(500);
//        $form->text('longitude', '经度')->rules('required');
//        $form->text('latitude', '纬度')->rules('required');

        $form->saved(function (Form $form) {
            $group = Restaurant::find($form->model()->id);
            $findurl = 'http://api.map.baidu.com/geodata/v3/poi/list?ak=CBM0h05b2zbdiZy9a1DsjgdnxNTr6hyl&geotable_id=202730&title='.$group->title;
            $sql = $this->http_get($findurl);
            if (json_decode($sql)->status == 0 && json_decode($sql)->size != 0) {
                return false;
            }
            $url = 'http://api.map.baidu.com/geodata/v3/poi/create';
            $data['title'] = $group->title;
            $data['img'] = env('APP_URL').'/uploads/'.$group->thumb;
            $data['cafe_id'] = $group->id;
            $data['psf'] = $group->psf;
            $data['qsj'] = $group->qsj;
            $data['address'] = $group->address;
            $data['latitude'] = $group->latitude;
            $data['longitude'] = $group->longitude;
            $data['geotable_id'] = 202730;
            $data['coord_type'] = 3;
            $data['ak'] = 'CBM0h05b2zbdiZy9a1DsjgdnxNTr6hyl';
            $rew = $this->post_curls($url,$data);
            if (json_decode($rew)->status != 0) {
                $rew = $this->post_curls($url,$data);
                if (json_decode($rew)->status != 0) {
                    $rew = $this->post_curls($url,$data);
                    if (json_decode($rew)->status != 0) {
                        $rew = $this->post_curls($url,$data);
                    }
                }
            }
        });
        return $form;
    }


    /**
     * POST请求https接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $post [请求的参数]
     * @return  string
     */
    public function post_curls($url, $post)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式

    }

    function http_get($url) {
        $curl = curl_init(); //初始化
        curl_setopt($curl, CURLOPT_URL, $url); //设置抓取的url
        curl_setopt($curl, CURLOPT_HEADER, 0); //设置为0不返回请求头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($curl); //执行命令
        curl_close($curl); //关闭URL请求
        return $data; //返回获得的数据

    }
}
