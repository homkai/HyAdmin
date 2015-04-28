<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if IE 8]> <html lang="zh-CN" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="zh-CN" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh-CN">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php if(empty($pageTitle)): echo S('SITE_ADMIN_TITLE'); else: echo ($pageTitle); ?> - <?php echo S('SITE_ADMIN_TITLE'); endif; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="<?php echo S('SITE_ADMIN_DESCRIPTION');?>" name="description"/>
<meta content="homyit.cn" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="/HyFrame/Public/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->

<?php if(is_array($_assets["GLOBAL"]["CSS"])): foreach($_assets["GLOBAL"]["CSS"] as $key=>$item): ?><link href="_PUBLIC__/assets/global/styles/<?php echo ($item); ?>" rel="stylesheet" type="text/css"/><?php endforeach; endif; ?>
<?php if(is_array($_assets["PLUGINS"]["CSS"])): foreach($_assets["PLUGINS"]["CSS"] as $key=>$item): ?><link href="/HyFrame/Public/assets/global/plugins/<?php echo ($item); ?>" rel="stylesheet" type="text/css"/><?php endforeach; endif; ?>
<?php if(is_array($_assets["PAGES"]["CSS"])): foreach($_assets["PAGES"]["CSS"] as $key=>$item): ?><link href="/HyFrame/Public/assets/pages/styles/<?php echo ($item); ?>" rel="stylesheet" type="text/css"/><?php endforeach; endif; ?>

