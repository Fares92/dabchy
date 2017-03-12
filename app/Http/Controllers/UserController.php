<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Article;
use App\Service;
use App\Sub_interest;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Contracts\User as ProviderUser;

class UserController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate', 'signUp', 'createOrGetUser', 'forgetPassword']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        //dd($request); role
        $credentials = $request->only('email', 'password');
        // $user = User::where('email','=',$request->get('email'))->where('provider_user_id','=',$request->get('provider_user_id'))->first();
        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate_social(Request $request)
    {
        $user = User::where('email', '=', $request->get('email'))->where('provider_user_id', '=', $request->get('provider_user_id'))->first();
        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return $token;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        $rules = array(
            'name' => 'required',                        // just a normal required validation
            'email' => 'required|email|unique:users',     // required and must be unique in the ducks table
            'password' => 'required'

            // 'password_confirm' => 'required|same:password'           // required and has to match the password field
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            // get the error messages from the validator
            $messages = $validator->messages();
            return response()->json($messages);


        } else {


            $name = $request->get('name');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $password = $request->get('password');
            $provider_user_id = $request->get('provider_user_id');
            $provider = $request->get('provider');
            //$confirmation_code = str_random(30);
            //$data_conf = ['confirmation_code' => $confirmation_code];
            $user = ['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'client', 'phone' => $phone
                , 'provider_user_id' => $provider_user_id, 'provider' => $provider];
            User::create($user);
//        $title = 'confirmation code';
//        $content = $confirmation_code;
//
//        Mail::send([], ['title' => $title, 'content' => $content], function ($message) use ($email, $name) {
//
//            $message->from('fareswardeni@gmail.com', 'Karbia');
//
//            $message->to($email);
//
//        });

            //Flash::message('Thanks for signing up! Please check your email.');
            //$req=new Request((array)$user);
            return $this->authenticate($request);
            //return $user;
        }

    }

    /**
     * @param ProviderUser $providerUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrGetUser(ProviderUser $providerUser)
    {
        //dd('res'.$providerUser);
        $user = DB::table('users')->where('provider', 'facebook')
            ->where('provider_user_id', $providerUser->getId())
            ->first();
        /**
         * if user is registred
         */
        if ($user) {

            $req = new Request((array)$user);
            // dd($user);
            return $this->authenticate_social($req);
        } /**
         * if user is not registred
         */
        else {
            $user1 = DB::table('users')->where('email', $providerUser->getEmail())->first();
            if (!$user1) {
                //dd($providerUser);
                $user1 = ['name' => $providerUser->getName(), 'email' => $providerUser->getEmail(), 'password' => $providerUser->getId(),
                    'provider_user_id' => $providerUser->getId(),
                    'provider' => 'facebook'];
            }
            //dd($user1);
            $req1 = new Request($user1);
            if ($user) {
                return $this->authenticate($req1);
            }
            return $this->signUp($req1);

        }

    }

    /**
     * @param array $providerUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrGetUserInsta(array $providerUser)
    {
        $user = DB::table('users')->where('provider', 'instagram')
            ->where('provider_user_id', $providerUser['id'])
            ->first();
        if ($user) {

            $req = new Request((array)$user);
            //dd($user);
            return $this->authenticate_social($req);
        } /**
         * if user is not registred
         */
        else {
            $user1 = DB::table('users')->where('email', $providerUser['username'] . '@instagram.com')->first();
            if (!$user1) {
                //dd($providerUser);
                $user1 = ['name' => $providerUser['username'], 'email' => $providerUser['username'] . '@instagram.com', 'password' => $providerUser['id'],
                    'provider_user_id' => $providerUser['id'],
                    'provider' => 'instagram'];
            }

            $req1 = new Request((array)$user1);
            //dd($req1);
            if ($user) {
                return $this->authenticate($req1);
            }
            return $this->signUp($req1);

        }

    }

    public function logout()
    {
        //dd(JWTAuth::getToken());
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Success logout']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign_subInterests(Request $request)
    {
        // dd($request->interests);
        //$area = json_decode();
        $userAuth = JWTAuth::parseToken()->authenticate();
        $user = User::find($userAuth->id);
        //dd($request->CheckedInterestsFirst);
        foreach ($request->CheckedInterestsFirst as $i => $v) {
            $user->sub_interests()->attach($v['id']);
            //echo $v['name'] . '</br>';
        }
        return response()->json(['message' => 'Success']);
    }

    public function my_interests()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $myInterests = $user->sub_interests;
        //dd($myInterests);
        return $myInterests;
    }

    public function edit_interests(Request $request)
    {
        $userAuth = JWTAuth::parseToken()->authenticate();
        $user = User::find($userAuth->id);
        $myinterests = $this->my_interests();
        foreach ($myinterests as $i => $int) {
            $user->sub_interests()->detach($int['id']);
        }
        $sub_interests = $request->CheckedInterests;
        for ($i = 0; $i < sizeof($sub_interests); $i++) {
            $user->sub_interests()->attach($sub_interests[$i]);
        }

        return response()->json(['message' => 'Success']);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function editProfile()
    {
        $user_c = null;
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user->provider) {
            $user_c = ['name' => $user->name, 'email' => $user->email, 'password' => $user->password, 'photo' => $user->photo, 'phone' => $user->phone,
                'country' => $user->country, 'location' => $user->location];
        } else {

            $user_c = ['name' => $user->name, 'photo' => $user->photo, 'phone' => $user->phone,
                'country' => $user->country, 'location' => $user->location];
        }


        return response()->json(['user' => $user_c, 'message' => 'you are client']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        //$user->photo=$request->get('photo');
        $user->country = $request->get('country');
        $user->location = $request->get('location');
        $user->save();
        //dd($user);

        return response()->json(['message' => 'Successfully updated']);

    }

    public function uploadPhoto(Request $request)
    {
        $this->validate($request, [

            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $user = JWTAuth::parseToken()->authenticate();
        $now = Carbon::now();
        $logo = $request->file('logo');
        if ($logo) {
            $input['photoname'] = 'new' . $user->id . '.' . $now . '.' . $logo->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $logo->move($destinationPath, $input['photoname']);
            $user->photo = 'images/' . $input['photoname'];
            $user->save();
            return response()->json(['success', 'Logo Upload successful']);
        } else
            return response()->json(['Error', 'no way']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $u_like = false;
        
        $result = array();
        $advertisements = array();
        //$likers_id=array();

        $salons = User::where([
            ['role', 'like', 'salon']
        ])->inRandomOrder()->get();
        foreach ($salons as $s) {
            $likers_id = array();
            $u_like = false;
            foreach ($s->followers as $f) {
                array_push($likers_id, $f->id);

            }

            //
            if (in_array($user->id, $likers_id))
                $u_like = true;

            if ($s->business_photos) {

                $result[] = array(
                    'salon_id' => $s->id,
                    'salon_name_ar' => $s->business_name_ar,
                    'salon_name' => $s->business_name,
                    'salon_logo' => $s->photo,
                    'salon_country' => $s->country,
                    'salon_business_photos' => $s->business_photos,
                    'services' => $s->services,
                    'likes' => $s->likes,
                    'type' => 'salon',
                    'u_like' => $u_like

                );
            }


        }
        $advs = Advertisement::all();
        foreach ($advs as $a) {
            $advertisements[] = array(
                'salon_id' => (int)$a->user_id,
                'name' => $a->name,
                'type' => 'advertisement'

            );

        }

        for ($i = 0, $x = rand(1, sizeof($result)); $i < sizeof($advertisements); $i++, $x = $x + 3) {
            //$x=rand(1,sizeof($result));
            array_splice($result, $x, 0, array($advertisements[$i]));
        }
        //dd($result);
        $paginate = 3;
        $page = Input::get('page', 1);
        $offSet = ($page * $paginate) - $paginate;
        $itemsForCurrentPage = array_slice($result, $offSet, $paginate, true);
        $result = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($result), $paginate, $page);
        $result = $result->toArray();
        return $result;
        //return new JsonResponse(['results' => $paginatedSearchResults]);
    }

    public function getArticleFromPosition(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $fs=$user->favorits();
        $brunds = array();
        $colors = array();

        foreach ($fs as $f) {
            array_push($brunds, $f->favorit_brand);

        }
        foreach ($fs as $f) {
            array_push($colors, $f->favorit_color);

        }


        $position=$request->get('position');

        $articles=Article::where([
            ['city', 'like', $position ]
        ])->whereIn(['brund', $brunds],['color', $colors])
        ->get();

        return response()->json(['articles'=>$articles]);


    }

    public function get_salon(Request $request)
    {
//        $service_array_id=[];
        $id = $request->get('id');
        $salon = User::find($id);
        $days = $salon->days;
        $services = $salon->services;

        $business_photos = $salon->business_photos;
        $menu_photos = $salon->menu_photos;
        $facilities = $salon->facilities;
        $result = array();
        foreach ($services as $s) {
            $serv = Service::find($s->id);
            $result[] = array(
                'Service_id' => $serv->id,
                'Service_name' => $serv->sub_interest->name,
                'Service_name_ar' => $serv->sub_interest->name_ar,
                'Service_price' => $serv->price
            );

        }
        return response()->json(['salon' => $salon, 'services_list' => $result, 'days' => $days]);
    }

    public function search(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $service = $request->get('service');
        $salon = $request->get('salon');
        $location = $request->get('location');
        $sub_array_id = [];
        $salon_array_id = [];
        $result = array();
        /* search using service */
        if ($service and !$salon and !$location) {
            $subs = Sub_interest::where('name', 'like', '%' . $service . '%')->get();
            foreach ($subs as $s) {
                array_push($sub_array_id, $s['id']);
            }
            $services = DB::table('services')
                ->whereIn('sub_interest_id', $sub_array_id)
                ->pluck('user_id');
            //            $services = Service::where('name','like','%'.$service.'%')
//                ->pluck('user_id');
            // dd($services);
//            $salons_1 = DB::table('users')
//                ->where('role', 'salon')
//                ->whereIn('id', $services)
//                ->get();
            $salons = User::where('role', 'like', 'salon')
                ->whereHas('facilities', function ($query) use ($service) {
                    $query->where('name', 'like', '%' . $service . '%');
                })
                ->orwhereIn('id', $services)
                ->inRandomOrder()
                ->get();


        } /* search using salon */
        elseif ($salon and !$service and !$location) {
            $salons = User::where([
                ['role', 'like', 'salon'],
                ['business_name', 'like', '%' . $salon . '%']
            ])->inRandomOrder()
                ->get();


        } /* search using location */
        elseif ($location and !$service and !$salon) {
            $salons = User::where([
                ['role', 'like', 'salon'],
                ['location', 'like', '%' . $location . '%']
            ])->inRandomOrder()->get();

        } /* search using service and salon */
        elseif ($service and $salon and !$location) {
            $subs = Sub_interest::where('name', 'like', '%' . $service . '%')->get();
            foreach ($subs as $s) {
                array_push($sub_array_id, $s['id']);
            }
            $services = DB::table('services')
                ->whereIn('sub_interest_id', $sub_array_id)
                ->pluck('user_id');
            $salons = User::where([['role', 'like', 'salon'], ['business_name', 'like', '%' . $salon . '%']])
                ->whereHas('facilities', function ($query) use ($service) {
                    $query->where('name', 'like', '%' . $service . '%');
                })
                ->orwhereIn('id', $services)
                ->inRandomOrder()
                ->get();


        } /* search using service and location */
        elseif ($service and $location and !$salon) {

            $subs = Sub_interest::where('name', 'like', '%' . $service . '%')->get();
            foreach ($subs as $s) {
                array_push($sub_array_id, $s['id']);
            }
            $services = DB::table('services')
                ->whereIn('sub_interest_id', $sub_array_id)
                ->pluck('user_id');
            $salons = User::where([['role', 'like', 'salon'], ['location', 'like', '%' . $location . '%']])
                ->whereHas('facilities', function ($query) use ($service) {
                    $query->where('name', 'like', '%' . $service . '%');
                })
                ->orwhereIn('id', $services)
                ->inRandomOrder()
                ->get();


        } /* search using salon and location */
        elseif ($salon and $location and !$service) {
            $salons = User::where([
                ['role', 'like', 'salon'],
                ['location', 'like', '%' . $location . '%'],
                ['business_name', 'like', '%' . $salon . '%']
            ])->get();

        } else {

            $subs = Sub_interest::where('name', 'like', '%' . $service . '%')->get();
            foreach ($subs as $s) {
                array_push($sub_array_id, $s['id']);
            }
            $services = DB::table('services')
                ->whereIn('sub_interest_id', $sub_array_id)
                ->pluck('user_id');

            $salons = User::where([['role', 'like', 'salon'], ['business_name', 'like', '%' . $salon . '%'], ['location', 'like', '%' . $location . '%']])
                ->whereHas('facilities', function ($query) use ($service) {
                    $query->where('name', 'like', '%' . $service . '%');
                })
                ->orwhereIn('id', $services)
                ->inRandomOrder()
                ->get();


        }

        foreach ($salons as $s) {
            $likers_id = array();
            $u_like = false;
            foreach ($s->followers as $f) {
                array_push($likers_id, $f->id);

            }

            //
            if (in_array($user->id, $likers_id))
                $u_like = true;

            if ($s->business_photos) {

                $result[] = array(
                    'salon_id' => $s->id,
                    'salon_name_ar' => $s->business_name_ar,
                    'salon_name' => $s->business_name,
                    'salon_logo' => $s->photo,
                    'salon_country' => $s->country,
                    'salon_business_photos' => $s->business_photos,
                    'services' => $s->services,
                    'likes' => $s->likes,
                    'type' => 'salon',
                    'u_like' => $u_like

                );
            }


        }
        $paginate = 3;
        $page = Input::get('page', 1);
        $offSet = ($page * $paginate) - $paginate;
        $itemsForCurrentPage = array_slice($result, $offSet, $paginate, true);
        $result = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($result), $paginate, $page);
        $result = $result->toArray();
        return $result;


    }

    public function changePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $old = $request->get('old_password');
        $new = $request->get('new_password');
        //dd(Hash:: ($old,$user->password));
        //dump($old);
        if (Hash::check($old, $user->password)) {
            //dd($new);
            $user->password = Hash::make($new);
            $user->save();
            return response()->json(['message' => 'password changed']);

        } else
            return response()->json(['message' => 'old password incorrect']);


    }

    public function forgetPassword(Request $request)
    {
        //$user = JWTAuth::parseToken()->authenticate();
        $mdp = "ok";
        Mail::to($request->get('email'))
            ->send();
        return response()->json(['message' => 'mail sent']);
    }

    public function contactUs(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $email_from = $user->email;
        $subject = $request->get('subject');
        $content = $request->get('message');
        $title = '';
        $email = 'fareswardeni@gmail.com';
        if (count(Mail::failures()) <= 0) {
            Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message) use ($email, $subject, $email_from) {
                $message->from($email_from, 'Lily');
                $message->to($email)->subject($subject);

            });
            return response()->json(['message' => 'Request completed']);
        } else
            return response()->json(['message' => 'Error']);


    }

    public function suggestSalon(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $email_from = $user->email;
        $salon_name = $request->get('salon_name');
        $address = $request->get('address');
        $phone_number = $request->get('phone_number');
        //$content='Salon name : '.$salon_name.'<br>'.'Address: '.$address.'<br>'.'Phone number: '.$phone_number;
        $title = '';
        $email = 'fareswardeni@gmail.com';
        if (count(Mail::failures()) <= 0) {
            Mail::send('emails.sendSuggest', ['title' => $title, 'salon_name' => $salon_name, 'address' => $address, 'phone_number' => $phone_number]
                , function ($message) use ($email, $email_from) {
                    $message->from($email_from, 'Lily');
                    $message->to($email)->subject('Suggesstion');

                });
            return response()->json(['message' => 'Request completed']);
        } else
            return response()->json(['message' => 'Error']);

    }

    public function reportError(Request $request)
    {
        $errors = $request->errors;
        //dd($errors);
        $user = JWTAuth::parseToken()->authenticate();
        $email_from = $user->email;
        $content = '';
        foreach ($errors as $es => $error) {
            $content = $content . $error . ',';
        }

        $title = '';
        $email = 'fareswardeni@gmail.com';
        if (count(Mail::failures()) <= 0) {
            Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message) use ($email, $email_from) {
                $message->from($email_from, 'Lily');
                $message->to($email)->subject('Report error');

            });
            return response()->json(['message' => 'Request completed']);
        } else
            return response()->json(['message' => 'Error']);


    }

    public function likeSalon(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $id = $request->get('id');
        $salon = User::find($id);
        $user->following()->save($salon);
        //$user->following()->detach($salon);
        //$user->users()->attach($id);
        //$salon->users()->attach($user->id);
        $salon->likes++;

        if ($salon->save())
            return response()->json(['message' => 'Success']);
        else
            return response()->json(['message' => 'error']);

    }

    public function dislikeSalon(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $id = $request->get('id');
        $salon = User::find($id);
        $user->following()->detach($salon);
        $salon->likes--;

        if ($salon->save())
            return response()->json(['message' => 'Success']);
        else
            return response()->json(['message' => 'error']);

    }


}
