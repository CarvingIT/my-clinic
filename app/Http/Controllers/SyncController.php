<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class SyncController extends Controller
{
    /**
     * Show the sync data form (for admin dashboard)
     */
    public function showSyncForm(): View
    {
        return view('admin.sync-data');
    }

    /**
     * Sync data from online API
     */
    public function syncData(Request $request): RedirectResponse
    {
        $request->validate([
            'sync_date' => 'nullable|date|before_or_equal:today',
            'api_username' => 'required|string|max:255',
            'api_password' => 'required|string|max:255',
            'sync_all' => 'nullable|boolean',
        ]);

        $date = $request->input('sync_date');
        $username = $request->input('api_username');
        $password = $request->input('api_password');
        $syncAll = $request->boolean('sync_all', false);
        $syncAll = $request->boolean('sync_all', false);

        try {
            // Call sync service directly to get detailed stats
            $syncService = app(\App\Services\SyncService::class);
            $result = $syncService->syncFromApi($date, $username, $password, $syncAll);

            if (isset($result['stats'])) {
                $stats = $result['stats'];
                $message = $result['message'] ?? 'Data synced successfully from online server.';

                return redirect()->back()->with('success', $message)->with('sync_stats', $stats);
            } else {
                return redirect()->back()->with('error', 'Sync failed: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for manual sync (optional)
     */
    public function apiSyncData(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        $date = $request->input('date');

        try {
            $exitCode = Artisan::call('MC:SyncData', [
                '--date' => $date
            ]);

            if ($exitCode === 0) {
                // Get the command output and parse stats
                $output = Artisan::output();
                $stats = $this->parseSyncStats($output);

                $message = 'Data synced successfully from online server.';

                return response()->json(['message' => $message, 'stats' => $stats['stats']]);
            } else {
                $output = Artisan::output();
                return response()->json(['error' => trim($output)], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Parse sync statistics from command output
     */
    private function parseSyncStats(string $output): array
    {
        $stats = [];
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^Patients restored:\s*(\d+)$/', $line, $matches)) {
                $stats['patients_restored'] = (int) $matches[1];
            } elseif (preg_match('/^Patients imported:\s*(\d+)$/', $line, $matches)) {
                $stats['patients_imported'] = (int) $matches[1];
            } elseif (preg_match('/^Patients updated:\s*(\d+)$/', $line, $matches)) {
                $stats['patients_updated'] = (int) $matches[1];
            } elseif (preg_match('/^Patients skipped:\s*(\d+)$/', $line, $matches)) {
                $stats['patients_skipped'] = (int) $matches[1];
            } elseif (preg_match('/^Follow-ups added:\s*(\d+)$/', $line, $matches)) {
                $stats['follow_ups_added'] = (int) $matches[1];
            } elseif (preg_match('/^Follow-ups updated:\s*(\d+)$/', $line, $matches)) {
                $stats['follow_ups_updated'] = (int) $matches[1];
            } elseif (preg_match('/^Follow-ups skipped:\s*(\d+)$/', $line, $matches)) {
                $stats['follow_ups_skipped'] = (int) $matches[1];
            }
        }

        // Build summary message
        $summaryParts = [];

        $totalPatients = ($stats['patients_restored'] ?? 0) + ($stats['patients_imported'] ?? 0) + ($stats['patients_updated'] ?? 0);
        if ($totalPatients > 0) {
            $summaryParts[] = "{$totalPatients} patients processed";
            if (isset($stats['patients_imported']) && $stats['patients_imported'] > 0) {
                $summaryParts[] = "{$stats['patients_imported']} new";
            }
            if (isset($stats['patients_updated']) && $stats['patients_updated'] > 0) {
                $summaryParts[] = "{$stats['patients_updated']} updated";
            }
            if (isset($stats['patients_restored']) && $stats['patients_restored'] > 0) {
                $summaryParts[] = "{$stats['patients_restored']} restored";
            }
        }

        $totalFollowUps = ($stats['follow_ups_added'] ?? 0) + ($stats['follow_ups_updated'] ?? 0);
        if ($totalFollowUps > 0) {
            $summaryParts[] = "{$totalFollowUps} follow-ups";
            if (isset($stats['follow_ups_added']) && $stats['follow_ups_added'] > 0) {
                $summaryParts[] = "{$stats['follow_ups_added']} new";
            }
            if (isset($stats['follow_ups_updated']) && $stats['follow_ups_updated'] > 0) {
                $summaryParts[] = "{$stats['follow_ups_updated']} updated";
            }
        }

        return [
            'stats' => $stats,
            'summary' => implode(', ', $summaryParts)
        ];
    }
}
