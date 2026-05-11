@if(isset($items) && $items->count())
<div class="container-fluid py-4 bg-white">
    <div class="container">

        <h5 class="text-primary mb-3">
            <i class="fa fa-users me-1"></i> Ministers
        </h5>

        <div class="row g-3">
            @foreach($items as $item)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="minister-item text-center">
                        @if($item->file)
                            <img src="{{ asset('storage/' . $item->file) }}"
                                 alt="{{ $item->name }}"
                                 class="minister-photo">
                        @elseif($item->file_icon)
                            <img src="{{ asset('storage/' . $item->file_icon) }}"
                                 alt="{{ $item->name }}"
                                 class="minister-photo">
                        @else
                            <div class="minister-photo-placeholder">
                                <i class="fa fa-user fa-2x"></i>
                            </div>
                        @endif

                        <p class="minister-name">{{ $item->name }}</p>

                        @if($item->description)
                            <p class="minister-role">{{ $item->description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<style>
.minister-item { padding: 8px; }
.minister-photo {
    width: 100%; max-width: 130px; height: 160px;
    object-fit: cover; object-position: top;
    border-radius: 6px; border: 2px solid #1a3a6b;
    margin-bottom: 8px;
}
.minister-photo-placeholder {
    width: 130px; height: 160px; background: #e8eef5;
    border-radius: 6px; border: 2px solid #1a3a6b;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px; color: #1a3a6b;
}
.minister-name { font-size: 13px; font-weight: 700; color: #1a3a6b; margin-bottom: 2px; }
.minister-role { font-size: 11px; color: #666; margin-bottom: 0; }
</style>
@endif
