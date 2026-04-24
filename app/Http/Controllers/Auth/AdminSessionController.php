<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.admin-login');
    }

    public function store(Request $request)
    {
        return app(AuthenticatedSessionController::class)->adminStore($request);
    }
}
