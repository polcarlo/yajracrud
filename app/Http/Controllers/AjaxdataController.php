<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Foundation\Validation\ValidatesRequests;
//use Illuminate\Support\Facades\Validator;
use Validator;
use App\Student;
use Yajra\DataTables\Datatables;

class AjaxdataController extends Controller
{
	public function __construct(Student $student, Validator $validator)
	{
		$this->student = $student;
		$this->validator = $validator;

	}
	function index()
	{
		return view('student.ajaxdata');
	}

	function getdata()
	{
		$students = $this->student::select(['id','first_name','last_name']);
	/* 	$datatables = app('datatables');
       return $datatables->eloquent($students)->addColumn('action', function($student){
                    return '<a href="#" class="btn btn-xs btn-primary edit" id="'.$student->id.'"><i class="glyphicon glyphicon-edit"></i> Edit</a><a href="#" class="btn btn-xs btn-danger delete" id="'.$student->id.'"><i class="glyphicon glyphicon-remove"></i>Delete</a>';
            })->make(true);*/

        //with tbody in source code
        return Datatables::of($students)
			->addColumn('action', function($student){
					return '<a href="#" class="btn btn-xs btn-primary edit" id="'.$student->id.'"><i class="glyphicon glyphicon-edit"></i> Edit</a><a href="#" class="btn btn-xs btn-danger delete" id="'.$student->id.'"><i class="glyphicon glyphicon-remove"></i>Delete</a>';
			})
			->make(true);
	}
    
    function postdata(Request $request)
    {
    	$validation = $this->validator::make($request->all(),[
    			'first_name' => 'required',
    			'last_name' => 'required'
    	]);

    	$error_array = array();
    	$success_output = '';
    	if($validation->fails())
    	{
    		foreach($validation->messages()->getMessages() as $field_name => $messages)
    		{
    			$error_array[] = $messages;
    		}
    	}else
    	{
    		if($request->get('button_action') == 'insert')
    		{
    			$student = new Student([
    				'first_name' => $request->get('first_name'),
    				'last_name' =>$request->get('last_name')

    			]);
    			$student->save();
    			$success_output = '<div class="alert alert-success">Data Inserted</div>';
    		}
    		if($request->get('button_action') == 'update')
    		{
    			$student = $this->student::find($request->get('student_id'));
    			$student->first_name = $request->get('first_name');
    			$student->last_name = $request->get('last_name');
    			$student->save();
    			$success_output = '<div class="alert alert-success"> Data Updated</div>';

    		}
    	}

    	$output = array(
    		'error' => $error_array,
    		'success' => $success_output
    	);

    	echo json_encode($output);
    }
    public function fetchdata(Request $request)
    {
    	$id = $request->input('id');
    	$student = $this->student::find($id);
    	$output = array(
    		'first_name'	=> $student->first_name,
    		'last_name'		=> $student->last_name
    	);
    	echo json_encode($output);
    }

    public function removedata(Request $request)
    {
    	$student = $this->student::find($request->input('id'));
    	if($student->delete())
    	{
    		echo 'Data Deleted';
    	}
    }
}
