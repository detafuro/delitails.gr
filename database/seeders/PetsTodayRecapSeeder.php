<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * The write-up of Pets Today 2026 (Athens, 2–4 October), with the photo
 * gallery from the booth. Idempotent: re-running updates the post in place.
 */
class PetsTodayRecapSeeder extends Seeder
{
    private const SLUG = 'pets-today-2026-recap';
    private const PHOTO_DIR = 'posts/pets-today-2026';

    /** Gallery order, with the alt text for each shot. */
    private const PHOTOS = [
        ['01.jpg', 'The Delitails team at booth A6'],
        ['02.jpg', 'Visitors around the Delitails booth'],
        ['03.jpg', 'The aisle outside booth A6 during the show'],
        ['04.jpg', 'Talking treats with pet shop owners'],
        ['05.jpg', 'A meeting at the booth table'],
        ['06.jpg', 'A Delitails team member by the shelves of chews'],
        ['07.jpg', 'Shelves stacked with natural chews'],
        ['08.jpg', 'Jars of treats laid out for visitors to open and smell'],
        ['09.jpg', 'A schnauzer up at the counter for a closer look'],
        ['10.jpg', 'A dog walking the shelves of the booth'],
        ['11.jpg', 'A poodle in a bandana taking a break by the booth'],
        ['12.jpg', 'The Delitails banner: dogs love it, tails show it'],
    ];

    public function run(): void
    {
        $post = Post::updateOrCreate(
            ['slug' => self::SLUG],
            [
                'title' => 'Pets Today 2026: three days, a lot of wagging',
                'excerpt' => 'We packed up booth A6 on Sunday night with an emptier van and a much longer list of friends.',
                'body' => $this->body('en'),
                'featured_image' => self::PHOTO_DIR.'/booth.jpg',
                'category_id' => BlogCategory::where('slug', 'news')->value('id'),
                'author' => 'Delitails Team',
                'tags' => ['pets today', 'expo'],
                'is_published' => true,
                'published_at' => now(),
                'seo_title' => 'Delitails at Pets Today 2026, Athens — how it went',
                'seo_description' => 'Three days at booth A6: the people we met, the dogs who judged us and everything we had on the shelves at Pets Today 2026 in Athens.',
            ]
        );

        $post->saveTranslations([
            'title' => 'Pets Today 2026: τρεις μέρες, πολλές ουρές',
            'excerpt' => 'Μαζέψαμε το περίπτερο A6 την Κυριακή το βράδυ με ένα φορτηγάκι πιο άδειο και μια λίστα φίλων πολύ πιο μεγάλη.',
            'body' => $this->body('el'),
            'seo_title' => 'Η Delitails στην Pets Today 2026, Αθήνα — πώς πήγε',
            'seo_description' => 'Τρεις μέρες στο περίπτερο A6: οι άνθρωποι που γνωρίσαμε, οι σκύλοι που μας έκριναν και όλα όσα είχαμε στα ράφια στην Pets Today 2026.',
        ]);

        $this->attachPhotos($post);
    }

    private function attachPhotos(Post $post): void
    {
        $disk = Storage::disk('public');

        foreach (self::PHOTOS as $i => [$file, $alt]) {
            $path = self::PHOTO_DIR.'/'.$file;
            if (! $disk->exists($path)) {
                continue;
            }

            PostImage::updateOrCreate(
                ['post_id' => $post->id, 'path' => $path],
                ['alt' => $alt, 'sort_order' => $i + 1]
            );
        }
    }

    private function body(string $locale): string
    {
        $boxes = $locale === 'el' ? '/el/display-boxes' : '/en/display-boxes';

        $paragraphs = $locale === 'el' ? [
            ['Τρεις μέρες στην Αθήνα', 'Από τις 2 έως τις 4 Οκτωβρίου βρεθήκαμε στην Pets Today 2026, στο περίπτερο A6. Τρεις μέρες γεμάτες κόσμο, κουβέντες και δείγματα που άνοιγαν και ξανάνοιγαν — και μια ομάδα που στο τέλος της Κυριακής είχε μείνει σχεδόν χωρίς φωνή, αλλά με χαμόγελο.'],
            ['Νέοι άνθρωποι, νέοι φίλοι', 'Γνωρίσαμε pet shops από όλη την Ελλάδα, συνεργάτες και πολλούς ανθρώπους που απλώς αγαπούν τον σκύλο τους. Ακούσαμε ερωτήσεις, προτάσεις και ιστορίες, και φύγαμε με μια λίστα νέων φίλων πολύ μεγαλύτερη από εκείνη που είχαμε όταν ήρθαμε.'],
            ['Οι πιο αυστηροί κριτές', 'Οι τετράποδοι επισκέπτες ήταν, φυσικά, οι πιο αυστηροί κριτές. Σταμάτησαν στα ράφια, μύρισαν, δοκίμασαν και έδωσαν την ετυμηγορία τους με τον μόνο τρόπο που ξέρουν: κουνώντας την ουρά τους. Καλύτερη κριτική δεν γίνεται.'],
            ['Τι δείξαμε', 'Στο περίπτερο είχαμε όλη τη σειρά μας: φυσικές λιχουδιές μίας πρωτεΐνης, λιχουδιές εκπαίδευσης και λουκάνικα, μαζί με τα <a href="'.$boxes.'">display boxes</a> που φτιάξαμε για τον πάγκο των pet shops. Τα βάζα ήταν εκεί για να ανοίγουν, να μυρίζουν και να δοκιμάζονται — γιατί έτσι καταλαβαίνει κανείς τη διαφορά.'],
            ['Τα λέμε την επόμενη φορά', 'Ευχαριστούμε όλους όσοι πέρασαν από το A6. Φύγαμε με ένα φορτηγάκι πιο άδειο απ’ ό,τι ήρθε και με πολλές ιδέες για τη συνέχεια. Τα λέμε την επόμενη φορά — και μέχρι τότε, οι ουρές να κουνιούνται.'],
        ] : [
            ['Three days in Athens', 'From 2 to 4 October we were at Pets Today 2026, booth A6. Three days of people, conversations and sample jars that kept being opened — and a team that by Sunday evening had almost no voice left, but plenty of smiles.'],
            ['New people, new friends', 'We met pet shops from all over Greece, partners, and plenty of people who simply love their dog. We heard questions, suggestions and stories, and we left with a list of new friends far longer than the one we arrived with.'],
            ['The toughest judges', 'The four-legged visitors were, of course, the toughest judges. They stopped at the shelves, sniffed, tasted and gave their verdict the only way they know: by wagging. There is no better review than that.'],
            ['What we had on the shelves', 'The whole range was there: single-protein natural chews, training treats and sausages, along with the <a href="'.$boxes.'">display boxes</a> we built for pet shop counters. The jars were there to be opened, smelled and tried — that is how you tell the difference.'],
            ['See you next time', 'Thank you to everyone who stopped by A6. We drove home with an emptier van than we came with and a lot of ideas for what comes next. See you next time — and until then, keep those tails wagging.'],
        ];

        return collect($paragraphs)
            ->map(fn ($p) => '<p><strong>'.$p[0].'</strong></p><p>'.$p[1].'</p>')
            ->implode('<p><br></p>');
    }
}
