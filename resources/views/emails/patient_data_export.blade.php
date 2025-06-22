
@component('mail::message')
# Patient Data Export

Dear Recipient,

Attached is the JSON file containing the data for patient {{ $patient->name }} (ID: {{ $patient->patient_id }}).

Please use this file to import the patient data into another system as needed.

Thank you,
Clinic Management System

@component('mail::button', ['url' => url('/')])
My Clinic
@endcomponent
@endcomponent


