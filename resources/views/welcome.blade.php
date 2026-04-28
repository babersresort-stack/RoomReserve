@extends('layouts.app')

@section('content')
    @php
        $resortSlides = [
            [
                'src' => '/assets/gate.jpg',
                'title' => 'Entrance lane',
                'caption' => 'A calm entry path leading guests toward reception and the room wings.',
            ],
            [
                'src' => '/assets/double3.jpg',
                'title' => 'Shared ambiance area',
                'caption' => 'An open social atmosphere designed for groups and shared stays.',
            ],
            [
                'src' => '/assets/single3.jpg',
                'title' => 'Quiet room atmosphere',
                'caption' => 'A private, restful setup for guests looking for a quiet overnight retreat.',
            ],
        ];
    @endphp

    <section class="stack">
        <div
            class="panel hero-banner hero-slab landing-hero stack p-10 md:p-12 lg:p-14 text-white"
            x-data="{
                slides: @js($resortSlides),
                activeSlide: 0,
                touchStartX: 0,
                previousSlide() {
                    this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
                },
                nextSlide() {
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                },
                onTouchStart(event) {
                    this.touchStartX = event.touches[0].clientX;
                },
                onTouchEnd(event) {
                    const deltaX = event.changedTouches[0].clientX - this.touchStartX;
                    if (deltaX > 50) {
                        this.previousSlide();
                    } else if (deltaX < -50) {
                        this.nextSlide();
                    }
                }
            }"
        >
            <div class="row">
                <span class="hero-logo-wrap">
                    <img class="hero-logo" src="/assets/logo.jpg" alt="RoomReserve logo">
                </span>
                <span class="pill border-white/20 bg-white/15 text-white">Modern hotel booking</span>
            </div>

            <div class="resort-showcase">
                <div
                    class="resort-slider-stage"
                    @touchstart="onTouchStart($event)"
                    @touchend="onTouchEnd($event)"
                >
                    <template x-for="(slide, index) in slides" :key="index">
                        <figure class="resort-slide" x-show="activeSlide === index" x-transition.opacity.duration.300ms>
                            <img :src="slide.src" :alt="slide.title" :class="slide.image_class || ''">
                            <figcaption>
                                <strong x-text="slide.title"></strong>
                                <span x-text="slide.caption"></span>
                            </figcaption>
                        </figure>
                    </template>

                    <button type="button" class="button slider-nav resort-nav resort-nav-prev" aria-label="Previous resort photo" @click="previousSlide()">&larr;</button>
                    <button type="button" class="button slider-nav resort-nav resort-nav-next" aria-label="Next resort photo" @click="nextSlide()">&rarr;</button>
                </div>

                <div class="between resort-gallery-meta">
                    <p class="text-sm text-white/85">Swipe left or right to preview the resort ambiance before reserving.</p>
                    <div class="row resort-dots">
                        <template x-for="(_, index) in slides" :key="`dot-${index}`">
                            <button
                                type="button"
                                class="resort-dot"
                                :class="{ 'is-active': activeSlide === index }"
                                @click="activeSlide = index"
                                :aria-label="`Show resort photo ${index + 1}`"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="landing-hero-copy space-y-4">
                <h1 class="landing-headline">Reserve rooms with a calm, polished experience built for guests and staff.</h1>
                <p class="landing-subcopy text-white/90">
                    RoomReserve keeps the booking path focused, the room information visible, and the admin workflow straightforward from the first tap to the final confirmation.
                </p>
            </div>

            <div class="booking-strip panel bg-white/96 text-slate-900 shadow-none">
                <form class="grid gap-4 lg:grid-cols-[1.2fr,1fr,1fr,0.8fr,auto]" method="GET" action="{{ route('rooms.index') }}">
                    <div>
                        <label for="room_id">Room</label>
                        <select id="room_id" name="room_id">
                            <option value="">Any room</option>
                            @foreach ($featuredRooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }} - {{ $room->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="check_in_at">Check-in</label>
                        <input id="check_in_at" name="check_in_at" type="datetime-local">
                    </div>
                    <div>
                        <label for="check_out_at">Check-out</label>
                        <input id="check_out_at" name="check_out_at" type="datetime-local">
                    </div>
                    <div>
                        <label for="guests">Guests</label>
                        <input id="guests" name="guests" type="number" min="1" value="1">
                    </div>
                    <div class="lg:self-end">
                        <button class="button primary booking-cta w-full" type="submit">Check availability</button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="hero-metric">
                    <p>Available rooms</p>
                    <strong>{{ $stats['available_rooms'] }}</strong>
                </div>
                <div class="hero-metric">
                    <p>Total rooms</p>
                    <strong>{{ $stats['total_rooms'] }}</strong>
                </div>
                <div class="hero-metric">
                    <p>Public reviews</p>
                    <strong>{{ $reviews->total() }}</strong>
                </div>
                <div class="hero-metric">
                    <p>Average rating</p>
                    <strong>{{ number_format($stats['average_rating'], 1) }}/5</strong>
                </div>
            </div>
        </div>

        <section class="grid cols-3">
            <article class="card stack">
                <div class="text-2xl">01</div>
                <h3>Hotel-style booking flow</h3>
                <p class="muted">Pick a room, check availability, and confirm a stay through a clean resort booking path.</p>
            </article>
            <article class="card stack">
                <div class="text-2xl">02</div>
                <h3>Guest and staff access</h3>
                <p class="muted">One login keeps guest reservations and staff operations moving inside the same hotel system.</p>
            </article>
            <article class="card stack">
                <div class="text-2xl">03</div>
                <h3>Room details at a glance</h3>
                <p class="muted">Photos, occupancy, and rates stay visible so guests can choose the best room quickly.</p>
            </article>
        </section>

        <section class="grid cols-2">
            <div class="panel stack featured-panel">
                <div class="between featured-header">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sky-700">Featured stays</p>
                        <h2 class="page-title mt-1">Rooms with clear capacity and status.</h2>
                    </div>
                    <a class="button" href="{{ route('rooms.index') }}">See all rooms</a>
                </div>
                <div class="featured-grid">
                    @foreach ($featuredRooms as $room)
                        <article class="card stack room-spotlight">
                            <div class="between">
                                <span class="pill">{{ $room->code }}</span>
                                <span class="badge badge-{{ $room->status }}">{{ $room->status }}</span>
                            </div>
                            <div class="grid gap-4 md:grid-cols-[180px,1fr] items-start">
                                @if ($room->image_url)
                                    <img src="{{ $room->image_url }}" alt="{{ $room->name }}" style="width:100%;height:180px;object-fit:cover;border-radius:18px;">
                                @endif
                                <div class="stack">
                                    <h3>{{ $room->name }}</h3>
                                    @if ($room->average_rating)
                                        <p class="muted">Guest rating {{ number_format($room->average_rating, 1) }}/5</p>
                                    @endif
                                    <p class="muted">Capacity {{ $room->capacity }} | PHP {{ number_format($room->base_rate, 2) }} nightly</p>
                                    <p>{{ $room->description }}</p>
                                    <a class="button primary" href="{{ route('rooms.show', $room) }}">View room</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="panel stack">
                <div class="between">
                    <div>
                        <h2>Guest reviews</h2>
                        <p class="muted text-sm">Public feedback is grouped into pages so it stays readable on small screens.</p>
                    </div>
                    <span class="pill">Public feedback</span>
                </div>
                @if ($reviews->isNotEmpty())
                    <div class="stack review-list">
                        @foreach ($reviews as $review)
                            <article class="muted-box stack review-card">
                                <div class="between">
                                    <strong>{{ $review->user->name }}</strong>
                                    <span class="badge badge-confirmed">{{ $review->rating }}/5</span>
                                </div>
                                <p class="muted">{{ $review->room->name }}</p>
                                <p>{{ $review->comments }}</p>
                            </article>
                        @endforeach
                    </div>
                    <div>
                        {{ $reviews->links() }}
                    </div>
                @else
                    <p class="muted">No public reviews yet. Once guests start rating rooms, the highlights will appear here.</p>
                @endif
            </div>
        </section>
    </section>
@endsection
