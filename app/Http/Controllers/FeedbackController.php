<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        return view('admin.feedback.index', [
            'feedbacks' => Feedback::query()
                ->with(['room', 'user', 'booking'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }
}
