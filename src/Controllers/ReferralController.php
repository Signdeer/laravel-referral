<?php

namespace Jijunair\LaravelReferral\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class ReferralController extends Controller
{
    /**
     * Assign a referral code to the user.
     *
     * @param  string  $referralCode
     * @return RedirectResponse
     */
    // public function assignReferrer($referralCode)
    // {
    //     $refCookieName = config('referral.cookie_name');
    //     $refCookieExpiry = config('referral.cookie_expiry');
    //     if (Cookie::has($refCookieName)) {
    //         // Referral code cookie already exists, redirect to configured route
    //         return redirect()->route(config('referral.redirect_route'));
    //     } else {
    //         // Create a referral code cookie and redirect to configured route
    //         $ck = Cookie::make($refCookieName, $referralCode, $refCookieExpiry);
    //         return redirect()->route(config('referral.redirect_route'))->withCookie($ck);
    //     }
    // }
    
    public function assignReferrer(Request $request, $referralCode) {
        $refCookieName = config('referral.cookie_name');
        $refCookieExpiry = config('referral.cookie_expiry');
        $redirectRoute = config('referral.redirect_route');

        $shouldRedirect = $request->has('redirect');

        if ($shouldRedirect || Cookie::has($refCookieName)) {
            $ck = Cookie::make($refCookieName, $referralCode, $refCookieExpiry);
            return redirect()->route($redirectRoute)->withCookie($ck);
        }

        // Check if parent app overrides the view
        $view = View::exists('referral.preview')
            ? 'referral.preview'
            : 'laravel-referral::preview'; // default in package

        return response()
            ->view($view, [
                'referralCode' => $referralCode,
                'redirectUrl' => route($redirectRoute, ['ref' => $referralCode]),
            ])
            ->withCookie(cookie()->make($refCookieName, $referralCode, $refCookieExpiry));
    }

    /**
     * Generate referral codes for existing users.
     *
     * @return JsonResponse
     */
    public function createReferralCodeForExistingUsers()
    {
        $userModel = resolve(config('referral.user_model'));
        $users = $userModel::cursor();

        foreach ($users as $user) {
            if (!$user->hasReferralAccount()) {
                $user->createReferralAccount();
            }
        }

        return response()->json(['message' => 'Referral codes generated for existing users.']);
    }
}
