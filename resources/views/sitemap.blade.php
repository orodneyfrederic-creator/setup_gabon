{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc>{{ route('categories.index') }}</loc><changefreq>weekly</changefreq></url>
    <url><loc>{{ route('about') }}</loc><changefreq>monthly</changefreq></url>
    @foreach($categories as $categorie)
        <url><loc>{{ route('home', ['category' => $categorie->id]) }}</loc>@if($categorie->updated_at)<lastmod>{{ $categorie->updated_at->toAtomString() }}</lastmod>@endif</url>
    @endforeach
    @foreach($produits as $produit)
        <url><loc>{{ route('produits.show', $produit->id) }}</loc>@if($produit->updated_at)<lastmod>{{ $produit->updated_at->toAtomString() }}</lastmod>@endif<priority>0.8</priority></url>
    @endforeach
    @foreach(['legal.cgv', 'legal.retours', 'legal.confidentialite', 'legal.mentions'] as $routeLegale)
        <url><loc>{{ route($routeLegale) }}</loc><changefreq>yearly</changefreq></url>
    @endforeach
</urlset>
