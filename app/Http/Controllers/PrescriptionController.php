<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PDF;

class PrescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Generate a prescription PDF for a specific follow-up.
     *
     * @param  FollowUp  $followup
     * @return \Illuminate\Http\Response
     */
    public function generate(FollowUp $followup)
    {
        $patient = $followup->patient;
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Get the prescription template from database
        $template = Template::findBySlug('prescription');

        if (!$template || !$template->is_active) {
            // Fallback: render from a Blade view if template is missing/inactive
            return $this->generateFromBlade($followup, $patient, $checkUpInfo);
        }

        // Build placeholder data from the follow-up and patient
        $data = $this->buildPlaceholderData($followup, $patient, $checkUpInfo);

        // Render the template with data
        $htmlContent = $template->render($data);

        // Generate PDF
        $pdf = PDF::loadHTML($htmlContent);
        $pdf->setOption('page-size', 'A5');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('enable-local-file-access', true);

        $filename = 'prescription_' . str_replace(' ', '_', $patient->name) . '_' . $followup->created_at->format('Y-m-d') . '.pdf';

        return $pdf->inline($filename);
    }

    /**
     * Show a print-ready HTML view of the prescription (for direct browser printing).
     *
     * @param  FollowUp  $followup
     * @return \Illuminate\Contracts\View\View
     */
    public function printView(FollowUp $followup)
    {
        $patient = $followup->patient;
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];
        $data = $this->buildPlaceholderData($followup, $patient, $checkUpInfo);

        $template = Template::findBySlug('prescription');

        if ($template && $template->is_active) {
            $htmlContent = $template->render($data);

            // Inject print toolbar and auto-print script into template HTML
            $pdfUrl = route('followups.prescription', $followup->id);
            $printToolbar = <<<TOOLBAR
<div class="print-toolbar" style="text-align:center;padding:12px;background:#fff;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:100;">
    <button onclick="window.print();" style="padding:8px 24px;font-size:14px;font-weight:600;border:none;border-radius:6px;cursor:pointer;margin:0 6px;background:#2c5282;color:#fff;">🖨️ Print Prescription</button>
    <button onclick="window.location.href='{$pdfUrl}'" style="padding:8px 24px;font-size:14px;font-weight:600;border:none;border-radius:6px;cursor:pointer;margin:0 6px;background:#38a169;color:#fff;">📥 Download PDF</button>
    <button onclick="window.close();" style="padding:8px 24px;font-size:14px;font-weight:600;border:none;border-radius:6px;cursor:pointer;margin:0 6px;background:#e53e3e;color:#fff;">✕ Close</button>
</div>
<style>@media print { .print-toolbar { display: none !important; } body { background: #fff; } }</style>
TOOLBAR;

            $autoprint = '<script>window.addEventListener("load",function(){setTimeout(function(){window.print();},500);});</script>';

            // Insert toolbar after <body> tag and script before </body>
            $htmlContent = str_replace('<body>', '<body>' . $printToolbar, $htmlContent);
            $htmlContent = str_replace('</body>', $autoprint . '</body>', $htmlContent);

            return response($htmlContent)
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        // Fallback to Blade view
        $data['followup_id'] = $followup->id;

        return view('prescriptions.print', $data);
    }

    /**
     * Build the placeholder data array from patient & follow-up.
     */
    private function buildPlaceholderData(FollowUp $followup, Patient $patient, array $checkUpInfo): array
    {
        // Calculate patient age
        $patientAge = $patient->birthdate
            ? floor(abs(now()->diffInYears($patient->birthdate)))
            : 'N/A';

        // Build nadi text from check_up_info fields
        $nadiParts = [];
        $nadiKeys = ['वात', 'पित्त', 'कफ', 'सूक्ष्म'];
        foreach ($nadiKeys as $key) {
            if (!empty($checkUpInfo[$key])) {
                $nadiParts[] = "<strong>{$key}:</strong> " . (is_array($checkUpInfo[$key]) ? implode(', ', $checkUpInfo[$key]) : $checkUpInfo[$key]);
            }
        }

        // Also include nadi text field if present
        if (!empty($checkUpInfo['nadi'])) {
            $nadiParts[] = $checkUpInfo['nadi'];
        }

        $nadiText = !empty($nadiParts) ? implode(' &nbsp;|&nbsp; ', $nadiParts) : '—';

        // Lakshane / diagnosis
        $lakshane = $followup->diagnosis ?? '—';

        // Nidan
        $nidan = $checkUpInfo['nidan'] ?? '—';

        // Chikitsa
        $chikitsa = $checkUpInfo['chikitsa'] ?? '—';

        // Days & packets
        $days = $checkUpInfo['days'] ?? '—';
        $packets = $checkUpInfo['packets'] ?? '—';

        // Vishesh
        $vishesh = $patient->vishesh ?? '—';

        // Doctor name
        $doctorName = $checkUpInfo['user_name'] ?? ($followup->doctor ? $followup->doctor->name : 'Doctor');

        // Branch name
        $branchName = $checkUpInfo['branch_name'] ?? 'Clinic';

        // Payment info
        $amountBilled = number_format($followup->amount_billed ?? 0, 2);
        $amountPaid = number_format($followup->amount_paid ?? 0, 2);
        $amountDue = number_format($followup->total_due ?? 0, 2);

        return [
            'patient_name'   => $patient->name ?? '—',
            'patient_age'    => $patientAge,
            'patient_gender' => $patient->gender ?? '—',
            'patient_id'     => $patient->patient_id ?? $patient->id,
            'patient_mobile' => $patient->mobile_phone ?? '—',
            'patient_address' => $patient->address ?? '—',
            'patient_weight' => $patient->weight ? $patient->weight . ' kg' : '—',
            'patient_height' => $patient->height ? $patient->height . ' cm' : '—',
            'current_date'   => now()->format('d/m/Y'),
            'followup_date'  => $followup->created_at->format('d M Y, h:i A'),
            'branch_name'    => $branchName,
            'doctor_name'    => $doctorName,
            'nadi'           => $nadiText,
            'nidan'          => $nidan,
            'lakshane'       => $lakshane,
            'chikitsa'       => $chikitsa,
            'days'           => $days,
            'packets'        => $packets,
            'vishesh'        => strip_tags($vishesh),
            'amount_billed'  => $amountBilled,
            'amount_paid'    => $amountPaid,
            'amount_due'     => $amountDue,
        ];
    }

    /**
     * Fallback: generate prescription from Blade view.
     */
    private function generateFromBlade(FollowUp $followup, Patient $patient, array $checkUpInfo)
    {
        $data = $this->buildPlaceholderData($followup, $patient, $checkUpInfo);

        $pdf = PDF::loadView('prescriptions.pdf', $data);
        $pdf->setOption('page-size', 'A5');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('encoding', 'UTF-8');

        $filename = 'prescription_' . str_replace(' ', '_', $patient->name) . '_' . $followup->created_at->format('Y-m-d') . '.pdf';

        return $pdf->inline($filename);
    }
}
