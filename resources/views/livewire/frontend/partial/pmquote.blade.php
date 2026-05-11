@if(isset($items) && $items->count())
<div class="container-fluid bg-white py-4 border-bottom">
    <div class="container">

        @foreach($items as $item)
        <div class="row align-items-center border bg-white rounded p-3 mb-3 shadow-sm">

            {{-- Photo --}}
            <div class="col-md-2 text-center mb-3 mb-md-0">
                @if($item->file_icon)
                    <img src="{{ asset('storage/' . $item->file_icon) }}"
                         alt="{{ $item->name }}"
                         class="img-fluid"
                         style="max-height:180px; object-fit:cover; object-position:top; border-radius:6px; border:3px solid #1a3a6b;">
                @else
                    <div style="width:120px; height:150px; background:#e8eef5; border-radius:6px;
                                border:3px solid #1a3a6b; display:flex; align-items:center;
                                justify-content:center; margin:0 auto; color:#1a3a6b; font-size:13px;">
                        Photo
                    </div>
                @endif
            </div>

            {{-- Quote content --}}
            <div class="col-md-10 minstermaine_heading ps-md-4">
                {!! $item->content !!}
            </div>

        </div>
        @endforeach

    </div>
</div>

<style>
.minstermaine_heading p {
    font-size: 18px;
    font-style: italic;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.7;
    margin-bottom: 12px;
}
.minstermaine_heading h3,
.minstermaine_heading h4 {
    color: #1a3a6b;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 2px;
}
.minstermaine_heading small,
.minstermaine_heading span {
    color: #555;
    font-size: 13px;
}
</style>
@endif
