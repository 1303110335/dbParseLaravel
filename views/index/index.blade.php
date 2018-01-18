<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title','首页')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="{{asset('favicon.ico')}}" rel="shortcut icon">
    @include('admin.index.header');
    @include('UEditor::head')

</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <div class="page-logo">
            <a href="{{url('admin/index')}}">
                <img src="{!! asset('logo.png') !!}" height="20px" alt="logo" class="logo-default" /> </a>
            <div class="menu-toggler sidebar-toggler"> </div>
        </div>
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="username username-hide-on-mobile"> {{username()}} </span>
                        <i  ></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="{{url('admin/reset')}}">
                                <i  ></i>修改密码</a>
                        </li>
                        <li>
                            <a href="{{url('admin/loginOut')}}">
                                <i  ></i>退出登录</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix"> </div>
<div class="page-container">
    <div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
            <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                <li class="sidebar-toggler-wrapper hide">
                    <div class="sidebar-toggler"> </div>
                </li>
                @foreach(menu() as $k => $v)
                    <li class="nav-item">
                        <a href="{{$v['url']}}" class="nav-link nav-toggle">
                            <i class="{{$v['icon']}}" ></i>
                            <span class="title">{{$v['title']}}</span>
                            <span class="selected"></span>
                            <span  ></span>
                        </a>
                        <ul class="sub-menu">
                           @foreach($v['menu'] as $k => $v)
                                <li class="nav-item">
                                    <a href="{{url($v['url'])}}" class="nav-link ">
                                        <i class="{{$v['icon']}}" ></i>
                                        <span class="title">{{$v['title']}}</span>
                                        @if(isset($v['amount']))
                                            <span class="badge {{$v['class']}}">{{$v['amount']}}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@yield('index')
</div>
@include('admin.index.footer');
<!--<script src="../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>-->
<!--<script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>-->
<!-- END THEME LAYOUT SCRIPTS -->                             s
@yield('script')
</body>
<script>
    UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
    UE.Editor.prototype.getActionUrl = function(action) {
        if (action == 'uploadimage' || action == 'uploadscrawl' || action == 'uploadimage') {
            return '{{url('admin/upload/ueditorUpload')}}';
        } else if (action == 'uploadvideo') {
            return '{{url('admin/upload/ueditorUpload')}}';
        } else {
            return this._bkGetActionUrl.call(this, action);
        }
    }
</script>
</html>