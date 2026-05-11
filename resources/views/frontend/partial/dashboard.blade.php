@if(isset($items) && $items->count())

@php
    $parent = $items->whereNull('parent_id')->first()
              ?? $items->first();
    $stats  = $items->where('parent_id', $parent?->id)->values();
    if ($stats->isEmpty()) {
        $stats = $items->skip(1)->values();
    }
@endphp

<div class="dashboard-section">

    <h5 class="section-heading mb-3">
        <i class="fa fa-bar-chart me-1"></i> Dashboard
    </h5>

    <div class="dashboard-card">

        {{-- Left: Title block --}}
        <div class="dashboard-title-block">
            @if($parent)
                @if($parent->file_icon)
                    <img src="{{ asset('storage/' . $parent->file_icon) }}"
                         alt="{{ $parent->name }}"
                         style="height:50px; margin-bottom:10px;">
                @elseif($parent->icon)
                    <i class="{{ $parent->icon }} fa-2x mb-2 d-block"></i>
                @endif
                <p class="mb-0 fw-bold" style="font-size:15px; line-height:1.4;">
                    {{ $parent->name }}
                </p>
            @endif
        </div>

        {{-- Right: Stat grid --}}
        <div class="dashboard-stats-grid">
            @foreach($stats as $stat)
                <div class="dashboard-stat-item">

                    @if($stat->file_icon)
                        <img src="{{ asset('storage/' . $stat->file_icon) }}"
                             alt="{{ $stat->name }}"
                             style="height:36px; margin-bottom:6px;">
                    @elseif($stat->icon)
                        <i class="{{ $stat->icon }} fa-2x mb-2 d-block" style="color:#1a3a6b;"></i>
                    @endif

                    <div class="stat-value">
                        {{ $stat->badge_text ?? data_get($stat->custom_data, 'value', '—') }}
                    </div>

                    <div class="stat-unit">
                        {{ data_get($stat->custom_data, 'unit', '') }}
                    </div>

                    <div class="stat-label">{{ $stat->name }}</div>

                </div>
            @endforeach
        </div>

    </div>

</div>

<style>
.section-heading { color: #1a3a6b; font-size: 18px; font-weight: 700; }
.dashboard-card {
    display: flex; align-items: stretch;
    border: 1px solid #dee2e6; border-radius: 8px;
    overflow: hidden; background: #fff;
    min-height: 180px;
}
.dashboard-title-block {
    background: #1a3a6b; color: #fff;
    padding: 20px 16px; min-width: 160px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
}
.dashboard-title-block p { color: #fff; }
.dashboard-stats-grid {
    flex: 1; display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 0;
}
.dashboard-stat-item {
    padding: 16px 12px; text-align: center;
    border-left: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
}
.stat-value { font-size: 22px; font-weight: 700; color: #1a3a6b; line-height: 1.2; }
.stat-unit  { font-size: 12px; color: #888; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: #444; line-height: 1.3; }
@media (max-width: 576px) {
    .dashboard-card { flex-direction: column; }
    .dashboard-title-block { min-width: 100%; }
}
</style>
@endif
