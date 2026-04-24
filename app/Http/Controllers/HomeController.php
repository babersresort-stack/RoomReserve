<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'featuredRooms' => Room::query()
                ->where('status', 'available')
                ->withAvg('feedback as average_rating', 'rating')
                ->orderBy('code')
                ->limit(3)
                ->get(),
            'reviews' => Feedback::query()
                ->with(['room', 'user'])
                ->where('is_public', true)
                ->whereNotNull('comments')
                ->where('comments', '!=', '')
                ->latest()
                ->paginate(4)
                ->withQueryString(),
            'stats' => [
                'total_rooms' => Room::query()->count(),
                'available_rooms' => Room::query()->where('status', 'available')->count(),
                'average_rating' => (float) (Feedback::query()->avg('rating') ?? 0),
            ],
        ]);
    }
}
