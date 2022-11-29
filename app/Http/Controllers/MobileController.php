<?php

namespace App\Http\Controllers;

use App\ClientRegister;
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

    public function ClientProfileUpdate(Request $request)
    {
        $id = $request->input('id');
        $userid = $request->input('user_id');
        $name_with_Ins = $request->input('name_with_Ins');
        $full_name = $request->input('full_name');
        $nic = $request->input('nic');
        $birth_day = $request->input('birth_day');
        $address = $request->input('address');
        $nationallity = $request->input('nationallity');

        $ClientRegister = '';

        if ($id == "new") {
            $ClientRegister = new ClientRegister();
            $type = "inserted";
        } else {
            $existing_ClientRegister = ClientRegister::where('id', '=', $id)->first();
            if ($existing_ClientRegister) {
                $ClientRegister = ClientRegister::find($existing_ClientRegister->id);
                $type = "updated";
            }
        }

        $ClientRegister->user_id = $userid;
        $ClientRegister->name_with_Ins = $name_with_Ins;
        $ClientRegister->full_name = $full_name;
        $ClientRegister->nic = $nic;
        $ClientRegister->birth_day = $birth_day;
        $ClientRegister->address = $address;
        $ClientRegister->nationallity = $nationallity;



        if ($ClientRegister->save()) {

            return response()->json(['http_status' => 'success', 'message' => 'registre details ' . $type . ' successfully', 'userId' => $ClientRegister->user_id], 200);
        } else {
            return response()->json(['http_status' => 'error', 'message' => 'User cannot be submitted'], 200);
        }
    }

}
