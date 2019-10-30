<?php

namespace App\Tenancy\Controllers;

use App\Group;
use App\Http\Controllers\Controller;
use App\User;
use App\XsUser;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GroupController extends Controller
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
            ->header('提货点')
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
        $content->row('<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">点击获取经纬度</a>');
        return $content
            ->header('提货点')
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
            ->header('提货点')
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
        $grid = new Grid(new Group);

        $grid->model()->orderBy('created_at','desc');

        $grid->id('取货点编号');
        $grid->u_id('微信用户昵称')->display(function ($v){
            return User::find($v)->nickname;
        });
        $grid->title('取货点名称')->label();
        $grid->xqname('小区名称');
        $grid->address('社区地址');
        $grid->name('负责人姓名');
        $grid->tel('负责人电话');
        $grid->column('ywy','业务员')->display(function (){
            return XsUser::where('u_id',User::find($this->u_id)['p_id'])->first()['name'];
        })->label();
        $grid->created_at('申请时间')->label();
        $grid->disableExport(false);
        $grid->quickSearch('title');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器

            $filter->where(function ($query) {
                $query->whereHas('leader', function ($query) {
                    $query->where('nickname', 'like', "%{$this->input}%");
                });
            }, '微信昵称');
            $filter->like('title', '取货点名称');
            $filter->like('name', '负责人姓名');
            $filter->like('tel', '负责人电话');
            $filter->between('created_at', '申请时间')->datetime();
            $filter->where(function ($query) {
                $query->whereHas('leader.fuser.ywy', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, '业务员');
        });
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
        $show = new Show(Group::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->address('Address');
        $show->longitude('Longitude');
        $show->latitude('Latitude');
        $show->leader_id('Leader id');
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
        $form = new Form(new Group);
        $form->hidden('id','id');
        $form->text('title', '社区名称');
        $form->text('xqname', '小区名称');
        $form->text('address', '社区地址');
        $form->text('longitude', '经度')->rules('required');
        $form->text('latitude', '纬度')->rules('required');

        $form->saved(function (Form $form) {
            $group = Group::find($form->id);
            $findurl = 'http://api.map.baidu.com/geodata/v3/poi/list?ak=CBM0h05b2zbdiZy9a1DsjgdnxNTr6hyl&geotable_id=202291&title='.$group->title;
            $sql = $this->http_get($findurl);
            if (json_decode($sql)->status == 0 && json_decode($sql)->size != 0) {
                return false;
            }
            $url = 'http://api.map.baidu.com/geodata/v3/poi/create';
            $data['title'] = $group->title;
            $data['group_id'] = $group->id;
            $data['address'] = $group->address;
            $data['latitude'] = $group->latitude;
            $data['longitude'] = $group->longitude;
            $data['coord_type'] = 3;
            $data['geotable_id'] = 202291;
            $data['xqname'] = $group->xqname;
            $data['tel'] = $group->tel;
            $data['name'] = $group->name;
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
