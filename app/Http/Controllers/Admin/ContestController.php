<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreContestRequest;
use App\Models\Contest;
use App\Support\Dates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContestController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contest::class);

        $query = Contest::query()->withCount('entries');

        if ($search = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$search}%");
        }

        $contests = $query->orderByDesc('starts_at')->paginate(15)->withQueryString();

        return view('admin.contests.index', compact('contests'));
    }

    public function create()
    {
        $this->authorize('create', Contest::class);

        $contest = new Contest([
            'starts_at' => now()->startOfHour()->addHour(),
            'ends_at' => now()->addWeek()->startOfHour(),
            'winners_count' => 1,
            'runners_up_count' => 2,
            'phone_field' => Contest::PHONE_OPTIONAL,
            'auto_draw' => true,
            'newsletter_opt_in' => true,
        ]);

        return view('admin.contests.create', compact('contest'));
    }

    public function store(StoreContestRequest $request)
    {
        $contest = Contest::create($this->payload($request));
        $contest->saveTranslations($request->input('el'));

        return redirect()->route('admin.contests.edit', $contest)->with('success', 'Contest created.');
    }

    public function edit(Contest $contest)
    {
        $this->authorize('update', $contest);

        $contest->loadCount('entries')->load(['draws.performer', 'awarded']);

        return view('admin.contests.edit', compact('contest'));
    }

    public function update(StoreContestRequest $request, Contest $contest)
    {
        $this->authorize('update', $contest);

        $contest->update($this->payload($request, $contest));
        $contest->saveTranslations($request->input('el'));

        return redirect()->route('admin.contests.edit', $contest)->with('success', 'Contest updated.');
    }

    public function destroy(Contest $contest)
    {
        $this->authorize('delete', $contest);

        if ($contest->banner_image) {
            Storage::disk('public')->delete($contest->banner_image);
        }
        $contest->delete();

        return redirect()->route('admin.contests.index')->with('success', 'Contest deleted.');
    }

    /** Validated input turned into column values (image upload + field repeater). */
    private function payload(StoreContestRequest $request, ?Contest $contest = null): array
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['auto_draw'] = $request->boolean('auto_draw');
        $data['newsletter_opt_in'] = $request->boolean('newsletter_opt_in');

        // The admin types Athens wall-clock time; the column holds UTC.
        $data['starts_at'] = Dates::fromLocal($data['starts_at']);
        $data['ends_at'] = Dates::fromLocal($data['ends_at']);

        if ($request->hasFile('banner_image')) {
            if ($contest?->banner_image) {
                Storage::disk('public')->delete($contest->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('contests', 'public');
        } elseif ($request->boolean('__remove_banner_image')) {
            if ($contest?->banner_image) {
                Storage::disk('public')->delete($contest->banner_image);
            }
            $data['banner_image'] = null;
        } else {
            unset($data['banner_image']);
        }

        $data['extra_fields'] = $this->fields($request);
        unset($data['fields'], $data['el']);

        return $data;
    }

    /** @return array<int, array<string, mixed>> */
    private function fields(StoreContestRequest $request): array
    {
        return collect($request->input('fields', []))
            ->filter(fn ($row) => trim((string) ($row['label'] ?? '')) !== '')
            ->values()
            ->map(function ($row, $i) {
                $label = trim((string) $row['label']);

                return [
                    'key' => Str::slug((string) ($row['key'] ?? '') ?: $label, '_') ?: 'field_'.($i + 1),
                    'label' => $label,
                    'label_el' => trim((string) ($row['label_el'] ?? '')),
                    'type' => array_key_exists($row['type'] ?? '', Contest::FIELD_TYPES) ? $row['type'] : 'text',
                    'options' => trim((string) ($row['options'] ?? '')),
                    'required' => (bool) ($row['required'] ?? false),
                ];
            })
            ->all();
    }
}
