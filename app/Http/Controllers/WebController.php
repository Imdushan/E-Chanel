<?php

namespace App\Http\Controllers;

use App\UniversityBasic;
use App\UniversityCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class WebController extends Controller
{
    public function ChangeDoctorLiveStatus(Request $request){

        //$doc_id =$request->input('doc_id');
        $doc_id ="1";
        $available =$request->input('available');

        $database = app('firebase.database');
        //  $postData = [
        //     '2' => ['available' => "true",'lat' =>'6.797','lng' =>'7.215',]
        //     ];

            $postData = new stdClass();
            $postData->available = true;
            $postData->lat = 6.797;
            $postData->lng = 7.215;

         $database->getReference('doctor/'.$doc_id.'/')->set($postData);
         //$database->getReference('doctor')->push($postData);




    }






}
