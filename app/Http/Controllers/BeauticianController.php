<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class BeauticianController extends Controller
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $password = $request->get('password');
        $user = ['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'beautician', 'phone' => $phone];
        User::create($user);
        return $this->authenticate($request);

    }

    /**
     * @param ProviderUser $providerUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $user = DB::table('users')->where('provider', 'facebook')
            ->where('provider_user_id', $providerUser->getId())
            ->first();
        if ($user) {
            $req = new Request($user);
            return $this->authenticate($req);
        } else {
            $user = User::whereEmail($providerUser->getEmail())->first();
            if (!$user) {
                $user = ['name' => $providerUser->getName(), 'email' => $providerUser->getEmail(), 'password' => $providerUser->getId(),
                    'provider_user_id' => $providerUser->getId(),
                    'provider' => 'facebook'];

            }
            $req = new Request($user);
            return $this->signUp($req);

        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['result' => $user, 'message' => 'you are beautician']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
