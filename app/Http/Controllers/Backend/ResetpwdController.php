<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Users;



class ResetpwdController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'resetpwd','title'=>'Password','routing'=>'admin.controller','icon'=>'<i class="fa fa-key" aria-hidden="true"></i>'];


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
        $this->model = new Users;
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


    public function index(Request $request, $condition=[], $setting=[])
    {
        return view('backend.v'.$this->obj_info['name'].'.index');
    } /*../function..*/


    public function trash(Request $request)
    {
        return null;
    } /*../function..*/


    public function store(Request $request)
    {
        return null;

        
    } /*../function..*/

    public function update(Request $request)
    {
        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            
            $validator = $this->validation($request);
            if ($validator->fails()) {
               
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$this->args['userinfo']['id']]);
               
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input()
                                ]
                ];
              
                    

            } else {
                $data=$this->setinfo($request, true);
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);

                ############
                $savetype=strtolower($request->input('savetype'));
                $success_ms = __('ccms.suc_edit');
              
                #when use ajax to SAVE
                    $routing=url_builder($obj_info['routing'],[$obj_info['name'],'ajaxreturn']);
                        return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                    ]
                                    ];
                #end ajax SAVE
              
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms
                                                        
                                                    ]
                                    ];   
               
            }
        } /*../if POST..*/

    } /*../end fun..*/

    public function edit(Request $request, $id=0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info=$this->obj_info;
        $input = null;
        if ($request->session()->has('input')) 
        {
           #No need to retrieve data becoz already set by Form#
            $editid=$this->args['userinfo']['id'];
            goto skip;
        }
        
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['userinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty() && $editid!=$this->args['userinfo']['id'])
        {

            return __('ccms.rqnvalid');
        }

        

        skip:
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info')


                )->with(
                    [
                        'dflang' => $this->dflang,
                        'submitto'      => 'update',
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit')
                    ]
                );
    } /*../end fun..*/


   

    public function validation($request, $isupdate=false){

        // validate
            // read more on validation at http://laravel.com/docs/validation
            $user=$this->args['userinfo'];
            $update_rules= [];
            $rules = [
                        'password'       => ['required', function ($attribute, $value, $fail) use ($user) {
        if (!\Hash::check($value, $user['pwd'])) {
            return $fail(__('ccms.cpwdwrong'));
        }
    }],
                        'newpassword'       => [
                          'required',
                          'min:6',             // must be at least 10 characters in length
                          'regex:/[a-z]/',      // must contain at least one lowercase letter
                          'regex:/[A-Z]/',      // must contain at least one uppercase letter
                          //'regex:/[0-9]/',      // must contain at least one digit
                          'regex:/[@$!%*#?&]/', // must contain a special character
                        ],//required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[\d\X]).*$/',
                        'cnewpassword'  => 'required|same:newpassword'
                    ];

            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'same' => __('ccms.samepwd'),
                'min' => __('ccms.weakpwd'),
                'regex' => __('ccms.weakpwd'),
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){
        $tableData = [
            'password' => \Hash::make($request->input('newpassword')),                       
        ];

        return ['tableData' => $tableData, 'id'=>$this->args['userinfo']['id']];
        

    }/*../function..*/





    public function delete(Request $request, $id=0)
    {
        return null;

    } /*../function..*/

    public function restore(Request $request, $id=0)
    {
       return null;

    } /*../function..*/


    public function destroy(Request $request, $id=0)
    {
        return null;

    } /*../function..*/
  
  public function ajaxreturn(Request $request){
        $return = [
                    'callback' => 'afterreset',
                    'container' => 'air_windows',
                    'data' => '',
                    'message' => __('ccms.suc_edit')
                ];

        return json_encode($return);
    }/*../function..*/
  
     public function updateapi(Request $request)
    {
        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            
            $validator = $this->validation($request);
            if ($validator->fails()) {
                return response()->json([
                      'act' => false,
                      'obj_info' => $obj_info,
                      'input' => $request->input(),
                      'errors' => $validator->errors()->first(),
                  ]);

            } else {
                $data=$this->setinfo($request, true);
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);

                ############
                return response()->json([
                              'act' => true,
                              'obj_info' => $obj_info,
                  ]);   
               
            }
        } /*../if POST..*/

    } /*../end fun..*/


}