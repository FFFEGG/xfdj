@extends('commodity.common')


@section('content')
    <div class="row-content am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <div class="widget am-cf">
                    <div class="widget-head am-cf">
                        <div class="widget-title  am-cf">
                            供应商审核
                        </div>

                    </div>
                    @if (session('success'))
                        <div class="am-alert am-alert-success" data-am-alert>
                            <button type="button" class="am-close">&times;</button>{{ session('success')  }}  </div>
                    @endif
                    <div class="widget-body  am-fr">

                        <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                            <div class="am-form-group">
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs">

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="am-u-sm-12">
                            <table width="100%" class="am-table">
                                <thead>
                                <tr>
                                    <th>公司名称</th>
                                    <th>联系人电话</th>
                                    <th>资质类型</th>
                                    <th>提交时间</th>
                                    <th>操作</th>
                                    <th>账号状态</th>
                                    <th>审核</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $v)
                                    <tr class="gradeX">
                                        <td>{{ $v->name }}</td>
                                        <td>{{ $v->tel }}</td>
                                        <td>{{ \App\GysType::find($v->type )['name']  }}</td>
                                        <td>
                                            {{ $v->created_at }}
                                        </td>
                                        <td>
                                            <a href="/commodity/gysgoodslist/{{ $v->id }}">查看ta发布的产品</a>
                                        </td>
                                        <td>
                                            @if($v->status == 0)
                                                审核中
                                            @endif
                                            @if($v->status == 1)
                                                审核通过
                                            @endif
                                            @if($v->status == -1)
                                                账号冻结
                                            @endif
                                        </td>
                                        <td>
                                            <a href="/commodity/gysedit/{{ $v->id }}">审核</a>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- more data -->
                                </tbody>
                            </table>
                        </div>
                        <div class="am-u-lg-12 am-cf">

                            <div class="am-fr">
                                <ul class="am-pagination tpl-pagination">
                                    {{ $list->links() }}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
