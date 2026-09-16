<?php

namespace App\Exports;

use App\Models\FollowUp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;


class FollowUpExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{

    public $req;

    public function __construct($req)
    {
        $this->req = $req;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $follow_ups = FollowUp::whereNotNull('patient_id')->with(['patient', 'doctor']);

        // Applying time_period filter (overrides from_date and to_date)
        if ($this->req->filled('time_period') && $this->req->time_period != 'all') {
            switch ($this->req->time_period) {
                case 'today':
                    $follow_ups->whereDate('created_at', Carbon::today());
                    break;
                case 'last_week':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $follow_ups->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            // Apply date filters if time_period is not set or is "all"
            if ($this->req->filled('from_date')) {
                $follow_ups->where('created_at', '>=', Carbon::parse($this->req->input('from_date'))->startOfDay());
            }
            if ($this->req->filled('to_date')) {
                $follow_ups->where('created_at', '<=', Carbon::parse($this->req->input('to_date'))->endOfDay());
            }
        }
        if ($this->req->input('branch_name') != 'all' && $this->req->filled('branch_name')) {
            $selectedBranch = $this->req->input('branch_name');
            $follow_ups = $follow_ups->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }
        if ($this->req->input('doctor') != 'all' && $this->req->filled('doctor')) {
            $selectedDoctor = $this->req->input('doctor');
            $follow_ups = $follow_ups->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
        }

        $fuItems = $follow_ups->get()->map(function ($fu) {
            $checkUpInfo = json_decode($fu->check_up_info, true) ?? [];
            return (object) [
                'type' => 'followup',
                'date' => $fu->created_at,
                'patient' => $fu->patient,
                'doctor_name' => $checkUpInfo['user_name'] ?? optional($fu->doctor)->name ?? 'N/A',
                'amount_billed' => (float) ($fu->amount_billed ?? 0),
                'payment_method' => $fu->payment_method,
                'amount_paid' => (float) $fu->amount_paid,
                'branch_name' => $checkUpInfo['branch_name'] ?? 'N/A',
            ];
        });

        // Standalone Payments
        $payQuery = \App\Models\Payment::with(['patient', 'receiver'])
            ->whereNull('follow_up_id')
            ->where('status', 'posted')
            ->whereHas('patient');

        if ($this->req->input('branch_name') != 'all' && $this->req->filled('branch_name')) {
            $payQuery->where('branch_name', $this->req->input('branch_name'));
        }
        if ($this->req->input('doctor') != 'all' && $this->req->filled('doctor')) {
            $selectedDoctor = $this->req->input('doctor');
            $payQuery->where(function ($subQ) use ($selectedDoctor) {
                $subQ->whereHas('receiver', function ($sq) use ($selectedDoctor) {
                    $sq->where('name', $selectedDoctor);
                });
            });
        }

        if ($this->req->filled('time_period') && $this->req->time_period != 'all') {
            switch ($this->req->time_period) {
                case 'today':
                    $payQuery->whereDate('paid_at', Carbon::today());
                    break;
                case 'last_week':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                        Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            if ($this->req->filled('from_date')) {
                $payQuery->whereDate('paid_at', '>=', Carbon::parse($this->req->input('from_date'))->startOfDay());
            }
            if ($this->req->filled('to_date')) {
                $payQuery->whereDate('paid_at', '<=', Carbon::parse($this->req->input('to_date'))->endOfDay());
            }
        }

        $payItems = $payQuery->get()->map(function ($p) {
            return (object) [
                'type' => 'payment',
                'date' => $p->paid_at ?? $p->created_at,
                'patient' => $p->patient,
                'doctor_name' => optional($p->receiver)->name ?? 'Standalone Payment',
                'amount_billed' => 0.0,
                'payment_method' => ucfirst($p->payment_method),
                'amount_paid' => (float) $p->amount,
                'branch_name' => $p->branch_name ?? 'N/A',
            ];
        });

        return $fuItems->concat($payItems)->sortByDesc('date')->values();
    }

    public function headings(): array
    {
        return ["Date", "Patient Name", "Patient ID", "Doctor", "Amount Billed", "Payment Method", "Amount Paid", "Branch Name"];
    }

    public function map($item): array
    {
        $patientId = $item->patient ? $item->patient->patient_id : 'N/A';

        return [
            optional($item->date)->format('d M Y, h:i A'),
            optional($item->patient)->name ?? 'N/A',
            $patientId,
            $item->doctor_name ?? 'N/A',
            number_format($item->amount_billed, 2),
            $item->payment_method ?? 'N/A',
            number_format($item->amount_paid, 2),
            $item->branch_name ?? 'N/A',
        ];
    }

    // Force UTF-8 encoding with BOM
    public function getCsvSettings(): array
    {
        return [
            'output_encoding' => 'UTF-8',
            'use_bom' => true, // Important for Marathi text
        ];
    }
}
