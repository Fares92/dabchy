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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password','role');

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
    public function getSalons()
    {
        $result=array();
        $salons=User::where([
            ['role','like','salon']
        ])->get();
        foreach ($salons as  $s) {
            $result[]=array(
                'salon_id'=>$s->id,
                'salon_name_ar'=>$s->business_name_ar,
                'salon_name'=>$s->business_name,
                'salon_owner_name'=>$s->name,
                'salon_owner_email'=>$s->email,
                'salon_location'=>$s->location,
                'salon_photo'=>$s->photo

            );
        }
        return $result;
    }
    public function editProfile($id_s)
    {
        $user_c = null;
        $user = User::find($id_s);
        if (!$user->provider) {
            $user_c = ['name'=>$user->name,'business_name' => $user->business_name,'business_name_ar'=>$user->business_name_ar, 'email' => $user->business_email,'photo' => $user->photo, 'phone' => $user->phone,
                'business_type'=>$user->business_type,'facebook'=>$user->link_facebook,'instagram'=>$user->link_instagram,'twitter'=>$user->link_twitter,'mobile' => $user->mobile_number,
                'website'=>$user->website,'loyalty_program'=>$user->loyalty_program,
                'address'=>$user->address,'additional_info'=>$user->additional_info,'home_service'=>$user->home_service,
                'country' => $user->country, 'location' => $user->location,'latitude'=>$user->latitude,
                'longitude'=>$user->longitude,'credit_card'=>$user->credit_card,'member_ships'=>$user->member_ships];
        }

        $business_photo=$user->business_photos;
        $menu_photo=$user->menu_photos;
        $facilities=$user->facilities;
        $days=$user->days;
        $partner_ships=$user->partnerShips;
        return response()->json(['user' => $user_c, 'business_photos' => $business_photo,
            'menu_photos'=>$menu_photo,
            'days'=>$days,'facilities'=>$facilities,'partner_ships'=>$partner_ships]);
    }
    public function updateProfile(Request $request,$id_s)
    {
        $user = User::find($id_s);
        $user->name=$request->get('name');
        $user->business_name = $request->get('business_name');
        $user->business_type = $request->get('business_type');
        $user->business_name_ar = $request->get('business_name_ar');
        $user->business_email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->mobile_number = $request->get('mobile');
        $user->credit_card=$request->get('credit_card');
        $user->member_ships=$request->get('member_ships');
        $user->additional_info=$request->get('additional_info');
        $user->home_service=$request->get('home_service');
        //$user->photo=$request->get('logo');
        $user->link_facebook=$request->get('link_facebook');
        $user->link_instagram=$request->get('link_instagram');
        $user->link_twitter=$request->get('link_twitter');
        $user->loyalty_program=$request->get('loyalty_program');
        $user->website=$request->get('website');
        $user->country = $request->get('country');
        $user->location = $request->get('location');
        $user->latitude= $request->get('latitude');
        $user->longitude=$request->get('longitude');


        $user->save();

        return response()->json(['message' => 'Successfully updated']);

    }
    public function upload_photo(Request $request,$id_s)
    {
        $user = User::find($id_s);
        $now = Carbon::now();
        $logo=$request->file('logo');
        // dd($logo);
        if($logo)
        {
            $logo = $request->file('logo');
            $input['photoname'] = 'new'.$user->id.'.'.$now.'.'.$logo->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $logo->move($destinationPath, $input['photoname']);
            $user->photo ='images/'.$input['photoname'];
            $user->save();
            return response()->json(['success','Logo Upload successful']);
        }




        else
            return response()->json(['error','error upload']);

    }
    public function add_address(Request $request,$id_s){
        if($request->get('address'))
        {
            $user =$user = User::find($id_s);
            $user->address =$request->get('address');
            $user->country = $request->get('country');
            $user->location = $request->get('location');
            $user->latitude=$request->get('latitude');
            $user->longitude=$request->get('longitude');
            $user->save();
            return response()->json(['message' => 'address successfully updated']);
        }
        return response()->json(['message' => 'address not found']);
    }
    public function attach_facilities(Request $request,$id_s)
    {
        $user = User::find($id_s);
//        //dd($request->facilities);
//        foreach ($request->facilities as $i => $f) {
//            $facility =['name'=>$f['name']];
//            $sfacility=Facility::create($facility);
//            $user->facilities()->save($sfacility);
//        }
//        return response()->json('success');
        $name=$request->get('name');
        if($name)
        {
            $facility =['name'=>$name];
            $sfacility=Facility::create($facility);
            $user->facilities()->save($sfacility);
            return response()->json('success');
        }
        else
            return response()->json('error');
    }
    public function attach_days(Request $request,$id_s)
    {

        $user = User::find($id_s);

        $d_id=$request->get('d_id');
        $h_from=$request->get('from');
        $h_to=$request->get('to');
        //dd($d_id);
        if($d_id<8)
        {
            $day= Day::find($d_id);
            //dd($day);
            $user->days()->detach($day);
            $user->days()->save($day,['h_from'=>$h_from,'h_to'=>$h_to]);
            //$user->pivot->save();
            return response()->json('success');
        }

        else
            return response()->json('error day');
    }
    public function attach_partnerShips(Request $request,$id_s)
    {
        $user = User::find($id_s);
        $name=$request->get('name');
        $percent=$request->get('percent');
        if($name&&$percent)
        {
            //foreach ($request->partner_ships as $i => $f) {
            $partner_ship =['name'=>$name,'percent'=>$percent];
            $sPartner_ship=PartnerShip::create($partner_ship);
            $user->partnerShips()->save($sPartner_ship);
            // }
            return response()->json('success');
        }
        return response()->json('error');
    }
    public function detach_partnerShip($id_p){

        //$id=$request->get('id_partner_ship');
        //dd($id_p);
        $partner_ship=PartnerShip::find($id_p);
        $partner_ship->delete();
        return response()->json(['message'=>'success delete']);
    }
    public function add_service(Request $request,$id_s)
    {

        $user = User::find($id_s);
        $service_sub_id=$request->get('sub_interest_id');
        $sub_interest=Sub_interest::find($service_sub_id);
        //dd($sub_interest);
        //$service_price=$request->get('price');
        //$service_type_price=$request->get('type_price');
        $service0=['price'=>0,'type_price'=>''];
        $service=Service::create($service0);
        $user->services()->save($service);
        $sub_interest->services()->save($service);

        return response()->json(['success','Service created']);

    }
    public function detach_service($id_serv){

        //$id=$request->get('id_partner_ship');
        //dd($id_p);
        $service=Service::find($id_serv);
        $service->delete();
        return response()->json(['message'=>'success delete']);
    }
    public function get_services($id_s)
    {
        $user = User::find($id_s);
        $services = $user->services;


        $result = array();
        foreach ($services as $s) {
            $serv = Service::find($s->id);
            $result[] = array(
                'Service_name' => $serv->sub_interest->name
                //'service' => $serv,
            );
        }
        return response()->json(['result', $result]);

    }
    public function getClients()
    {
        $result=array();
        $salons=User::where([
            ['role','like','client']
        ])->get();
        foreach ($salons as  $s) {
            $result[]=array(
                'client_id'=>$s->id,
                'client_name'=>$s->name,
                'client_email'=>$s->email,
                'client_photo'=>$s->photo,
                'client_phone'=>$s->phone,
                'client_mobile'=>$s->mobile_number,
                'client_country'=>$s->coutry,
                'client_location'=>$s->location
            );
        }
        return $result;
    }
    public function change_password(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $old=$request->get('old_password');
        $new=$request->get('new_password');
        //dd(Hash:: ($old,$user->password));
        //dump($old);
        if(Hash::check($old,$user->password))
        {
            //dd($new);
            $user->password=Hash::make($new);
            $user->save();
            return response()->json(['message' => 'password changed']);

        }
        else
            return response()->json(['message' => 'old password incorrect']);
    }
    public function add_advertisement(Request $request,$id_s)
    {
        $now = Carbon::now();
        $user = User::find($id_s);
        $advertisements= $request->file('advertisement');
        if($advertisements)
        {
            $i=1;
//            foreach($menu_photos as $m_photo) {
            // Set the destination path
            $destinationPath = public_path('/ad_images');
            // Get the orginal filname or create the filename of your choice
            $input['photoname'] = $i.'.'.$user->id.'.'.$now.'.'.$advertisements->getClientOriginalExtension();
            // Copy the file in our upload folder
            $advertisements->move($destinationPath, $input['photoname']);
            $adv =['name'=>'ad_images/'.$input['photoname']];
            $adphoto=Advertisement::create($adv);
            $user->advertisements()->save($adphoto);

            //         }
            return response()->json(['success','advertisement Upload successful']);
        }
        else
            return response()->json(['error','error upload']);
    }
    public function delete_advertisement($id_ad)
    {


        $advertisement=Advertisement::find($id_ad);
        $advertisement->delete();
        return response()->json(['message'=>'success delete']);

    }
    public function get_advertisement($id_s)
    {
        $advertisement=Advertisement::where([
            ['user_id','=',$id_s]
        ])->orderBy('id', 'desc')->first();;
        return $advertisement;
    }
    public function attach_business_photos(Request $request,$id_s)
    {
        $now = Carbon::now();
        $user = User::find($id_s);
        $business_photos= $request->file('business_photos');

        if($business_photos)
        {
            $i=1;
            //          foreach($business_photos as $b_photo) {
            // Set the destination path
            $destinationPath = public_path('/b_images');
            // Get the orginal filname or create the filename of your choice
            $input['photoname'] = 'b'.'.'.$user->id.'.'.$now.'.'.$business_photos->getClientOriginalExtension();
            // Copy the file in our upload folder
            $business_photos->move($destinationPath, $input['photoname']);
            $business_photo =['name'=>'b_images/'.$input['photoname']];

            $bphoto=Business_photo::create($business_photo);
            $user->business_photos()->save($bphoto);
            $i++;
            //         }
            return response()->json(['success','business_photos Upload successful']);
        }
        else
            return response()->json(['error','error upload']);



    }
    public function attach_menu_photos(Request $request,$id_s)
    {
        $now = Carbon::now();
        $user = User::find($id_s);
        $menu_photos= $request->file('menu_photos');
        if($menu_photos)
        {
            $i=1;
//            foreach($menu_photos as $m_photo) {
            // Set the destination path
            $destinationPath = public_path('/m_images');
            // Get the orginal filname or create the filename of your choice
            $input['photoname'] = 'm'.'.'.$user->id.'.'.$now.'.'.$menu_photos->getClientOriginalExtension();
            // Copy the file in our upload folder
            $menu_photos->move($destinationPath, $input['photoname']);
            $menu_photo =['name'=>'m_images/'.$input['photoname']];
            $mphoto=Menu_photo::create($menu_photo);
            $user->menu_photos()->save($mphoto);

            //         }
            return response()->json(['success','menu_photos Upload successful']);
        }
        else
            return response()->json(['error','error upload']);
    }

}
