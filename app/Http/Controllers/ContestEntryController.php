<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContestEntryRequest;
use App\Models\Contest;
use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use App\Support\Turnstile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContestEntryController extends Controller
{
    /**
     * Takes an entry. Answers JSON for the Alpine form (no reload) and falls
     * back to a redirect for browsers with JS off.
     */
    public function store(ContestEntryRequest $request, Contest $contest)
    {
        abort_unless(Setting::get('contests_page_status', 'draft') === 'public', 404);
        abort_unless($contest->is_published, 404);

        if (! $contest->isOpen()) {
            return $this->fail($request, 'contest', $contest->state === Contest::STATE_SCHEDULED
                ? __('This contest has not started yet.')
                : __('This contest has closed. No more entries.'));
        }

        if (! Turnstile::verify($request->input('cf-turnstile-response'), $request->ip())) {
            return $this->fail($request, 'cf-turnstile-response', __('Could not verify you are human. Please try again.'));
        }

        $data = $request->validated();

        $extra = [];
        foreach ($contest->fieldDefinitions() as $field) {
            $value = $data['extra'][$field['key']] ?? null;
            $extra[$field['key']] = $field['type'] === 'checkbox' ? (bool) $value : $value;
        }

        $entry = $contest->entries()->create([
            'name' => $data['name'],
            'email' => mb_strtolower($data['email']),
            'phone' => $contest->collectsPhone() ? ($data['phone'] ?? null) : null,
            'extra' => $extra ?: null,
            'accepted_terms' => true,
            'marketing_consent' => $request->boolean('marketing_consent'),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        // Separate, explicit consent — only then does the entrant join the list.
        if ($entry->marketing_consent) {
            NewsletterSubscriber::updateOrCreate(['email' => $entry->email], ['is_active' => true]);
        }

        $message = __('You are in! Good luck — we will email the winner when the draw happens.');

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return redirect()
            ->route('contests.show', ['contest' => $contest->slug])
            ->with('success', $message);
    }

    /** Error shaped like a validation failure, in whichever format the caller wants. */
    private function fail(Request $request, string $key, string $message): JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message, 'errors' => [$key => [$message]]], 422);
        }

        throw ValidationException::withMessages([$key => $message]);
    }
}
