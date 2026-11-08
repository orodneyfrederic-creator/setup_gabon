{{-- Note en étoiles (lecture seule), demi-étoile comprise --}}
@php $note = (float) $note; @endphp
<span class="inline-flex items-center gap-0.5 {{ $taille ?? 'text-sm' }}" role="img" aria-label="Note : {{ number_format($note, 1, ',', ' ') }} sur 5">
    @foreach (range(1, 5) as $position)
        @if ($note >= $position)
            <i class="fa-solid fa-star text-amber-400" aria-hidden="true"></i>
        @elseif ($note >= $position - 0.5)
            <i class="fa-solid fa-star-half-stroke text-amber-400" aria-hidden="true"></i>
        @else
            <i class="fa-regular fa-star text-slate-300" aria-hidden="true"></i>
        @endif
    @endforeach
</span>
