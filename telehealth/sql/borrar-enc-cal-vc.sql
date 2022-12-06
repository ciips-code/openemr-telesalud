-- BORRAR TODAS LAS AGENDAS
-- truncate table openemr_postcalendar_events;
-- BORRAR VIDEO CONFERENCIAS 
-- truncate table telehealth_vc;
-- truncate table telehealth_vc_log;
-- BORRAR TODOS ENCUENTROS
-- truncate table form_encounter;
-- truncate table forms;
-- BORRAR TODOS LOS DOCUMETOS
-- truncate table categories_to_documents;
-- truncate table documents;

SELECT 
    *
FROM
    telehealth_vc_log
ORDER BY id DESC;

SELECT 
    *
FROM
    telehealth_vc
ORDER BY pc_eid DESC;