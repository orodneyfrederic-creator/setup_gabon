<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\SavTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SavController extends Controller
{
    public function index(Request $request)
    {
        $query = SavTicket::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%")
                    ->orWhere('client_tel', 'like', "%{$search}%")
                    ->orWhere('designation_produit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('gerante.sav.index', compact('tickets'));
    }

    public function create()
    {
        return view('gerante.sav.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_tel' => 'required|string|max:20',
            'designation_produit' => 'required|string|max:255',
            'description_panne' => 'required|string',
            'date_retour_prevue' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $reference = 'SAV-'.date('Ymd').'-'.strtoupper(Str::random(4));

        SavTicket::create([
            'reference' => $reference,
            'client_nom' => $validated['client_nom'],
            'client_tel' => $validated['client_tel'],
            'designation_produit' => $validated['designation_produit'],
            'description_panne' => $validated['description_panne'],
            'date_retour_prevue' => $validated['date_retour_prevue'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'statut' => 'ouvert',
        ]);

        return redirect()->route('gerante.sav.index')->with('success', 'Ticket SAV ouvert avec succès.');
    }

    public function show(SavTicket $sav)
    {
        return view('gerante.sav.show', compact('sav'));
    }

    public function update(Request $request, SavTicket $sav)
    {
        $validated = $request->validate([
            'statut' => 'required|in:ouvert,en_cours,resolu,clos',
            'notes' => 'nullable|string',
            'date_retour_prevue' => 'nullable|date',
        ]);

        $sav->update($validated);

        return back()->with('success', 'Le ticket SAV a été mis à jour.');
    }
}
