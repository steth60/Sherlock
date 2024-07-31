<?php

// app/Actions/Fortify/CustomRegisterResponse.php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Http\Request;

class CustomRegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        return redirect()->intended(route('verification.notice'));
    }
}
