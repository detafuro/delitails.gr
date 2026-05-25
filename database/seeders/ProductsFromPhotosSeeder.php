<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsFromPhotosSeeder extends Seeder
{
    private const ANIMALS = [
        'Beef'    => ['order' => 1, 'desc' => 'Slow, dense, satisfying. The classic chew.'],
        'Chicken' => ['order' => 2, 'desc' => 'Lean and lightweight. Easy wins for the bowl.'],
        'Duck'    => ['order' => 3, 'desc' => 'Richer flavour, perfect for picky chewers.'],
        'Lamb'    => ['order' => 4, 'desc' => 'Soft, mild, gentle on sensitive stomachs.'],
        'Rabbit'  => ['order' => 5, 'desc' => 'Single-protein favourite for the most refined palates.'],
        'Turkey'  => ['order' => 6, 'desc' => 'Lean and low-fat — the everyday treat.'],
        'Pork'    => ['order' => 7, 'desc' => 'Bold-flavoured, long-lasting chews.'],
        'Horse'   => ['order' => 8, 'desc' => 'Hypoallergenic, single-source. For the sensitive crowd.'],
    ];

    private const PRODUCT_META = [
        'BEEF-EAR'              => ['Beef Ear',              'Beef',    Product::TYPE_NATURAL_CHEWS],
        'BEEF-HEAD-SKIN'        => ['Beef Head Skin',        'Beef',    Product::TYPE_NATURAL_CHEWS],
        'BEEF-LUNG-CUBES'       => ['Beef Lung Cubes',       'Beef',    Product::TYPE_TRAINING_TREATS],
        'BEEF-PIZZLE'           => ['Beef Pizzle',           'Beef',    Product::TYPE_NATURAL_CHEWS],
        'BEEF-TENDON'           => ['Beef Tendon',           'Beef',    Product::TYPE_NATURAL_CHEWS],
        'BEEF-TRACHEA'          => ['Beef Trachea',          'Beef',    Product::TYPE_NATURAL_CHEWS],
        'CHICKEN-FEET'          => ['Chicken Feet',          'Chicken', Product::TYPE_NATURAL_CHEWS],
        'CHICKEN-NECKS'         => ['Chicken Necks',         'Chicken', Product::TYPE_NATURAL_CHEWS],
        'CHICKEN-WINGS'         => ['Chicken Wings',         'Chicken', Product::TYPE_NATURAL_CHEWS],
        'DUCK-FEET'             => ['Duck Feet',             'Duck',    Product::TYPE_NATURAL_CHEWS],
        'DUCK-NECKS'            => ['Duck Necks',            'Duck',    Product::TYPE_NATURAL_CHEWS],
        'FURRY-BEEF-EAR'        => ['Furry Beef Ear',        'Beef',    Product::TYPE_NATURAL_CHEWS],
        'FURRY-BEEF-HEAD-SKIN'  => ['Furry Beef Head Skin',  'Beef',    Product::TYPE_NATURAL_CHEWS],
        'FURRY-LAMB-EARS'       => ['Furry Lamb Ears',       'Lamb',    Product::TYPE_NATURAL_CHEWS],
        'FURRY-RABBIT-EARS'     => ['Furry Rabbit Ears',     'Rabbit',  Product::TYPE_NATURAL_CHEWS],
        'HORSE-SKIN'            => ['Horse Skin',            'Horse',   Product::TYPE_NATURAL_CHEWS],
        'LAMB-EARS'             => ['Lamb Ears',             'Lamb',    Product::TYPE_NATURAL_CHEWS],
        'LAMB-LUNG-CUBES'       => ['Lamb Lung Cubes',       'Lamb',    Product::TYPE_TRAINING_TREATS],
        'PORK-EAR'              => ['Pork Ear',              'Pork',    Product::TYPE_NATURAL_CHEWS],
        'RABBIT-EARS'           => ['Rabbit Ears',           'Rabbit',  Product::TYPE_NATURAL_CHEWS],
        'TURKEY-FOOT'           => ['Turkey Foot',           'Turkey',  Product::TYPE_NATURAL_CHEWS],
    ];

    public function run(): void
    {
        $this->wipeExisting();
        $animalIds = $this->seedAnimalCategories();
        $this->seedProducts($animalIds);
    }

    private function wipeExisting(): void
    {
        foreach (Product::with('images')->get() as $product) {
            if ($product->featured_image) Storage::disk('public')->delete($product->featured_image);
            foreach ($product->images as $img) Storage::disk('public')->delete($img->path);
        }
        ProductImage::query()->delete();
        Product::query()->delete();

        foreach (ProductCategory::all() as $cat) {
            if ($cat->image) Storage::disk('public')->delete($cat->image);
        }
        ProductCategory::query()->delete();
    }

    /** @return array<string,int> animal name → category id */
    private function seedAnimalCategories(): array
    {
        $ids = [];
        foreach (self::ANIMALS as $name => $meta) {
            $cat = ProductCategory::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $meta['desc'],
                'sort_order' => $meta['order'],
                'is_active' => true,
            ]);
            $ids[$name] = $cat->id;
        }
        return $ids;
    }

    private function seedProducts(array $animalIds): void
    {
        $sourceDir = base_path('photos');
        $pouches = glob($sourceDir.'/POUCH_*.jpg') ?: [];
        sort($pouches);

        if (empty($pouches)) {
            $this->command?->warn('No POUCH_*.jpg files found in '.$sourceDir);
            return;
        }

        $i = 0;
        foreach ($pouches as $pouchPath) {
            $basename = basename($pouchPath);
            $rest = preg_replace('/^POUCH_/', '', $basename);
            $stem = preg_replace('/\.jpe?g$/i', '', $rest);

            $meta = self::PRODUCT_META[$stem] ?? null;
            if ($meta === null) {
                $this->command?->warn("No metadata for {$stem}, skipping.");
                continue;
            }
            [$title, $animal, $type] = $meta;
            $categoryId = $animalIds[$animal] ?? null;

            $featuredFilename = 'pouch_'.Str::slug($stem).'.jpg';
            Storage::disk('public')->putFileAs('products', new File($pouchPath), $featuredFilename);

            $i++;
            $product = Product::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'sku' => 'SKU-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'short_description' => 'Single-ingredient, naturally air-dried treat. Hand-prepared in small batches with no nasties.',
                'description' => "A loud little treat for serious chewers.\n\nSingle-ingredient. Naturally air-dried. Sourced from suppliers we know personally and prepared in small batches. No additives, no preservatives, no apologies.\n\nStore in a cool, dry place. Always supervise your pet while they enjoy.",
                'price' => 6.90,
                'sale_price' => null,
                'stock_status' => 'in_stock',
                'featured_image' => 'products/'.$featuredFilename,
                'category_id' => $categoryId,
                'type' => $type,
                'is_published' => true,
                'is_featured' => $i <= 8,
                'sort_order' => $i,
            ]);

            $productPath = $sourceDir.'/PRODUCT_'.$rest;
            if (is_file($productPath)) {
                $secondaryFilename = 'product_'.Str::slug($stem).'.jpg';
                Storage::disk('public')->putFileAs('products', new File($productPath), $secondaryFilename);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => 'products/'.$secondaryFilename,
                    'sort_order' => 0,
                ]);
            }
        }

        $this->command?->info("Seeded {$i} products across ".count($animalIds)." animal categories.");
    }
}