<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="/HyFrame/Public/assets/global/styles/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/styles/plugins.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/layout/styles/layout.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/layout/styles/themes/blue.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/styles/hyframe.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-sidebar-closed-hide-logo">
<?php echo ($_developers); ?>
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="index.html">
            <img src="/HyFrame/Public/assets/layout/img/logo-default.png" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN HEADER SEARCH BOX -->
            <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
            <form class="hy-search-form search-form search-form-expanded" action="extra_search.html" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." name="query">
                    <span class="input-group-btn">
                    <a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
                    </span>
                </div>
            </form>
            <!-- END HEADER SEARCH BOX -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-bell"></i>
                        <span class="badge badge-default">
                        <?php echo count($hyAlerts);?> </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3><span class="bold"><?php echo count($hyAlerts);?></span> 条待处理的信息</h3>
                                <a href="<?php echo U('System/HyAlert/all');?>">查看全部</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                    <?php if(empty($hyAlerts)): ?><li style="padding:10px;">暂无未读消息！</li><?php endif; ?>
                                    <?php if(is_array($hyAlerts)): $i = 0; $__LIST__ = $hyAlerts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                        <?php if(!empty($v['url'])): ?><a href="<?php echo U($v['url']);?>"><?php else: ?><a href="javascript:;"><?php endif; ?>
                                            <span class="time">
                                                <?php echo (to_time($v["create_time"],5)); ?>
                                            </span>
                                            <span class="details">
                                                <?php $arr=explode(',',$v['icon']); $icon=$arr[1]; $label=$arr[0]?:'label-success'; ?>
                                                <span class="label label-sm label-icon <?php echo ($label); ?>">
                                                <i class="fa <?php echo ($icon); ?> fa-fw"></i>
                                                </span>
                                                                                                                            【<?php echo ($v["category"]); ?>】<?php echo ($v["message"]); ?>                                                                         
                                            </span>
                                        </a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN INBOX DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-envelope-open"></i>
                        <span class="badge badge-default">
                        1 </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>你有 <span class="bold">1</span> 封未读邮件！</h3>
                                <a href="page_inbox.html">查看全部</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                    <li>
                                        <a href="inbox.html?a=view">
                                        <span class="photo">
                                            <img src="/HyFrame/Public/assets/layout/img/avatar2.jpg" class="img-circle" alt="">
                                        </span>
                                        <span class="subject">
	                                        <span class="from">宏奕</span>
	                                        <span class="time">刚刚</span>
                                        </span>
                                        <span class="message">TODO...  TODO...  TODO...  </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->                    
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img alt="" class="img-circle hide1" src="<?php echo session('avatarFile');?>"/>
                            <span class="username username-hide-on-mobile">
                            <?php echo session('userName');?>（<?php echo session('roleTitle');?>）</span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo U('System/User/profile');?>">
                                <i class="icon-user"></i> 个人信息</a>
                            </li>
                            <?php $roleSwitch=session('roleSwitch'); ?>
                            <?php if(is_array($roleSwitch)): $i = 0; $__LIST__ = array_slice($roleSwitch,1,null,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                                <a href="<?php echo U('System/HyStart/switchs',array('role'=>$key));?>">
                                <i class="icon-settings"></i> 切换到 - <?php echo ($v); ?></a>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo U('System/HyStart/logout');?>">
                                <i class="icon-logout"></i> 退出系统 </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container hy-page-<?php echo strtolower(CONTROLLER_NAME);?>">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <ul id="hy-sidebar-menu" class="page-sidebar-menu page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <h3 class="page-title">
                HyFrame数据管理框架&nbsp;<small>简洁、流畅、安全、自适应移动设备</small>
            </h3>
            <div class="page-bar">
                
<ul class="page-breadcrumb">
	<li><i class="fa fa-home"></i> <a href="/HyFrame">首页</a> <i class="fa fa-angle-right"></i></li>
	<li><a href="javascript:;">欢迎中心</a></li>
</ul>

                
            </div>
            <div id="hy-alert-wrapper"></div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            
<!-- BEGIN DASHBOARD STATS -->
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue-soft" href="javascript:;">
        <div class="visual">
            <i class="fa fa-comments"></i>
        </div>
        <div class="details">
            <div class="number">
                 1349
            </div>
            <div class="desc">
                 New Feedbacks
            </div>
        </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red-soft" href="javascript:;">
        <div class="visual">
            <i class="fa fa-trophy"></i>
        </div>
        <div class="details">
            <div class="number">
                 12,5M$
            </div>
            <div class="desc">
                 Total Profit
            </div>
        </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green-soft" href="javascript:;">
        <div class="visual">
            <i class="fa fa-shopping-cart"></i>
        </div>
        <div class="details">
            <div class="number">
                 549
            </div>
            <div class="desc">
                 New Orders
            </div>
        </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light purple-soft" href="javascript:;">
        <div class="visual">
            <i class="fa fa-globe"></i>
        </div>
        <div class="details">
            <div class="number">
                 +89%
            </div>
            <div class="desc">
                 Brand Popularity
            </div>
        </div>
        </a>
    </div>
</div>
<!-- END DASHBOARD STATS -->
<div class="clearfix">
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
   <div class="portlet light ">
       <div class="portlet-title">
           <div class="caption">
               <i class="icon-bar-chart font-green-sharp hide"></i>
               <span class="caption-subject font-green-sharp bold uppercase">Site Visits</span>
               <span class="caption-helper">weekly stats...</span>
           </div>
           <div class="actions">
               <div class="btn-group btn-group-devided" data-toggle="buttons">
                   <label class="btn btn-transparent grey-salsa btn-circle btn-sm active">
                   <input type="radio" name="options" class="toggle" id="option1">New</label>
                   <label class="btn btn-transparent grey-salsa btn-circle btn-sm">
                   <input type="radio" name="options" class="toggle" id="option2">Returning</label>
               </div>
           </div>
       </div>
       <div class="portlet-body">
           <div id="site_statistics_loading">
               <img src="/HyFrame/Public/assets/admin/layout/img/loading.gif" alt="loading"/>
           </div>
           <div id="site_statistics_content" class="display-none">
               <div id="site_statistics" class="chart">
               </div>
           </div>
       </div>
   </div>
   <!-- END PORTLET-->
</div>
<div class="col-md-6 col-sm-6">
    <!-- BEGIN PORTLET-->
   <div class="portlet light ">
       <div class="portlet-title">
           <div class="caption">
               <i class="icon-share font-red-sunglo hide"></i>
               <span class="caption-subject font-red-sunglo bold uppercase">Revenue</span>
               <span class="caption-helper">monthly stats...</span>
           </div>
           <div class="actions">
               <div class="btn-group">
                   <a href="" class="btn grey-salsa btn-circle btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                   Filter Range&nbsp;<span class="fa fa-angle-down">
                   </span>
                   </a>
                   <ul class="dropdown-menu pull-right">
                       <li>
                           <a href="javascript:;">
                           Q1 2014 <span class="label label-sm label-default">
                           past </span>
                           </a>
                       </li>
                       <li>
                           <a href="javascript:;">
                           Q2 2014 <span class="label label-sm label-default">
                           past </span>
                           </a>
                       </li>
                       <li class="active">
                           <a href="javascript:;">
                           Q3 2014 <span class="label label-sm label-success">
                           current </span>
                           </a>
                       </li>
                       <li>
                           <a href="javascript:;">
                           Q4 2014 <span class="label label-sm label-warning">
                           upcoming </span>
                           </a>
                       </li>
                   </ul>
               </div>
           </div>
       </div>
       <div class="portlet-body">
           <div id="site_activities_loading">
               <img src="/HyFrame/Public/assets/admin/layout/img/loading.gif" alt="loading"/>
           </div>
           <div id="site_activities_content" class="display-none">
               <div id="site_activities" style="height: 228px;">
               </div>
           </div>
           <div style="margin: 20px 0 10px 30px">
               <div class="row">
                   <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                       <span class="label label-sm label-success">
                       Revenue: </span>
                       <h3>$13,234</h3>
                   </div>
                   <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                       <span class="label label-sm label-danger">
                       Shipment: </span>
                       <h3>$1,134</h3>
                   </div>
                   <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                       <span class="label label-sm label-primary">
                       Orders: </span>
                       <h3>235090</h3>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!-- END PORTLET-->
    </div>
</div>
<div class="clearfix">
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-blue-steel hide"></i>
                    <span class="caption-subject font-blue-steel bold uppercase">Recent Activities</span>
                </div>
                <div class="actions">
                    <div class="btn-group">
                        <a class="btn btn-sm btn-default btn-circle" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        Filter By <i class="fa fa-angle-down"></i>
                        </a>
                        <div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
                            <label><input type="checkbox"/> Finance</label>
                            <label><input type="checkbox" checked=""/> Membership</label>
                            <label><input type="checkbox"/> Customer Support</label>
                            <label><input type="checkbox" checked=""/> HR</label>
                            <label><input type="checkbox"/> System</label>
                        </div>
                    </div>
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
                <ul class="feeds">
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-info">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 4 pending tasks. <span class="label label-sm label-warning ">
                                        Take action <i class="fa fa-share"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 Just now
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:;">
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-success">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         Finance Report for year 2013 has been released.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 20 mins
                            </div>
                        </div>
                        </a>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-danger">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 5 pending membership that requires a quick review.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 24 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-info">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         New order received with <span class="label label-sm label-success">
                                        Reference Number: DR23923 </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 30 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-success">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 5 pending membership that requires a quick review.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 24 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-default">
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
                                        Overdue </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 2 hours
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:;">
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-default">
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         IPO Report for year 2013 has been released.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 20 mins
                            </div>
                        </div>
                        </a>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-info">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 4 pending tasks. <span class="label label-sm label-warning ">
                                        Take action <i class="fa fa-share"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 Just now
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:;">
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-danger">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         Finance Report for year 2013 has been released.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 20 mins
                            </div>
                        </div>
                        </a>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-default">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 5 pending membership that requires a quick review.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 24 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-info">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         New order received with <span class="label label-sm label-success">
                                        Reference Number: DR23923 </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 30 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-success">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         You have 5 pending membership that requires a quick review.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 24 mins
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-warning">
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
                                        Overdue </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 2 hours
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:;">
                        <div class="col1">
                            <div class="cont">
                                <div class="cont-col1">
                                    <div class="label label-sm label-info">
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="cont-col2">
                                    <div class="desc">
                                         IPO Report for year 2013 has been released.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col2">
                            <div class="date">
                                 20 mins
                            </div>
                        </div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="scroller-footer">
                <div class="btn-arrow-link pull-right">
                    <a href="javascript:;">See All Records</a>
                    <i class="icon-arrow-right"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6 col-sm-6">
    <div class="portlet light tasks-widget">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-green-haze hide"></i>
                <span class="caption-subject font-green-haze bold uppercase">Tasks</span>
                <span class="caption-helper">tasks summary...</span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <a class="btn green-haze btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    More <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:;">
                            <i class="i"></i> All Project </a>
                        </li>
                        <li class="divider">
                        </li>
                        <li>
                            <a href="javascript:;">
                            AirAsia </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                            Cruise </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                            HSBC </a>
                        </li>
                        <li class="divider">
                        </li>
                        <li>
                            <a href="javascript:;">
                            Pending <span class="badge badge-danger">
                            4 </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                            Completed <span class="badge badge-success">
                            12 </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;">
                            Overdue <span class="badge badge-warning">
                            9 </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;">
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="task-content">
                <div class="scroller" style="height: 305px;" data-always-visible="1" data-rail-visible1="1">
                   <!-- START TASK LIST -->
                   <ul class="task-list">
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Present 2013 Year IPO Statistics at Board Meeting </span>
                               <span class="label label-sm label-success">Company</span>
                               <span class="task-bell">
                               <i class="fa fa-bell-o"></i>
                               </span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Hold An Interview for Marketing Manager Position </span>
                               <span class="label label-sm label-danger">Marketing</span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               AirAsia Intranet System Project Internal Meeting </span>
                               <span class="label label-sm label-success">AirAsia</span>
                               <span class="task-bell">
                               <i class="fa fa-bell-o"></i>
                               </span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Technical Management Meeting </span>
                               <span class="label label-sm label-warning">Company</span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Kick-off Company CRM Mobile App Development </span>
                               <span class="label label-sm label-info">Internal Products</span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Prepare Commercial Offer For SmartVision Website Rewamp </span>
                               <span class="label label-sm label-danger">SmartVision</span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Sign-Off The Comercial Agreement With AutoSmart </span>
                               <span class="label label-sm label-default">AutoSmart</span>
                               <span class="task-bell">
                               <i class="fa fa-bell-o"></i>
                               </span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li>
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               Company Staff Meeting </span>
                               <span class="label label-sm label-success">Cruise</span>
                               <span class="task-bell">
                               <i class="fa fa-bell-o"></i>
                               </span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                       <li class="last-line">
                           <div class="task-checkbox">
                               <input type="checkbox" class="liChild" value=""/>
                           </div>
                           <div class="task-title">
                               <span class="task-title-sp">
                               KeenThemes Investment Discussion </span>
                               <span class="label label-sm label-warning">KeenThemes </span>
                           </div>
                           <div class="task-config">
                               <div class="task-config-btn btn-group">
                                   <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                   <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                   </a>
                                   <ul class="dropdown-menu pull-right">
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-check"></i> Complete </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-pencil"></i> Edit </a>
                                       </li>
                                       <li>
                                           <a href="javascript:;">
                                           <i class="fa fa-trash-o"></i> Cancel </a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                       </li>
                   </ul>
                   <!-- END START TASK LIST -->
                    </div>
                </div>
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-cursor font-purple-intense hide"></i>
                    <span class="caption-subject font-purple-intense bold uppercase">General Stats</span>
                </div>
                <div class="actions">
                    <a href="javascript:;" class="btn btn-sm btn-circle btn-default easy-pie-chart-reload">
                    <i class="fa fa-repeat"></i> Reload </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number transactions" data-percent="55">
                                <span>
                                +55 </span>
                                %
                            </div>
                            <a class="title" href="javascript:;">
                            Transactions <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number visits" data-percent="85">
                                <span>
                                +85 </span>
                                %
                            </div>
                            <a class="title" href="javascript:;">
                            New Visits <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number bounce" data-percent="46">
                                <span>
                                -46 </span>
                                %
                            </div>
                            <a class="title" href="javascript:;">
                            Bounce <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-equalizer font-purple-plum hide"></i>
                    <span class="caption-subject font-red-sunglo bold uppercase">Server Stats</span>
                    <span class="caption-helper">monthly stats...</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                    <a href="" class="reload">
                    </a>
                    <a href="" class="remove">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_bar"></div>
                            <a class="title" href="javascript:;">
                            Network <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_bar2"></div>
                            <a class="title" href="javascript:;">
                            CPU Load <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_line"></div>
                            <a class="title" href="javascript:;">
                            Load Rate <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN REGIONAL STATS PORTLET-->
   <div class="portlet light ">
       <div class="portlet-title">
           <div class="caption">
               <i class="icon-share font-red-sunglo"></i>
               <span class="caption-subject font-red-sunglo bold uppercase">Regional Stats</span>
           </div>
           <div class="actions">
               <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
               <i class="icon-cloud-upload"></i>
               </a>
               <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
               <i class="icon-wrench"></i>
               </a>
               <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;">
               </a>
               <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
               <i class="icon-trash"></i>
               </a>
           </div>
       </div>
       <div class="portlet-body">
           <div id="region_statistics_loading">
               <img src="/HyFrame/Public/assets/admin/layout/img/loading.gif" alt="loading"/>
           </div>
           <div id="region_statistics_content" class="display-none">
               <div class="btn-toolbar margin-bottom-10">
                   <div class="btn-group btn-group-circle" data-toggle="buttons">
                       <a href="" class="btn grey-salsa btn-sm active">
                       Users </a>
                       <a href="" class="btn grey-salsa btn-sm">
                       Orders </a>
                   </div>
                   <div class="btn-group pull-right">
                       <a href="" class="btn btn-circle grey-salsa btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                       Select Region <span class="fa fa-angle-down">
                       </span>
                       </a>
                       <ul class="dropdown-menu pull-right">
                           <li>
                               <a href="javascript:;" id="regional_stat_world">
                               World </a>
                           </li>
                           <li>
                               <a href="javascript:;" id="regional_stat_usa">
                               USA </a>
                           </li>
                           <li>
                               <a href="javascript:;" id="regional_stat_europe">
                               Europe </a>
                           </li>
                           <li>
                               <a href="javascript:;" id="regional_stat_russia">
                               Russia </a>
                           </li>
                           <li>
                               <a href="javascript:;" id="regional_stat_germany">
                               Germany </a>
                           </li>
                       </ul>
                   </div>
               </div>
               <div id="vmap_world" class="vmaps display-none">
               </div>
               <div id="vmap_usa" class="vmaps display-none">
               </div>
               <div id="vmap_europe" class="vmaps display-none">
               </div>
               <div id="vmap_russia" class="vmaps display-none">
               </div>
               <div id="vmap_germany" class="vmaps display-none">
               </div>
           </div>
       </div>
   </div>
   <!-- END REGIONAL STATS PORTLET-->
</div>
<div class="col-md-6 col-sm-6">
    <!-- BEGIN PORTLET-->
   <div class="portlet light">
       <div class="portlet-title tabbable-line">
           <div class="caption">
               <i class="icon-globe font-green-sharp"></i>
               <span class="caption-subject font-green-sharp bold uppercase">Feeds</span>
           </div>
           <ul class="nav nav-tabs">
               <li class="active">
                   <a href="#tab_1_1" data-toggle="tab">
                   System </a>
               </li>
               <li>
                   <a href="#tab_1_2" data-toggle="tab">
                   Activities </a>
               </li>
               <li>
                   <a href="#tab_1_3" data-toggle="tab">
                   Recent Users </a>
               </li>
           </ul>
       </div>
       <div class="portlet-body">
           <!--BEGIN TABS-->
           <div class="tab-content">
               <div class="tab-pane active" id="tab_1_1">
                   <div class="scroller" style="height: 339px;" data-always-visible="1" data-rail-visible="0">
                       <ul class="feeds">
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                You have 4 pending tasks. <span class="label label-sm label-info">
                                               Take action <i class="fa fa-share"></i>
                                               </span>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New version v1.4 just lunched!
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        20 mins
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-danger">
                                               <i class="fa fa-bolt"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                Database server #12 overloaded. Please fix the issue.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        24 mins
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        30 mins
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        40 mins
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-warning">
                                               <i class="fa fa-plus"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        1.5 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
                                               Overdue </span>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        2 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-default">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        3 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-warning">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        5 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        18 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-default">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        21 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        22 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-default">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        21 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        22 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-default">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        21 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        22 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-default">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        21 hours
                                   </div>
                               </div>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-info">
                                               <i class="fa fa-bullhorn"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received. Please take care of it.
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        22 hours
                                   </div>
                               </div>
                           </li>
                       </ul>
                   </div>
               </div>
               <div class="tab-pane" id="tab_1_2">
                   <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                       <ul class="feeds">
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New order received
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        10 mins
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-danger">
                                               <i class="fa fa-bolt"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                Order #24DOP4 has been rejected. <span class="label label-sm label-danger ">
                                               Take action <i class="fa fa-share"></i>
                                               </span>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        24 mins
                                   </div>
                               </div>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                           <li>
                               <a href="javascript:;">
                               <div class="col1">
                                   <div class="cont">
                                       <div class="cont-col1">
                                           <div class="label label-sm label-success">
                                               <i class="fa fa-bell-o"></i>
                                           </div>
                                       </div>
                                       <div class="cont-col2">
                                           <div class="desc">
                                                New user registered
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col2">
                                   <div class="date">
                                        Just now
                                   </div>
                               </div>
                               </a>
                           </li>
                       </ul>
                   </div>
               </div>
               <div class="tab-pane" id="tab_1_3">
                   <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Robert Nilson </a>
                                       <span class="label label-sm label-success label-mini">
                                       Approved </span>
                                   </div>
                                   <div>
                                        29 Jan 2013 10:45AM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 10:45AM
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Eric Kim </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 12:45PM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-danger">
                                       In progress </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 11:55PM
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Eric Kim </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 12:45PM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-danger">
                                       In progress </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 11:55PM
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Eric Kim </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 12:45PM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-danger">
                                       In progress </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 11:55PM
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Eric Kim </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 12:45PM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-danger">
                                       In progress </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 11:55PM
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Eric Kim </a>
                                       <span class="label label-sm label-info">
                                       Pending </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 12:45PM
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6 user-info">
                               <img alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar.png" class="img-responsive"/>
                               <div class="details">
                                   <div>
                                       <a href="javascript:;">
                                       Lisa Miller </a>
                                       <span class="label label-sm label-danger">
                                       In progress </span>
                                   </div>
                                   <div>
                                        19 Jan 2013 11:55PM
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <!--END TABS-->
       </div>
   </div>
   <!-- END PORTLET-->
    </div>
</div>
<div class="clearfix">
</div>
<div class="row">
<div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
   <div class="portlet light calendar ">
       <div class="portlet-title ">
           <div class="caption">
               <i class="icon-calendar font-green-sharp"></i>
               <span class="caption-subject font-green-sharp bold uppercase">Calendar</span>
           </div>
       </div>
       <div class="portlet-body">
           <div id="calendar">
           </div>
       </div>
   </div>
   <!-- END PORTLET-->
</div>
<div class="col-md-6 col-sm-6">
    <!-- BEGIN PORTLET-->
   <div class="portlet light ">
       <div class="portlet-title">
           <div class="caption">
               <i class="icon-bubble font-red-sunglo"></i>
               <span class="caption-subject font-red-sunglo bold uppercase">Chats</span>
           </div>
           <div class="inputs">
               <div class="portlet-input input-inline input-small">
                   <div class="input-icon right">
                       <i class="icon-magnifier"></i>
                       <input type="text" class="form-control input-circle" placeholder="search...">
                   </div>
               </div>
           </div>
       </div>
       <div class="portlet-body" id="chats">
           <div class="scroller" style="height: 353px;" data-always-visible="1" data-rail-visible1="1">
               <ul class="chats">
                   <li class="in">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar1.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Bob Nilson </a>
                           <span class="datetime">
                           at 20:09 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="out">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar2.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Lisa Wong </a>
                           <span class="datetime">
                           at 20:11 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="in">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar1.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Bob Nilson </a>
                           <span class="datetime">
                           at 20:30 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="out">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar3.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Richard Doe </a>
                           <span class="datetime">
                           at 20:33 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="in">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar3.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Richard Doe </a>
                           <span class="datetime">
                           at 20:35 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="out">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar1.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Bob Nilson </a>
                           <span class="datetime">
                           at 20:40 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="in">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar3.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Richard Doe </a>
                           <span class="datetime">
                           at 20:40 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                       </div>
                   </li>
                   <li class="out">
                       <img class="avatar" alt="" src="/HyFrame/Public/assets/admin/layout/img/avatar1.jpg"/>
                       <div class="message">
                           <span class="arrow">
                           </span>
                           <a href="javascript:;" class="name">
                           Bob Nilson </a>
                           <span class="datetime">
                           at 20:54 </span>
                           <span class="body">
                           Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. sed diam nonummy nibh euismod tincidunt ut laoreet. </span>
                       </div>
                   </li>
               </ul>
           </div>
           <div class="chat-form">
               <div class="input-cont">
                   <input class="form-control" type="text" placeholder="Type a message here..."/>
               </div>
               <div class="btn-cont">
                   <span class="arrow">
                   </span>
                   <a href="" class="btn blue icn-only">
                   <i class="fa fa-check icon-white"></i>
                   </a>
               </div>
           </div>
       </div>
   </div>
   <!-- END PORTLET-->
</div>
</div>

            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
         2014-2015 &copy; 宏奕工作室 Homyit Studio
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS -->
<script type="text/javascript">
/* GLOBAL URL */
var _ROOT_ = '/HyFrame',
    _PUBLIC_ = '/HyFrame/Public',
    _INDEX_ = '/HyFrame/index.php',
    _ACTION_ = '/HyFrame/index.php/System/Index/index',
    _MODULE_ = '/HyFrame/index.php/System',
    _CONTROLLER_ = '/HyFrame/index.php/System/Index';
</script>
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/HyFrame/Public/assets/global/plugins/respond.min.js"></script>
<script src="/HyFrame/Public/assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="/HyFrame/Public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/jquery.blockui.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/store+json2.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/crypto.custom.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/bootbox.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->

<?php if(is_array($_assets["GLOBAL"]["JS"])): foreach($_assets["GLOBAL"]["JS"] as $key=>$item): ?><script src="/HyFrame/Public/assets/global/scripts/<?php echo ($item); ?>" type="text/javascript"></script><?php endforeach; endif; ?>
<?php if(is_array($_assets["PLUGINS"]["JS"])): foreach($_assets["PLUGINS"]["JS"] as $key=>$item): ?><script src="/HyFrame/Public/assets/global/plugins/<?php echo ($item); ?>" type="text/javascript"></script><?php endforeach; endif; ?>
<?php if(is_array($_assets["PAGES"]["JS"])): foreach($_assets["PAGES"]["JS"] as $key=>$item): ?><script src="/HyFrame/Public/assets/pages/scripts/<?php echo ($item); ?>" type="text/javascript"></script><?php endforeach; endif; ?>

<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN CORE SCRIPTS -->
<script src="/HyFrame/Public/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/layout/scripts/layout.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/scripts/hyframe.js" type="text/javascript"></script>
<!-- END CORE SCRIPTS -->
<script type="text/javascript">
jQuery(document).ready(function() {
    HyFrame.initMenu(<?php echo ($jsonMenu); ?>);
    Metronic.init();
    Layout.init();
    HyFrame.init();
});
</script>

<script>
$(document).ready(function(){
	//Index.init();
});
</script>

<!-- END JAVASCRIPTS -->
<!-- BEGIN ANALYTICS -->
<div class="hidden">
<script type="text/javascript">
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?41c66ca2d9e89051b8f673b25979b6a0";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</div>
<!-- END ANALYTICS -->
</body>
<!-- END BODY -->
</html>