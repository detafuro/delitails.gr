<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\Faq;
use App\Models\FaqGroup;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSettings();
        $this->seedCategoriesAndProducts();
        $this->seedFaqs();
        $this->seedBlog();
        $this->seedStores();
        $this->seedTestimonials();
    }

    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@delitails.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedSettings(): void
    {
        $values = [
            'site_name' => 'Delitails',
            'contact_email' => 'bark@delitails.test',
            'contact_phone' => '+30 210 000 0000',
            'contact_address' => 'Kitchen HQ, Athens, Greece',
            'social_facebook' => 'https://facebook.com/',
            'social_instagram' => 'https://instagram.com/',
            'social_tiktok' => 'https://tiktok.com/',
            'social_youtube' => 'https://youtube.com/',
            'newsletter_heading' => 'Join the pack',
            'newsletter_text' => 'New batches, restock alerts, and the occasional ridiculous deal. Drop your email — we promise to keep it loud, not noisy.',
            'announcement_messages' => "Free shipping over €40 — let them eat treats.\nHand-baked. Small-batch. Loud as hell.\nNew treats just landed. Sink your teeth in.",
            'hero_heading' => 'TREATS WITH ATTITUDE',
            'hero_subheading' => 'Loud little snacks for picky pets and the humans who feed them. Small-batch, big personality, zero filler.',
            'hero_cta_text' => 'Shop the pack',
            'hero_cta_link' => '/products',
            'seo_default_title' => 'Delitails — Premium pet treats with attitude',
            'seo_default_description' => 'Small-batch, hand-baked treats for the loudest, best-behaved (and not so) pets around.',
            'footer_text' => 'Loud treats for good dogs and louder cats. Hand-baked, small batch, raised on rebellion.',
        ];
        foreach ($values as $key => $value) {
            Setting::set($key, $value);
        }
    }

    private function seedCategoriesAndProducts(): void
    {
        $categories = [
            ['name' => 'Crunchy', 'description' => 'Slow-baked, satisfyingly snappy. The classic crowd-pleaser.'],
            ['name' => 'Chewy', 'description' => 'Soft, long-lasting and built for the patient chewer.'],
            ['name' => 'Mini bites', 'description' => 'Pocket-sized rewards. Training friendly, walk friendly, sneaky friendly.'],
            ['name' => 'Toppers', 'description' => 'Sprinkle, scatter, level up the bowl.'],
            ['name' => 'Birthday', 'description' => 'For the wag-worthy occasions and good-boy days.'],
            ['name' => 'Cats', 'description' => 'For the household tyrant. They deserve the loud stuff too.'],
        ];

        $categoryModels = collect();
        foreach ($categories as $i => $c) {
            $cat = ProductCategory::updateOrCreate(['name' => $c['name']], [
                'description' => $c['description'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
            $categoryModels->push($cat);
        }

        $products = [
            ['Howl Bites', 'Crunchy', 6.90, null, true],
            ['Wild Sticks', 'Chewy', 8.50, 7.20, true],
            ['Tiny Tail Treats', 'Mini bites', 4.90, null, true],
            ['Bowl Confetti', 'Toppers', 5.50, null, false],
            ['Birthday Riot', 'Birthday', 9.90, null, true],
            ['Cat Riot Crunch', 'Cats', 5.40, null, false],
            ['Crackle Chunks', 'Crunchy', 6.40, 5.80, false],
            ['Long Day Chews', 'Chewy', 7.90, null, true],
            ['Pocket Rewards', 'Mini bites', 3.90, null, false],
            ['Furry Sprinkles', 'Toppers', 5.20, null, false],
            ['Loud Bones', 'Crunchy', 7.20, null, false],
            ['Hush Puffs (Cat)', 'Cats', 4.90, 4.10, true],
        ];

        $i = 0;
        foreach ($products as [$title, $catName, $price, $sale, $featured]) {
            $cat = $categoryModels->firstWhere('name', $catName);
            Product::updateOrCreate(['title' => $title], [
                'category_id' => $cat?->id,
                'price' => $price,
                'sale_price' => $sale,
                'short_description' => 'Hand-baked, small batch. Crafted with real ingredients and a stubborn refusal to compromise.',
                'description' => "A loud little treat your pet will lose their mind over.\n\nMade in small batches with simple, recognisable ingredients. No nasties, no shortcuts, no apologies. Store in a cool, dry place and try not to eat them yourself.",
                'sku' => 'SKU-'.str_pad(++$i, 4, '0', STR_PAD_LEFT),
                'stock_status' => 'in_stock',
                'is_published' => true,
                'is_featured' => $featured,
                'sort_order' => $i,
            ]);
        }
    }

    private function seedFaqs(): void
    {
        $groups = [
            'About the treats' => [
                ['What goes into your treats?', 'We use simple, recognisable ingredients sourced from suppliers we know personally. No artificial colours, no fillers, no surprise additives.'],
                ['Are the treats safe for puppies and kittens?', 'Most of our range is suitable from weaning age. Each product page lists the recommended age, and our minis are designed with tiny mouths in mind.'],
                ['Do you cater for allergies?', 'Yes. Several recipes are grain-free and single-protein. Look out for the badge on each product page.'],
            ],
            'Orders & shipping' => [
                ['How fast do you ship?', 'Orders placed before 3pm usually leave the kitchen the same day. Most customers get them within 2–4 working days.'],
                ['Do you ship internationally?', 'We ship across the EU. Other regions: get in touch and we will work something out.'],
                ['Can I track my order?', 'Yes — once your order ships, you will get a tracking link by email.'],
            ],
            'Returns & support' => [
                ['What if my pet does not like a treat?', 'Hard to believe — but if it happens, drop us a line within 14 days and we will sort it out.'],
                ['How do I store the treats?', 'A cool, dry place, sealed up. They keep their crunch and flavour that way.'],
            ],
        ];

        $gi = 0;
        foreach ($groups as $name => $faqs) {
            $group = FaqGroup::updateOrCreate(['name' => $name], ['sort_order' => $gi++, 'is_active' => true]);
            foreach ($faqs as $j => [$q, $a]) {
                Faq::updateOrCreate(['question' => $q], [
                    'answer' => $a,
                    'group_id' => $group->id,
                    'sort_order' => $j,
                    'is_active' => true,
                    'show_on_homepage' => $j === 0,
                ]);
            }
        }
    }

    private function seedBlog(): void
    {
        $cats = ['Behind the kitchen', 'Pet wellbeing', 'Recipes & ideas'];
        $catModels = collect();
        foreach ($cats as $i => $name) {
            $catModels->push(BlogCategory::updateOrCreate(['name' => $name], ['sort_order' => $i, 'is_active' => true]));
        }

        $posts = [
            ['What "small batch" actually means', 'Behind the kitchen', 'Everyone says small batch. Almost nobody means it. Here is how we work — and why it matters for what ends up in the bowl.'],
            ['Five signs your dog is bored (and how to fix it)', 'Pet wellbeing', 'Restless evenings, half-eaten toys, that look. Boredom shows up in dozens of small ways — here is how we read the signs and what we do about it.'],
            ['DIY treat night: bowl-toppers on a budget', 'Recipes & ideas', 'When the cupboard is bare and the dog is judging, here is the fast, cheap, surprisingly impressive fix.'],
        ];

        foreach ($posts as $i => [$title, $cat, $excerpt]) {
            Post::updateOrCreate(['title' => $title], [
                'category_id' => $catModels->firstWhere('name', $cat)?->id,
                'excerpt' => $excerpt,
                'body' => $excerpt."\n\nMore stories like this drop every few weeks. Stick around — we have things to say and a lot of opinions about kibble.\n\nFollow the kitchen on socials and you will hear about new batches first.",
                'is_published' => true,
                'published_at' => now()->subDays($i * 7),
                'author' => 'The kitchen',
                'tags' => ['kitchen', 'wellbeing', 'treats'],
            ]);
        }
    }

    private function seedStores(): void
    {
        $stores = [
            ['Bark & Bone Athens', 'Athens', '10434', 'Plateia Kotzia 4', '+30 210 111 2222'],
            ['The Loud Bowl', 'Thessaloniki', '54622', 'Tsimiski 88', '+30 2310 333 444'],
            ['Whisker & Wag', 'Patras', '26221', 'Riga Feraiou 12', '+30 2610 555 666'],
            ['Pawhouse Crete', 'Heraklion', '71202', '1821 St. 5', '+30 2810 777 888'],
        ];

        foreach ($stores as $i => [$name, $city, $postcode, $address, $phone]) {
            Store::updateOrCreate(['name' => $name], [
                'city' => $city,
                'postcode' => $postcode,
                'address' => $address,
                'phone' => $phone,
                'opening_hours' => "Mon–Fri: 09:00 – 20:00\nSat: 10:00 – 18:00\nSun: closed",
                'map_link' => 'https://maps.google.com/?q='.urlencode($address.' '.$city),
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }
    }

    private function seedTestimonials(): void
    {
        $items = [
            ['Eleni K.', 'Pixel', 'Pixel demolished a whole bag in two days. We had to hide them. Five out of five paws.'],
            ['Yiannis M.', 'Boomer', 'Switched from the bland stuff. Boomer is louder, happier and frankly insufferable.'],
            ['Maria T.', 'Athena', 'The chewy ones kept Athena occupied for an entire dinner. Worth every cent.'],
        ];

        foreach ($items as $i => [$author, $pet, $quote]) {
            Testimonial::updateOrCreate(['author' => $author], [
                'pet_name' => $pet,
                'quote' => $quote,
                'rating' => 5,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }
    }
}
