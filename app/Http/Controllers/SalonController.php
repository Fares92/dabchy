<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Business_photo;
use App\Day;
use App\Facility;
use App\Menu_photo;
use App\PartnerShip;
use App\Service;
use App\Sub_interest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Sub_InterestController;

class SalonController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate', 'signUp']]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password', 'role');

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

    public function logout()
    {
        //dd(JWTAuth::getToken());
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Success logout']);
    }

    //step1
    public function signUp(Request $request)
    {
        $rules = array(
            'name' => 'required',                        // just a normal required validation
            'email' => 'required|email|unique:users',     // required and must be unique in the ducks table
            'password' => 'required',
            'business_type' => 'required'
            // 'password_confirm' => 'required|same:password'           // required and has to match the password field
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            // get the error messages from the validator
            $messages = $validator->messages();
            return response()->json($messages);

        } else {

            $name = $request->get('name');
            $email = $request->get('email');
            $password = $request->get('password');
            $business_name = $request->get('business_name');
            $business_name_ar = $request->get('business_name_ar');
            $business_type = $request->get('business_type');
            $user = ['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'salon', 'business_name' => $business_name,
                'business_name_ar' => $business_name_ar, 'business_type' => $business_type];
            //dd($user) ;
            User::create($user);
            return $this->authenticate($request);
        }

    }

    //step2
    public function create_Profile(Request $request)
    {
        $rules = array(
            //'phone' => 'required',                        // just a normal required validation
            //'mobile_number' => 'required',     // required and must be unique in the ducks table
            'business_email' => 'required|email',
            // 'website' => 'required'
            // 'facebook'=>'required',
            //'instagram'=>'required',
            // 'password_confirm' => 'required|same:password'           // required and has to match the password field
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            // get the error messages from the validator
            $messages = $validator->messages();
            return response()->json($messages);


        } else {
            $user = JWTAuth::parseToken()->authenticate();
            $user->phone = $request->get('phone');
            $user->mobile_number = $request->get('mobile_number');
            $user->business_email = $request->get('business_email');
            $user->website = $request->get('website');
            $user->link_facebook = $request->get('facebook');
            $user->link_instagram = $request->get('instagram');
            $user->save();
            return response()->json(['message' => 'Successfully updated']);
        }
    }

    //step3 add address informations
    public function add_address(Request $request)
    {
        if ($request->get('address')) {
            $user = JWTAuth::parseToken()->authenticate();
            $user->address = $request->get('address');
            $user->country = $request->get('country');
            $user->location = $request->get('location');
            $user->latitude = $request->get('latitude');
            $user->longitude = $request->get('longitude');

//        // Split it into 3 pieces, with the delimiter being a comma. This creates an array.
//        $split = explode(",", $full_address) ;
//        // Get the last value in the array.
//        // count($split) returns the total amount of values.
//        // Use -1 to get the index.
//        $country=$split[count($split)-1];
//        $location=$split[count($split)-2];
//        $address=$split[count($split)-3];
//        $user->country = $country;
//        $user->location = $location;
//        $user->address = $address;


            $user->save();
            return response()->json(['message' => 'address successfully updated']);
        }
        return response()->json(['message' => 'address not found']);
    }

    //step4
    public function upload_Photo(Request $request)
    {
        $this->validate($request, [

            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $user = JWTAuth::parseToken()->authenticate();
        $now = Carbon::now();
        $logo = $request->file('logo');
        $input['photoname'] = 'new' . $user->id . '.' . $now . '.' . $logo->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $logo->move($destinationPath, $input['photoname']);
        $user->photo = 'images/' . $input['photoname'];
        $user->save();
        return response()->json(['success', 'Logo Upload successful']);

    }

    public function attach_business_photos(Request $request)
    {
        $now = Carbon::now();
        $user = JWTAuth::parseToken()->authenticate();
        $business_photos = $request->file('business_photos');

        if ($business_photos) {

            //          foreach($business_photos as $b_photo) {
            // Set the destination path
            $destinationPath = public_path('/b_images');
            // Get the orginal filname or create the filename of your choice
            $input['photoname'] = 'b' . '.' . $user->id . '.' . $now . '.' . $business_photos->getClientOriginalExtension();
            // Copy the file in our upload folder
            $business_photos->move($destinationPath, $input['photoname']);
            $business_photo = ['name' => 'b_images/' . $input['photoname']];

            $bphoto = Business_photo::create($business_photo);
            $user->business_photos()->save($bphoto);

            //         }
            return response()->json(['success', 'business_photos Upload successful']);
        } else
            return response()->json(['error', 'error upload']);


    }

    public function attach_menu_photos(Request $request)
    {
        $now = Carbon::now();
        $user = JWTAuth::parseToken()->authenticate();
        $menu_photos = $request->file('menu_photos');
        if ($menu_photos) {

//            foreach($menu_photos as $m_photo) {
            // Set the destination path
            $destinationPath = public_path('/m_images');
            // Get the orginal filname or create the filename of your choice
            $input['photoname'] = 'm' . '.' . $user->id . '.' . $now . '.' . $menu_photos->getClientOriginalExtension();
            // Copy the file in our upload folder
            $menu_photos->move($destinationPath, $input['photoname']);
            $menu_photo = ['name' => 'm_images/' . $input['photoname']];
            $mphoto = Menu_photo::create($menu_photo);
            $user->menu_photos()->save($mphoto);


            return response()->json(['success', 'menu_photos Upload successful']);
        } else
            return response()->json(['error', 'error upload']);
    }

    //step5
    public function complete_Profile(Request $request)
    {
        $rules = array(
            'credit_card' => 'required',
            'member_ships' => 'required',

        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            // get the error messages from the validator
            $messages = $validator->messages();
            return response()->json($messages);


        } else {
            $user = JWTAuth::parseToken()->authenticate();
            $user->credit_card = $request->get('credit_card');
            $user->member_ships = $request->get('member_ships');
            // dd($user->loyalty_program);
            $user->save();
            return response()->json(['message' => 'Success']);
        }
    }

    public function delete_business_photo($id)
    {


        $photo = Business_photo::find($id);
        $photo->delete();
        return response()->json(['message' => 'success delete']);

    }

    public function delete_menu_photo($id)
    {

        $photo = menu_photo::find($id);
        $photo->delete();
        return response()->json(['message' => 'success delete']);

    }

    public function add_Service(Request $request)
    {
        $userAuth = JWTAuth::parseToken()->authenticate();
        $user = User::find($userAuth->id);
        $service_sub_id = $request->get('sub_interest_id');
        $sub_interest = Sub_interest::find($service_sub_id);
        //dd($sub_interest);
        // $service_price=$request->get('price');
        // $service_type_price=$request->get('type_price');
        $service0 = ['name' => $sub_interest->name, 'name_ar' => $sub_interest->name_ar];
        $service = Service::create($service0);
        $user->services()->save($service);
        $sub_interest->services()->save($service);

        return response()->json(['success', 'Service created']);

    }

    public function detach_Service($id_serv)
    {

        //$id=$request->get('id_partner_ship');
        //dd($id_p);
        $service = Service::find($id_serv);
        $service->delete();
        return response()->json(['message' => 'success delete']);
    }

    public function get_services()
    {
        $userAuth = JWTAuth::parseToken()->authenticate();
        $user = User::find($userAuth->id);
        $services = $user->services;


        $result = array();
        foreach ($services as $s) {
            $serv = Service::find($s->id);
            $result[] = array(
                'Service_name' => $serv->sub_interest->name,
                //'service' => $serv,
            );
        }
        return response()->json(['result', $result]);

    }

    public function editProfile()
    {
        $user_c = null;
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user->provider) {
            $user_c = ['name' => $user->name, 'business_name' => $user->business_name, 'business_name_ar' => $user->business_name_ar, 'email' => $user->business_email,
                'photo' => $user->photo, 'phone' => $user->phone,
                'business_type' => $user->business_type, 'facebook' => $user->link_facebook,
                'instagram' => $user->link_instagram, 'twitter' => $user->link_twitter, 'mobile' => $user->mobile_number, 'address' => $user->address,
                'website' => $user->website, 'loyalty_program' => $user->loyalty_program, 'additional_info' => $user->additional_info,
                'country' => $user->country, 'location' => $user->location, 'latitude' => $user->latitude, 'home_service' => $user->home_service,
                'longitude' => $user->longitude, 'credit_card' => $user->credit_card, 'member_ships' => $user->member_ships];
        }

        $business_photo = $user->business_photos;
        $menu_photos = $user->menu_photos;
        $facilities = $user->facilities;
        $days = $user->days;
        $partner_ships = $user->partnerShips;
        return response()->json(['user' => $user_c, 'business_photos' => $business_photo, 'menu_photos' => $menu_photos,
            'days' => $days, 'facilities' => $facilities, 'partner_ships' => $partner_ships]);
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->name = $request->get('name');
        $user->business_name = $request->get('business_name');
        $user->business_type = $request->get('business_type');
        $user->business_name_ar = $request->get('business_name_ar');
        //$user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->mobile_number = $request->get('mobile');
        $user->credit_card = $request->get('credit_card');
        $user->member_ships = $request->get('member_ships');
        //$user->loyalty_program=$request->get('loyalty_program');
        $user->additional_info = $request->get('additional_info');
        $user->home_service = $request->get('home_service');

//        $now = Carbon::now();
//        $logo = $request->file('logo');
//        $input['photoname'] = $user->id.'.'.$now.'.'.$logo->getClientOriginalExtension();
//        $destinationPath = public_path('/images');
//        $logo->move($destinationPath, $input['photoname']);
//        $user->photo ='images/'.$input['photoname'];
        $user->link_facebook = $request->get('link_facebook');
        $user->link_instagram = $request->get('link_instagram');
        $user->link_twitter = $request->get('link_twitter');
        $user->website = $request->get('website');
        $user->country = $request->get('country');
        $user->location = $request->get('location');
        $user->latitude = $request->get('latitude');
        $user->longitude = $request->get('longitude');
        $user->save();

        return response()->json(['message' => 'Successfully updated']);

    }

    public function attach_facilities(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
//        //dd($request->facilities);
//        foreach ($request->facilities as $i => $f) {
//            $facility =['name'=>$f['name']];
//            $sfacility=Facility::create($facility);
//            $user->facilities()->save($sfacility);
//        }
//        return response()->json('success');
        $name = $request->get('name');
        if ($name) {
            $facility = ['name' => $name];
            $sfacility = Facility::create($facility);
            $user->facilities()->save($sfacility);
            return response()->json('success');
        } else
            return response()->json('error');
    }

    public function attach_days(Request $request)
    {

        $user0 = JWTAuth::parseToken()->authenticate();
        $user = User::find($user0->id);
        $d_id = $request->get('d_id');
        $h_from = $request->get('from');
        $h_to = $request->get('to');
        //dd($d_id);
        if ($d_id < 8) {
            $day = Day::find($d_id);
            //dd($day);
            $user->days()->detach($day);
            $user->days()->save($day, ['h_from' => $h_from, 'h_to' => $h_to]);
            //$user->pivot->save();
            return response()->json('success');
        } else
            return response()->json('error day');


    }

    public function attach_partnerShips(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        //dd($request->facilities);
        /*  foreach ($request->partner_ships as $i => $f) {
              $partner_ship =['name'=>$f['name'],'percent'=>$f['percent']];
              $sPartner_ship=PartnerShip::create($partner_ship);
              $user->partnerShips()->save($sPartner_ship);
          }
          return response()->json('success');*/
        $name = $request->get('name');
        $percent = $request->get('percent');
        if ($name && $percent) {
            //foreach ($request->partner_ships as $i => $f) {
            $partner_ship = ['name' => $name, 'percent' => $percent];
            $sPartner_ship = PartnerShip::create($partner_ship);
            $user->partnerShips()->save($sPartner_ship);
            // }
            return response()->json('success');
        }
        return response()->json('error');
    }

    public function detach_partnerShip($id_p)
    {

        //$id=$request->get('id_partner_ship');
        //dd($id_p);
        $partner_ship = PartnerShip::find($id_p);
        $partner_ship->delete();
        return response()->json(['message' => 'success delete']);
    }

    public function get_days()
    {
        $days = Day::all();
        //dd($days);
        return $days;
    }

    public function getAll()
    {
        $result = array();
        $salons = User::where([
            ['role', 'like', 'salon']

        ])->get();

        foreach ($salons as $s) {
            $result[] = array(
                'salon_name_ar' => $s->business_name_ar,
                'salon_name' => $s->business_name,
            );
        }

        return $result;
    }

    public function change_settings(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $name = $request->get('name');
        $email = $request->get('email');
        $old = $request->get('old_password');
        $new = $request->get('new_password');
        $user->name = $name;
        $user->email = $email;
        if ($old) {
            if (Hash::check($old, $user->password)) {
                //dd($new);
                $user->password = Hash::make($new);

                //return response()->json(['message' => 'password changed']);

            } else
                return response()->json(['message' => 'old password incorrect']);
        }
        $user->save();
        return response()->json(['message' => 'success']);

    }

    public function salonFollowers()
    {

        $user = JWTAuth::parseToken()->authenticate();

        if ($user) {
            foreach ($user->followers AS $follower) {
                echo $follower->email . "<br />";
            }
        }
    }

}
