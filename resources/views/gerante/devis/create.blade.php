@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-700"></i>
                <span>Nouveau Devis</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Créer une proposition commerciale pour un client</p>
        </div>
        <div>
            <a href="{{ route('gerante.devis.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
            <p class="font-bold mb-2"><i class="fa-solid fa-triangle-exclamation"></i> Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gerante.devis.store') }}" method="POST" id="devisForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Informations Client -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-user text-blue-600 mr-2"></i> Informations Client
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Nom Complet *</label>
                            <input type="text" name="client_nom" value="{{ old('client_nom') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Téléphone *</label>
                            <input type="text" name="client_telephone" value="{{ old('client_telephone') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                            <input type="email" name="client_email" value="{{ old('client_email') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Entreprise (Optionnel)</label>
                            <input type="text" name="client_entreprise" value="{{ old('client_entreprise') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-note-sticky text-blue-600 mr-2"></i> Notes & Conditions
                    </h2>
                    <textarea name="notes" rows="4" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all" placeholder="Ex: Devis valable 30 jours...">{{ old('notes', "Devis valable 30 jours à compter de la date d'émission.") }}</textarea>
                </div>
            </div>

            <!-- Produits et Lignes -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                            <i class="fa-solid fa-list text-blue-600 mr-2"></i> Produits du Devis
                        </h2>
                        <button type="button" onclick="ajouterLigne()" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-blue-500 hover:text-blue-700 text-slate-600 text-xs font-bold rounded-lg transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Ajouter une ligne
                        </button>
                    </div>

                    <div class="p-0 overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse" id="lignesTable">
                            <thead>
                                <tr class="bg-white border-b border-slate-100">
                                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/2">Produit</th>
                                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/6">P.U (FCFA)</th>
                                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/6">Qté</th>
                                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/6 text-right">S-Total</th>
                                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="lignesBody">
                                <!-- Les lignes seront insérées ici par JS -->
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-600 uppercase">Montant Total :</td>
                                    <td colspan="2" class="px-6 py-4 text-right">
                                        <span class="text-xl font-black text-blue-700" id="montantTotalAffichage">0</span>
                                        <span class="text-sm font-bold text-blue-700 ml-1">FCFA</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="p-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-save"></i> Enregistrer le Devis
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- Template Ligne Caché -->
    <template id="ligneTemplate">
        <tr class="ligne-devis hover:bg-slate-50 transition-colors">
            <td class="px-6 py-3">
                <select name="lignes[__INDEX__][id_p]" onchange="majLigne(this)" required class="produit-select w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                    <option value="" data-prix="0">Sélectionner un produit...</option>
                    @foreach($produits as $p)
                        <option value="{{ $p->id }}" data-prix="{{ $p->prix_unitaire }}">{{ $p->reference }} - {{ $p->designation }} (Stock: {{ $p->quantite }})</option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-3">
                <input type="text" readonly class="prix-unitaire w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 text-right" value="0">
            </td>
            <td class="px-6 py-3">
                <input type="number" name="lignes[__INDEX__][quantite]" value="1" min="1" oninput="majLigne(this)" required class="quantite w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none text-center">
            </td>
            <td class="px-6 py-3 text-right">
                <span class="sous-total font-bold text-slate-800">0</span>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="supprimerLigne(this)" class="w-8 h-8 rounded-lg text-slate-400 hover:bg-rose-100 hover:text-rose-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        let indexLigne = 0;

        function ajouterLigne() {
            const template = document.getElementById('ligneTemplate');
            const tbody = document.getElementById('lignesBody');
            
            let clone = template.content.cloneNode(true);
            
            // Remplacer __INDEX__ par l'index réel
            let inputs = clone.querySelectorAll('select, input');
            inputs.forEach(input => {
                if(input.name) {
                    input.name = input.name.replace('__INDEX__', indexLigne);
                }
            });
            
            tbody.appendChild(clone);
            indexLigne++;
            
            calculerTotalGlobal();
        }

        function supprimerLigne(button) {
            button.closest('tr').remove();
            calculerTotalGlobal();
        }

        function majLigne(element) {
            const tr = element.closest('tr');
            const select = tr.querySelector('.produit-select');
            const option = select.options[select.selectedIndex];
            
            const prix = parseFloat(option.getAttribute('data-prix')) || 0;
            const quantite = parseInt(tr.querySelector('.quantite').value) || 0;
            
            const sousTotal = prix * quantite;
            
            // Affichage P.U
            tr.querySelector('.prix-unitaire').value = formatNumber(prix);
            
            // Affichage Sous-total
            tr.querySelector('.sous-total').textContent = formatNumber(sousTotal);
            
            calculerTotalGlobal();
        }

        function calculerTotalGlobal() {
            let total = 0;
            const lignes = document.querySelectorAll('.ligne-devis');
            
            lignes.forEach(tr => {
                const select = tr.querySelector('.produit-select');
                const option = select.options[select.selectedIndex];
                const prix = parseFloat(option.getAttribute('data-prix')) || 0;
                const quantite = parseInt(tr.querySelector('.quantite').value) || 0;
                total += (prix * quantite);
            });
            
            document.getElementById('montantTotalAffichage').textContent = formatNumber(total);
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }

        // Ajouter une ligne par défaut au chargement
        document.addEventListener('DOMContentLoaded', () => {
            ajouterLigne();
        });
    </script>

@endsection
