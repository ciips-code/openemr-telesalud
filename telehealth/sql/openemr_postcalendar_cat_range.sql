-- aregar campo para habilitar categoria de rango de horarios
alter table openemr_postcalendar_categories add column pc_range smallint DEFAULT 0 COMMENT  'calendar hours rang category' ;
-- actualizar la categoria in_office
update openemr_postcalendar_categories set pc_range=1 where  pc_constant_id='in_office';