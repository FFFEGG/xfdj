@extends('commodity.common')


@section('content')
    <div class="row-content am-cf">

        <div class="row">

            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <div class="widget am-cf">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">账户审核</div>
                        <div class="widget-function am-fr">
                            <a href="javascript:;" class="am-icon-cog"></a>
                        </div>
                    </div>
                    <form class="am-form am-form-horizontal" method="post">
                        @csrf
                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">公司名称</label>
                            <div class="am-u-sm-10">
                                <p class="am-form-label am-align-left">{{ $gys->name }}</p>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">联系人电话</label>
                            <div class="am-u-sm-10">
                                <p class="am-form-label am-align-left">{{ $gys->tel }}</p>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">供应商分类</label>
                            <div class="am-u-sm-10">
                                <p class="am-form-label am-align-left">{{ \App\GysType::find($gys->type)->name }}</p>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">营业执照</label>
                            <div class="am-u-sm-10">
                                <div class="am-form-label am-align-left">
                                    <a  target="_blank" href="{{ $gys->file }}"> <img src="{{ $gys->file }}" alt="" width="300" style="padding: 10px;border: 1px #eee solid;border-radius: 3px"></a>
                                </div>
                            </div>
                        </div>


                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">资质证书</label>
                            <div class="am-u-sm-10">
                                <div class="am-form-label am-align-left">
                                    @foreach(explode(',',$gys->hyzz) as $v)
                                    <a  target="_blank" href="{{ $v }}"> <img src="{{ $v }}" alt="" width="300" style="padding: 10px;border: 1px #eee solid;border-radius: 3px"></a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">申请时间</label>
                            <div class="am-u-sm-10">
                                <p class="am-form-label am-align-left">{{ $gys->created_at }}</p>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">审核状态</label>
                            <div class="am-u-sm-10">

                                <div class="am-form-group">
                                    <label class="am-radio-inline">
                                        <input type="radio"  value="0" @if($gys->status == 0) checked @endif name="status"> 审核中
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" value="1"  @if($gys->status == 1) checked @endif name="status"> 审核通过
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" value="-1"  @if($gys->status == -1) checked @endif name="status"> 冻结账户
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div for="doc-ipt-3" class="am-u-sm-2 am-form-label">
                                <p><button type="submit" class="am-btn am-btn-default">提交</button></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
