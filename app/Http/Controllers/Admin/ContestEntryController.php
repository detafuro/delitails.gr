<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contest;
use App\Models\ContestEntry;
use App\Support\ContestDrawRunner;
use App\Support\Dates;
use App\Support\Xlsx;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContestEntryController extends AdminController
{
    public function index(Request $request, Contest $contest)
    {
        $this->authorize('view', $contest);

        $entries = $this->filtered($request, $contest)->paginate(25)->withQueryString();

        $stats = [
            'total' => $contest->entries()->count(),
            'marketing' => $contest->entries()->where('marketing_consent', true)->count(),
            'today' => $contest->entries()->where('created_at', '>=', now()->startOfDay())->count(),
        ];

        return view('admin.contests.entries', compact('contest', 'entries', 'stats'));
    }

    /**
     * Download the current filter as CSV (default) or Excel (.xlsx) — including
     * the consent columns, so a newsletter export can be filtered on them.
     */
    public function export(Request $request, Contest $contest): StreamedResponse|BinaryFileResponse
    {
        $this->authorize('view', $contest);

        $fields = $contest->fieldDefinitions();
        $query = $this->filtered($request, $contest);
        $stamp = now()->format('Ymd-His');

        if ($request->string('format')->toString() === 'xlsx') {
            return $this->exportXlsx($contest, $query, $fields, 'contest-'.$contest->slug.'-entries-'.$stamp.'.xlsx');
        }

        $filename = 'contest-'.$contest->slug.'-entries-'.$stamp.'.csv';

        return response()->streamDownload(function () use ($query, $fields, $contest) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads the Greek characters

            fputcsv($out, array_merge(
                ['Name', 'Email', 'Phone'],
                array_map(fn ($f) => $f['label'], $fields),
                ['Accepted terms', 'Newsletter consent', 'Result', 'Entered at'],
            ));

            $query->chunk(500, function ($rows) use ($out, $fields, $contest) {
                foreach ($rows as $entry) {
                    fputcsv($out, array_merge(
                        [$entry->name, $entry->email, $entry->phone],
                        array_map(fn ($f) => $entry->extraValue($f['key']) ?? '', $fields),
                        [
                            $entry->accepted_terms ? 'yes' : 'no',
                            $entry->marketing_consent ? 'yes' : 'no',
                            $entry->award_rank ? $contest->awardLabel($entry->award_rank, false) : '',
                            Dates::local($entry->created_at)?->format('Y-m-d H:i:s'),
                        ],
                    ));
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Same rows and columns as the CSV, as a real .xlsx workbook. */
    private function exportXlsx(Contest $contest, $query, array $fields, string $filename): BinaryFileResponse
    {
        $headings = array_merge(
            ['Name', 'Email', 'Phone'],
            array_map(fn ($f) => $f['label'], $fields),
            ['Accepted terms', 'Newsletter consent', 'Result', 'Entered at'],
        );

        $rows = (function () use ($query, $fields, $contest) {
            foreach ($query->lazy(500) as $entry) {
                yield array_merge(
                    [$entry->name, $entry->email, $entry->phone],
                    array_map(fn ($f) => $entry->extraValue($f['key']) ?? '', $fields),
                    [
                        $entry->accepted_terms ? 'yes' : 'no',
                        $entry->marketing_consent ? 'yes' : 'no',
                        $entry->award_rank ? $contest->awardLabel($entry->award_rank, false) : '',
                        Dates::local($entry->created_at)?->format('Y-m-d H:i:s'),
                    ],
                );
            }
        })();

        $path = Xlsx::write($headings, $rows, 'Entries');

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function destroy(Contest $contest, ContestEntry $entry)
    {
        $this->authorize('update', $contest);
        abort_unless($entry->contest_id === $contest->id, 404);

        $entry->delete();

        return back()->with('success', 'Entry deleted.');
    }

    /** GDPR clean-up: strip personal data from every entry once the contest is over. */
    public function purge(Contest $contest)
    {
        $this->authorize('update', $contest);

        $count = ContestDrawRunner::purgeEntrantData($contest);

        return redirect()->route('admin.contests.entries.index', $contest)
            ->with('success', "Personal data removed from {$count} entries.");
    }

    private function filtered(Request $request, Contest $contest)
    {
        $query = $contest->entries()->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        match ($request->string('filter')->toString()) {
            'marketing' => $query->where('marketing_consent', true),
            'winners' => $query->whereNotNull('award_rank'),
            default => null,
        };

        return $query;
    }
}
