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

<link href="/HyFrame/Public/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.css" rel="stylesheet" type="text/css"/>
<link href="/HyFrame/Public/assets/global/plugins/jquery-fileapi/css/jquery-fileapi.custom.css" rel="stylesheet" type="text/css"/>

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
                
<?php echo ($pageTitle); ?>&nbsp;<small id="hy-subtitle"></small>

            </h3>
            <div class="page-bar">
                
<ul class="page-breadcrumb" id="hy-breadcrumb"></ul>

                
<div class="page-toolbar">
	<button type="button" class="btn btn-fit-height grey-salt hy-refresh">
		<i class="fa fa-refresh"></i><span class="hidden-480">&nbsp;刷新</span>
	</button>
</div>

            </div>
            <div id="hy-alert-wrapper"></div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            
<?php echo ($_middle); ?>
<div id="hy-all-container">
	<div class="portlet light hy-table-container">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-table font-green-sharp fa-fw"></i>
	            <span class="caption-subject font-green-sharp bold"><span class="hy-title">&nbsp;&nbsp;&nbsp;&nbsp;</span>列表</span>
	            <span class="caption-helper"></span>
	        </div>
	        <div class="dt-top-actions pull-right"></div>
	    </div>
	    <div class="portlet-body">
	        <table class="table table-striped table-bordered table-hover dt-responsive"  cellspacing="0" width="100%">
	        </table>
	    </div>
	</div>
	<div class="modal fade hy-form-modal" data-backdrop="static"></div>
	<div class="modal fade hy-detail-modal bs-modal-lg" data-backdrop="true"></div>
</div>
<?php echo ($_bottom); ?>

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
    _ACTION_ = '/HyFrame/index.php/System/HyNotice/all',
    _MODULE_ = '/HyFrame/index.php/System',
    _CONTROLLER_ = '/HyFrame/index.php/System/HyNotice';
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

<script src="/HyFrame/Public/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/jquery-validation/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">window.FileAPI = {staticPath: _PUBLIC_+'/assets/global/plugins/jquery-fileapi/FileAPI/'};</script>
<script src="/HyFrame/Public/assets/global/plugins/jquery-fileapi/FileAPI/FileAPI.min.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/jquery-fileapi/FileAPI/FileAPI.exif.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/plugins/jquery-fileapi/jquery.fileapi.custom.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/scripts/jquery.hyall.js" type="text/javascript"></script>
<script src="/HyFrame/Public/assets/global/scripts/hy-import.js" type="text/javascript"></script>
<?php echo ($_script); ?>

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

<script type="text/javascript">
$(document).ready(function() {
	HyFrame.initBreadcrumb(<?php echo ($jsonBreadcrumb); ?>);
	$hyall = $('#hy-all-container').hyall(<?php echo ($jsonAll); ?>);
	<?php if(is_array($initJS)) foreach($initJS as $k=>$v){ echo is_string($k) ? "$k.init($v);" : "$v.init();"; } ?>	
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