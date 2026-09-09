<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\User;
use App\Support\Dates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The admin screens render and the CRUD round-trips (including the field builder). */
class ContestAdminScreensTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_create_screen_and_store(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.contests.create'))->assertOk()->assertSee('Entry form');

        $response = $this->actingAs($admin)->post(route('admin.contests.store'), [
            'title' => 'Summer giveaway',
            'slug' => '',
            'prize' => 'A treat box',
            'starts_at' => '2026-09-10T10:00',
            'ends_at' => '2026-09-20T20:00',
            'is_published' => '1',
            'auto_draw' => '1',
            'winners_count' => 1,
            'runners_up_count' => 2,
            'phone_field' => Contest::PHONE_REQUIRED,
            'newsletter_opt_in' => '1',
            'fields' => [
                ['label' => 'Dog name', 'label_el' => 'Όνομα σκύλου', 'key' => '', 'type' => 'text', 'options' => '', 'required' => '1'],
                ['label' => '', 'label_el' => '', 'key' => '', 'type' => 'text', 'options' => '', 'required' => '0'],
            ],
            'el' => ['title' => 'Καλοκαιρινός διαγωνισμός'],
        ]);

        $contest = Contest::firstWhere('slug', 'summer-giveaway');
        $response->assertRedirect(route('admin.contests.edit', $contest));

        $this->assertTrue($contest->is_published);
        $this->assertSame('Καλοκαιρινός διαγωνισμός', $contest->translation('title'));

        // Blank rows dropped, key auto-derived from the label.
        $this->assertCount(1, $contest->extra_fields);
        $this->assertSame('dog_name', $contest->extra_fields[0]['key']);

        // Typed as Athens wall-clock, stored as UTC (+3 in September).
        $this->assertSame('2026-09-10 07:00:00', $contest->starts_at->toDateTimeString());
        $this->assertSame('2026-09-10T10:00', Dates::local($contest->starts_at)->format('Y-m-d\TH:i'));
    }

    public function test_closing_date_must_follow_the_opening_date(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.contests.store'), [
                'title' => 'Bad dates',
                'starts_at' => '2026-09-20T10:00',
                'ends_at' => '2026-09-10T10:00',
                'winners_count' => 1,
                'runners_up_count' => 2,
                'phone_field' => Contest::PHONE_OPTIONAL,
            ])
            ->assertSessionHasErrors('ends_at');
    }

    public function test_deleting_a_contest_removes_its_entries(): void
    {
        $admin = $this->admin();
        $contest = Contest::create([
            'title' => 'Doomed', 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(),
            'winners_count' => 1, 'runners_up_count' => 2, 'phone_field' => Contest::PHONE_OPTIONAL,
        ]);
        $contest->entries()->create(['name' => 'Someone', 'email' => 'someone@example.com']);

        $this->actingAs($admin)->delete(route('admin.contests.destroy', $contest))->assertRedirect();

        $this->assertDatabaseCount('contests', 0);
        $this->assertDatabaseCount('contest_entries', 0);
    }
}
