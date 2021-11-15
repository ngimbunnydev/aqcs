<?php
namespace App\Plugins\Combofilter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;


class CombofilterController extends Controller
{

	public function __construct(){
	
	}

	public function index()
    {
    	return view('combofilter.response');
    }
	
    
}