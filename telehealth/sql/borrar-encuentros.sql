-- reiniciar secuentas
truncate table sequences;
insert into sequences values(1);
-- BORRAR TODAS LAS AGENDAS
truncate table openemr_postcalendar_events;
-- BORRAR VIDEO CONFERENCIAS 
truncate table telehealth_vc;
truncate table telehealth_vc_log;
-- BORRAR TODOS ENCUENTROS
truncate table form_encounter;
truncate table forms;
-- BORRAR TODAS LAS NOTAS CLINICAS
truncate table form_clinical_notes;
-- BORRAR FIRMAS
truncate table esign_signatures;
-- BORRAR PROBLEMAS
truncate table issue_encounter;
-- BORRAR TODOS LOS DOCUMETOS
truncate table categories_to_documents;
truncate table documents;
