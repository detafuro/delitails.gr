<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportContentTranslations extends Command
{
    protected $signature = 'translations:import-content
        {file=database/data/content-translations.el.json : JSON file of model => id => field => value}
        {--locale=el}
        {--force : Overwrite translations that already exist}';

    protected $description = 'Seed content_translations from a JSON file (skips rows that already exist unless --force)';

    public function handle(): int
    {
        $path = base_path($this->argument('file'));
        if (! is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        if (! is_array($data)) {
            $this->error('Invalid JSON.');
            return self::FAILURE;
        }

        $locale = $this->option('locale');
        $written = $skipped = $missing = 0;

        foreach ($data as $type => $rows) {
            foreach ($rows as $id => $fields) {
                if (! $type::find($id)) {
                    $missing += count($fields);
                    $this->warn("Missing {$type} #{$id} — skipped.");
                    continue;
                }
                foreach ($fields as $field => $value) {
                    $keys = [
                        'translatable_type' => $type,
                        'translatable_id' => (int) $id,
                        'locale' => $locale,
                        'field' => $field,
                    ];
                    $exists = DB::table('content_translations')->where($keys)->exists();
                    if ($exists && ! $this->option('force')) {
                        $skipped++;
                        continue;
                    }
                    DB::table('content_translations')->updateOrInsert($keys, [
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $written++;
                }
            }
        }

        $this->info("Done: {$written} written, {$skipped} already present (kept), {$missing} for missing records.");
        return self::SUCCESS;
    }
}
