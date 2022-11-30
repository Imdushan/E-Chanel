<?php

namespace App\Http\Controllers;

use App\ClientRegister;
use App\Doctor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileController extends Controller
{
    public function saveClientRegistration(Request $request){
        $userName = $request->input('userName');
        $userMobile = $request->input('userMobile');
        $userEmail = $request->input('userEmail');
        $userPassword = $request->input('userPassword');
        $dpdProvince = "western province";
        $hashPassword = Hash::make($userPassword);

        $user = new User();
        $user->name = $userName;
        $user->email = $userEmail;
        $user->password = $hashPassword;
        $user->mobile = $userMobile;
        $user->province = $dpdProvince;
        $user->role = 'client';
        $user->section = 'BSREG';
            if($user->save()){
                    return response()->json(['http_status' => 'success'],200);
            }
    }

    public function registerDoctor(Request $request)
    {

        $id = $request->input('id');
        $medical_id = $request->input('medical_id');
        $mobile_no = $request->input('mobile_no');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $email = $request->input('email');
        $hospital_name = $request->input('hospital_name');
        $specialization = $request->input('specialization');
        $available_date = $request->input('available_date');
        $available_time = $request->input('available_time');
        $mobile_no = $request->input('password');

        //----data send e-chaneling API and get return doctor id then assing $echaneling_id---//

        $echaneling_id = "";

        $dataRegister = '';

        if ($id == "new") {
            $dataRegister = new Doctor();
            $type = "inserted";
        } else {
            $existing_data = Doctor::where('id', '=', $id)->first();
            if ($existing_data) {
                $dataRegister = Doctor::find($existing_data->id);
                $type = "updated";
            }
        }

        $dataRegister->medical_id = $medical_id;
        $dataRegister->echaneling_id = $echaneling_id;
        $dataRegister->mobile_no = $mobile_no;
        $dataRegister->lat = $lat;
        $dataRegister->lng = $lng;
        if($id=="new"){
            $dataRegister->email = $email;
        }
        $dataRegister->hospital_name = $hospital_name;
        $dataRegister->specialization = $specialization;
        $dataRegister->available_date = $available_date;
        $dataRegister->available_time = $available_time;

        if ($dataRegister->save()) {
            //remove this code//
            $echanel = Doctor::find($dataRegister->id);
            $echanel->echaneling_id=$dataRegister->id;
            //remove this code//

            return response()->json(['http_status' => 'success', 'message' => 'registre details ' . $type . ' successfully', 'medical_id' => $dataRegister->medical_id], 200);
        } else {
            return response()->json(['http_status' => 'error', 'message' => 'data cannot be submitted'], 200);
        }
    }

}
