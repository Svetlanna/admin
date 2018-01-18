<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Peoples;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   $peoples=Peoples::all();
       return view('home')->with('peoples',$peoples);

    }
    public function newPeople(Request $request){
        if($request->ajax()){
           $peoples=Peoples::create($request->all());
           return response()->json($peoples);
        }
    }

   public function getUpdate(Request $request){
        if($request->ajax()){
            
           $peoples=Peoples::find($request->id);
           return Response($peoples);
        }
    }

   public function newtUpdate(Request $request){
        if($request->ajax()){
            $peoples=Peoples::find($request->id());
            $peoples->fname=$request->fname;
            $peoples->lname=$request->lname;
            $peoples->sex=$request->sex;
            $peoples->email=$request->email;
            $peoples->phone=$request->phone;
            $peoples->password=$request->password;
            $peoples->save();
          return Response($peoples);
        }
    }
     


public function deletePeople(Request $request){
 if($request->ajax()){
        Peoples::destroy($request->id);
return Response()->json(['sms'=>'delete successfuly']);
    }

}

}