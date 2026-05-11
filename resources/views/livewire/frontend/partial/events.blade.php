@if(isset($items) && $items->count())

@php
    $now   = \Carbon\Carbon::now();
    $year  = $now->year;
    $month = $now->month;

    $days_in_month = $now->daysInMonth;
    $first_day_of_week = \Carbon\Carbon::createFromDate($year, $month, 1)->dayOfWeek;

    $event_dates = [];
    foreach ($items as $item) {
        if (!empty($item->custom_data['event_date'])) {
            $event_dates[] = \Carbon\Carbon::parse($item->custom_data['event_date'])->day;
        }
    }
@endphp

<div class="events-section">

    <h5 class="section-heading mb-3">
        <i class="fa fa-calendar me-1"></i> Event Calendar
    </h5>

    <div class="calendar-box">

        {{-- Calendar header --}}
        <div class="calendar-header">
            <button class="cal-nav" id="calPrev">&#8249;</button>
            <span class="cal-month-year">{{ $now->format('F Y') }}</span>
            <button class="cal-nav" id="calNext">&#8250;</button>
            <button class="cal-today-btn">today</button>
        </div>

        {{-- Day labels --}}
        <div class="calendar-grid cal-days-header">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="cal-day-label">{{ $day }}</div>
            @endforeach
        </div>

        {{-- Date cells --}}
        <div class="calendar-grid cal-dates">
            {{-- Empty cells before first day --}}
            @for($e = 0; $e < $first_day_of_week; $e++)
                <div class="cal-date-cell empty"></div>
            @endfor

            @for($d = 1; $d <= $days_in_month; $d++)
                @php
                    $isToday   = $d === $now->day;
                    $hasEvent  = in_array($d, $event_dates);
                @endphp
                <div class="cal-date-cell
                    {{ $isToday  ? 'cal-today'  : '' }}
                    {{ $hasEvent ? 'cal-has-event' : '' }}">
                    {{ $d }}
                    @if($hasEvent)<span class="cal-dot"></span>@endif
                </div>
            @endfor
        </div>

    </div>

    {{-- Upcoming events list --}}
    @php $upcoming = $items->take(4); @endphp
    @if($upcoming->count())
        <div class="upcoming-events mt-3">
            @foreach($upcoming as $ev)
                @php
                    $href = match($ev->type) {
                        'external' => $ev->url,
                        'file'     => asset('storage/' . ($ev->file ?? '')),
                        default    => route('show.content', $ev->slug),
                    };
                @endphp
                <a href="{{ $href }}"
                   @if(in_array($ev->type, ['external','file'])) target="_blank" rel="noopener" @endif
                   class="upcoming-event-item">
                    <span class="ev-name">{{ $ev->name }}</span>
                    <i class="fa fa-arrow-right ev-arrow"></i>
                </a>
            @endforeach
        </div>
    @endif

</div>

<style>
.section-heading { color: #1a3a6b; font-size: 18px; font-weight: 700; }
.calendar-box { border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden; background: #fff; }
.calendar-header {
    background: #1a3a6b; color: #fff;
    display: flex; align-items: center;
    padding: 8px 12px; gap: 8px;
}
.cal-month-year { flex: 1; text-align: center; font-weight: 600; font-size: 15px; }
.cal-nav {
    background: none; border: 1px solid rgba(255,255,255,0.4);
    color: #fff; border-radius: 4px; width: 26px; height: 26px;
    cursor: pointer; font-size: 16px; line-height: 1;
    display: flex; align-items: center; justify-content: center;
}
.cal-nav:hover { background: rgba(255,255,255,0.2); }
.cal-today-btn {
    background: #fff; color: #1a3a6b; border: none;
    border-radius: 4px; padding: 2px 10px; font-size: 12px;
    font-weight: 600; cursor: pointer;
}
.calendar-grid {
    display: grid; grid-template-columns: repeat(7, 1fr);
}
.cal-days-header { background: #f0f4f8; border-bottom: 1px solid #dee2e6; }
.cal-day-label {
    text-align: center; font-size: 11px; font-weight: 600;
    color: #666; padding: 6px 0;
}
.cal-dates { padding: 4px; gap: 2px; }
.cal-date-cell {
    text-align: center; padding: 5px 2px; font-size: 13px;
    border-radius: 4px; cursor: default;
    position: relative; min-height: 28px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #333;
}
.cal-date-cell.empty { background: none; }
.cal-today { background: #1a3a6b; color: #fff; border-radius: 50%; font-weight: 700; }
.cal-has-event { font-weight: 700; color: #c0392b; }
.cal-dot {
    width: 5px; height: 5px; background: #c0392b;
    border-radius: 50%; margin-top: 1px;
}
.upcoming-events { border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden; }
.upcoming-event-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 14px; border-bottom: 1px solid #eee;
    color: #111; text-decoration: none; font-size: 13px;
}
.upcoming-event-item:last-child { border-bottom: none; }
.upcoming-event-item:hover { background: #1a3a6b; color: #fff; }
.upcoming-event-item:hover .ev-arrow { color: #fff; }
.ev-arrow { color: #1a3a6b; font-size: 12px; }
</style>
@endif
