@extends('layouts.app')

@section('content')
    <section class="section stack">
        <div class="between">
            <div>
                <h1>Available rooms</h1>
                <p class="muted">Browse live room status and open a booking when the timing works.</p>
            </div>
        </div>



        <div class="row">
            <span class="pill">Status legend</span>
            <span class="badge badge-available"><span class="status-dot"></span>available</span>
            <span class="badge badge-maintenance"><span class="status-dot"></span>maintenance</span>
            <span class="badge badge-unavailable"><span class="status-dot"></span>unavailable</span>
        </div>

        <div class="grid cols-3">
            @forelse ($rooms as $room)
                <article class="card stack">
                    @if ($room->image_url)
                        <img src="{{ $room->image_url }}" alt="{{ $room->name }}">
                    @endif
                    <div class="row">
                        <span class="pill">{{ $room->code }}</span>
                        <span class="badge badge-{{ $room->status }}">{{ $room->status }}</span>
                    </div>
                    <h3>{{ $room->name }}</h3>
                    <p class="muted">Capacity {{ $room->capacity }} | Rate PHP {{ number_format($room->base_rate, 2) }} / night</p>
                    <div class="row">
                        @if ($room->average_rating)
                            <span class="pill">{{ number_format($room->average_rating, 1) }}/5 rating</span>
                        @endif
                        <span class="pill">{{ $room->active_booking_count }} active bookings</span>
                    </div>
                    <p>{{ $room->description }}</p>
                    <div class="row">
                        <a class="button primary" href="{{ route('rooms.show', $room) }}">View details</a>
                        <a class="button" href="{{ route('rooms.availability', $room) }}">Live status</a>
                    </div>
                </article>
            @empty
                <article class="panel">
                    <h3>No rooms match your filters</h3>
                    <p class="muted">Try adjusting dates, guest count, or clearing filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection
