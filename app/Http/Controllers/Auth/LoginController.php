<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form with roles.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Get all available roles for login, except delivery_personnel
        $roles = \App\Models\Role::where('name', '!=', 'delivery_personnel')->get()->unique('name')->values();
        return view('auth.login', compact('roles'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        // Auto-create manufacturer profile if user is a manufacturer and profile is missing
        if ($user->role && $user->role->name === 'manufacturer' && !$user->manufacturer) {
            \App\Models\Manufacturer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        if ($user->role) {
            return redirect($user->role->getDashboardRoute());
        }
        return redirect($this->redirectTo);
    }

    /**
     * Override the login method to check for role match.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        $roleId = $request->input('role_id');

        // Attempt login with credentials
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            if ($user->role_id == $roleId) {
                return $this->sendLoginResponse($request);
            } else {
                Auth::logout();
                return back()->withErrors(['role_id' => 'Selected role does not match your account role.'])->withInput($request->except('password'));
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
