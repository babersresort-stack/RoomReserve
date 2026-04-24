@extends('layouts.app')

@section('content')
    <section class="section stack">
        <div class="between">
            <div>
                <h1>{{ $isAdmin ? 'All bookings' : 'My bookings' }}</h1>
                <p class="muted">Track reservation status, check timing, and manage stays from here.</p>
            </div>
        </div>

        <form class="panel stack" method="GET" action="{{ $isAdmin ? route('admin.bookings.index') : route('bookings.index') }}">
            <div class="grid cols-4">
                <div>
                    <label for="date">Check-in date</label>
                    <input id="date" name="date" type="date" value="{{ $filters['date'] ?? '' }}">
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="availability">Availability</label>
                    <select id="availability" name="availability">
                        <option value="">All</option>
                        <option value="available" @selected(($filters['availability'] ?? '') === 'available')>Available</option>
                        <option value="unavailable" @selected(($filters['availability'] ?? '') === 'unavailable')>Unavailable</option>
                    </select>
                </div>
                <div class="row" style="align-items:flex-end;">
                    <button class="button primary" type="submit">Apply filters</button>
                    <a class="button" href="{{ $isAdmin ? route('admin.bookings.index') : route('bookings.index') }}">Reset</a>
                </div>
            </div>
        </form>

        <div class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Room</th>
                        @if ($isAdmin)
                            <th>Guest</th>
                        @endif
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total bill</th>
                        <th>Availability</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="booking-card">
                            <td><a href="{{ $isAdmin ? route('admin.bookings.show', $booking) : route('bookings.show', $booking) }}">{{ $booking->reference }}</a></td>
                            <td>{{ $booking->room->name }}</td>
                            @if ($isAdmin)
                                <td>{{ $booking->user->name }}</td>
                            @endif
                            <td>{{ $booking->check_in_at->format('M j, Y g:i A') }}</td>
                            <td>{{ $booking->check_out_at->format('M j, Y g:i A') }}</td>
                            <td>PHP {{ number_format($booking->total_bill, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $booking->availability_label === 'available' ? 'available' : 'unavailable' }}">
                                    {{ ucfirst($booking->availability_label) }}
                                </span>
                            </td>
                            <td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 8 : 7 }}">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
