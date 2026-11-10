<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * The counter-top display boxes. Same treats as the retail pouches, packed by
 * the piece for pet shops, so the copy follows the retail products' voice.
 *
 * Idempotent: re-running updates the boxes in place by slug.
 */
class DisplayBoxSeeder extends Seeder
{
    /** Photo every box shares as its second gallery shot. */
    private const CLOSED_BOX_IMAGE = 'products/display-box-closed.jpg';

    public function run(): void
    {
        $sort = 100;

        foreach ($this->boxes() as $box) {
            $category = ProductCategory::where('slug', $box['category'])->first();

            $product = Product::updateOrCreate(
                ['slug' => $box['slug']],
                [
                    'title' => $box['title'],
                    'sku' => $box['sku'],
                    'weight' => $box['qty'].' pcs',
                    'short_description' => $this->shortDescription($box['qty'], 'en'),
                    'description' => $this->description($box, 'en'),
                    'characteristics' => $box['characteristics']['en'],
                    'seo_title' => $box['title'].' Display Box — '.$box['qty'].' pcs',
                    'seo_description' => $this->shortDescription($box['qty'], 'en'),
                    'price' => 0,
                    'stock_status' => 'in_stock',
                    'category_id' => $category?->id,
                    'type' => Product::TYPE_NATURAL_CHEWS,
                    'pack_type' => Product::PACK_DISPLAY_BOX,
                    'is_published' => true,
                    'is_featured' => false,
                    'sort_order' => ++$sort,
                ]
            );

            $product->saveTranslations([
                'title' => $box['el_title'],
                'weight' => $box['qty'].' τμχ',
                'short_description' => $this->shortDescription($box['qty'], 'el'),
                'description' => $this->description($box, 'el'),
                'characteristics' => $box['characteristics']['el'],
                'seo_title' => 'Display Box '.$box['el_title'].' — '.$box['qty'].' τμχ',
                'seo_description' => $this->shortDescription($box['qty'], 'el'),
            ]);

            $this->attachImages($product, $box['image']);
        }
    }

    private function shortDescription(int $qty, string $locale): string
    {
        return $locale === 'el'
            ? "Display box πάγκου με {$qty} τεμάχια. Μίας πρωτεΐνης, χωρίς δημητριακά, έτοιμο για το ράφι."
            : "Counter-top display box of {$qty} pieces. Single-protein, grain-free, ready for the shelf.";
    }

    private function description(array $box, string $locale): string
    {
        $qty = $box['qty'];

        $opening = $locale === 'el'
            ? "Display box {$qty} τεμαχίων, φτιαγμένο για τον πάγκο. Σηκώνεις το καπάκι, το ακουμπάς δίπλα στο ταμείο και πουλάει μόνο του."
            : "Display box of {$qty} pieces, built for the counter. Lift the lid, set it down by the till and let it sell itself.";

        $sourcing = $box['made'] === 'sticks'
            ? ($locale === 'el'
                ? 'Μία πρωτεΐνη, χωρίς δημητριακά. Παρασκευή σε μικρές παρτίδες, από κρέας προμηθευτών που γνωρίζουμε προσωπικά.'
                : 'Single protein, grain-free. Made in small batches from meat we source from suppliers we know personally.')
            : ($locale === 'el'
                ? 'Ένα μόνο συστατικό. Φυσική αποξήρανση στον αέρα. Πρώτες ύλες από προμηθευτές που γνωρίζουμε προσωπικά, παρασκευή σε μικρές παρτίδες. Χωρίς πρόσθετα, χωρίς συντηρητικά, χωρίς δικαιολογίες.'
                : 'Single-ingredient. Naturally air-dried. Sourced from suppliers we know personally and prepared in small batches. No additives, no preservatives, no apologies.');

        $storage = $locale === 'el'
            ? 'Φυλάσσετε σε δροσερό, ξηρό μέρος. Επιβλέπετε πάντα το κατοικίδιό σας όσο την απολαμβάνει.'
            : 'Store in a cool, dry place. Always supervise your pet while they enjoy.';

        return implode("\n\n", [$opening, $box['line'][$locale], $sourcing, $storage]);
    }

