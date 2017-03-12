<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

//use Laravel\Socialite\Facades\Socialite;
//use SocialiteProviders\Manager\ServiceProvider;


class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback(UserController $userController)
    {
        // when facebook call us a with token
        //$user = \Socialite::driver('facebook')->user();
        $res = $userController->createOrGetUser(Socialite::driver('facebook')->user());
        return $res;
        //getId(), getNickname(), getName(), getEmail(), getAvatar()
    }
    public function redirect_insta()
    {
         return Socialite::driver('instagram')->redirect();
    }

//    public function getToken(Request $request)
//    {
//        //dd($request);
//        $token=$request->get('0');
//        //dd($token);
//        return response()->json(compact('token'));
//    }

    public function callback_insta(UserController $userController)
    {
        $headers[]=
        // when facebook call us a with token
        //$user = \Socialite::driver('facebook')->user();
        $user=Socialite::driver('instagram')->user()->user;
        //dd($user);
        $res=$userController->createOrGetUserInsta($user);
        //dd($res);
        $token=['id'=>1,'value'=>$res];
        Token::create($token);
        //return response()->json($res);
        //$req = new Request((array)$res);
        //return response()->json(compact('res'));
        //$this->getToken($req);
        //getId(), getNickname(), getName(), getEmail(), getAvatar()
    }
    public function getToken()
    {
        $t=Token::find(1);
        $token=$t->value;
        $t->delete();
        return response()->json(compact('token'));
    }


}
