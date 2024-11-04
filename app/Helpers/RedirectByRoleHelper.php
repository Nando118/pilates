<?php

namespace App\Helpers;

class RedirectByRoleHelper
{
    public static function redirectBasedOnRole($user)
    {
        if ($user->hasRole("super_admin") || $user->hasRole("admin")) {
            return redirect()->route("dashboard");
        } elseif ($user->hasRole("coach") || $user->hasRole("client")) {
            return redirect()->route("home");
        }

        return abort(403, 'Unauthorized');
    }
}
