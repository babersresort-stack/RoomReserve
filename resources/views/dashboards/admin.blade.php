@extends('layouts.app')

@section('content')
    <section class="dashboard-layout">
        <aside class="panel sidebar stack">
            <span class="pill">Admin dashboard</span>
            <h1 class="page-title sidebar-title">Reservation control center.</h1>
            <p class="page-subtitle">
                Manage room inventory, inspect future bookings, and review guest feedback from a single control surface.
            </p>
            <div class="card stack">
                <img src="{{ Vite::asset('resources/asset/room-ocean.svg') }}" alt="Hotel room preview">
                <p class="muted">Photo-led room cards help staff identify inventory faster.</p>
            </div>
            <div class="stack pt-2">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">Dashboard <span>Overview</span></a>
                <a class="nav-link" href="{{ route('admin.rooms.index') }}">Rooms <span>{{ $roomCount }}</span></a>
                <a class="nav-link" href="{{ route('admin.bookings.index') }}">Reservations <span>{{ $bookingCount }}</span></a>
                <a class="nav-link" href="{{ route('admin.feedback.index') }}">Feedback <span>{{ $feedbackCount }}</span></a>
            </div>
            <div class="row pt-2">
                <a class="button primary" href="{{ route('admin.rooms.create') }}">Add room</a>
                <a class="button" href="{{ route('admin.bookings.index') }}">View queue</a>
            </div>
        </aside>

        <div class="stack">
            <section class="grid cols-4">
                <div class="muted-box">
                    <h3>{{ $roomCount }}</h3>
                    <p class="muted">Rooms total</p>
                </div>
                <div class="muted-box">
                    <h3>{{ $bookingCount }}</h3>
                    <p class="muted">Bookings total</p>
                </div>
                <div class="muted-box">
                    <h3>{{ $feedbackCount }}</h3>
                    <p class="muted">Feedback records</p>
                </div>
                <div class="muted-box">
                    <h3>{{ $upcomingBookings->count() }}</h3>
                    <p class="muted">Upcoming stays</p>
                </div>
            </section>

            <section class="panel stack">
                <div class="between">
                    <h2>Upcoming stays</h2>
                    <span class="pill">Live reservations</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingBookings as $booking)
                                <tr>
                                    <td><a href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->reference }}</a></td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->room->name }}</td>
                                    <td>{{ $booking->check_in_at->format('M j, Y g:i A') }}</td>
                                    <td>{{ $booking->check_out_at->format('M j, Y g:i A') }}</td>
                                    <td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No upcoming reservations.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection
