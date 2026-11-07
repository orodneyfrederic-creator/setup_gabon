@php
    $statutsCommande = [
        'en_attente' => ['En attente', 'bg-amber-100 text-amber-900'],
        'confirmee' => ['Confirmée', 'bg-blue-100 text-blue-800'],
        'livree' => ['Livrée', 'bg-emerald-100 text-emerald-800'],
        'annulee' => ['Annulée', 'bg-slate-200 text-slate-700'],
    ];
    [$libelleStatut, $classesStatut] = $statutsCommande[$statut] ?? [ucfirst(str_replace('_', ' ', $statut)), 'bg-slate-100 text-slate-700'];
@endphp
<span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-semibold {{ $classesStatut }}">{{ $libelleStatut }}</span>
