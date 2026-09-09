<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Setting;

class ContestController extends Controller
{
    public function index()
    {
        $this->abortUnlessPageIsPublic();

        $open = Contest::open()->orderBy('ends_at')->get();
        $upcoming = Contest::upcoming()->orderBy('starts_at')->get();
        $past = Contest::finished()->orderByDesc('ends_at')->limit(12)->get();

        return view('site.contests.index', compact('open', 'upcoming', 'past'));
    }

    public function show(Contest $contest)
    {
        $this->abortUnlessVisible($contest);

        return view('site.contests.show', compact('contest'));
    }

    /** Winner announcement — public only once the draw has run. */
    public function winner(Contest $contest)
    {
        $this->abortUnlessVisible($contest);
        abort_unless($contest->isDrawn(), 404);

        $contest->load('awarded');

        return view('site.contests.winner', compact('contest'));
    }

    private function abortUnlessVisible(Contest $contest): void
    {
        $this->abortUnlessPageIsPublic();

        // Admins can preview a draft contest; everyone else gets a 404.
        abort_unless($contest->is_published || auth()->user()?->isAdmin(), 404);
    }

    private function abortUnlessPageIsPublic(): void
    {
        abort_unless(
            Setting::get('contests_page_status', 'draft') === 'public' || auth()->user()?->isAdmin(),
            404
        );
    }
}
