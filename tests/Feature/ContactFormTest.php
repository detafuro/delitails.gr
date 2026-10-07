<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ελένη Παπαδοπούλου',
            'email' => 'eleni@example.gr',
            'message' => 'Θα ήθελα τιμοκατάλογο.',
        ], $overrides);
    }

    public function test_a_visitor_can_send_a_message_and_the_owner_is_emailed(): void
    {
        Mail::fake();
        Setting::set('contact_email', 'info@delitails.gr');

        $this->post(route('contact.send', ['locale' => 'el']), $this->payload())
            ->assertRedirect(route('contact', ['locale' => 'el']))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('contact_messages', 1);
        Mail::assertSent(ContactMessageReceived::class);
    }

    public function test_repeat_clicks_do_not_file_the_same_message_again(): void
    {
        Mail::fake();

        foreach (range(1, 4) as $i) {
            $this->post(route('contact.send', ['locale' => 'el']), $this->payload())->assertSessionHas('success');
        }

        $this->assertSame(1, ContactMessage::count());
        Mail::assertSent(ContactMessageReceived::class, 1);
    }

    public function test_a_different_message_from_the_same_person_still_goes_through(): void
    {
        Mail::fake();

        $this->post(route('contact.send', ['locale' => 'el']), $this->payload());
        $this->post(route('contact.send', ['locale' => 'el']), $this->payload(['message' => 'Μια δεύτερη, διαφορετική ερώτηση.']));

        $this->assertSame(2, ContactMessage::count());
    }

    public function test_the_honeypot_blocks_bots(): void
    {
        Mail::fake();

        $this->post(route('contact.send', ['locale' => 'el']), $this->payload(['hp_field' => 'http://spam.example']))
            ->assertSessionHasErrors('hp_field');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_turnstile_rejects_a_submission_without_a_token(): void
    {
        Mail::fake();
        Setting::set('turnstile_site_key', '1x00000000000000000000AA');
        Setting::set('turnstile_secret_key', '2x0000000000000000000000000000000AA'); // always-fails test key

        $this->post(route('contact.send', ['locale' => 'el']), $this->payload())
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_the_widget_only_renders_when_keys_are_configured(): void
    {
        $this->get(route('contact', ['locale' => 'el']))->assertOk()->assertDontSee('cf-turnstile');

        Setting::set('turnstile_site_key', '1x00000000000000000000AA');
        Setting::set('turnstile_secret_key', '1x0000000000000000000000000000000AA');

        $this->get(route('contact', ['locale' => 'el']))->assertOk()
            ->assertSee('cf-turnstile')
            ->assertSee('challenges.cloudflare.com/turnstile/v0/api.js');
    }
}
