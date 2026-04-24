@extends('layouts.app')

@section('content')
    <section class="section grid cols-2">
        <div class="panel stack">
            <span class="pill">{{ $isEdit ? 'Edit room' : 'New room' }}</span>
            <h1>{{ $isEdit ? 'Update room details' : 'Create a room listing' }}</h1>
            <p class="muted">Keep room records consistent so booking availability stays accurate.</p>
            <div class="card stack">
                <h2>Photo guidance</h2>
                <p class="muted">Upload a clear room image so guests can recognize the room quickly.</p>
                @if ($isEdit && $room->image_url)
                    <img src="{{ $room->image_url }}" alt="{{ $room->name }}" style="width:100%;height:220px;object-fit:cover;border-radius:18px;">
                    <p class="muted">Current image: {{ $room->image_path }}</p>
                @else
                    <div class="muted-box" style="min-height:220px;display:grid;place-items:center;text-align:center;">
                        <span class="muted">No image uploaded yet</span>
                    </div>
                @endif
            </div>
        </div>

        <form class="panel stack" method="POST" action="{{ $isEdit ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <div>
                <label for="code">Code</label>
                <input id="code" name="code" type="text" value="{{ old('code', $room->code) }}" required>
            </div>
            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $room->name) }}" required>
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $room->description) }}</textarea>
            </div>
            <div class="grid cols-2">
                <div>
                    <label for="capacity">Capacity</label>
                    <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $room->capacity) }}" required>
                </div>
                <div>
                    <label for="base_rate">Base rate</label>
                    <input id="base_rate" name="base_rate" type="number" step="0.01" min="0" value="{{ old('base_rate', $room->base_rate) }}" required>
                </div>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    @foreach (['available', 'maintenance', 'unavailable'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $room->status ?? 'available') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amenities">Amenities</label>
                <textarea id="amenities" name="amenities" placeholder="Wi-Fi, Balcony, Breakfast">{{ old('amenities', is_array($room->amenities ?? null) ? implode(', ', $room->amenities) : '') }}</textarea>
            </div>
            <div>
                <label for="image">Room image</label>
                <input id="image" name="image" type="file" accept="image/*">
                <p class="muted">Images are stored directly in the public uploads folder so they load without extra setup.</p>
            </div>
            <div class="stack">
                <span class="pill">Image notes</span>
                <p class="muted">Use landscape images for the best card layout on both mobile and desktop.</p>
            </div>
            <button class="button primary" type="submit">{{ $isEdit ? 'Save changes' : 'Create room' }}</button>
        </form>
    </section>
@endsection
