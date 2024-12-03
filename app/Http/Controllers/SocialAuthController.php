<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Auth\User;

use Auth;
use Socialite;
use Cache;


class SocialAuthController extends Controller
{
    
    /**
     * @param string $provider
     *
     * @return $this
     */
    public function providerRedirect($provider = ''){
        
        if (!in_array($provider, ['facebook', 'twitter', 'github'])) {
            return redirect('/login')->withErrors('This is not a valid link.');
        }
        

        return Socialite::driver($provider)->redirect();
        
    }


    /**
     * @param string $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function providerRedirectCallback($provider = ''){
        
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('/auth/social');
        }
        

        if(($user_exists = User::whereEmail($user->email)->first()) != null)
            $token = $user_exists->createToken('API Social Access for '.$user_exists->email,['*'])->accessToken;
        else{
            
            $new_user = new User();
            $new_user->email = $user->email;
            $new_user->name = $user->name;
            $new_user->provider = $provider;
            $new_user->provider_unique_id = $user->id;
            $new_user->status = 'activated';
            $new_user->activation_token = generateUuid();
            $new_user->save();

            $token = $new_user->createToken('API Social Access for '.$user_exists->email,['*'])->accessToken;
            
        }


        Cache::put('access_token', $token, 1);
        return redirect('/auth/social');
        
    }
    

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(){
        
        if (!Cache::has('access_token')) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        $token = Cache::get('access_token');
        Cache::forget('access_token');


        return response()->json(['message' => 'You are successfully logged in!', 'token' => $token]);
        
    }
    
    
}
