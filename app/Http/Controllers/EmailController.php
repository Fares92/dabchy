<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class EmailController extends Controller
{
    public function forgetPassword(Request $request){
        $title = "New password";
        $email=$request->get('email');
        $user=User::where('email','like','%'.$email.'%')->first();
        $content = str_random(8);
        if($user)
        {
            $user->password=Hash::make($content);
            //$user->password=$content;
            $user->save();
            Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message) use ($email)
            {



                $message->to($email)->subject('Regenerate password ');

            });
            return response()->json(['message' => 'Request completed']);
        }
        else
            return response()->json(['message' => 'email not found']);




    }
}
