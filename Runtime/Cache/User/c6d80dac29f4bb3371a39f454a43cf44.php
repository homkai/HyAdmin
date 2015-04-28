<?php if (!defined('THINK_PATH')) exit();?><div id="hy-detail-wrapper" class="hy-detail-wrapper">
	<?php if(1===count($data)) $col0=12;else $col0=6; ?>
	<div class="row detail-tables">
		<div class="col-md-<?php echo ($col0); ?>">
			<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(1===$mod) continue; ?>
				<div class="portlet box <?php echo ($v["style"]); ?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa <?php echo ($v["icon"]); ?> fa-fw"></i><b class="title"><?php echo ($v["title"]); ?></b>
						</div>
					</div>
					<div class="portlet-body">
					<?php if(is_array($v['value'])): foreach($v['value'] as $k1=>$v1): if(!$v1) continue; if($v['cols']) list($col1,$col2)=is_array($v['cols'])?$v['cols']:explode(',',$v['cols']); else { $col1=4;$col2=12-$col1; } ?>
						<div class="row static-info">
							<div class="col-xs-<?php echo ($col1); ?> name"><?php echo ($k1); ?></div>
							<div class="col-xs-<?php echo ($col2); ?> value"><?php echo ($v1); ?></div>
						</div><?php endforeach; endif; ?>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		<div class="col-md-6">
			<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(0===$mod) continue; ?>
				<div class="portlet box <?php echo ($v["style"]); ?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa <?php echo ($v["icon"]); ?> fa-fw"></i><b class="title"><?php echo ($v["title"]); ?></b>
						</div>
					</div>
					<div class="portlet-body">
					<?php if(is_array($v['value'])): foreach($v['value'] as $k1=>$v1): if(!$v1) continue; if($v['cols']) list($col1,$col2)=is_array($v['cols'])?$v['cols']:explode(',',$v['cols']); else { $col1=4;$col2=12-$col1; } ?>
						<div class="row static-info">
							<div class="col-xs-<?php echo ($col1); ?> name"><?php echo ($k1); ?></div>
							<div class="col-xs-<?php echo ($col2); ?> value"><?php echo ($v1); ?></div>
						</div><?php endforeach; endif; ?>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
	<div class="table-responsive">
		<div class="note note-warning display-none chart-tips"></div>
		<div class="chart-container"></div>
	</div>
<div class="clearfix"></div>
<script type="text/javascript">
var hyJSONData=[<?php echo ($data["json"]); ?>] && [<?php echo ($data["json"]); ?>][0];
</script>