@extends('layouts.app')

@section('content')
    <section class="section stack">
        <div class="between">
            <div>
                <h1>Guest feedback</h1>
                <p class="muted">Monitor room ratings and review comments submitted after stays.</p>
            </div>
        </div>

        <div class="panel stack">
            <p class="muted text-sm">
                Showing {{ $feedbacks->firstItem() ?? 0 }}-{{ $feedbacks->lastItem() ?? 0 }} of {{ $feedbacks->total() }} feedback entries.
            </p>

            @forelse ($feedbacks as $feedback)
                <div class="muted-box">
                    <div class="between">
                        <div>
                            <strong>{{ $feedback->room->name }}</strong>
                            <div class="muted">{{ $feedback->user->name }} | Booking {{ $feedback->booking?->reference ?? 'N/A' }}</div>
                        </div>
                        <span class="pill">{{ $feedback->rating }}/5</span>
                    </div>
                    <p>{{ $feedback->comments ?: 'No written comment.' }}</p>
                </div>
            @empty
                <p class="muted">No feedback has been posted yet.</p>
            @endforelse

            <div>
                {{ $feedbacks->links() }}
            </div>
        </div>
    </section>
@endsection
