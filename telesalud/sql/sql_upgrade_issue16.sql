-- Nuevo encuetro - Nueva visita 
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = REPLACE(definition ,'Nuevo Encuentro','Nueva Visita')
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition LIKE '%Nuevo Encuentro%';
-- Vitales - Signos Vitales 
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = REPLACE(definition ,'Vitales','Signos Vitales')
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Vitales';
	-- Open Encounter
	-- Traduciendo.. 
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name ='Open Encounter' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Abrir Visita'
	);

	
-- Clinical Notes
-- Traducir Clinical Notes - Nombre de nacimiento
-- se corre por unica vez 
INSERT INTO lang_constants ( `constant_name`) VALUES ('Clinical Notes');
-- Traduciendo.. 
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name ='Clinical Notes' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Nota Clínica'
	);
-- Traducir Medical Issues - Nombre de nacimiento
-- se corre por unica vez 
INSERT INTO lang_constants ( `constant_name`) VALUES ('Medical Issues');
-- Traduciendo.. 
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name ='Medical Issues' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Problemas'
	);
	
	-- Resumen de registros médicos
	
	INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name ='Medical Record Dashboard' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Resumen de registros médicos
	'
	);