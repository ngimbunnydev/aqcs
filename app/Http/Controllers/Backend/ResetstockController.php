<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Backend\Addstock;
use App\Models\Backend\Addstocks;
use App\Models\Backend\Adjuststock;
use App\Models\Backend\Adjuststocks;
use App\Models\Backend\Stock;
use App\Models\Backend\Stocktracking;
use App\Models\Backend\Stockcount;
use App\Models\Backend\Stockcounts;


use App\Models\Backend\Invoice;
use App\Models\Backend\Invoices;

use App\Models\Backend\Productreturn;
use App\Models\Backend\Productreturns;

use App\Models\Backend\Quotation;
use App\Models\Backend\Quotations;
use App\Models\Backend\Qtstatus;

use App\Models\Backend\Purchase;
use App\Models\Backend\Purchases;
use App\Models\Backend\Ppayment;
use App\Models\Backend\Purchaseorder;
use App\Models\Backend\Purchaseorders;

use App\Models\Backend\Rpayment;
use App\Models\Backend\Product;
use App\Models\Backend\Favproduct;
use App\Models\Backend\Pcategory;
use App\Models\Backend\Unit;
use App\Models\Backend\Size;
use App\Models\Backend\Color;
use App\Models\Backend\Customer;
use App\Models\Backend\Ctype;
use App\Models\Backend\Cchannel;
use App\Models\Backend\Dchannel;

  
use App\Models\Backend\Expense;
use App\Models\Backend\Expenses;

use App\Models\Backend\Branch;
use App\Models\Backend\Warehouse;
use App\Models\Backend\Users;

use App\Models\Backend\Deliverynote;
use App\Models\Backend\Cashinout;
use App\Models\Backend\Systracking;
use App\Models\Backend\Dishnote;
use App\Models\Backend\Table;
use App\Models\Backend\Printer;


//Clinic
use App\Models\Backend\Hmsappointment;
use App\Models\Backend\Hmsbloodgroup;
use App\Models\Backend\Hmsconsultation;
use App\Models\Backend\Hmsconsultationlist;
use App\Models\Backend\Hmsimageryresult;
use App\Models\Backend\Hmslaboresult;
use App\Models\Backend\Hmsqa;
use App\Models\Backend\Hmstreatment;

//Accounting
use App\Models\Backend\Accountno;
use App\Models\Backend\Accountgeneraljournal;
use App\Models\Backend\Accountjournalentry;


use App\Http\Controllers\Backend\SystemconfigController;

class ResetstockController extends Controller
{
    private $args;
    public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
        $systemconfig = new SystemconfigController($args);

    } /*../function..*/
    public function index()
    {
      
//       if(dbis('sccm')){
//          \DB::table('cms_productdetail')->truncate();
//         $p = Product::select('*')->get();
//         foreach($p as $r){
//           \DB::table('cms_productdetail')->insert([
//             'pdd_id' => 0,
//             'pd_id' => $r->pd_id,
//             'lg_code' => 'en',
//             'translate' => '{"des":"","metatitle":"","metakeyword":"","metades":""}'
//           ]);
//           //dd($r->pd_id);
//         }
//         return null;
//       }
//       return null;
      /*0000000000000*/
      
      
        Addstock::truncate();
        Addstocks::truncate();
        Adjuststock::truncate();
        Adjuststocks::truncate();
        Stock::truncate();
        Stocktracking::truncate();
        Stockcount::truncate();
        Stockcounts::truncate();
        Product::where('pd_id','<>',0)->update(['avgcost'=>0]);
        Systracking::truncate();
        //Accountno::where('accno_id','<>', 0)->update(['balance'=>0]);
        Accountgeneraljournal::truncate();
        Accountjournalentry::truncate();
      
      
        if(config('ccms.backend')=='beta')
        {
          Customer::truncate();
          Cchannel::truncate();
          Dchannel::truncate();
          Favproduct::truncate();
          
          Invoice::truncate();
          Invoices::truncate();
          Rpayment::truncate();
          Cashinout::truncate();
          
          Quotation::truncate();
          Quotations::truncate();
          Qtstatus::truncate();
          
          Purchase::truncate();
          Purchases::truncate();
          Ppayment::truncate();

          Purchaseorder::truncate();
          Purchaseorders::truncate();
          
          Productreturn::truncate();
          Productreturns::truncate();

          Expense::truncate();
          Expenses::truncate();
          Accountno::where('accno_id','<>', 0)->update(['balance'=>0]);

          //Branch::truncate();
          //Warehouse::truncate();
          //Users::where('id','<>',0)->update(['branch_id'=>1, 'wh_id'=>1]);
          
          Deliverynote::truncate();
          
          
          
          //Clinic
          if(config('sysconfig.posfor')=='clinic'){
            Hmsappointment::truncate();
            Hmsbloodgroup::truncate();
            Hmsconsultation::truncate();
            Hmsconsultationlist::truncate();
            Hmsimageryresult::truncate();
            Hmslaboresult::truncate();
            Hmsqa::truncate();
            Hmstreatment::truncate();
          }
          
          
        }
     
      
        
      if(config('ccms.backend')=='unknown')
      {
        
      
        Branch::truncate();
        Warehouse::truncate();

        Users::where('id','<>',0)->update(['branch_id'=>0, 'wh_id'=>0]);
      
        \DB::table('cms_filecategory')->truncate();
        \DB::table('cms_filemanager')->truncate();

        //$product =new Product;
        //$product->update(['sizes'=>'','colors'=>'']);
        Product::truncate();
        \DB::table('cms_productdetail')->truncate();
        \DB::table('cms_productfile')->truncate();
      
        Pcategory::truncate();
        Unit::truncate();
        Size::truncate();
        Color::truncate();

        Dishnote::truncate();
        Table::truncate();
        Printer::truncate();
        
        
        Ctype::truncate();
        Accountno::truncate();
        
      }
      


    } /*../function..*/


    
}