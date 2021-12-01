<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use PDF;
use SnappyImage;

class DashboardController extends Controller
{
	private $obj_info=['name'=>'home','title'=>'Dashboard','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
  public function __construct(array $args){ //public function __construct(Array args){
        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];

	} /*../function..*/
  
	public function index(){
    
		$blade= view('backend.dashboard')->with(['obj_info'=>$this->obj_info])
      ->render();
    return $blade;

	}

  public function pdfgenerate(Request $request, $id=0){
            $blade = 'backend.dashboard';
            $width_size = 3508;
            $path_img = resource_path('views/backend/widget/');
            $img_name = 'echarts.jpg';
            $image = SnappyImage::loadView($blade,
                             [
                               'obj_info' => $this->obj_info
                             ]
            );
            
            
            //$image->setOption('width', (int)$width_size);
            //$image->setOption('width', 219);
            //$image->setOption('width', 375);
            $image->setOption('quality', 100);
           
            
            
            $image->save($path_img.$img_name,true);

    // // 
       
    //         $format = [
    //           'mode'                 => '',
    //           'format'               => array(80,1440),
    //           'orientation'          => 'P',
    //           'margin_left'          => 10,
    //           'margin_right'         => 10,
    //           'margin_top'           => 0,
    //           'margin_bottom'        => 20,
    //           'margin_header'        => 0,
    //           'margin_footer'        => 10,
             
              
    //       ];
    //       $data = ['obj_info' => $this->obj_info];
          
    //         $pdf = PDF::loadView($blade, $data,[],$format);

    //         $option = 1;
    //         $pdftitle = 'abc';
    //        if($option==1)
    //             return $pdf->stream($pdftitle.'.pdf');
    //         else
    //             return $pdf->download($pdftitle.'.pdf');
            
    //     //end

    



    
    
}/*../function..*/
  
  

  public function indexapi(Request $request, $condition=[], $setting=[])
  {
    $pos = [
      'title' => __('label.lb09'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'pos',
      'color' => '0xFFffd43b',
      'route' => '/posindex'
    ];
    
    $hold = [
      'title' => __('label.lb03'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'fa.solidBookmark',
      'color' => '0xFF3342b7',
      'route' => '/holdindex'
    ];
    
    $invoice = [
      'title' => __('label.lb14'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'invoice',
      'color' => '0xFF000099',
      'route' => '/invoiceindex'
    ];
    
    $rpayment = [
      'title' => __('label.lb15'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'rpayment',
      'color' => '0xFF69AA46',
      'route' => '/rpaymentindex'
    ];
    
    $expense = [
      'title' => __('label.lb16'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'fa.handHoldingUsd',
      'color' => '0xFFDD5A43',
      'route' => '/productindex'
    ];
    
    $product = [
      'title' => __('label.lb10'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'product',
      'color' => '0xFF2fcbd4',
      'route' => '/productindex'
    ];
    
    $currenstock = [
      'title' => __('label.lb12'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'currentstock',
      'color' => '0xFFFF892A',
      'route' => '/currentstockindex'
    ];
    
    $alertstock = [
      'title' => __('label.alertstock'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'alertstock',
      'color' => '0xFFD15B47',
      'route' => '/alertstockindex'
    ];
    
    $webappsyn = [
      'title' => __('label.webappsyn'),
      'subtitle' => "Bocali, Apple",
      'event'=> "4 Items",
      'icon'=> 'sync',
      'color' => '0xFFF8F8F8F8',
      'route' => '/sync'
    ];
    
    return response()->json([
      $pos,
      $product,
    //$hold,
      $invoice,
      $currenstock,
      $rpayment,
       
      $alertstock,
       
      
     // $webappsyn,
      
//       $expense,
             
             
      
          ]);
    
  }
  
  
  
  
  /*
option = {
  xAxis: {
    type: 'category',
    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      data: [120, 200, 150, 80, 70, 110, 130],
      type: 'bar',
      showBackground: true,
      backgroundStyle: {
        color: 'rgba(180, 180, 180, 0.2)'
      },
      markLine: {
                  symbol:"none",
                 data: [{name: 'Standard', yAxis: 100}],
                 lineStyle: {
                    color: 'red',
                    type: 'solid',
                    width: 3
                },
            }
    }
  ]
};

  */
  
  
  
  
}  