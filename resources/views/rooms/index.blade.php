@extends('layouts.app')

@section('content')
    <section class="section stack">
        <div class="between">
            <div>
                <h1>Available rooms</h1>
                <p class="muted">Browse live room status and open a booking when the timing works.</p>
            </div>
        </div>

        <form class="panel stack" method="GET" action="{{ route('rooms.index') }}">
            <div class="grid cols-4">
                <div>
                    <label for="room_id">Room ID</label>
                    <input id="room_id" name="room_id" type="number" min="1" value="{{ $filters['room_id'] ?? '' }}" placeholder="Any">
                </div>
                <div>
                    <label for="check_in_at">Check-in</label>
                    <input id="check_in_at" name="check_in_at" type="datetime-local" value="{{ $filters['check_in_at'] ?? '' }}">
                </div>
                <div>
                    <label for="check_out_at">Check-out</label>
                    <input id="check_out_at" name="check_out_at" type="datetime-local" value="{{ $filters['check_out_at'] ?? '' }}">
                </div>
                <div>
                    <label for="guests">Guests</label>
                    <input id="guests" name="guests" type="number" min="1" value="{{ $filters['guests'] ?? '' }}" placeholder="Any">
                </div>
            </div>
            <div class="row">
                <button class="button primary" type="submit">Check availability</button>
                <a class="button" href="{{ route('rooms.index') }}">Reset filters</a>
            </div>
        </form>

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
