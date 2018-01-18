@extends('admin.index.index')
@section('title','<?=$title?>')
@section('index')
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-settings font-dark"></i>
                                <span class="caption-subject font-dark sbold uppercase"><?=$title?></span>
                                <a href="<?=$listAction?>" class="small">【返回】</a>
                            </div>
                        </div>

                        <div class="portlet-body form">
                            <form class="form-horizontal" role="form" method="post" action="<?=$addAction?>" id="form" enctype="multipart/form-data">
                                {{csrf_field()}}

                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        <button class="close" data-close="alert"></button>
                                        <span>{{session('error')}}</span>
                                    </div>
                                @endif

                                <div class="form-body">
                                    <?php
                                    foreach($data as $key => $item):
                                    $message = $item['val'] ??'';
                                    $type = $item['type'] ?? '';
                                    $name = $key ??'';
                                    ?>

                                    <?php if ($type == 'text'):?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="<?=$message?>" name="<?=$name?>" value="{{old('<?=$name?>')}}">
                                                @if($errors->has('<?=$name?>'))
                                                    <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    <?php endif;?>

                                    <?php if ($type == 'select'):?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-9">
                                                <select class="form-control" name="<?=$name?>" id="<?=$name?>">
                                                    @foreach (<?=$lowerName?>_<?=$name?>(-1) as $key => $item)
                                                        <option value="{{$key}}" @if($key==(old('<?=$name?>')))selected @endif>{{$item}}</option>
                                                    @endforeach
                                                </select>

                                                @if($errors->has('<?=$name?>'))
                                                    <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                                @endif
                                            </div>
                                        </div>

                                    <?php endif;?>


                                    <?php if ($type == 'img'):?>
                                        <div class="form-group">
                                            <label for="<?=$name?>" class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-9">
                                                <img style="height: 80px;" src="{{getOssImgUrl() . old('<?=$name?>')}}" id="id_<?=$name?>" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="<?=$name?>" class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-7">
                                                <input type="file" id="file_<?=$name?>" name="file_<?=$name?>">
                                                <input type="hidden" id="<?=$name?>" name="<?=$name?>" value="{{old('<?=$name?>')}}">
                                                @if($errors->has('<?=$name?>'))
                                                    <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-2">
                                                <input type="button" value="上传" onclick="uploadPic(this, '<?=$name?>')"/>
                                            </div>
                                        </div>
                                    <?php endif;?>


                                    <?php if ($type == 'datetime'):?>
                                        <div class="form-group">
                                            <label for="dtp_input2" class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-4">
                                                <div class="input-group date form_date col-md-12" >
                                                    <input id="<?=$name?>" class="form-control" name="<?=$name?>"  size="16" type="text" value="{{old('<?=$name?>')}}" readonly>
                                                </div>
                                                <span class="help-block">
                                                @if($errors->has('<?=$name?>'))
                                                        <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                                    @endif
                                            </span>
                                            </div>
                                            <input type="hidden" id="dtp_input2"  value="{{old('<?=$name?>')}}" /><br/>
                                        </div>
                                    <?php endif;?>

                                    <?php if ($type == 'checkbox'):?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"><?=$message?></label>
                                            <div class="col-md-9">
                                                <input type="checkbox" name="<?=$name?>" value="1" @if(1==(old('<?=$name?>')))checked @endif>是
                                                @if($errors->has('<?=$name?>'))
                                                    <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    <?php endif;?>


                                    <?php if ($type == 'radio'):?>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?=$message?></label>
                                        <div class="col-md-9">
                                            <input type="radio"  name="<?=$name?>" value="0" @if(0==(old('<?=$name?>')))checked @endif> 非<?=$message?>
                                            <input type="radio"  name="<?=$name?>" value="1" @if(1==(old('<?=$name?>')))checked @endif> <?=$message?>
                                            @if($errors->has('<?=$name?>'))
                                                <span class="help-block">{{$errors->first('<?=$name?>')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <?php endforeach;?>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <input type="submit" id="btn" class="btn yellow"/>
                                            <button type="reset" class="btn default">取消</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- END SAMPLE FORM PORTLET-->

                </div>
            </div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->

@endsection

@section('script')
    <script src="{!! asset('js/laydate/laydate.js') !!}" type="text/javascript"></script>

    <script type="text/javascript">
        var ossImgUrl = '{{getOssImgUrl()}}';
        var _token = '{{csrf_token()}}';
        var url = '{{url('admin/upload')}}';

        function uploadPic(a,inputId){
            var id = 'id_' + inputId;
            var file = 'file_' + inputId;
            $.ajaxFileUpload({
                url:url,
                type:"post",
                fileElementId:file,
                dataType:'json',
                // Form数据
                data:{file_name:file,'_token':_token},
                success:function(data){
                    console.log(data);
                    if(data.status){
                        $('#'+inputId).val(data.file_name);
                        $('#'+id).attr('src', ossImgUrl + data.file_name);
                    }else{
                        alert(data.msg);
                    }
                }
            });
        }

        <?php
        foreach($data as $key => $item):
            $message = $item['val'] ??'';
            $type = $item['type'] ?? '';
            $name = $key ??'';
            ?>
            <?php if($type == 'datetime'):?>
                var st = laydate.render({
                    elem: '#<?=$name?>',
                    type: 'datetime',
                    format:'yyyy-MM-dd HH:mm:ss',
                    min:'{{date('Y-m-d H:i:s')}}',
                    max:'2099-12-30 59:59:59',
                    calendar: true,
                    change:function (data) {
                        count_days();
                    }
                });
            <?php endif?>

            <?php if ($type == 'checkbox'):?>
            init_<?=$name?>();
            function init_<?=$name?>()
            {
                var timing = $('input[name="<?=$name?>"]').is(':checked');
                if (timing) {
                    $('#<?=$name?>_block').show();
                } else {
                    $('#<?=$name?>_block').hide();
                }
            }
            //定时触发时间
            $('input[name="<?=$name?>"]').change(function(){
                init_<?=$name?>();
            });
            <?php endif?>
        <?endforeach;?>
    </script>
@endsection
