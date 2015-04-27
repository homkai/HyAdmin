<?php
return array(
		// 自动加载静态资源（HyFrameController）
		'LOAD_ASSETS'			=>array(
			'GLOBAL'	=>	array(
					'CSS'	=>	array(
							
					),
					'JS'	=>	array(
							
					)
			),
			'PLUGINS'	=>	array(
					'CSS'	=>	array(
							'System/HyNotice/all'	=>	'umeditor/themes/default/css/umeditor.css',
					),
					'JS'	=>	array(
							'System/HyNotice/all'	=>	'umeditor/umeditor.config.js',
							'System/HyNotice/all'	=>	'umeditor/umeditor.min.js',
					)
			),
			'PAGES'		=>	array(
					'CSS'	=>	array(
							
					),
					'JS'	=>	array(
							'System/HyAlert/all'	=>	'hy-alert.js',
							'System/HyAlertA2/all'	=>	'hy-alert.js',
					)
			)
		),
		// 管理员消息ICON（HyAlertA2Model）
		'ADMIN_ALERT_ICON'		=>	'label-info,fa-bell',
);