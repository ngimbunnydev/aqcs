@php 
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;

	// setup require vars
	$title = 'Date-Time-Report';
	$filename = $title.".xlsx";

  $head_row = $start_row = 5;
  $content_row = $head_row + 1;
  $from_char = strtoupper(ord('A'));
	$to_char = strtoupper(ord('K'));
  $from_row = 1;
	$to_row = 1;
  list($red, $green, $blue, $white, $black, $orange) = [ 'DD5A43', '69AA46', '478FCA', 'FFFFFF', '000000', 'FF892A' ];
  // generate cell char with dynamic vars
	for($i = $from_char; $i <= $to_char; $i++){
		${'cell_'.strtolower(chr($i))} = chr($i);
	}
	// geerate row num with dynamic vars
	for($x = $from_row; $x <= $to_row; $x++){
      ${'row_'.$x} = $x;
  }
  // header
    $header = [
    	'a' => [
    		'title' => 'Date-Time',
    		'width' => 30,
      ]
    ];
    $from_char = ord('b');
    $i=0;
    foreach ($airtype as $item){
      
      $cell = chr($from_char+$i);
      $header[$cell] = ['title' => $item['title'],'width' => 15,];
      $i++;
    }
  $header_cell = array_keys($header);
  $start_cell = ${'cell_'.$header_cell[0]};
  $end_cell = ${'cell_'.end($header_cell)};
  
	$spreadsheet = new Spreadsheet();

	#page setup
	$spreadsheet->getActiveSheet()->getPageSetup()
    			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT); // ORIENTATION_PORTRAIT, ORIENTATION_LANDSCAPE
	$spreadsheet->getActiveSheet()->getPageSetup()
    			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
  $spreadsheet->getActiveSheet()->getPageSetup()->setScale(65);
	$spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.75);
	$spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.25);
	$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.25);
	$spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.75);
	//$sheet = $spreadsheet->setActiveSheetIndex(0);

	$sheet = $spreadsheet->getActiveSheet();
  $spreadsheet->getActiveSheet()->setTitle('Products');

	#drawing top header
	$spreadsheet->getActiveSheet()->setCellValue($start_cell.$row_1, $title);
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.$row_1.':'.$end_cell.$row_1);
	$spreadsheet->getActiveSheet()->getStyle($start_cell.$row_1)->applyFromArray([
		'font' => [ 'bold' => true, 'size' => 20 ],
		'alignment' => [ 'horizontal' => 'center', 'vertical' => 'bottom' ],
	]);

  #Location
	$spreadsheet->getActiveSheet()->setCellValue($start_cell.'2', 'Location: '.$device_info['location']);
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.'2'.':'.$end_cell.'2');
	$spreadsheet->getActiveSheet()->getStyle($start_cell.'2')->applyFromArray([
		'font' => [ 'bold' => true, 'size' => 15 ],
		'alignment' => [ 'horizontal' => 'left', 'vertical' => 'bottom' ],
	]);

  #Device
	$spreadsheet->getActiveSheet()->setCellValue($start_cell.'3', 'Device: '.$device_info['device'].'('.$device_info['device_index'].')');
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.'3'.':'.$end_cell.'3');
	$spreadsheet->getActiveSheet()->getStyle($start_cell.'3')->applyFromArray([
		'font' => [ 'bold' => true, 'size' => 15 ],
		'alignment' => [ 'horizontal' => 'left', 'vertical' => 'bottom' ],
	]);

  #drawing header
  foreach ($header as $char => $attr) {
      $spreadsheet->getActiveSheet()->setCellValue(${'cell_'.$char}.$head_row, $attr['title']);
      #styling
      if(!empty($attr['width'])){   
          $spreadsheet->getActiveSheet()->getColumnDimension(${'cell_'.$char})->setWidth($attr['width']);
      }
  }
  $spreadsheet->getActiveSheet()->getRowDimension($head_row)->setRowHeight(25);
  $spreadsheet->getActiveSheet()
              ->getStyle($start_cell.$head_row.':'.$end_cell.$head_row)
              ->applyFromArray([
                'font' => [ 'name' => 'Arial', 'size' => 11, 'bold' => true ],
                'alignment' => [ 'horizontal' => 'center', 'vertical' => 'center' ],
                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'C5D9F1' ] ]
              ]);
  // cost permission
  $hasPermisson = checkpermission("product-cost", $args['userinfo']);
	if($results->count()){
    $rowInd = 1;
    foreach($results as $row){
      $id = explode(',', $row->airtype_id);
			$val = explode(',', $row->qty);
			$data = array_combine($id, $val);

      ///
      $spreadsheet->getActiveSheet()
                    ->setCellValue($start_cell.$content_row, $row->record_datetime);
      
      $from_char = ord('b');
      $i=0;
      foreach ($airtype as $item){
        
        $cell = chr($from_char+$i);
        $nexcell = ${'cell_'.$cell};
        $spreadsheet->getActiveSheet()
                    ->setCellValue($nexcell.$content_row, $data[$item['airtype_id']]);
        $i++;
      }
      $content_row++;
      ///
    }
    $last_row = $content_row;
    $before_last = $last_row - 1;
    $content_row = $head_row + 1;
    // vertical all
    $spreadsheet->getActiveSheet()
                ->getStyle($start_cell.$content_row.':'.$end_cell.$before_last)
                ->getAlignment()->setVertical('center');
    $spreadsheet->getActiveSheet()
                ->getStyle($start_cell.$content_row.':'.$end_cell.$before_last)
                ->applyFromArray([
                  'borders' => [
                      'horizontal' => [
                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                          'color' => [ 'rgb' => '8DB4E2']
                      ],
                  ]
                ]);
    
  }
  
     
  #download
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename='.str_replace(" ", "-", $filename));
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
@endphp