truncate table form_encounter;
truncate table form_clinical_notes;
truncate table openemr_postcalendar_events;
truncate table documents;
truncate TABLE `telehealth_vc`;
SELECT 
    *
FROM
    form_encounter
ORDER BY id DESC limit 1;
