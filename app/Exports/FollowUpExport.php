<?php

namespace App\Exports;

use App\Models\FollowUp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class FollowUpExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return FollowUp::with('patient')->get(); // Eager load the patient relationship
        // return FollowUp::all();  // Fetch all follow-ups
    }

    public function headings(): array
    {
        return ["Date", "Patient Name","Patient ID", "Amount Billed", "Amount Paid", "Branch Name"];
    }

    public function map($followUp): array
    {

        $checkUpInfo = json_decode($followUp->check_up_info, true);
        $branchName = $checkUpInfo['branch_name'] ?? 'N/A'; // Default to 'N/A' if not found

         // Get patient_id from the patient relationship
         $patientId = $followUp->patient ? $followUp->patient->patient_id : 'N/A';

        return [
            optional($followUp->created_at)->format('d M Y, h:i A'),
            optional($followUp->patient)->name ?? 'N/A',
            $patientId,
            $followUp->amount_billed,
            $followUp->amount_paid,
            $branchName,
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
