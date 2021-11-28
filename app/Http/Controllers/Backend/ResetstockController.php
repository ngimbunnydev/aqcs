<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;



use App\Models\Backend\Branch;
use App\Models\Backend\Users;

use App\Models\Backend\Airqualitydetail;
use App\Models\Backend\Airqualitymonitoring;
use App\Models\Backend\Airtype;
use App\Models\Backend\Benchmark;
use App\Models\Backend\Color;
use App\Models\Backend\Device;
use App\Models\Backend\Evaluation;
use App\Models\Backend\Location;



//use App\Http\Controllers\Backend\SystemconfigController;

class ResetstockController extends Controller
{
    private $args;
    public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
        //$systemconfig = new SystemconfigController($args);

    } /*../function..*/
    public function index()
    {
      
      Airqualitydetail::truncate();
      Airqualitymonitoring::truncate();

        if(1==1)
        {
          
          Airtype::truncate();
          Benchmark::truncate();
          Color::truncate();
          Device::truncate();
          Evaluation::truncate();
          Location::truncate();
          
        }

        if(1==2)
        {
          Branch::truncate();
        }
     

    } /*../function..*/


    
}