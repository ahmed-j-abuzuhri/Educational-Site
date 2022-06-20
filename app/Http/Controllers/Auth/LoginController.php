<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
        $find_user = User::where('email', $user->getEmail())->first();
        if($find_user)
        {
            Auth::login($find_user);
            return redirect("/");
        }
        else
        {
            $new_user = new User;
            $new_user->name = $user->getName(); 
            $new_user->email = $user->getEmail(); 
            $new_user->password = bcrypt(12345678); 

            if( $new_user->save() )
            {
                Auth::login($new_user);
                return redirect("/");
            }
        }
        return redirect("/");
    }
}
