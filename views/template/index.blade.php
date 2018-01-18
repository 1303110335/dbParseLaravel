@extends('admin.index.index')
@section('title','<?=$title?>管理')
@section('index')
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN PAGE BAR -->
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="index.html"><?=$title?>管理</a>
                    </li>
                </ul>
            </div>
            <!-- END PAGE BAR -->
            <!-- END PAGE HEADER-->
            <!-- BEGIN DASHBOARD STATS 1-->
            <!-- BEGIN EXAMPLE TABLE PORTLET-->

            <!-- END DASHBOARD STATS 1-->
            <div class="row margin-top-20">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-dark nocap">
                                <i></i>
                                <span class="caption-subject bold uppercase"><?=$title?>管理</span>
                            </div>
                            <form>
                                <div class="form-group">
                                    <div class="col-md-3 margin-bottom-20" style="padding-left: 0">
                                        <div class="input-group select2-bootstrap-append">
                                            <input type="hidden" name="is_delete" value="{{request('is_delete')}}">
                                            <input type="text" class="form-control" placeholder="<?=$message?>"
                                                   name="<?=$code?>" value="{{request('title')}}">
                                            <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit" data-select2-open="multi-append">
                                        <span class="fa fa-search"></span>
                                    </button>
                                </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if(checkAuth('<?=$addAuthUrl?>'))
                                <a href="<?=$addUrl?>" class="btn blue">
                                    <i></i>添加<?=$title?></a>
                            @endif
                            <div class="tools"></div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="delete_all" value="1"/>批量操作</th>
                                    <th>编号</th>
                                    <?php foreach($data as $item):?>
                                    <th><?php echo $item['val'] ?? '';?></th>
                                    <?php endforeach;?>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <th><input type="checkbox" name="delete_ids[]" value="{{$item->id}}"/></th>
                                        <td>{{ $item->id }}</td>
                                        <?php foreach($data as $key => $item):?>
                                        <td><?php echo '{{ $item->' . $key . ' }}';?></td>
                                        <?php endforeach;?>
                                        <td class="text-left">
                                            @if(checkAuth('<?=$editAuthUrl?>'))
                                                <a class="btn btn-xs margin-bottom-5 default" id="edit"
                                                   href="<?=$editUrl?>">编辑</a>
                                            @endif
                                            @if($item->is_delete == 1 && checkAuth("admin/<?=$className?>/recover/{id}") && request('is_delete'))
                                                <a class="btn btn-xs margin-bottom-5 default recover" onclick="operate('{{url("admin/<?=$className?>/recover")}}', '{{$item->id}}')"
                                                   href="javascript:void(0);">恢复</a>
                                            @endif
                                            @if(checkAuth("admin/<?=$className?>/delete/{id}") && !request('is_delete'))
                                                <a class="btn btn-xs margin-bottom-5 default recover" onclick="del('{{url("admin/<?=$className?>/delete")}}', '{{$item->id}}')"
                                                   href="javascript:void(0);">删除</a>
                                            @endif
                                            @if($item->status != 1 && checkAuth('admin/<?=$className?>/up/{id}') && !request('is_delete'))
                                                <a class="btn btn-xs margin-bottom-5 default up"  onclick="operate('{{url("admin/<?=$className?>/up")}}', '{{$item->id}}')"
                                                   href="javascript:void(0);">上架</a>
                                            @endif
                                            @if($item->status == 1 && checkAuth('admin/<?=$className?>/down/{id}') && !request('is_delete'))
                                                <a class="btn btn-xs margin-bottom-5 default down" onclick="operate('{{url("admin/<?=$className?>/down")}}', '{{$item->id}}')"
                                                   href="javascript:void(0);">下架</a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                    <div class="row" style="text-align: right;">
                        {{ $data->links() }}
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                    @if(!request('is_delete'))
                        <a href="{{url("admin/<?=$className?>/?is_delete=1")}}" class="btn blue"><i></i>回收站</a>
                        @if(checkAuth('admin/<?=$className?>/delete_patch'))
                            <a href="javascript:void(0);" id="deleteBtn" class="btn blue"><i></i>批量删除</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->

@endsection


@section('script')
    <script type="text/javascript">
        $('#delete_all').click(function(){
            var isChecked = $(this).is(':checked');
            $('input[name="delete_ids[]"]').prop("checked", isChecked);
        })

        $('#deleteBtn').click(function () {
            var ids = '';

            $.each($('input[name="delete_ids[]"]'), function () {
                if (this.checked) {
                    ids += $(this).val() + ',';
                }
            });

            $.ajax({
                url: "{{url('admin/<?=$className?>/delete_patch')}}",
                type: "post",
                data: {"_token": '{{csrf_token()}}', "ids": ids},
                success: function (msg) {
                    if (msg.code == 1) {
                        alert(msg.message);
                        window.location.reload();
                    } else {
                        alert(msg.message);
                    }
                }
            });
        })

        function del(url, id)
        {
            layer.confirm('确定要删除吗?', {btn: ['确定', '取消']}, function (index) {
                    layer.close(index);
                    var load = layer.load(2,{shade:[0.5,'#000000']});
                    operate(url, id);
                }
            )
        }

        function operate(url, id) {
            $.ajax({
                url: url + "/"+id,
                type: "post",
                data: {"_token":'{{csrf_token()}}'},
                success: function(msg) {
                    if(msg.code == 1){
                        alert(msg.message);
                        window.location.reload();
                    }else{
                        alert(msg.message);
                    }
                }
            });
        }
    </script>
@endsection