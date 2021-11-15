<?php
return [
	'mode'                 => '',
	'format'               => 'A4',
	'default_font_size'    => '12',
	'default_font'         => 'sans-serif',
	'margin_left'          => 10,
	'margin_right'         => 10,
	'margin_top'           => 45,
	'margin_bottom'        => 20,
	'margin_header'        => 10,
	'margin_footer'        => 10,
	'orientation'          => 'P',
	'title'                => 'Laravel mPDF',
	'author'               => '',
	'watermark'            => '',
	'show_watermark'       => true,
	'watermark_font'       => 'sans-serif',
	'display_mode'         => 'fullpage',
	'watermark_text_alpha' => 0.1,
  'custom_font_dir'      => base_path('resources/fonts/'),
  'custom_font_data' 	   => ['khmerfont' => [
                                              'R'  => 'KhmerOS.ttf',    // regular font
                                              'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                                              //'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
                                            ]
                            ],
	'auto_language_detection'  => true,
	'temp_dir'               => '',
	//'pdfa' 			=> false,
        //'pdfaauto' 		=> false,
];