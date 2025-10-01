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
            'sync_date' => 'required|date|before_or_equal:today',
            'api_username' => 'required|string|max:255',
            'api_password' => 'required|string|max:255',
        ]);

        $date = $request->input('sync_date');
        $username = $request->input('api_username');
        $password = $request->input('api_password');

        try {
            // Run the sync command with credentials
            $exitCode = Artisan::call('MC:SyncData', [
                '--date' => $date,
                '--username' => $username,
                '--password' => $password,
            ]);

            if ($exitCode === 0) {
                // Get the command output
                $output = Artisan::output();
                return redirect()->back()->with('success', 'Data synced successfully from online server.');
            } else {
                $output = Artisan::output();
                return redirect()->back()->with('error', 'Sync failed: ' . trim($output));
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
                return response()->json(['message' => 'Data synced successfully from online server.']);
            } else {
                $output = Artisan::output();
                return response()->json(['error' => trim($output)], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
