<?php

namespace App\Marketing\Controllers;

use App\Marketing\Extensions\ThdZcExporter;
use App\ThdZc;
use App\Http\Controllers\Controller;
use App\User;
use App\XsUser;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ThdZcController extends Controller
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
            ->description('审核')
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
        $grid = new Grid(new ThdZc);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('审核编号');
        $grid->u_id('微信昵称')->display(function (){
            return User::find($this->u_id)['nickname'];
        })->style('max-width:50px;word-break:break-all;');
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->shopname('取货点名称')->label();
        $grid->xqname('小区名称');
        $grid->address('地址')->style('max-width:200px;word-break:break-all;');
        $grid->column('ywy','业务员')->display(function (){
            return XsUser::where('u_id',User::find($this->u_id)['p_id'])->first()['name'];
        })->label();
        $grid->msg('备注');
//        $grid->sfzz('身份证正面')->display(function ($v){
//            return '<a target="_blank" href="'.$v.'"><img src="'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
//        });
//        $grid->sfzf('身份证反面')->display(function ($v){
//            return '<a target="_blank" href="'.$v.'"><img src="'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
//        });
//        $grid->sfzsc('手持身份证')->display(function ($v){
//            return '<a target="_blank" href="'.$v.'"><img src="'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
//        });
        $grid->yyzz('营业执照')->display(function ($v){
            if (substr($v,0,5) != 'https') {
                return '<a target="_blank" href="'.env('APP_URL').'/uploads/'.$v.'"><img src="'.env('APP_URL').'/uploads/'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            } else {
                return '<a target="_blank" href="'.$v.'"><img src="'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            }

        });
        $grid->mtz('门头照')->display(function ($v){
            if (substr($v,0,4) != 'https') {
                return '<a target="_blank" href="'.env('APP_URL').'/uploads/'.$v.'"><img src="'.env('APP_URL').'/uploads/'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            } else {
                return '<a target="_blank" href="'.$v.'"><img src="'.$v.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            }
        });
        $grid->is_sh('是否审核通过')->display(function ($v){
            if ($v == 1) {
                return '通过';
            }
            if ($v == -1) {
                return '不通过';
            }
            return '<a href="/mark_admin/thd_pass?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-primary">是</button></a>'
                .'<a href="/mark_admin/thd_close?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-reddit">否</button></a>';
        });
        $grid->created_at('申请时间')->label();

        $grid->disableExport(false);
        $grid->exporter(new ThdZcExporter());

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器

            $filter->scope('dsh', '待审核')->where('is_sh', 0);

            $filter->where(function ($query) {
                $query->whereHas('leader', function ($query) {
                    $query->where('nickname', 'like', "%{$this->input}%");
                });
            }, '微信昵称');
            $filter->like('shopname', '取货点名称');
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
        $show = new Show(ThdZc::findOrFail($id));

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
        $show->yyzz('Yyzz');
        $show->shopname('Shopname');
        $show->xqname('Xqname');
        $show->address('Address');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ThdZc);

        $form->number('u_id', '审核编号');
        $form->text('name', '姓名');
        $form->text('tel', '电话');
        $form->image('yyzz', '营业执照')->uniqueName();
        $form->image('mtz', '门头照')->uniqueName();
        $form->text('shopname', '店铺名称');
        $form->text('xqname', '小区名称');
        $form->text('address', '地址');

        return $form;
    }
}
