@extends('commodity.common')


@section('content')
    <div class="row-content am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <div class="widget am-cf">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">{{ $name }}-产品列表</div>
                        <div class="widget-function am-fr">
                            <a href="javascript:;" class="am-icon-cog"></a>
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-padding">
                        @if (session('success'))
                            <div class="am-alert am-alert-success" data-am-alert>
                                <button type="button" class="am-close">&times;</button>{{ session('success')  }}  </div>
                        @endif
                        @foreach($list as $v)
                            <form action="/commodity/goods_status" method="post">
                                @csrf
                                <div class="am-u-sm-4">
                                    <img class="am-thumbnail" src="{{ explode(',',$v->image)[0] }}" alt=""
                                         style="width: 100%;height: 300px"/>
                                    <h5 style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">{{ $v->title }}</h5>

                                    <div class="am-form-group">
                                        <label class="am-radio-inline">
                                            <input type="radio" name="is_pass"  value="0" @if($v->is_pass == 0) checked @endif name="is_pass"> 审核中
                                        </label>
                                        <label class="am-radio-inline">
                                            <input type="radio" name="is_pass"   value="1"  @if($v->is_pass == 1) checked @endif name="is_pass"> 审核通过
                                        </label>
                                    </div>
                                    <input type="hidden" name="id" value="{{ $v->id }}">
                                    <p><button type="submit" class="am-btn am-btn-default">提交</button></p>
                                </div>
                            </form>

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