    private function attachImages(Product $product, string $image): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($image)) {
            $product->update(['featured_image' => $image]);
        }

        // The closed-box shot doubles as the card's hover image.
        if ($disk->exists(self::CLOSED_BOX_IMAGE)) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'path' => self::CLOSED_BOX_IMAGE],
                ['sort_order' => 1]
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function boxes(): array
    {
        $beefPizzleCharacteristics = [
            'en' => '<ul><li>Single-ingredient &amp; high in protein</li><li>Promotes strong jaws and clean teeth</li><li>Long-lasting chew for serious chewers</li><li>Ideal for medium to large breeds</li></ul>',
            'el' => '<ul><li>Ενός συστατικού &amp; πλούσιο σε πρωτεΐνη</li><li>Ενισχύει τη γνάθο και καθαρίζει τα δόντια</li><li>Μάσημα μεγάλης διάρκειας για σοβαρούς μασουλητές</li><li>Ιδανικό για μεσαίες έως μεγάλες φυλές</li></ul>',
        ];
        // The lamb pizzle is the small one — medium breeds, not the big dogs.
        $pizzleCharacteristics = [
            'en' => '<ul><li>Single-ingredient &amp; high in protein</li><li>Promotes strong jaws and clean teeth</li><li>Long-lasting chew for serious chewers</li><li>Ideal for medium breeds</li></ul>',
            'el' => '<ul><li>Ενός συστατικού &amp; πλούσιο σε πρωτεΐνη</li><li>Ενισχύει τη γνάθο και καθαρίζει τα δόντια</li><li>Μάσημα μεγάλης διάρκειας για σοβαρούς μασουλητές</li><li>Ιδανικό για μεσαίες φυλές</li></ul>',
        ];
        $tracheaCharacteristics = [
            'en' => '<ul><li>Single-ingredient &amp; joint-supportive</li><li>Natural source of glucosamine and chondroitin</li><li>Crunchy texture promotes dental health</li><li>Suitable for dogs of all sizes</li></ul>',
            'el' => '<ul><li>Ενός συστατικού &amp; φιλική προς τις αρθρώσεις</li><li>Φυσική πηγή γλυκοζαμίνης και χονδροϊτίνης</li><li>Η τραγανή υφή προάγει τη στοματική υγεία</li><li>Κατάλληλη για σκύλους όλων των μεγεθών</li></ul>',
        ];
        $skinCharacteristics = [
            'en' => '<ul><li>Hypoallergenic &amp; single-protein</li><li>Low in fat, high in chew value</li><li>Fully digestible &amp; free from additives</li><li>Ideal for medium to large dogs with sensitivities</li></ul>',
            'el' => '<ul><li>Υποαλλεργικό &amp; μίας πρωτεΐνης</li><li>Χαμηλό σε λιπαρά, με πολύ μάσημα</li><li>Πλήρως εύπεπτο &amp; χωρίς πρόσθετα</li><li>Ιδανικό για μεσαίους έως μεγάλους σκύλους με ευαισθησίες</li></ul>',
        ];
        $camelCharacteristics = [
            'en' => '<ul><li>Novel, exotic protein — ideal for dogs with allergies</li><li>Hypoallergenic &amp; single-protein</li><li>Naturally lean, with a long-lasting chew</li><li>Ideal for medium to large dogs</li><li>Gently air-dried, no additives or preservatives</li></ul>',
            'el' => '<ul><li>Νέα, εξωτική πρωτεΐνη — ιδανική για σκύλους με αλλεργίες</li><li>Υποαλλεργικό &amp; μίας πρωτεΐνης</li><li>Φυσικά άπαχο, με μάσημα που κρατάει</li><li>Ιδανικό για μεσαίους έως μεγάλους σκύλους</li><li>Απαλή αποξήρανση στον αέρα, χωρίς πρόσθετα ή συντηρητικά</li></ul>',
        ];
        $stickCharacteristics = [
            'en' => '<ul><li>Single-protein &amp; grain-free</li><li>Soft texture, easy to break in half</li><li>The right size for training and rewards</li><li>Suitable for dogs of all sizes</li></ul>',
            'el' => '<ul><li>Μίας πρωτεΐνης &amp; χωρίς δημητριακά</li><li>Μαλακή υφή, σπάει εύκολα στα δύο</li><li>Στο σωστό μέγεθος για εκπαίδευση και επιβραβεύσεις</li><li>Κατάλληλα για σκύλους όλων των μεγεθών</li></ul>',
        ];
        $sausageCharacteristics = [
            'en' => '<ul><li>Single-protein &amp; grain-free</li><li>Long and soft, easy to break in half</li><li>Made with an edible collagen casing</li><li>Suitable for dogs of all sizes</li></ul>',
            'el' => '<ul><li>Μίας πρωτεΐνης &amp; χωρίς δημητριακά</li><li>Μακρύ και μαλακό, σπάει εύκολα στα δύο</li><li>Σε βρώσιμο έντερο κολλαγόνου</li><li>Κατάλληλο για σκύλους όλων των μεγεθών</li></ul>',
        ];

        return [
            [
                'slug' => 'lamb-pizzle-display-box',
                'sku' => 'SKU-DB-01',
                'title' => 'Lamb Pizzle',
                'el_title' => 'Αρνίσιο Μόριο',
                'qty' => 100,
                'category' => 'lamb',
                'made' => 'air-dried',
                'image' => 'products/display-box-lamb-pizzle.jpg',
                'line' => [
                    'en' => 'A loud little chew for serious chewers — lean lamb pizzle, dried hard and gone slowly.',
                    'el' => 'Μια δυνατή μικρή λιχουδιά για σοβαρούς μασουλητές — άπαχο αρνίσιο μόριο, καλά αποξηραμένο, που κρατάει.',
                ],
                'characteristics' => $pizzleCharacteristics,
            ],
            [
                'slug' => 'lamb-trachea-display-box',
                'sku' => 'SKU-DB-02',
                'title' => 'Lamb Trachea',
                'el_title' => 'Αρνίσια Τραχεία',
                'qty' => 45,
                'category' => 'lamb',
                'made' => 'air-dried',
                'image' => 'products/display-box-lamb-trachea.jpg',
                'line' => [
                    'en' => 'Light, crunchy rings of lamb trachea — a natural source of glucosamine and chondroitin.',
                    'el' => 'Ελαφριοί, τραγανοί δακτύλιοι από αρνίσια τραχεία — φυσική πηγή γλυκοζαμίνης και χονδροϊτίνης.',
                ],
                'characteristics' => $tracheaCharacteristics,
            ],
            [
                'slug' => 'turkey-sticks-display-box',
                'sku' => 'SKU-DB-03',
                'title' => 'Turkey Sticks',
                'el_title' => 'Γαλοπούλα Στικ',
                'qty' => 250,
                'category' => 'turkey',
                'made' => 'sticks',
                'image' => 'products/display-box-turkey-sticks.jpg',
                'line' => [
                    'en' => 'Soft turkey sticks, the right size for pockets, training and a quick reward at the counter.',
                    'el' => 'Μαλακά στικ γαλοπούλας, στο σωστό μέγεθος για την τσέπη, την εκπαίδευση και μια γρήγορη επιβράβευση στο ταμείο.',
                ],
                'characteristics' => $stickCharacteristics,
            ],
            [
                'slug' => 'horse-skin-display-box',
                'sku' => 'SKU-DB-04',
                'title' => 'Horse Skin',
                'el_title' => 'Δέρμα Αλόγου',
                'qty' => 40,
                'category' => 'horse',
                'made' => 'air-dried',
                'image' => 'products/display-box-horse-skin.jpg',
                'line' => [
                    'en' => 'Hypoallergenic horse skin, low in fat and high in chew value.',
                    'el' => 'Υποαλλεργικό δέρμα αλόγου, χαμηλό σε λιπαρά, με πολύ μάσημα.',
                ],
                'characteristics' => $skinCharacteristics,
            ],
            [
                'slug' => 'camel-skin-display-box',
                'sku' => 'SKU-DB-05',
                'title' => 'Camel Skin',
                'el_title' => 'Δέρμα Καμήλας',
                'qty' => 25,
                'category' => 'camel',
                'made' => 'air-dried',
                'image' => 'products/display-box-camel-skin.jpg',
                'line' => [
                    'en' => 'A novel, exotic protein — the one to reach for when everything else is off the table.',
                    'el' => 'Μια νέα, εξωτική πρωτεΐνη — αυτή που επιλέγεις όταν όλα τα άλλα αποκλείονται.',
                ],
                'characteristics' => $camelCharacteristics,
            ],
            [
                'slug' => 'goat-skin-display-box',
                'sku' => 'SKU-DB-06',
                'title' => 'Goat Skin',
                'el_title' => 'Δέρμα Κατσικιού',
                'qty' => 30,
                'category' => 'goat',
                'made' => 'air-dried',
                'image' => 'products/display-box-goat-skin.jpg',
                'line' => [
                    'en' => 'Lean goat skin, gently air-dried — easy on sensitive stomachs.',
                    'el' => 'Άπαχο δέρμα κατσικιού, απαλά αποξηραμένο — ήπιο για ευαίσθητα στομάχια.',
                ],
                'characteristics' => $skinCharacteristics,
            ],
            [
                'slug' => 'chicken-sticks-display-box',
                'sku' => 'SKU-DB-07',
                'title' => 'Chicken Sticks',
                'el_title' => 'Κοτόπουλο Στικ',
                'qty' => 250,
                'category' => 'chicken',
                'made' => 'sticks',
                'image' => 'products/display-box-chicken-sticks.jpg',
                'line' => [
                    'en' => 'Soft chicken sticks, the right size for pockets, training and a quick reward at the counter.',
                    'el' => 'Μαλακά στικ κοτόπουλου, στο σωστό μέγεθος για την τσέπη, την εκπαίδευση και μια γρήγορη επιβράβευση στο ταμείο.',
                ],
                'characteristics' => $stickCharacteristics,
            ],
            [
                'slug' => 'turkey-sausage-sticks-display-box',
                'sku' => 'SKU-DB-08',
                'title' => 'Turkey Sausage Sticks',
                'el_title' => 'Λουκάνικο Γαλοπούλας Στικ',
                'qty' => 100,
                'category' => 'turkey',
                'made' => 'sticks',
                'image' => 'products/display-box-turkey-sausage-sticks.jpg',
                'line' => [
                    'en' => 'Long, soft turkey sausage sticks in an edible collagen casing.',
                    'el' => 'Μακριά, μαλακά λουκάνικα γαλοπούλας σε βρώσιμο έντερο κολλαγόνου.',
                ],
                'characteristics' => $sausageCharacteristics,
            ],
            [
                'slug' => 'chicken-sausage-sticks-display-box',
                'sku' => 'SKU-DB-09',
                'title' => 'Chicken Sausage Sticks',
                'el_title' => 'Λουκάνικο Κοτόπουλου Στικ',
                'qty' => 100,
                'category' => 'chicken',
                'made' => 'sticks',
                'image' => 'products/display-box-chicken-sausage-sticks.jpg',
                'line' => [
                    'en' => 'Long, soft chicken sausage sticks in an edible collagen casing.',
                    'el' => 'Μακριά, μαλακά λουκάνικα κοτόπουλου σε βρώσιμο έντερο κολλαγόνου.',
                ],
                'characteristics' => $sausageCharacteristics,
            ],
            [
                'slug' => 'beef-pizzle-display-box',
                'sku' => 'SKU-DB-10',
                'title' => 'Beef Pizzle',
                'el_title' => 'Μοσχαρίσιο Μόριο',
                'qty' => 70,
                'category' => 'beef',
                'made' => 'air-dried',
                'image' => 'products/display-box-beef-pizzle.jpg',
                'line' => [
                    'en' => 'Beef pizzle, dried hard and gone slowly — the long chew serious chewers settle in with.',
                    'el' => 'Μοσχαρίσιο μόριο, καλά αποξηραμένο, που κρατάει — το μάσημα με το οποίο στρώνονται και οι σοβαροί μασουλητές.',
                ],
                'characteristics' => $beefPizzleCharacteristics,
            ],
        ];
    }
}
