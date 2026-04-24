<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return $request->user()?->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }

    public function guest(Request $request): View
    {
        $user = $request->user();

        return view('dashboards.guest', [
            'roomCount' => Room::query()->where('status', 'available')->count(),
            'bookingCount' => Booking::query()->where('user_id', $user->id)->count(),
            'upcomingBookings' => Booking::query()
                ->with('room')
                ->where('user_id', $user->id)
                ->where('check_in_at', '>=', now())
                ->orderBy('check_in_at')
                ->limit(5)
                ->get(),
            'feedbackCount' => Feedback::query()->where('user_id', $user->id)->count(),
        ]);
    }

    public function admin(): View
    {
        return view('dashboards.admin', [
            'roomCount' => Room::query()->count(),
            'bookingCount' => Booking::query()->count(),
            'upcomingBookings' => Booking::query()
                ->with(['room', 'user'])
                ->where('check_in_at', '>=', now())
                ->orderBy('check_in_at')
                ->limit(5)
                ->get(),
            'feedbackCount' => Feedback::query()->count(),
        ]);
    }
}
