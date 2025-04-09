<?php

namespace App\Exports;

use App\Models\FollowUp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class FollowUpExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{

    public $req;

    public function __construct($req){
        $this->req = $req;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $follow_ups = FollowUp::whereNotNull('patient_id');
        if(!empty($this->req->input('from_date'))){
            $follow_ups = $follow_ups->where('created_at','>=',$this->req->input('from_date'));
        }
        if(!empty($this->req->input('to_date'))){
            $follow_ups = $follow_ups->where('created_at','<=',$this->req->input('to_date'));
        }
        if($this->req->input('branch_name') != 'all'){
            $selectedBranch = $this->req->input('branch_name');
            $follow_ups = $follow_ups->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }
        if($this->req->input('doctor') != 'all'){
            $follow_ups = $follow_ups->where('doctor_id',$this->req->input('doctor'));
        }

        return $follow_ups->with('patient')->get(); // Eager load the patient relationship
    }

    public function headings(): array
    {
        return ["Date", "Patient Name", "Patient ID", "Doctor", "Amount Billed", "Amount Paid", "Branch Name"];
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
            $followUp->doctor->name,
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
