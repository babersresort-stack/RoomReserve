<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Room;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredRooms = Room::query()
            ->where('status', 'available')
            ->withAvg('feedback as average_rating', 'rating')
            ->orderBy('code')
            ->limit(3)
            ->get();

        // If the `feedback` table doesn't exist (fresh checkout / DB not migrated),
        // return safe empty values to avoid a 500 error on the homepage.
        if (! Schema::hasTable((new Feedback())->getTable())) {
            $reviews = new LengthAwarePaginator([], 0, 4, request()->input('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            $averageRating = 0.0;
        } else {
            $reviews = Feedback::query()
                ->with(['room', 'user'])
                ->where('is_public', true)
                ->whereNotNull('comments')
                ->where('comments', '!=', '')
                ->latest()
                ->paginate(4)
                ->withQueryString();

            $averageRating = (float) (Feedback::query()->avg('rating') ?? 0);
        }

        return view('welcome', [
            'featuredRooms' => $featuredRooms,
            'reviews' => $reviews,
            'stats' => [
                'total_rooms' => Room::query()->count(),
                'available_rooms' => Room::query()->where('status', 'available')->count(),
                'average_rating' => $averageRating,
            ],
        ]);
    }
}
