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
        'title' => __('label.id'),
        'width' => 8
      ],
      'c' => [
        'title' => __('label.barcode'),
        'width' => 12
      ],
      'd' => [
        'title' => __('label.lb70'),
        'width' => 35
      ],
      'e' => [
        'title' => __('label.lb64'),
        'width' => 36
      ],
      'f' => [
        'title' => __('label.lb71'),
        'width' => 17
      ],
      'g' => [
        'title' => __('label.lb57'),
        'width' => 21
      ],
      'h' => [
        'title' => __('label.lb57').'('.__('ccms.average').')',
        'width' => 21
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
  $spreadsheet->getActiveSheet()->setTitle('CS-'.date('d-m'));

	#drawing top header
	$spreadsheet->getActiveSheet()->setCellValue($start_cell.$row_1, $title);
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.$row_1.':'.$end_cell.$row_1);
	$spreadsheet->getActiveSheet()->getStyle($start_cell.$row_1)->applyFromArray([
		'font' => [ 'bold' => true, 'size' => 20 ],
		'alignment' => [ 'horizontal' => 'center', 'vertical' => 'bottom' ],
	]);
  $spreadsheet->getActiveSheet()->setCellValue($start_cell.$row_2, date('d/m/Y'));
	$spreadsheet->getActiveSheet()->mergeCells($start_cell.$row_2.':'.$end_cell.$row_2);
	$spreadsheet->getActiveSheet()->getStyle($start_cell.$row_2)->applyFromArray([
		'alignment' => [ 'horizontal' => 'center', 'vertical' => 'center' ],
	]);

  #drawing header
  foreach ($header as $char => $attr) {
      $spreadsheet->getActiveSheet()->setCellValue(${'cell_'.$char}.$head_row, $attr['title']);
      #styling
      if(!empty($attr['width'])){   
          $spreadsheet->getActiveSheet()->getColumnDimension(${'cell_'.$char})->setWidth($attr['width']);
      }
  }
  $spreadsheet->getActiveSheet()->getRowDimension($head_row)->setRowHeight(30);
  $spreadsheet->getActiveSheet()
              ->getStyle($start_cell.$head_row.':'.$end_cell.$head_row)
              ->applyFromArray([
                'font' => [ 'name' => 'Arial', 'size' => 11, 'bold' => true ],
                'alignment' => [ 'horizontal' => 'center', 'vertical' => 'center' ],
                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'C5D9F1' ] ]
              ]);
	if($results->count()){
    $rowInd = 1;
    $qty_grandtoal=$cost_fifo=$cost_avg=$totalcost_fifo=$totalcost_avg = 0;
    foreach($results as $row){
      $html = new PhpOffice\PhpSpreadsheet\Helper\Html();
      $qty = $row->qty;
      $newqty = [];
      $qty = json_decode($qty, true);

      $qty = sumarray($qty);
      $xtracost = json_decode($row->xtracost, true);
      
      $cost_fifo = $row->cost;
      $cost_avg = $row->pcost;

      $totalextracost = sumextracost($qty, $xtracost);

      $costfifo_extra = $cost_fifo + $totalextracost;
      $costavg_extra  = $cost_avg + $totalextracost;

      $totalcost_fifo+= $costfifo_extra;
      $totalcost_avg+= $costavg_extra;

      $qty_html = view('backend.vproduct.sizecolorinfo')->with([
        'qty' => $qty,
        'allsizes' => $allsizes, 
        'allcolors' => $allcolors,
        'unit'=> $row->unit
      ])->render();
      $qty_html = $html->toRichTextObject($qty_html);
      $cost_html = '
        <p>'.formatmoney($lastfifocost[$row->id]??$row->avgcost,true).'/'.__('label.lb26').'</p>
        <p>'.__('label.lb57').':'.formatMoney($cost_fifo, true).'</p>
        <p>'.__('label.lb56').':'.formatMoney($totalextracost, true).'</p>
      ';
      $cost_html = $html->toRichTextObject($cost_html);
      $costavg_html = '
        <p>'.formatmoney($row->avgcost,true).'/'.__('label.lb26').'</p>
        <p>'.__('label.lb57').':'.formatMoney($cost_avg, true).'</p>
        <p>'.__('label.lb56').':'.formatMoney($totalextracost, true).'</p>
      ';
      $costavg_html = $html->toRichTextObject($costavg_html);

      $spreadsheet->getActiveSheet()
                  ->setCellValue($start_cell.$content_row, $rowInd)
                  ->setCellValue($cell_b.$content_row, $row->id)
                  ->setCellValue($cell_c.$content_row, $row->barcode)
                  ->setCellValue($cell_d.$content_row, $row->product.config('costmethod'))
                  ->setCellValue($cell_e.$content_row, $qty_html)
                  ->setCellValue($cell_f.$content_row, $row->qty_total)
                  ->setCellValue($cell_g.$content_row, $cost_html)
                  ->setCellValue($end_cell.$content_row, $costavg_html);

      $spreadsheet->getActiveSheet()->getStyle($cell_f.$content_row)->getNumberFormat()->setFormatCode('0.00 "'.$row->unit.'"');  
      $total_hii = $green;
      if($row->qty_total==0){
        $total_hii = $orange;
      }elseif($row->qty_total<0){
        $total_hii = $red;
      }
      $spreadsheet->getActiveSheet()
                   ->getStyle($cell_f.$content_row)
                   ->getFont()->getColor()->setRGB($total_hii);
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
                ]
              ]);
  #A-B horizontal
  $spreadsheet->getActiveSheet()
              ->getStyle($start_cell.$content_row.':'.$cell_b.$before_last)
              ->getAlignment()->setHorizontal('center');
  #D-H Wrap text
  $spreadsheet->getActiveSheet()
              ->getStyle($cell_d.$content_row.':'.$end_cell.$before_last)
              ->getAlignment()->setWrapText(true);
  #F sum
  $spreadsheet->getActiveSheet()
              ->setCellValue($cell_f.$last_row, '=SUM('.$cell_f.$content_row.':'.$cell_f.$before_last.')');
  $spreadsheet->getActiveSheet()->getStyle($cell_f.$last_row)->getNumberFormat()->setFormatCode('0.00');
  $spreadsheet->getActiveSheet()
                   ->getStyle($cell_f.$last_row)
                   ->getFont()->getColor()->setRGB('00B050');
  #G-H sum 
  $spreadsheet->getActiveSheet()
              ->setCellValue($cell_g.$last_row, $totalcost_fifo)
              ->setCellValue($end_cell.$last_row, $totalcost_avg);
  $spreadsheet->getActiveSheet()->getStyle($cell_g.$last_row.':'.$end_cell.$last_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
  $spreadsheet->getActiveSheet()
                   ->getStyle($cell_g.$last_row.':'.$end_cell.$last_row)
                   ->getFont()->getColor()->setRGB('215967');
  // last row      
  $spreadsheet->getActiveSheet()->getRowDimension($last_row)->setRowHeight(30);   
  $spreadsheet->getActiveSheet()
              ->getStyle($cell_f.$last_row.':'.$end_cell.$last_row)
              ->applyFromArray([
                'font' => [ 'name' => 'Arial', 'size' => 11, 'bold' => true ],
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