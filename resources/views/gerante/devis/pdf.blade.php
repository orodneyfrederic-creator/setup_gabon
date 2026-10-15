@php
    // Taux de TVA : utilise $devi->taux_tva si le champ existe, sinon repli à 18% (taux standard Gabon).
    $tauxTva     = $devi->taux_tva ?? 18;
    $tauxCss     = 1;
    $montantHT   = (float) $devi->montant_total;
    $montantTva  = round($montantHT * $tauxTva / 100);
    $montantCss  = round($montantHT * $tauxCss / 100);
    $montantTTC  = $montantHT + $montantTva + $montantCss;

    $conditionsReglement = $devi->conditions_reglement ?? '30% à la commande, le solde à la livraison.';

    $logoPath = public_path('img/logo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;

    $bgPath = public_path('img/design_devis.png');
    $bgSrc  = file_exists($bgPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($bgPath))
        : null;
@endphp
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Devis {{ $devi->numero }}</title>
<style>
    /* ─── Base : structure reprise de sparksuite/simple-html-invoice-template ─── */
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
       
        box-shadow: 0 0 10px rgba(43, 95, 138, .12);
        font-size: 13px;
        line-height: 20px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #3b4a5a;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
        border-collapse: collapse;
    }
    .invoice-box table td { padding: 5px; vertical-align: top; }

    .invoice-box table tr.top table td { padding-bottom: 20px; }
    .invoice-box table tr.top table td.title {
        font-size: 34px;
        line-height: 34px;
        color: #1f3a5f;
        font-weight: 800;
    }
    .invoice-box table tr.top table td.meta {
        text-align: right;
        font-size: 12px;
        line-height: 1.9;
    }
    .invoice-box table tr.top table td.meta strong { color: #1f3a5f; }

    .invoice-box table tr.information table td { padding-bottom: 30px; font-size: 12px; line-height: 1.9; }
    .invoice-box .client-col { text-align: right; }
    .invoice-box .block-title {
        font-weight: 800;
        text-transform: uppercase;
        font-size: 10.5px;
        letter-spacing: 0.4px;
        color: #1f3a5f;
        margin-bottom: 4px;
    }

    .invoice-box .objet-row td { padding-bottom: 16px; font-size: 12.5px; color: #3b4a5a; }
    .invoice-box .objet-row strong { font-weight: 800; }

    /* ─── Tableau des lignes ─── */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.items thead td {
        background: #dbeafe;
        border-bottom: 1px solid #b6d6f2;
        color: #1f3a5f;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        padding: 8px 6px;
    }
    table.items tbody td {
        padding: 8px 6px;
        border-bottom: 1px solid #e3eef8;
        font-size: 12px;
    }
    table.items .col-qty   { text-align: center; width: 12%; }
    table.items .col-price { text-align: right;  width: 20%; }
    table.items .col-total { text-align: right;  width: 20%; }

    /* ─── Totaux ─── */
    table.totals { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.totals td { padding: 5px 6px; font-size: 12px; }
    table.totals td:first-child { width: 78%; text-align: right; color: #1f3a5f; }
    table.totals td:last-child  { width: 22%; text-align: right; }
    table.totals tr.grand-total td {
        border-top: 2px solid #2b5f8a;
        font-weight: 800;
        font-size: 13px;
        color: #1f3a5f;
        padding-top: 8px;
    }

    /* ─── Blocs après le tableau ─── */
    .closing-block { margin-top: 26px; font-size: 12px; color: #3b4a5a; line-height: 1.9; }
    .closing-block strong { font-weight: 800; color: #1f3a5f; }

    .sig-line { margin-top: 18px; font-size: 12px; }
    .sig-line table { width: 100%; }
    .sig-line td { padding: 0; }

    .conditions {
        margin-top: 22px;
        font-size: 11px;
        color: #555;
        line-height: 1.8;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    .footer {
        position: fixed;
        bottom: -14mm;
        left: 0; right: 0;
        text-align: center;
        font-size: 9px;
        color: #888;
    }

    @page { margin: 16mm 14mm 22mm 14mm; }
</style>
</head>
<body>

    @if($bgSrc)
    <img src="{{ $bgSrc }}" style="position: fixed; top: -16mm; left: -14mm; width: 210mm; height: 297mm; z-index: -1000;">
    @endif

    <div class="footer">
        N° NIF : XXXXXXXXXX — N° RCCM : XXXXXXXXX — SETUP-GABON — Libreville, Gabon
    </div>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">

            {{-- ═══ LOGO / TITRE + META DEVIS (row "top") ═══ --}}
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                @if($logoSrc)
                                    <img src="{{ $logoSrc }}" style="max-width:200px; max-height:60px; display:block;">
                                @else
                                    SETUP-GABON
                                @endif
                            </td>
                            <td class="meta">
                                <strong>Devis n°&nbsp;:</strong> {{ $devi->numero }}<br>
                                <strong>Date&nbsp;:</strong> {{ $devi->created_at->format('d/m/Y') }}<br>
                                @if($devi->date_validite)
                                    <strong>Validité&nbsp;:</strong> {{ \Carbon\Carbon::parse($devi->date_validite)->format('d/m/Y') }}
                                @else
                                    <strong>Durée de validité&nbsp;:</strong> 30 jours
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- ═══ ENTREPRISE + CLIENT (row "information") ═══ --}}
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <div class="block-title">SETUP-GABON (SARL)</div>
                                Libreville, Gabon<br>
                                +241 00 00 00 00<br>
                                contact@setup-gabon.com<br>
                                N°&nbsp;NIF&nbsp;: XXXXXXXXXX<br>
                                N°&nbsp;RCCM&nbsp;: XXXXXXXXX
                            </td>
                            <td class="client-col">
                                <div class="block-title">{{ $devi->client_nom }}</div>
                                @if($devi->client_entreprise)
                                    {{ $devi->client_entreprise }}<br>
                                @endif
                                @if($devi->client_tel)
                                    {{ $devi->client_tel }}<br>
                                @endif
                                @if($devi->client_email)
                                    {{ $devi->client_email }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- ═══ OBJET ═══ --}}
            @if($devi->notes)
            <tr class="objet-row">
                <td colspan="2"><strong>Objet&nbsp;:</strong> {{ Str::limit($devi->notes, 120) }}</td>
            </tr>
            @endif

            {{-- ═══ TABLEAU DES LIGNES ═══ --}}
            <tr>
                <td colspan="2">
                    <table class="items">
                        <thead>
                            <tr>
                                <td>Description</td>
                                <td class="col-qty">Quantité</td>
                                <td class="col-price">Prix HT</td>
                                <td class="col-total">Total HT</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devi->lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->designation }}</td>
                                <td class="col-qty">{{ $ligne->quantite }}</td>
                                <td class="col-price">
                                    @if(is_null($ligne->prix_unitaire) || (float) $ligne->prix_unitaire === 0.0)
                                        Inclus
                                    @else
                                        {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA
                                    @endif
                                </td>
                                <td class="col-total" style="font-weight:700;">
                                    @if(is_null($ligne->prix_unitaire) || (float) $ligne->prix_unitaire === 0.0)
                                        Inclus
                                    @else
                                        {{ number_format($ligne->total_ligne, 0, ',', ' ') }} FCFA
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>

            {{-- ═══ TOTAUX ═══ --}}
            <tr>
                <td colspan="2">
                    <table class="totals">
                        <tr>
                            <td>Total Hors Taxe</td>
                            <td>{{ number_format($montantHT, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td>TVA ({{ $tauxTva }}&nbsp;%)</td>
                            <td>{{ number_format($montantTva, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td>CSS ({{ $tauxCss }}&nbsp;%)</td>
                            <td>{{ number_format($montantCss, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="grand-total">
                            <td>Total TTC</td>
                            <td>{{ number_format($montantTTC, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>

        {{-- ═══ CLÔTURE ═══ --}}
        <div class="closing-block">
            Je reste à votre disposition pour toute information complémentaire.<br>
            Cordialement,<br>
            Si ce devis vous convient, veuillez le retourner signé précédé de la mention&nbsp;:<br>
            <strong>« BON POUR ACCORD ET EXÉCUTION DU DEVIS »</strong>
        </div>

        <div class="sig-line">
            <table>
                <tr>
                    <td style="width:45%;">Date&nbsp;:</td>
                    <td>Signature&nbsp;:</td>
                </tr>
            </table>
        </div>

        <div class="conditions">
            Condition de règlement&nbsp;: {{ $conditionsReglement }}<br>
            Ce devis tient lieu d'engagement après signature. Valable 30 jours à compter de sa date d'émission.
        </div>
    </div>

</body>
</html>