@php 
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;

	// setup require vars
	$title = $obj_info['title'];
	$filename = $title.".xlsx";

  $head_row = $start_row = 3;
  $content_row = $head_row + 1;
  $from_char = strtoupper(ord('A'));
	$to_char = strtoupper(ord('K'));
  $from_row = 1;
	$to_row = 2;
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
    		'title' => __('label.no'),
    		'width' => 5,
      ],
      'b' => [
        'title' => __('label.date'),
        'width' => 23
      ],
      'c' => [
        'title' => __('label.on'),
        'width' => 11
      ],
      'd' => [
        'title' => __('label.lb223'),
        'width' => 15
      ],
      'e' => [
        'title' => __('label.id'),
        'width' => 8
      ],
      'f' => [
        'title' => __('label.barcode'),
        'width' => 14
      ],
      'g' => [
        'title' => __('label.lb70'),
        'width' => 30
      ],
      'h' => [
        'title' => __('label.lb64'),
        'width' => 15
      ],
      'i' => [
        'title' => __('label.lb21'),
        'width' => 15
      ]
    ];
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
  $spreadsheet->getActiveSheet()->setTitle('DS-'.date('d-m'));

	#drawing top header
	$spreadsheet->getActiveSheet()->setCellValue($start_cell.$row_1, $title);
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.$row_1.':'.$end_cell.$row_1);
	$spreadsheet->getActiveSheet()->getStyle($start_cell.$row_1)->applyFromArray([
		'font' => [ 'bold' => true, 'size' => 20 ],
		'alignment' => [ 'horizontal' => 'center', 'vertical' => 'bottom' ],
	]);
  
  #drawing header
  foreach ($header as $char => $attr) {
      $spreadsheet->getActiveSheet()->setCellValue(${'cell_'.$char}.$head_row, $attr['title']);
      #styling
      if(!empty($attr['width'])){   
          $spreadsheet->getActiveSheet()->getColumnDimension(${'cell_'.$char})->setWidth($attr['width']);
      }
  }
  $spreadsheet->getActiveSheet()->getRowDimension($head_row)->setRowHeight(28);
  $spreadsheet->getActiveSheet()
              ->getStyle($start_cell.$head_row.':'.$end_cell.$head_row)
              ->applyFromArray([
                'font' => [ 'name' => 'Arial', 'size' => 10, 'bold' => true ],
                'alignment' => [ 'horizontal' => 'center', 'vertical' => 'center' ],
                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'C5D9F1' ] ]
              ]);
	if($results->count()){
    $rowInd = 1;
    $grand_qty = 0;
    foreach($results as $row){
      $html = new PhpOffice\PhpSpreadsheet\Helper\Html();
      $qty = $row->qty;
      $qty = json_decode($qty, true);
      $qty_amount = array_values($qty)[0] ?? 0;
      $grand_qty += $qty_amount;

      $qty_html = view('backend.vproduct.sizecolorinfo')->with([
        'qty' => $qty,
        'allsizes' => $allsizes, 
        'allcolors' => $allcolors,
        'unit'=> ''
      ])->render();
      $qty_html = $html->toRichTextObject($qty_html);
      $spreadsheet->getActiveSheet()
                  ->setCellValue($start_cell.$content_row, $rowInd)
                  ->setCellValue($cell_b.$content_row, date('d/m/Y h:i:s A', strtotime($row->track_date)))
                  ->setCellValue($cell_c.$content_row, $row->tracking_on)
                  ->setCellValue($cell_d.$content_row, $row->tracking_ref)
                  ->setCellValue($cell_e.$content_row, $row->pd_id)
                  ->setCellValue($cell_f.$content_row, $row->barcode)
                  ->setCellValue($cell_g.$content_row, $row->title)
                  ->setCellValue($cell_h.$content_row, $qty_html)
                  ->setCellValue($end_cell.$content_row, $row->username);
      $hibg = ($rowInd % 2 == 0) ? 'D9D9D9' : $white;
      $spreadsheet->getActiveSheet()->getStyle($start_cell.$content_row.':'.$end_cell.$content_row)->applyFromArray([
        'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => $hibg ] ]
      ]);
      $rowInd++;
      $content_row++;
    }
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
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => [ 'rgb' => '8DB4E2']
                    ],
                ]
              ]);
  #A-B horizontal
  $spreadsheet->getActiveSheet()
              ->getStyle($start_cell.$content_row.':'.$cell_b.$before_last)
              ->getAlignment()->setHorizontal('center');
  #E-F horizontal
  $spreadsheet->getActiveSheet()
              ->getStyle($cell_e.$content_row.':'.$cell_f.$before_last)
              ->getAlignment()->setHorizontal('center');
  #F-H Wrap text
  $spreadsheet->getActiveSheet()
              ->getStyle($cell_f.$content_row.':'.$cell_h.$before_last)
              ->getAlignment()->setWrapText(true);
  // last row
  //$last_row += 1;
  $spreadsheet->getActiveSheet()
              ->setCellValue($cell_g.$last_row, __('label.total'))
              ->setCellValue($cell_h.$last_row, $grand_qty);
  $spreadsheet->getActiveSheet()->getRowDimension($last_row)->setRowHeight(28);   
  $spreadsheet->getActiveSheet()
              ->getStyle($cell_g.$last_row.':'.$end_cell.$last_row)
              ->applyFromArray([
                'font' => [ 'name' => 'Arial', 'size' => 10, 'bold' => true ],
                'alignment' => [ 'horizontal' => 'center', 'vertical' => 'center' ],
                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'C5D9F1' ] ]
              ]);
        
  #download
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename='.str_replace(" ", "-", $filename));
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
@endphp