<?php

namespace Infinity\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Infinity\Events\UserAuthenticatedEvent;
use Infinity\Facades\Infinity;

class InfinityLoginController extends InfinityBaseController
{
    use AuthenticatesUsers;

    /**
     * Show the login view.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin(): View
    {
        return Infinity::view('auth.login');
    }

    /**
     * Handle the login attempt.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handleLogin(Request $request): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            /** @var \Infinity\Models\Users\User $user */
            $user = auth()->user();
            $user->update([
                'last_logged_in_at' => Carbon::now()
            ]);

            UserAuthenticatedEvent::dispatch($user);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Determine where to send the user once a successful login attempt has been made.
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function redirectTo(): mixed
    {
        return config('infinity.user.redirect', route('infinity.dashboard.show_dashboard'));
    }
}
