<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     * Optimized for sub-1-second performance
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check for too many login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Attempt login with optimized query
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Increment login attempts
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     * Optimized with email index for fast lookup
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Use remember token for faster authentication
        $remember = $request->filled('remember');
        
        $success = Auth::attempt($credentials, $remember);
        
        // Update last login time if login was successful
        if ($success) {
            $this->updateLastLoginTime();
        }
        
        return $success;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Update the last login time for the authenticated user.
     */
    protected function updateLastLoginTime()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->updateLastLogin();

            // Log the login activity
            $this->logLoginActivity($user);
        }
    }

    /**
     * Log user login activity.
     */
    protected function logLoginActivity($user)
    {
        try {
            // Create a simple activity log entry
            \DB::table('user_activities')->insert([
                'user_id' => $user->id,
                'action' => 'user_login',
                'description' => 'User logged in successfully',
                'performed_by' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the login process
            \Log::warning('Failed to log login activity', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
