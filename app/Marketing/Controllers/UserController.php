<?php

namespace App\Marketing\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\XsUser;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

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
            ->header('小程序用户')
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
        $grid->model()->withCount(['todaythds','thds','monthds','threemonthds'])->orderBy('todaythds_count','desc')->orderBy('created_at', 'desc');
        $grid->model()->whereIn('user_type', [0, 1, 2, 3]);
        $grid->id('会员编号');
        $grid->column('xm','业务员姓名')->display(function (){
            return XsUser::whereUId($this->id)->first()['name'];
        })->label();
        $grid->nickname('昵称');

//        $grid->gender('性别')->using([0 => '女', 1 => '男'])->label();
        $grid->avatar('头像')->image(50, 50);
//        $grid->phone('电话');
//        $grid->openid('Openid')->label();
        $grid->p_id('上级')->display(function ($v){
            return User::find($v)['avatar'];
        })->image(50,50);
        $grid->todaythds('本日合作提货点')->display(function ($thd) {
            return count($thd);
        })->label();
        $grid->thds('本周')->display(function ($thd) {
            return count($thd);
        });
        $grid->monthds('本月')->display(function ($thd) {
            return count($thd);
        });
        $grid->threemonthds('三个月')->display(function ($thd) {
            return count($thd);
        });
        $grid->toadysqdls('本日社区代理')->display(function ($thd) {
            return count($thd);
        })->label();
        $grid->sqdls('本周')->display(function ($thd) {
            return count($thd);
        });
        $grid->monsqdls('本月')->display(function ($thd) {
            return count($thd);
        });
        $grid->threemonsqdls('三个月')->display(function ($thd) {
            return count($thd);
        });
//        $grid->is_sh('审核状态')->display(function ($v) {
//            if ($this->user_type == 2 || $this->user_type == 0) {
//                return '正常';
//            }
//            return $v == 0 ? '审核中' : '已通过';
//        });

        $grid->user_type('用户类型')->radio([
            0 => '普通用户',
            1 => '社区代理',
            2 => '销售员',
            3 => '提货点',
        ]);

        $grid->filter(function ($filter) {

            // 在这里添加字段过滤器
            $filter->like('nickname', '昵称');
            $filter->scope('ptuser', '普通用户')->where('user_type', 0);
            $filter->scope('xs', '销售员')->where('user_type', 2);
            $filter->scope('sqdl', '社区代理')->where('user_type', 1);
            $filter->where(function ($query) {

                $query->whereHas('ywy', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%")->orWhere('tel', 'like', "%{$this->input}%");
                });

            }, '业务员姓名或电话');
//            $filter->scope('merchant', '社区商户')->where('is_merchant', 1);
        });
//        $grid->column('thd', 'ta的提货点')->display(function () {
//            return '点击查看' . User::where('p_id', $this->id)->orderby('created_at', 'desc')->where('user_type', 3)->get()->count();
//        })->expand(function ($model) {
//
//            $comments = User::where('p_id', $this->id)->orderby('created_at', 'desc')->where('user_type', 3)->get()->map(function ($comment) {
//                return [
//                    'id' => $comment->id,
//                    'avatar' => '<img src="' . $comment->avatar . ' " width=30>',
//                    'nickname' => $comment->nickname,
//                    'zprice' => $comment->zmoney,
//                    'created_at' => $comment->created_at,
//                ];
//            });
//
//            return new Table(['ID', '头像', '昵称', '收益', '注册时间'], $comments->toArray());
//        });
//
//
//        $grid->column('tdzt', 'ta的社区代理')->display(function () {
//            return '点击查看' . User::where('p_id', $this->id)->orderby('created_at', 'desc')->where('user_type', 1)->get()->count();
//        })->expand(function ($model) {
//
//            $commentss = User::where('p_id', $this->id)->orderby('created_at', 'desc')->where('user_type', 1)->get()->map(function ($v) {
//                return [
//                    'id' => $v->id,
//                    'avatar' => '<img src="' . $v->avatar . ' " width=30>',
//                    'nickname' => $v->nickname,
//                    'zprice' => $v->zmoney,
//                    'created_at' => $v->created_at,
//                ];
//            });
//
//            return new Table(['ID', '头像', '昵称', '收益', '注册时间'], $commentss->toArray());
//        });
        $grid->created_at('注册时间');

        $grid->disableActions();


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

        $form->text('nickname', 'Nickname');
        $form->number('gender', 'Gender');
        $form->image('avatar', 'Avatar');
        $form->mobile('phone', 'Phone');
        $form->text('openid', 'Openid');
        $form->text('formid', 'Formid');
        $form->number('p_id', 'P id');
        $form->number('user_type', 'User type');

        return $form;
    }
}
