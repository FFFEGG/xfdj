<div id="app" class=" ">

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">

                    <form method="post" class="form-horizontal">
                        @csrf
                        <div class="box-body">

                            <div class="fields-group">


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单状态</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control"
                                               style="border: none;font-weight: bold">{{ $order->status==1?'待配送':'已配送' }} </p>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id" value="{{ $order->id }}">
                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            @foreach($order->items as $v)

                                                <div class="row">
                                                    <h5 style="float: left;margin-right: 50px;width: 200px;margin-left: 25px"> {{ $v->goods->title }}</h5>
                                                    <img style="float: left;margin-right: 50px" width="100"
                                                         src="/uploads/{{ $v->goods->pics[0] }}" alt="">
                                                    <h5 style="float: left;margin-right: 50px">数量X {{ $v->num }}</h5>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">取货人信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                姓名：{{ $order->name }} 电话： {{ $order->tel }}
                                            </p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">提货点信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                提货点名称：{{ $order->group->title }}</p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                提货点详细地址：{{ $order->group->address }}</p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                小区：{{ $order->group->xqname }}</p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                负责人姓名：{{ $order->group->name }}</p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                负责人电话：{{ $order->group->tel }}</p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单付款时间</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                {{ $order->paid_at }}
                                            </p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">选择配送人员</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select class="form-control parent_id col-sm-8" name="u_id" id="" >
                                                @foreach(\App\LogPerson::get() as $v)
                                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="box-footer">

                            <div class="col-md-2">
                            </div>

                            <div class="col-md-8">

                                <div class="btn-group pull-left">
                                    <button type="submit" class="btn btn-primary">确认配送</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </section>
</div>
