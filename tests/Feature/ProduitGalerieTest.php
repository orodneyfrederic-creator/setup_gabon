<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProduitGalerieTest extends TestCase
{
    use RefreshDatabase;

    /** PNG 1 × 1 valide : évite de dépendre de l'extension GD dans les tests. */
    private const PNG_MINIMAL = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

    private function photo(string $nom): UploadedFile
    {
        $chemin = tempnam(sys_get_temp_dir(), 'photo');
        file_put_contents($chemin, base64_decode(self::PNG_MINIMAL));

        return new UploadedFile($chemin, $nom, 'image/png', null, true);
    }

    private function gerante(): Staff
    {
        return Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesProduit(array $complements = []): array
    {
        return array_merge([
            'reference' => 'LAP-HP-001',
            'designation' => 'HP Probook 440 G8',
            'prix_unitaire' => 465000,
            'quantite' => 5,
            'id_cat' => Category::firstOrCreate(['nom' => 'Ordinateurs portables'])->id,
            'id_m' => Marque::firstOrCreate(['nom' => 'HP'])->id,
        ], $complements);
    }

    public function test_la_gerante_cree_un_produit_avec_photos_et_fiche_technique(): void
    {
        Storage::fake('public');

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.produits.store'), $this->donneesProduit([
                'photos' => [$this->photo('face.png'), $this->photo('dos.png')],
                'caracteristiques' => [
                    ['libelle' => ' Processeur ', 'valeur' => 'Intel Core i5'],
                    ['libelle' => 'Mémoire vive', 'valeur' => ''],
                    ['libelle' => 'Stockage', 'valeur' => 'SSD 256 Go'],
                ],
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gerante.produits.index'));

        $produit = Produit::where('reference', 'LAP-HP-001')->firstOrFail();

        $this->assertNull($produit->image);
        $this->assertCount(2, $produit->images);
        foreach ($produit->images as $photo) {
            Storage::disk('public')->assertExists($photo->chemin);
        }
        $this->assertSame([
            ['libelle' => 'Processeur', 'valeur' => 'Intel Core i5'],
            ['libelle' => 'Stockage', 'valeur' => 'SSD 256 Go'],
        ], $produit->caracteristiques);
    }

    public function test_la_suppression_d_une_photo_ne_touche_que_le_produit_modifie(): void
    {
        Storage::fake('public');
        $autreProduit = Produit::create($this->donneesProduit(['reference' => 'AUTRE-001']));
        $photoAutreProduit = $autreProduit->images()->create(['chemin' => 'produits/autre.png', 'position' => 1]);

        $produit = Produit::create($this->donneesProduit());
        $photoASupprimer = $produit->images()->create(['chemin' => 'produits/a-supprimer.png', 'position' => 1]);
        $photoAGarder = $produit->images()->create(['chemin' => 'produits/a-garder.png', 'position' => 2]);
        Storage::disk('public')->put('produits/a-supprimer.png', 'x');

        $this->actingAs($this->gerante(), 'staff')
            ->put(route('gerante.produits.update', $produit), $this->donneesProduit([
                'supprimer_photos' => [$photoASupprimer->id, $photoAutreProduit->id],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($photoASupprimer);
        $this->assertModelExists($photoAGarder);
        $this->assertModelExists($photoAutreProduit);
        Storage::disk('public')->assertMissing('produits/a-supprimer.png');
    }

    public function test_la_fiche_produit_affiche_la_galerie_et_la_fiche_technique(): void
    {
        $produit = Produit::create($this->donneesProduit([
            'image' => 'produits/principale.jpg',
            'caracteristiques' => [['libelle' => 'Processeur', 'valeur' => 'Intel Core i5-1135G7']],
        ]));
        $produit->images()->create(['chemin' => 'produits/dos.jpg', 'position' => 1]);

        $this->get(route('produits.show', $produit->id))
            ->assertOk()
            ->assertSee('Fiche technique')
            ->assertSee('Intel Core i5-1135G7')
            ->assertSee('Afficher la photo 2')
            ->assertSee(asset('storage/produits/dos.jpg'), false);
    }
}
