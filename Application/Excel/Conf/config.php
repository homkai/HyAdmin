<?php
return array(
    'LOAD_ASSETS' =>array(
        'GLOBAL'	=>	array(
            'CSS'	=>	array(

            ),
            'JS'	=>	array(

            )
        ),
        'PLUGINS'	=>	array(
            'CSS'	=>	array(

            ),
            'JS'	=>	array(
                'Excel/excel'	=>	array(
                    'jquery-fileapi/FileAPI/FileAPI.min.js',
                    'jquery-fileapi/FileAPI/FileAPI.exif.js',
                    'jquery-fileapi/jquery.fileapi.custom.js'
                ),

            )
        ),

        'PAGES'		=>	array(
            'CSS'	=>	array(

            ),
            'JS'	=>	array(
                'Excel/excel' => 'excel-import.js',
                'SqlExport/export' => 'sql-export.js'
            )
        )
    ),
);