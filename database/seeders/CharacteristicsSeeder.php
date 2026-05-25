<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Imports the "characteristics" bullet lists fetched from argostreats.gr
 * and assigns them to the matching local product (by title).
 */
class CharacteristicsSeeder extends Seeder
{
    /** Local product title => HTML characteristics. */
    private const DATA = [
        'Beef Ear' => '<ul><li>Single-ingredient &amp; high in protein</li><li>Promotes strong jaw muscles and dental health</li><li>Long-lasting chew for medium to large dogs</li><li>Low in fat and easy to digest</li></ul>',
        'Beef Head Skin' => '<ul><li>Single-ingredient &amp; high in protein</li><li>Promotes dental health through intense chewing</li><li>Long-lasting and highly satisfying</li><li>Best suited for medium to large breeds</li></ul>',
        'Beef Lung Cubes' => '<ul><li>Single-ingredient &amp; low in fat</li><li>High in natural protein</li><li>Pre-cut into convenient, bite-sized pieces</li><li>Ideal for dogs of all sizes</li></ul>',
        'Beef Pizzle' => '<ul><li>Single-ingredient &amp; high in protein</li><li>Promotes strong jaws and clean teeth</li><li>Long-lasting chew for serious chewers</li><li>Ideal for medium to large breeds</li></ul>',
        'Beef Tendon' => '<ul><li>Single-ingredient &amp; high in collagen</li><li>Promotes strong jaws and clean teeth</li><li>Supports healthy joints and skin</li><li>Ideal for medium to large breeds</li></ul>',
        'Beef Trachea' => '<ul><li>Single-ingredient &amp; joint-supportive</li><li>Natural source of glucosamine and chondroitin</li><li>Crunchy texture promotes dental health</li><li>Suitable for dogs of all sizes</li></ul>',
        'Chicken Feet' => '<ul><li>Naturally rich in collagen &amp; glucosamine for joint support</li><li>Crunchy texture helps clean teeth and gums</li><li>High in protein and natural fats for energy</li><li>Small, single-ingredient chew suitable for all dogs</li><li>Gently air-dried, with no additives or preservatives</li></ul>',
        'Chicken Necks' => '<ul><li>Naturally rich in calcium &amp; phosphorus for bone and dental support</li><li>Crunchy texture helps reduce plaque and tartar build-up</li><li>High in protein with natural fats for energy and vitality</li><li>Small, single-ingredient chew suitable for all dogs</li></ul>',
        'Chicken Wings' => '<ul><li>High in calcium &amp; phosphorus for bone strength and dental support</li><li>Crunchy texture helps clean teeth naturally</li><li>Rich in protein and healthy fats for energy and vitality</li><li>Suitable for all sizes, from puppies to seniors</li></ul>',
        'Duck Feet' => '<ul><li>Supports joint health &amp; mobility with collagen &amp; glucosamine</li><li>Crunchy texture helps clean teeth and gums</li><li>High in protein and natural fats for energy</li><li>Single-ingredient, air-dried, and free from additives</li></ul>',
        'Duck Necks' => '<ul><li>Supports joint health &amp; mobility with collagen &amp; glucosamine</li><li>Crunchy texture helps clean teeth and gums</li><li>High in protein and natural fats for energy</li><li>Single-ingredient, air-dried, and free from additives</li></ul>',
        'Furry Beef Ear' => '<ul><li>Single-ingredient &amp; high in natural protein</li><li>Fur supports gut health and digestion</li><li>Promotes dental hygiene and chewing satisfaction</li><li>Best suited for medium to large breeds</li></ul>',
        'Furry Beef Head Skin' => '<ul><li>Single-protein &amp; naturally high in collagen</li><li>Fur provides natural digestive support</li><li>Tough and long-lasting for serious chewers</li><li>Perfect for medium to large dogs</li></ul>',
        'Furry Lamb Ears' => '<ul><li>Hypoallergenic &amp; single-protein</li><li>Fur aids digestion and gut health</li><li>Ideal for small to medium dogs</li><li>Light, low-fat chew for sensitive pups</li></ul>',
        'Furry Rabbit Ears' => '<ul><li>Single-protein &amp; hypoallergenic</li><li>Natural digestive support</li><li>Encourages chewing and promotes dental health</li><li>Ideal for small to medium dogs</li></ul>',
        'Horse Skin' => '<ul><li>Hypoallergenic &amp; single-protein</li><li>Low in fat, high in chew value</li><li>Fully digestible &amp; free from additives</li><li>Ideal for medium to large dogs with sensitivities</li></ul>',
        'Lamb Ears' => '<ul><li>Single-protein &amp; hypoallergenic</li><li>Low in fat and easy on the stomach</li><li>Supports dental hygiene through natural chewing</li><li>Ideal for puppies, seniors, and small dogs</li></ul>',
        'Lamb Lung Cubes' => '<ul><li>Single-protein &amp; hypoallergenic</li><li>Light, crunchy texture dogs love</li><li>Pre-cut into convenient, bite-sized pieces</li><li>Perfect for training and daily rewarding</li></ul>',
        'Pork Ear' => '<ul><li>Single-ingredient &amp; protein-rich</li><li>Supports dental hygiene through chewing</li><li>Naturally tasty and fully digestible</li><li>Ideal for medium to large dogs</li></ul>',
        'Rabbit Ears' => '<ul><li>Single-ingredient &amp; hypoallergenic</li><li>Supports dental hygiene</li><li>Perfect for small to medium dogs</li><li>Gently air-dried, no additives or preservatives</li></ul>',
        'Turkey Foot' => '<ul><li>Supports joint health &amp; mobility with collagen &amp; glucosamine</li><li>Generous size, ideal for medium &amp; large dogs</li><li>High in protein and natural fats for energy and vitality</li><li>Single-ingredient, air-dried, and free from additives</li></ul>',
    ];

    public function run(): void
    {
        $updated = 0;
        $missing = [];

        foreach (self::DATA as $title => $html) {
            $product = Product::where('title', $title)->first();
            if (! $product) {
                $missing[] = $title;
                continue;
            }
            $product->characteristics = $html;
            $product->save();
            $updated++;
        }

        $this->command?->info("Updated characteristics on {$updated} products.");
        if (!empty($missing)) {
            $this->command?->warn('Missing local products: '.implode(', ', $missing));
        }
    }
}
