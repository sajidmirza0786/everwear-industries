<?php

namespace App\Listeners;

use App\Http\Controllers\CartController;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class MergeCartOnLogin
{
    public function handle(Login $event)
    {
        CartController::mergeCart();
    }
}