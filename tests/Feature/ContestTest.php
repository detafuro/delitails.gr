<?php

namespace Tests\Feature;

use App\Mail\ContestDrawCompleted;
use App\Mail\ContestWinnerSelected;
use App\Models\Contest;
use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use App\Models\User;
use App\Support\ContestDrawRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('contests_page_status', 'public');
    }

    private function contest(array $attributes = []): Contest
    {
        return Contest::create(array_merge([
            'title' => 'Win a month of treats',
            'prize' => 'A month of treats',
            'terms' => '<p>Greece only.</p>',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(3),
            'is_published' => true,
            'auto_draw' => true,
            'winners_count' => 1,
            'runners_up_count' => 2,
            'phone_field' => Contest::PHONE_OPTIONAL,
            'newsletter_opt_in' => true,
        ], $attributes));
    }

    private function entryPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Maria Papadopoulou',
            'email' => 'maria@example.com',
            'phone' => '+30 210 0000000',
            'accept_terms' => '1',
        ], $overrides);
    }

    public function test_landing_page_is_public_and_shows_the_form(): void
    {
        $contest = $this->contest();

        $this->get(route('contests.show', ['locale' => 'el', 'contest' => $contest->slug]))
            ->assertOk()
            ->assertSee('accept_terms', escape: false);
    }

    public function test_the_landing_page_is_standalone_without_site_chrome(): void
    {
        $contest = $this->contest();

        $html = $this->get(route('contests.show', ['locale' => 'el', 'contest' => $contest->slug]))
            ->assertOk()->getContent();

        // No site header, nav or footer links — it is a campaign page, not a site page.
        $this->assertStringNotContainsString('nav-btn', $html);
        $this->assertStringNotContainsString('marquee', $html);
        $this->assertStringNotContainsString(route('products.index'), $html);
        // …but still branded: the logo links home and the form is right there.
        $this->assertStringContainsString(route('home'), $html);
        $this->assertStringContainsString('accept_terms', $html);
    }

    public function test_pages_are_hidden_while_the_section_is_draft(): void
    {
        Setting::set('contests_page_status', 'draft');
        $contest = $this->contest();

        $this->get(route('contests.show', ['locale' => 'el', 'contest' => $contest->slug]))->assertNotFound();
        $this->get(route('contests.index', ['locale' => 'el']))->assertNotFound();
    }

    public function test_an_unpublished_contest_is_not_reachable(): void
    {
        $contest = $this->contest(['is_published' => false]);

        $this->get(route('contests.show', ['locale' => 'el', 'contest' => $contest->slug]))->assertNotFound();
    }

    public function test_a_visitor_can_enter_an_open_contest(): void
    {
        $contest = $this->contest();

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]), $this->entryPayload())
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('contest_entries', [
            'contest_id' => $contest->id,
            'email' => 'maria@example.com',
            'accepted_terms' => true,
            'marketing_consent' => false,
        ]);
    }

    public function test_the_same_email_cannot_enter_twice(): void
    {
        $contest = $this->contest();
        $payload = $this->entryPayload();

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]), $payload)->assertOk();
        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertSame(1, $contest->entries()->count());
    }

    public function test_entries_are_refused_outside_the_window(): void
    {
        $upcoming = $this->contest(['starts_at' => now()->addDay(), 'ends_at' => now()->addWeek()]);
        $closed = $this->contest(['title' => 'Old one', 'starts_at' => now()->subWeek(), 'ends_at' => now()->subDay()]);

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $upcoming->slug]), $this->entryPayload())
            ->assertStatus(422);
        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $closed->slug]), $this->entryPayload(['email' => 'other@example.com']))
            ->assertStatus(422);

        $this->assertSame(0, $upcoming->entries()->count());
        $this->assertSame(0, $closed->entries()->count());
    }

    public function test_the_honeypot_blocks_bots(): void
    {
        $contest = $this->contest();

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]),
            $this->entryPayload(['hp_field' => 'http://spam.example']))
            ->assertStatus(422);

        $this->assertSame(0, $contest->entries()->count());
    }

    public function test_terms_must_be_accepted(): void
    {
        $contest = $this->contest();

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]),
            $this->entryPayload(['accept_terms' => '0']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('accept_terms');
    }

    public function test_required_extra_fields_are_enforced_and_stored(): void
    {
        $contest = $this->contest(['extra_fields' => [
            ['key' => 'dog_name', 'label' => 'Dog name', 'label_el' => 'Όνομα σκύλου', 'type' => 'text', 'options' => '', 'required' => true],
        ]]);
        $url = route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]);

        $this->postJson($url, $this->entryPayload())->assertStatus(422)->assertJsonValidationErrors('extra.dog_name');

        $this->postJson($url, $this->entryPayload(['extra' => ['dog_name' => 'Rex']]))->assertOk();

        $this->assertSame('Rex', $contest->entries()->first()->extraValue('dog_name'));
    }

    public function test_marketing_consent_subscribes_the_entrant(): void
    {
        $contest = $this->contest();

        $this->postJson(route('contests.enter', ['locale' => 'el', 'contest' => $contest->slug]),
            $this->entryPayload(['marketing_consent' => '1']))->assertOk();

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'maria@example.com', 'is_active' => true]);
        $this->assertSame(1, NewsletterSubscriber::count());
    }

    public function test_the_draw_picks_a_winner_with_runners_up_and_logs_it(): void
    {
        Mail::fake();
        $contest = $this->contest();
        foreach (range(1, 10) as $i) {
            $contest->entries()->create(['name' => "Entrant {$i}", 'email' => "e{$i}@example.com"]);
        }

        $draw = ContestDrawRunner::run($contest);

        $contest->refresh();
        $this->assertNotNull($contest->drawn_at);
        $this->assertSame(Contest::STATE_COMPLETED, $contest->state);
        $this->assertSame(10, $draw->entries_count);
        $this->assertSame(1, $contest->winners()->count());
        $this->assertSame(2, $contest->runnersUp()->count());
        $this->assertCount(3, $draw->result);

        Mail::assertSent(ContestWinnerSelected::class);
        Mail::assertSent(ContestDrawCompleted::class);
    }

    public function test_the_winner_page_only_exists_after_the_draw(): void
    {
        Mail::fake();
        $contest = $this->contest();
        $contest->entries()->create(['name' => 'Maria Papadopoulou', 'email' => 'maria@example.com']);
        $url = route('contests.winner', ['locale' => 'el', 'contest' => $contest->slug]);

        $this->get($url)->assertNotFound();

        ContestDrawRunner::run($contest);

        // Masked for GDPR: first name, last initial — never the email.
        $this->get($url)->assertOk()->assertSee('Maria P.')->assertDontSee('maria@example.com');
    }

    public function test_auto_draw_command_only_draws_closed_contests(): void
    {
        Mail::fake();
        $running = $this->contest();
        $closed = $this->contest(['title' => 'Closed one', 'starts_at' => now()->subWeek(), 'ends_at' => now()->subMinute()]);
        $manual = $this->contest(['title' => 'Manual one', 'starts_at' => now()->subWeek(), 'ends_at' => now()->subMinute(), 'auto_draw' => false]);

        foreach ([$running, $closed, $manual] as $contest) {
            $contest->entries()->create(['name' => 'Someone Else', 'email' => 'someone'.$contest->id.'@example.com']);
        }

        $this->artisan('contests:draw-due')->assertSuccessful();

        $this->assertNull($running->fresh()->drawn_at);
        $this->assertNotNull($closed->fresh()->drawn_at);
        $this->assertNull($manual->fresh()->drawn_at);
    }

    public function test_admin_can_manage_contests_and_export_entries(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $contest = $this->contest();
        $contest->entries()->create(['name' => 'Maria Papadopoulou', 'email' => 'maria@example.com', 'marketing_consent' => true]);

        $this->actingAs($admin)->get(route('admin.contests.index'))->assertOk()->assertSee($contest->title);
        $this->actingAs($admin)->get(route('admin.contests.edit', $contest))->assertOk();
        $this->actingAs($admin)->get(route('admin.contests.entries.index', $contest))->assertOk()->assertSee('maria@example.com');

        $csv = $this->actingAs($admin)->get(route('admin.contests.entries.export', $contest));
        $csv->assertOk();
        $this->assertStringContainsString('maria@example.com', $csv->streamedContent());

        $this->actingAs($admin)->post(route('admin.contests.draw', $contest))->assertRedirect();
        $this->assertNotNull($contest->fresh()->drawn_at);
    }

    public function test_purging_removes_personal_data_but_keeps_the_counts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $contest = $this->contest();
        $contest->entries()->create(['name' => 'Maria Papadopoulou', 'email' => 'maria@example.com', 'phone' => '2100000000']);

        $this->actingAs($admin)->delete(route('admin.contests.entries.purge', $contest))->assertRedirect();

        $entry = $contest->entries()->first();
        $this->assertSame(1, $contest->entries()->count());
        $this->assertStringNotContainsString('maria@example.com', $entry->email);
        $this->assertNull($entry->phone);
        $this->assertNotNull($contest->fresh()->entries_purged_at);
    }

    public function test_guests_cannot_reach_the_admin_screens(): void
    {
        $contest = $this->contest();

        $this->get(route('admin.contests.index'))->assertRedirect(route('login'));
        $this->post(route('admin.contests.draw', $contest))->assertRedirect(route('login'));
    }
}
