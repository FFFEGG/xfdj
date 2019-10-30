<?php

namespace App\Tenancy\Controllers;

use App\Cgy;
use App\Gys;
use App\GysType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use http\Env\Request;
use Illuminate\Support\MessageBag;

class GysController extends Controller
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
            ->header('供应商')
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
    public function edit(\Illuminate\Http\Request $request,$id, Content $content)
    {


        if ($request->isMethod('post')) {
            $gys = Gys::find($request->id);
            $gys->status = $request->status;
            $gys->save();

            $success = new MessageBag([
                'title'   => '操作成功',
                'message' => '修改供应商审核状态',
            ]);

            return redirect('/tenancy/gys')->with(compact('success'));

        }
        $gys = Gys::find($id);

        // 直接渲染视图输出，Since v1.6.12
        $content->view('commodity.admingysedit', ['gys'=>$gys]);
        return $content  ->header('供应商')->description('审核');
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
        $grid = new Grid(new Gys);

        $grid->id('Id');
        $grid->name('公司名称')->display(function ($v){
            return '<a style="color: white;" href="/tenancy/goodslist?&gys_id='.$this->id.'">'.$v.'</a>';
        })->label();
        $grid->tel('联系人电话');
//        $grid->file('营业执照')->display(function ($v){
//            $str = '';
//            foreach (explode(',',$v) as $item){
//                $str.= '<a target="_blank" href="'.env('APP_URL').$item.'"><img src="'.env('APP_URL').$item.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
//            }
//            return $str;
//        });
//        $grid->hyzz('资质证书')->display(function ($v){
//            $str = '';
//            foreach (explode(',',$v) as $item){
//                $str.= '<a target="_blank" href="'.env('APP_URL').$item.'"><img src="'.env('APP_URL').$item.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
//            }
//            return $str;
//        });
        $grid->type('类型')->display(function ($v){
            return GysType::find($v)->name;
        });
       
        $grid->username('用户名称');
        $grid->password('密码');
        $grid->created_at('申请时间');
        $grid->status('账户状态')->display(function ($v){
            if ($v == 0) {
                return '审核中';
            }
            if ($v == 1) {
                return '审核通过';
            }
            if ($v == -1) {
                return '审核通过';
            }
        });
        $grid->column('sh','操作')->display(function (){
            return '<a href="gys/'.$this->id.'/edit">审核</a>';
        });
        $grid->disableActions();
        $grid->disableCreateButton();
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
        $show = new Show(Gys::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->tel('Tel');
        $show->file('File');
        $show->type('Type');
        $show->username('Username');
        $show->password('Password');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        $form = new Form(new Gys);

        $form->text('name', '公司名称')->disable();
        $form->text('tel', '联系人电话')->disable();
//        $form->multipleImage('file', '营业执照')->removable();
//        $form->multipleImage('hyzz', '资质证书')->removable();
        $form->select('type', '供应商类型')->options('/tenancy/getgystype');
        $form->radio('status', '审核状态')->options(
            [
                0=>'审核中',
                1=>'审核通过',
                -1=>'冻结账户',
            ]
        );
        // 在表单提交前调用
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
        return $form;
    }
}
