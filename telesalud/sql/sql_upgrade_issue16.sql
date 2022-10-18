-- Nuevo encuetro - Nueva visita 
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = REPLACE(definition ,'Nuevo Encuentro','Nueva Visita')
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition LIKE '%Nuevo Encuentro%';