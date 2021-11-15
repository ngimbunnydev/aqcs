<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use App\Plugins\Filemanager\Filemanager;
use Zip;
class BackupController extends Controller
{
    private $args;
    private $model;
    private $tablename;
    private $fprimarykey='sttracking_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'backup','title'=>'Backups','routing'=>'admin.controller','icon'=>'<i class="fas fa-hdd"></i>'];

    private $protectme;


    public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb228');
        $this->protectme = [  
          config('ccms.protectact.index'),
        ];

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
    } /*../function..*/

    public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function default()
    {
       return null;
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return null;
    } /*../function..*/

    public function sfp($request, $results, $isPaginate=true)
    {
       return null;
    } /*../function..*/

    public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
  
        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with(['submitto'  => 'download'])
                ->with(['caption' => __('ccms.active')])
                ->with($setting);


    } /*../function..*/  
  
    public function download(Request $request){
      if($request->isMethod('POST')){
        $btype = $request->input('btype');
        $prefix_name = config('ccms.backend');
        $db_name = $prefix_name.'-'.time().'-db.sql.gz';
        if($btype=='db'){
          \Spatie\DbDumper\Databases\MySql::create()
              ->setDbName($prefix_name)
              ->setUserName(config('database.connections.mysql.username'))
              ->setPassword(config('database.connections.mysql.password'))
              ->addExtraOption('--routines')
              ->useCompressor(new \Spatie\DbDumper\Compressors\GzipCompressor())
              ->dumpToFile($db_name);
            return response()->download($db_name)->deleteFileAfterSend(true);
        }elseif($btype=='pic'){
          $pic_path = $prefix_name.'-'.time().'-pictures.zip';
          $filemanager = Filemanager::all();
          if($filemanager->count()>0){
            $zip = Zip::create($pic_path);

            foreach($filemanager as $row){
              $zip->add(resource_path('filelibrary').'/'.$row->media);
            }
            $zip->close();
          }
          return response()->download($pic_path)->deleteFileAfterSend(true);
        }
      }
      return redirect()->back();
    }
}