<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiUploadAssetRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetRequest;
use App\Http\Requests\Auth\ResetPinRequest;
use App\Http\Requests\Auth\ValidatePinRequest;
use App\Http\Requests\Auth\ResetConfirmRequest;
use App\Http\Requests\Auth\ResetChangeRequest;
use App\Http\Requests\Auth\RegisterLocationRequest;

use App\Services\Auth\AuthService;
use App\Services\Vendors\BiotrakDataService;

use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;
use App\Models\Auth\Task;

use App\Helpers\Util;

use Auth;
use Gravatar;
use Hash;
use DB;
use Carbon\Carbon;
use stdClass;
use Exception;

use Illuminate\Support\Facades\Log;

/**
 * AuthController.
 */
class AuthController extends ApiController
{

    private $service;


    public function __construct(AuthService $service){

        $this->service = $service;

    }


    /* init user authentication */
    public function authenticate(LoginRequest $request){

        if(($user = User::whereEmail($request->input('email'))->first()) == null) abort(422,'User does not Exist here.');
        elseif($user->status == 'pending_activation') abort(422,'Your account hasn\'t been activated. Please check your email & activate account.');
        elseif($user->status == 'denied') abort(422,'Your account is banned. Please contact system administrator.');
        elseif ($user->status != 'activated') abort(422,'There is something wrong with your account. Please contact system administrator.');


        /* authenticate via password */
        if(Hash::check($request->input('password'), $user->password)){

            $user->avatar = Gravatar::fallback('/images/users/avatar.png')->get($user->email);  // update gravatar avatar
            $user->save();

            $accessToken = $user->createToken('API Access for '.$user->email,['*'])->accessToken; // issue new access token
            return response()->json([
                'message'       => 'You are successfully logged in!',
                'access_token'  => $accessToken
            ],200);
        }else abort(422,'Password does not Match user email');

    }


    /* initiate user data */
    public function loadAuthLocation(Request $request){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(!is_object($data = $this->service->loadLocationData($request->all()))) abort(400,$data);
        else return response()->json((array)$data,200);

    }

    /* change an authorized users location */
    public function changeAuthLocation(Request $request, $location){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);

        $user->location_id = $location;                                         // assign the current user to the desired location
        $user->save();


