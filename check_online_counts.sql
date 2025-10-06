
// Check online database counts
SELECT 
    (SELECT COUNT(*) FROM patients WHERE updated_at >= '1900-01-01') as patients_since_1900,
    (SELECT COUNT(*) FROM patients) as total_patients,
    (SELECT COUNT(*) FROM follow_ups WHERE updated_at >= '1900-01-01') as followups_since_1900,
    (SELECT COUNT(*) FROM follow_ups) as total_followups;

