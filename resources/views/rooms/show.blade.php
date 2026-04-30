@extends('layouts.app')

@section('content')
    <section class="section pb-0">
        <div class="row">
            @if ($isAdmin)
                <a class="button" href="{{ route('admin.rooms.index') }}">Back to rooms</a>
                <a class="button" href="{{ route('admin.dashboard') }}">Back to dashboard</a>
            @else
                <a class="button" href="{{ route('rooms.index') }}">Back to rooms</a>
                <a class="button" href="{{ route('dashboard') }}">Back to dashboard</a>
            @endif
        </div>
    </section>

    <section class="section grid cols-2">
        <article class="panel stack">
            @if ($room->image_url)
                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" style="width:100%;border-radius:18px;max-height:320px;object-fit:cover;">
            @endif
            <div class="row">
                <span class="pill">{{ $room->code }}</span>
                <span class="badge badge-{{ $room->status }}">{{ $room->status }}</span>
                <span class="badge {{ $availability ? 'badge-confirmed' : 'badge-maintenance' }}">{{ $availability ? 'Bookable now' : 'Temporarily blocked' }}</span>
            </div>
            <h1>{{ $room->name }}</h1>
            <p class="muted">Capacity {{ $room->capacity }} | Base rate PHP {{ number_format($room->base_rate, 2) }} / night</p>
            @if ($room->average_rating)
                <p class="muted">Average guest rating {{ number_format($room->average_rating, 1) }}/5</p>
            @endif
            <p>{{ $room->description }}</p>
            @if (! empty($room->amenities))
                <div class="row">
                    @foreach ($room->amenities as $amenity)
                        <span class="pill">{{ $amenity }}</span>
                    @endforeach
                </div>
            @endif
        </article>

        @unless ($isAdmin)
            <form class="panel stack" method="POST" action="{{ route('bookings.store', $room) }}">
                @csrf
                <h2>Book this room</h2>
                <p class="muted" style="margin-top:-0.2rem;">Rate: PHP {{ number_format($room->base_rate, 2) }} per night{{ $room->capacity >= 10 ? ' per guest' : '' }}</p>

                <div>
                    <label>Reserved dates</label>
                    <div class="row" style="gap:0.45rem;">
                        @forelse ($reservedDates as $reservedDate)
                            <span class="pill" style="background:rgba(254,226,226,0.92);color:#991b1b;border-color:rgba(248,113,113,0.35);">
                                {{ \Illuminate\Support\Carbon::parse($reservedDate)->format('M j, Y') }}
                            </span>
                        @empty
                            <span class="pill">No active reserved dates</span>
                        @endforelse
                    </div>
                </div>

                <div id="availability-alert" style="display:none;padding:0.75rem;border-radius:0.75rem;border:1px solid rgba(248,113,113,0.35);background:rgba(254,226,226,0.92);color:#991b1b;font-size:0.875rem;">
                    <strong>⚠️ Unavailable</strong> — This room is already booked for the selected dates. Please choose different dates.
                </div>

                <div>
                    <label for="check_in_at">Check-in</label>
                    <input id="check_in_at" name="check_in_at" type="datetime-local" value="{{ old('check_in_at') }}" required>
                </div>
                <div>
                    <label for="check_out_at">Check-out</label>
                    <input id="check_out_at" name="check_out_at" type="datetime-local" value="{{ old('check_out_at') }}" required>
                </div>
                <div>
                    <label for="guests">Guests</label>
                    <input id="guests" name="guests" type="number" min="1" max="{{ $room->capacity }}" value="{{ old('guests', 1) }}" required>
                </div>
                <div>
                    <label>Estimated total bill</label>
                    <div id="total_bill_preview" class="pill" style="justify-content:flex-start;min-height:2.9rem;border-radius:0.75rem;">
                        PHP {{ number_format($room->base_rate, 2) }}
                    </div>
                </div>
                <div>
                    <label for="special_requests">Special requests</label>
                    <textarea id="special_requests" name="special_requests">{{ old('special_requests') }}</textarea>
                </div>
                <button id="submit-btn" class="button primary" type="submit">Confirm reservation</button>
            </form>
        @else
            <div class="panel stack">
                <h2>Admin access</h2>
                <p class="muted">Admin users can review room details here and make changes from the room editor.</p>
                <a class="button primary" href="{{ route('admin.rooms.edit', $room) }}">Edit room</a>
            </div>
        @endunless
    </section>

    <section class="section panel stack room-feedback-block">
        <h2>Recent feedback</h2>
        @forelse ($room->feedback as $feedback)
            <div class="muted-box">
                <div class="between">
                    <strong>{{ $feedback->user->name }}</strong>
                    <span class="pill">{{ $feedback->rating }}/5</span>
                </div>
                <p class="muted">{{ $feedback->comments }}</p>
            </div>
        @empty
            <p class="muted">No public feedback has been posted yet.</p>
        @endforelse
    </section>

    @unless ($isAdmin)
        <script>
            const checkInInput = document.getElementById('check_in_at');
            const checkOutInput = document.getElementById('check_out_at');
            const guestsInput = document.getElementById('guests');
            const totalBillPreview = document.getElementById('total_bill_preview');
            const availabilityAlert = document.getElementById('availability-alert');
            const submitBtn = document.getElementById('submit-btn');
            const reservedDates = @json($reservedDates);
            const nightlyRate = Number({{ (float) $room->base_rate }});
            const billsPerGuest = {{ $room->capacity >= 10 ? 'true' : 'false' }};
            const checkConflictUrl = '{{ route("rooms.check-conflict", $room) }}';

            function datePart(value) {
                return value ? value.slice(0, 10) : '';
            }

            function highlightReservedDate(input) {
                const selectedDate = datePart(input.value);
                if (selectedDate && reservedDates.includes(selectedDate)) {
                    input.style.borderColor = '#f59e0b';
                    input.style.backgroundColor = '#fffbeb';
                } else {
                    input.style.borderColor = '';
                    input.style.backgroundColor = '';
                }
            }

            function calculateNights() {
                if (!checkInInput.value || !checkOutInput.value) {
                    return 1;
                }

                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const milliseconds = checkOut.getTime() - checkIn.getTime();
                const days = Math.ceil(milliseconds / (1000 * 60 * 60 * 24));

                return Number.isFinite(days) && days > 0 ? days : 1;
            }

            function updateTotalBill() {
                const nights = calculateNights();
                const guests = Math.max(1, Number(guestsInput.value || 1));
                const multiplier = billsPerGuest ? guests : 1;
                const total = nightlyRate * nights * multiplier;

                totalBillPreview.textContent = `PHP ${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            async function checkAvailability() {
                if (!checkInInput.value || !checkOutInput.value) {
                    availabilityAlert.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    return;
                }

                try {
                    const response = await fetch(checkConflictUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({
                            check_in_at: checkInInput.value,
                            check_out_at: checkOutInput.value,
                        }),
                    });

                    const data = await response.json();

                    if (data.has_conflict) {
                        availabilityAlert.style.display = 'block';
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.5';
                        submitBtn.style.cursor = 'not-allowed';
                    } else {
                        availabilityAlert.style.display = 'none';
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.style.cursor = 'pointer';
                    }
                } catch (error) {
                    console.error('Error checking availability:', error);
                }
            }

            checkInInput.addEventListener('input', function () {
                highlightReservedDate(checkInInput);
                updateTotalBill();
                checkAvailability();
            });

            checkOutInput.addEventListener('input', function () {
                highlightReservedDate(checkOutInput);
                updateTotalBill();
                checkAvailability();
            });

            guestsInput.addEventListener('input', updateTotalBill);

            highlightReservedDate(checkInInput);
            highlightReservedDate(checkOutInput);
            updateTotalBill();
        </script>
    @endunless
@endsection
