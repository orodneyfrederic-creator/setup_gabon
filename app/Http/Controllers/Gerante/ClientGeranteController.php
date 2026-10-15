<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientGeranteController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'client')
            ->withCount('commandes')
            ->withSum('commandes', 'montant_c');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tel', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('gerante.clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = User::where('role', 'client')
            ->with(['commandes' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }, 'adresses'])
            ->withCount('commandes')
            ->withSum('commandes', 'montant_c')
            ->findOrFail($id);

        return view('gerante.clients.show', compact('client'));
    }
}
