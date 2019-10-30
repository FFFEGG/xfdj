<?php

namespace App\Marketing\Controllers;

use App\LeaderZc;
use App\Http\Controllers\Controller;
use App\Marketing\Extensions\LeaderExporter;
use App\User;
use App\XsUser;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LeaderZcController extends Controller
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
            ->header('社区代理申请')
            ->description('')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LeaderZc);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->u_id('用户')->display(function (){
            return '<img src="'.User::find($this->u_id)['avatar'].'" width=50/>';
        });
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->sfznum('身份证号码');
        $grid->msg('备注');
        $grid->column('ywy','业务员')->display(function (){
            return XsUser::where('u_id',User::find($this->u_id)['p_id'])->first()['name'];
        })->label();
//        $grid->sfzz('身份证正面')->image(300,100);
//        $grid->sfzf('身份证反面')->image(300,100);
//        $grid->sfzsc('手持身份证')->image(300,100);
        $grid->is_sh('是否审核通过')->display(function ($v){
            if ($v == 1) {
                return '通过';
            }
            if ($v == -1) {
                return '不通过';
            }
            return '<a href="/xfdj_admin/leader_pass?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-primary">是</button></a>'
            .'<br/><a href="/xfdj_admin/leader_close?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-reddit">否</button></a>';
        });
        $grid->updated_at('修改时间');
        $grid->created_at('申请时间');
        $grid->disableExport(false);
        $grid->exporter(new LeaderExporter());
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->between('created_at', '申请时间')->datetime();
            $filter->where(function ($query) {
                $query->whereHas('user.fuser.ywy', function ($query) {
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
        $show = new Show(LeaderZc::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->name('Name');
        $show->tel('Tel');
        $show->sfzz('Sfzz');
        $show->sfzf('Sfzf');
        $show->sfzsc('Sfzsc');
        $show->is_sh('Is sh');
        $show->updated_at('Updated at');
        $show->created_at('Created at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LeaderZc);

        $form->number('u_id', 'U id');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('sfzz', 'Sfzz');
        $form->text('sfzf', 'Sfzf');
        $form->text('sfzsc', 'Sfzsc');
        $form->switch('is_sh', 'Is sh');

        return $form;
    }
}
