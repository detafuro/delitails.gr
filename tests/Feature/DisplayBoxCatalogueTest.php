<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** /display-boxes is the retail catalogue in a different pack — the two must not leak into each other. */
class DisplayBoxCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'title' => 'Horse Skin',
            'slug' => 'horse-skin',
            'weight' => '100 gr',
            'price' => 0,
            'stock_status' => 'in_stock',
            'type' => Product::TYPE_NATURAL_CHEWS,
            'pack_type' => Product::PACK_RETAIL,
            'is_published' => true,
        ], $attributes));
    }

    public function test_each_listing_shows_only_its_own_pack(): void
    {
        $this->product();
        $this->product([
            'title' => 'Horse Skin Box',
            'slug' => 'horse-skin-display-box',
            'weight' => '40 pcs',
            'pack_type' => Product::PACK_DISPLAY_BOX,
        ]);

        $this->get('/en/products')
            ->assertOk()
            ->assertSee('Horse Skin')
            ->assertDontSee('Horse Skin Box');

        $this->get('/en/display-boxes')
            ->assertOk()
            ->assertSee('Horse Skin Box')
            ->assertSee('40 pcs');
    }

    public function test_a_product_is_not_served_under_the_other_packs_url(): void
    {
        $this->product();
        $this->product(['slug' => 'horse-skin-display-box', 'pack_type' => Product::PACK_DISPLAY_BOX]);

        $this->get('/en/products/horse-skin')->assertOk();
        $this->get('/en/display-boxes/horse-skin-display-box')->assertOk();

        $this->get('/en/display-boxes/horse-skin')->assertNotFound();
        $this->get('/en/products/horse-skin-display-box')->assertNotFound();
    }

    public function test_cards_link_to_the_listing_their_pack_belongs_to(): void
    {
        $this->product(['slug' => 'horse-skin-display-box', 'pack_type' => Product::PACK_DISPLAY_BOX]);

        $this->get('/en/display-boxes')->assertOk()->assertSee('/en/display-boxes/horse-skin-display-box', false);
    }

    public function test_filters_only_offer_categories_that_hold_this_pack(): void
    {
        $horse = ProductCategory::create(['name' => 'Horse', 'slug' => 'horse', 'is_active' => true]);
        $fish = ProductCategory::create(['name' => 'Fish', 'slug' => 'fish', 'is_active' => true]);

        $this->product(['slug' => 'horse-skin-display-box', 'pack_type' => Product::PACK_DISPLAY_BOX, 'category_id' => $horse->id]);
        $this->product(['title' => 'Sandlance', 'slug' => 'sandlance', 'category_id' => $fish->id]);

        $this->get('/en/display-boxes')->assertOk()->assertSee('Horse')->assertDontSee('Fish');
    }

    public function test_display_boxes_stay_off_the_home_page_grid(): void
    {
        $this->product(['title' => 'Horse Skin Box', 'slug' => 'horse-skin-display-box', 'pack_type' => Product::PACK_DISPLAY_BOX]);

        $this->get('/en')->assertOk()->assertDontSee('Horse Skin Box');
    }
}
