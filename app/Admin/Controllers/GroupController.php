<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Http\Controllers\Controller;
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
            ->header('社群')
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
            ->header('社群')
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
            ->header('社群')
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

        $grid->id('Id');
        $grid->title('社区名称');
        $grid->address('社区地址');
        $grid->xqname('小区名称');
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->latitude('latitude');
        $grid->longitude('longitude');
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');

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

        $form->text('title', '社区名称');
        $form->text('xqname', '小区名称');
        $form->text('address', '社区地址');
        $form->text('longitude', '经度');
        $form->text('latitude', '纬度');
        return $form;
    }
}
