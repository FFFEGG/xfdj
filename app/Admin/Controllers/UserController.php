<?php

namespace App\Admin\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
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
            ->header('用户')
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
        return $content
            ->header('用户')
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
        return $content
            ->header('用户')
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

        $grid = new Grid(new User);
        $grid->header(function ($query) {

            $gender = [
                'f' =>User::where('gender',0)->count(),
                'm' =>User::where('gender',1)->count(),
                '' =>User::where('gender','')->count(),
            ];

            $types = [
                'm' =>User::where('user_type',0)->count(),
                'f' =>User::where('user_type',1)->count(),
                '' =>User::where('user_type',2)->count(),
            ];
            $doughnut = view('admin.chart.gender', compact('gender'));
            $line = view('admin.chart.line', compact('types'));
            $nums = view('admin.chart.nums');
            $row = new Row();
            $row->column(6,new Box('用户分布比例', $line));
            $row->column(6,new Box('性别比例', $doughnut));
//            $row->column(4,new Box('每日新增用户', $nums));
            return $row;

        });

        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->nickname('昵称');
        $grid->gender('性别')->using([0 => '女', 1 => '男'])->label();
        $grid->avatar('头像')->image(50,50);
        $grid->phone('电话');
        $grid->openid('Openid')->label();
//        $grid->formid('Formid');
        $grid->p_id('上级')->display(function ($v){
            return User::find($v)['avatar'];
        })->image(50,50);
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        $grid->is_sh('审核状态')->display(function ($v){
            if ($this->user_type == 2 || $this->user_type==0) {
                return '正常';
            }
            return $v == 0? '审核中':'已通过';
        });
        $grid->user_type('用户类型')->radio([
            0 => '普通用户',
            1 => '社区代理',
            2 => '销售员',
            3 => '提货点',
        ]);
        $grid->filter(function($filter){

            // 在这里添加字段过滤器
            $filter->like('nickname', '昵称');
            $filter->scope('ptuser', '普通用户')->where('user_type', 0);
            $filter->scope('tz', '社区代理')->where('user_type', 1);
            $filter->scope('xs', '销售员')->where('user_type', 2);
            $filter->scope('thd', '提货点')->where('user_type', 3);
            $filter->equal('p_id','提货点')->select('/admin/getxsuser');
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
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->nickname('Nickname');
        $show->gender('Gender');
        $show->avatar('Avatar');
        $show->phone('Phone');
        $show->openid('Openid');
        $show->formid('Formid');
        $show->p_id('P id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->user_type('User type');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);


        $form->select('p_id', '修改上级')->options('/admin/getxsuser');
        $form->radio('user_type','用户角色')->options([
           0=>'普通用户',
           1=>'社区代理',
           2=>'销售人员',
           3=>'提货点',
        ]);
        return $form;
    }
}
