@extends('layouts.app')

@section('content')
    <section class="section stack">
        <div class="between">
            <div>
                <h1>Rooms</h1>
            </div>
            <a class="button primary" href="{{ route('admin.rooms.create') }}">Add room</a>
        </div>

        <form class="panel stack" method="GET" action="{{ route('admin.rooms.index') }}">
            <div class="grid admin-room-filters">
                <div>
                    <label for="search">Search rooms</label>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Code, name, or description">
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">Any status</option>
                        @foreach (['available', 'maintenance', 'unavailable'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="stack filter-actions">
                    <label class="invisible" aria-hidden="true">Actions</label>
                    <div class="row">
                        <button class="button primary w-full sm:w-auto" type="submit">Filter rooms</button>
                        <a class="button w-full sm:w-auto" href="{{ route('admin.rooms.index') }}">Reset</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rooms as $room)
                        <tr>
                            <td>
                                @if ($room->image_url)
                                    <img src="{{ $room->image_url }}" alt="{{ $room->name }}" style="width:84px;height:56px;object-fit:cover;border-radius:12px;">
                                @endif
                            </td>
                            <td>{{ $room->code }}</td>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>PHP {{ number_format($room->base_rate, 2) }}</td>
                            <td><span class="badge badge-{{ $room->status }}">{{ $room->status }}</span></td>
                            <td class="row">
                                <a class="button" href="{{ route('admin.rooms.show', $room) }}">View</a>
                                <a class="button" href="{{ route('admin.rooms.edit', $room) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Delete this room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
