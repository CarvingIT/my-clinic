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
     * Show the prescription builder where user can select fields to include.
     *
     * @param FollowUp $followup
     * @return \Illuminate\Contracts\View\View
     */
    public function builder(FollowUp $followup)
    {
        $patient = $followup->patient;
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Build all available field data
        $allFields = $this->buildAllFields($followup, $patient, $checkUpInfo);

        // Default selected fields (all except some optional ones)
        $defaultSelected = [
            'patient_name', 'patient_id', 'patient_age', 'patient_gender',
            'patient_mobile', 'patient_address', 'nadi', 'lakshane',
            'nidan', 'chikitsa', 'days', 'packets', 'amount_billed',
            'amount_paid', 'amount_due'
        ];

        return view('prescriptions.builder', [
            'followup' => $followup,
            'patient' => $patient,
            'allFields' => $allFields,
            'defaultSelected' => $defaultSelected,
        ]);
    }

    /**
     * Build prescription with selected fields and custom values.
     *
     * @param Request $request
     * @param FollowUp $followup
     * @return \Illuminate\Contracts\View\View
     */
    public function buildWithSelection(Request $request, FollowUp $followup)
    {
        $patient = $followup->patient;
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Get all available fields
        $allFields = $this->buildAllFields($followup, $patient, $checkUpInfo);

        // Get selected fields from request
        $selectedFields = $request->get('selected_fields', []);
        $customValues = $request->get('field_values', []);

        // Build prescription data with custom values overriding defaults
        $prescriptionData = [];
        foreach ($allFields as $key => $field) {
            if (in_array($key, $selectedFields)) {
                // Use custom value if provided, otherwise use default
                $prescriptionData[$key] = $customValues[$key] ?? $field['value'];
                $prescriptionData[$key . '_label'] = $field['label'];
            }
        }

        return view('prescriptions.generate', [
            'followup' => $followup,
            'patient' => $patient,
            'data' => $prescriptionData,
            'selectedFields' => $selectedFields,
        ]);
    }

    /**
     * Build all available fields from follow-up and patient data.
     */
    private function buildAllFields(FollowUp $followup, Patient $patient, array $checkUpInfo): array
    {
        // Calculate patient age
        $patientAge = $patient->birthdate
            ? floor(abs(now()->diffInYears($patient->birthdate)))
            : 'N/A';

        // Build nadi text from check_up_info fields (plain text only, stripped of HTML tags)
        $nadiParts = [];
        $nadiKeys = ['वात', 'पित्त', 'कफ', 'सूक्ष्म'];
        foreach ($nadiKeys as $key) {
            if (!empty($checkUpInfo[$key])) {
                $value = is_array($checkUpInfo[$key]) ? implode(', ', $checkUpInfo[$key]) : $checkUpInfo[$key];
                $nadiParts[] = "{$key}: " . strip_tags($value);
            }
        }
        if (!empty($checkUpInfo['nadi'])) {
            $nadiParts[] = strip_tags($checkUpInfo['nadi']);
        }
        $nadiText = !empty($nadiParts) ? implode("\n", $nadiParts) : '';

        // Doctor name
        $doctorName = $checkUpInfo['user_name'] ?? ($followup->doctor ? $followup->doctor->name : 'Doctor');
        $branchName = $checkUpInfo['branch_name'] ?? 'Clinic';

        return [
            // Patient Information Section
            'patient_name' => [
                'label' => __('messages.patient_name'),
                'value' => $patient->name ?? '',
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_id' => [
                'label' => __('messages.patient_id'),
                'value' => $patient->patient_id ?? $patient->id,
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_age' => [
                'label' => __('messages.patient_age'),
                'value' => $patientAge,
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_gender' => [
                'label' => __('messages.patient_gender'),
                'value' => $patient->gender ?? '',
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_mobile' => [
                'label' => __('messages.patient_mobile'),
                'value' => $patient->mobile_phone ?? '',
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_address' => [
                'label' => __('messages.patient_address'),
                'value' => $patient->address ?? '',
                'type' => 'textarea',
                'section' => 'patient_info'
            ],
            'patient_weight' => [
                'label' => __('messages.patient_weight'),
                'value' => $patient->weight ?? '',
                'type' => 'text',
                'section' => 'patient_info'
            ],
            'patient_height' => [
                'label' => __('messages.patient_height'),
                'value' => $patient->height ?? '',
                'type' => 'text',
                'section' => 'patient_info'
            ],

            // Medical Information Section
            'nadi' => [
                'label' => __('messages.nadi'),
                'value' => $nadiText,
                'type' => 'textarea',
                'section' => 'medical_info'
            ],
            'lakshane' => [
                'label' => __('messages.lakshane'),
                'value' => strip_tags($followup->diagnosis ?? ''),
                'type' => 'textarea',
                'section' => 'medical_info'
            ],
            'nidan' => [
                'label' => __('messages.nidan'),
                'value' => strip_tags($checkUpInfo['nidan'] ?? ''),
                'type' => 'textarea',
                'section' => 'medical_info'
            ],
            'chikitsa' => [
                'label' => __('messages.chikitsa'),
                'value' => strip_tags($checkUpInfo['chikitsa'] ?? ''),
                'type' => 'textarea',
                'section' => 'medical_info'
            ],

            // Treatment Details Section
            'days' => [
                'label' => __('messages.days'),
                'value' => $checkUpInfo['days'] ?? '',
                'type' => 'text',
                'section' => 'treatment_details'
            ],
            'packets' => [
                'label' => __('messages.packets'),
                'value' => $checkUpInfo['packets'] ?? '',
                'type' => 'text',
                'section' => 'treatment_details'
            ],
            'vishesh' => [
                'label' => __('messages.vishesh'),
                'value' => strip_tags($checkUpInfo['vishesh'] ?? ''),
                'type' => 'textarea',
                'section' => 'treatment_details'
            ],

            // Payment Information Section
            'amount_billed' => [
                'label' => __('messages.amount_billed'),
                'value' => number_format($followup->amount_billed ?? 0, 2),
                'type' => 'text',
                'section' => 'payment_info'
            ],
            'amount_paid' => [
                'label' => __('messages.amount_paid'),
                'value' => number_format($followup->amount_paid ?? 0, 2),
                'type' => 'text',
                'section' => 'payment_info'
            ],
            'amount_due' => [
                'label' => __('messages.amount_due'),
                'value' => number_format($followup->total_due ?? 0, 2),
                'type' => 'text',
                'section' => 'payment_info'
            ],

            // Clinic Information
            'branch_name' => [
                'label' => __('messages.branch_clinic'),
                'value' => $branchName,
                'type' => 'text',
                'section' => 'clinic_info'
            ],
            'doctor_name' => [
                'label' => __('messages.doctor_name'),
                'value' => $doctorName,
                'type' => 'text',
                'section' => 'clinic_info'
            ],
        ];
    }

    /**
     * Download the prescription as PDF.
     *
     * @param Request $request
     * @param FollowUp $followup
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadPdf(Request $request, FollowUp $followup)
    {
        $patient = $followup->patient;
        $selectedFields = $request->get('selected_fields', []);
        $customValues = $request->get('field_values', []);

        // Build prescription data
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];
        $allFields = $this->buildAllFields($followup, $patient, $checkUpInfo);

        $prescriptionData = [];
        foreach ($allFields as $key => $field) {
            if (in_array($key, $selectedFields)) {
                $prescriptionData[$key] = $customValues[$key] ?? $field['value'];
                $prescriptionData[$key . '_label'] = $field['label'];
            }
        }

        $viewData = [
            'data' => $prescriptionData,
            'followup' => $followup,
            'patient' => $patient,
            'selectedFields' => $selectedFields,
            'font_scale' => $request->get('font_scale', 1)
        ];

        $pdf = PDF::loadView('prescriptions.pdf-download', $viewData);
        $pdf->setOption('page-size', 'A4');
                // Dynamic Margins
        $pdf->setOption('margin-top', $request->get('margin_top', 10) . 'mm');
        $pdf->setOption('margin-bottom', $request->get('margin_bottom', 10) . 'mm');
        $pdf->setOption('margin-left', $request->get('margin_left', 10) . 'mm');
        $pdf->setOption('margin-right', $request->get('margin_right', 10) . 'mm');
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('enable-local-file-access', true);

        $filename = 'prescription_' . str_replace(' ', '_', $patient->name) . '_' . $followup->created_at->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Legacy: Generate a prescription PDF for a specific follow-up.
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