        if(!is_object($data = $this->service->loadLocationData($request->all()))) abort(400,$data);
        else return response()->json((array)$data,200);

    }

    /* clear any cache */
    public function clearAuthLocation(Request $request,$location){

        if(($user = Auth::guard('api')->user()) != null)
            if(method_exists($user->token(),'revoke'))
                $user->token()->revoke();

        // HINT: clear [ANY] location data cached..





		return response()->json(['message'  => 'User Site Cache has been Cleared'],200);

    }


    /* toggle mode */
    public function toggleMode(Request $request){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($success = $this->service->toggleMode($request->all(),$user)) == null) abort(400,'Couldnt toggle mode');
        elseif(!is_object($data = $this->service->loadLocationData($request->all()))) abort(400,$data);

        return response()->json((array)$data,200);

    }


    /* logout - by removing the personal token for the user if logged in */
    public function logout(){

        if(($user = Auth::guard('api')->user()) != null)
            if(method_exists($user->token(),'revoke'))
                $user->token()->revoke();


        return response()->json(['message' => 'You are successfully logged out!']);

    }


    /* register new user */
    public function register(RegisterRequest $request){

        if(($user = $this->service->createFromRegistration($request->all())) == null) abort(422,'Whoops - there was a hiccup with registration - please try again later.');


        $accessToken = $user->createToken('Cloud Access for '.$user->email,['*'])->accessToken; // issue new access token
        return response()->json([
                'message'       => 'Your Location has been Successfully Registered and you are logged in, Enjoy!',
                'access_token'  => $accessToken
        ],200);

    }


    /* register new location */
    public function registerLocation(RegisterLocationRequest $request){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($new = $this->service->registerNewLocation($request->all(),$user)) == null) abort(422,'Whoops - there was a hiccup with your location registration - please try again later.');


        return response()->json([
                'message'       => 'Your new location has been successfully Registered.  Please finish configuring your settings for this location..',
                'location_id'  => $new->id
        ],200);

    }



    /* activate new user */
    public function activate($activation_token){

        if(($user = User::whereActivationToken($activation_token)->first()) == null) abort(422,'Whoops - Activation link is invalid or expired - Please request another activation email from admin.');
        elseif($user->status == 'activated') abort(422,'Your account has already been activated!');
        elseif($user->status == 'denied') abort(422,'This account has been denied - you cannot activate');
        elseif($user->status != 'pending_activation') abort(422,'Invalid activation token!');


        $user = $this->service->activateUser($user);
        return response()->json(['message' => 'Your account has been activated!']);

    }


    /* forgot pasword */
    public function password(Request $request){

        if(($user = User::whereEmail($request->input('email'))->first()) == null) abort(422,'We couldn\'t found any user with this email. Please try again!');


        $user = $this->service->initiatePasswordReset($user,$request->all());
        return response()->json(['message' => 'We have sent you a password reminder email.  Please check your inbox in a few!']);

    }

    /* request re-activation via admin */
    public function reactivateRequest(Request $request){
        if(($user = User::whereEmail($request->input('email'))->first()) == null) {
            abort(422,'User does not Exist here.');
        } elseif($user->status == 'pending_activation') {
            $user = $this->service->initiateReactivateUserRequest($user,$request->all());
            return response()->json(['message' => 'We have emailed the site administrator your request.  Please contact them directly if for further assistance.']);
        } else {
            abort(422,'User does not Exist here.');
        }
    }

    /* validate password reset */
    public function validatePasswordReset(Request $request){

        if(($_token = $request->input('token')) == null) abort(422,'there was no reset token provided');
        elseif(($reset = DB::table('auth_password_resets')->where('token',$_token)->first()) == null) abort(422,'Invalid password reset token!');
        elseif(strtotime($reset->created_at) > (time()+3600)) abort(422,'Password reset token is expired. Please go back to login.');


        return response()->json(['message' => '']);

    }


    /* reset and change passwords */
    public function reset(ResetRequest $request){

        if(($user = User::whereEmail($request->input('email'))->first()) == null) abort(422,'We couldn\'t found any user with this email. Please try again!');
        elseif(($reset = DB::table('auth_password_resets')->where('email', '=', $request->input('email'))->where('token', '=',$request->input('token'))->first()) == null) abort(422,'Invalid password reset token!');
        elseif(strtotime($reset->created_at) > (time()+3600)) abort(422,'Password reset token is expired. Please request reset password again!');


        $user = $this->service->passwordReset($user,$request->all());
        return response()->json(['message' => 'Your password has been reset. Please login with new password..']);

    }


    /* change password */
    public function changePassword(ResetChangeRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($change = User::find($id)) == null) abort(422,'We are having a hiccup finding the user resource - aborting.');
        elseif(Hash::check($request->input('password'), $change->password)) abort(422,'New password cannot be same as existing password!');


        $change->password = bcrypt($request->input('password'));
        $change->save();


        return response()->json(['message' => ($user->id==$change->id ? 'Your password has been successfully changed.' : 'The password for '.$change->email.' has been successfully changed.')]);

    }

    /* change pin */
    public function changePIN(ResetPinRequest $request,$id){
        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($change = User::find($id)) == null) abort(422,'We are having a hiccup finding the user resource - aborting.');
        elseif($request->input('pincode') === $change->pincode) abort(422,'New PIN cannot be same as existing PIN!');

        $change->pincode = $request->input('pincode');
        $change->save();


        return response()->json(['message' => ($user->id == $change->id ? 'Your pin has been successfully changed.' : 'The PIN for '.$change->email.' has been successfully changed.')]);

    }


    /* (re)send activation request */
    public function sendActivation(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($staff = User::find($id)) == null) abort(422,'We are having a hiccup finding the user resource - aborting.');
        elseif($user->id==$staff->id) abort(422,'You cannot send an activation prompt to yourself.');


        $upd = $this->service->sendActivationRequest($staff,$request->all());
        return response()->json(['message' => 'We are sending a new Activation link request to '.$staff->email]);

    }

    /* a location owner requesting activation */
    public function requestActivation(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);

        $upd = $this->service->requestLocationActivation($request->all(),$id);
        return response()->json(['message' => 'Thank you - We are sending a new Activation request to admin and will be in touch shortly..']);

    }


    /* get a new token for logged in user */
    public function getANewToken(Request $request){

        if(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif($user->status == 'pending_activation') abort(422,'Your account hasn\'t been activated. Please check your email & activate account.');
        elseif($user->status == 'denied') abort(422,'Your account is banned. Please contact system administrator.');
        elseif ($user->status != 'activated') abort(422,'There is something wrong with your account. Please contact system administrator.');

        $accessToken = $user->createToken('API Access for '.$user->email,['*'])->accessToken; // issue new access token


        return response()->json([
                'message'       => 'A new API Access Token has been generated - Please save as this will not be presented again',
                'api_token'  => $accessToken
            ],200);

    }


    /* get model schema */
    public function loadSchemas(Request $request,$module,$list){

        if(!$list) abort(422,'Whoops - No schema names were prodived to load - aborting.');
        elseif(($user = Auth::guard('api')->user()) == null) return response(['authenticated' => false],401);
        elseif(($schemas = $this->service->loadSchemas(explode(',',$list),$module,$user)) == null) abort(422,'Whoops we could not load the schema files for some reason - aborting');







        return response()->json($schemas,200);

    }


    /* upload Image/File Asset */
    public function uploadAsset(ApiUploadAssetRequest $request,$model,$type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login in order to upload');
        elseif(($upd = $this->service->upload($request->file($type),$model,$type,$user->location_id)) == null) return response()->json(['message' => 'Couldnt Upload '.$type.' asset - General Issue - Aborting.'], 422);

        return $this->setStatusCode(201)->respond([
            'message'   => 'Successfully Uploaded '.ucwords($model).' Asset.',
            'uri'       => $upd
        ]);

    }


    /* get Regions */
    public function getRegions(Request $request,$country){

        $pay = Util::getStates($country);

        return response()->json((array)$pay,200);

    }

    /* Validate the PIN of an Adminstrator level user */
    public function validatePIN(ValidatePinRequest $request){

        if(($caller = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login in order to upload');


        if(($success = $this->service->validatePin($request->all(),$caller)) === true)
            return response()->json(['message' => 'Valid PIN'],200);
        else return response()->json(['message' => 'Invalid PIN'],403);

    }

    /* test a migration by type */
    public function testMigrationConnection(Request $request,$type) {

        if(($caller = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login in order to upload');

        switch($type){
            case 'btdb':
                $vs = new BiotrakDataService();
                return $vs->getBioTrakLocation($caller);
                break;
            default: abort(422, 'You need to Provide a type of Migration to test');
        }

    }

    public function startMigration(Request $request,$type) {

        if(($caller = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login in order to upload');

        switch($type){
            case 'btdb':
                $vs = new BiotrakDataService;
                return $vs->startMigration($request->all(),'btdb',$caller);
                break;
            default: abort(422, 'Whoops, you need to Provide a type for the migration');
        }

    }


}
