-- Modificar Formulario alta paciente #15
-- UOR: Indicates if the field is Unused, Optional or Required.
-- Seccion : 	Quien (1)
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE `form_id` = 'DEM'  	AND group_id = '1'  	AND `title` LIKE '%title%'  LIMIT 1;
-- Actualizar campo nombre de nacimiento campo obligatorio
UPDATE `openemr`.`layout_options`  SET uor = 2  WHERE	`form_id` = 'DEM' 	AND group_id = '1' 	AND `title` LIKE '%Birth Name%' LIMIT 1;
-- Traducir Birth Name - Nombre de nacimiento
-- se corre por unica vez 
INSERT INTO lang_constants ( `constant_name`) VALUES ('Birth Name');
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
			constant_name ='Birth Name' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Nombre de nacimiento'
	);
-- Ocultar campos 
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE `form_id` = 'DEM' 	AND group_id = '1' and description='User Defined Field';
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE `form_id` = 'DEM'  	AND group_id = '1'  	AND `title` LIKE '%Billing Note%'  LIMIT 1;
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE	`form_id` = 'DEM'  	AND group_id = '1'  	AND `title` LIKE '%Previous Names%'  	LIMIT 1;
-- Traducir S.S. - Nro. De Seguridad Social
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Nro. De Seguridad Social' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'S.S.';
-- Traducir Gender Identity - Identidad de Género
-- se corre por unica vez 
INSERT INTO lang_constants ( `constant_name`) VALUES ('Gender Identity');
-- Traduciendo...
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name LIKE '%Gender Identity%' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Identidad de Género'
	);
-- Traducir Sexual Orientation: - Orientacion sexual 
-- se corre por unica vez 
INSERT INTO lang_constants ( `constant_name`) VALUES ('Sexual Orientation');
-- Traduciendo...
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name LIKE '%Sexual Orientation%' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Orientación sexual'
	);
-- Traducir Identificador externo: - Tipo de documento 
-- Traduciendo... 
UPDATE lang_definitions AS ld, lang_constants AS lc  SET ld.definition = 'Tipo de documento'  
WHERE ld.cons_id = lc.cons_id 
	AND lang_id = 4  and `definition` LIKE '%Identificador externo%' AND `lang_id` = '4';
-- Traducir Licencia/Nº Id. - Nro de Documento 
-- Traduciendo... 
UPDATE lang_definitions AS ld, lang_constants AS lc  SET ld.definition = 'Nro de Documento'  
WHERE ld.cons_id = lc.cons_id 
	AND lang_id = 4  and `definition` LIKE '%Licencia/Nº Id.%' AND `lang_id` = '4';
	-- Actualizar campo nombre de nacimiento campo obligatorio
UPDATE `openemr`.`layout_options`  SET uor = 2  WHERE	`form_id` = 'DEM' 	AND group_id = '1' 	AND `title` LIKE '%External ID%' LIMIT 1;
-- Actualizar campo nombre de nacimiento campo obligatorio
UPDATE `openemr`.`layout_options`  SET uor = 2  WHERE	`form_id` = 'DEM' 	AND group_id = '1' 	AND `title` LIKE '%License/ID%' LIMIT 1;
-- Seccion : Contacto (2)
-- Oculat campo Trusted Email
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE	`form_id` = 'DEM' 	AND group_id = '2' 	AND  `title` LIKE '%Trusted Email%' LIMIT 1;
-- Oculat campo Nombre de la Madre 
UPDATE `openemr`.`layout_options`  SET uor = 0  WHERE	`form_id` = 'DEM' 	AND group_id = '2' 	AND `title` LIKE '%Mother%' 	LIMIT 1;
-- Seccion : Opciones (3)
-- Traducir Permitir Mensaje de Voz - Permitir mensaje telefónico
-- Traduciendo... 
UPDATE lang_definitions AS ld, lang_constants AS lc  SET ld.definition = 'Permitir mensaje telefónico' 
WHERE ld.cons_id = lc.cons_id 
	AND lang_id = 4  and  `definition` = 'Permitir Mensaje de Voz';
-- ocultar varios campos 
UPDATE `openemr`.`layout_options`  SET uor = 0  
WHERE `form_id` = 'DEM' AND `group_id` = '3';

UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='hipaa_message';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='hipaa_voice';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='hipaa_notice';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='hipaa_allowsms';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='hipaa_allowemail';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='providerID';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='provider_since_date';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='ref_providerID';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_imm_reg_use';

UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_imm_info_share';

UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_health_info_ex';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='providerID';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='provider_since_date';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='ref_providerID';
UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_imm_reg_use';

UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_imm_info_share';

UPDATE `openemr`.`layout_options`  SET uor = 1  WHERE	`form_id` = 'DEM' 	AND group_id = '3' 	AND 
field_id='allow_health_info_ex';

-- Seccion : Empresa (4)
-- ocultar em_street_line_2
UPDATE `openemr`.`layout_options`  SET uor = 0  
WHERE `form_id` = 'DEM' AND `group_id` = '4' and field_id='em_street_line_2';
-- seccion : Estados (5)
-- UPDATE `openemr`.`layout_options`  SET uor = 1 WHERE `form_id` = 'DEM' AND `group_id` = '5';
UPDATE `openemr`.`layout_options`  SET uor = 0 WHERE `form_id` = 'DEM' AND `group_id` = '5' and field_id='vfc';
-- 6 
-- Traducir Guardian  - Persona de Contacto 
-- Traduciendo... 
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE `constant_name` = 'Guardian'
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Persona de Contacto'
	);

-- Modificar Finder por --> "Buscador de Pacientes" 
-- UPDATE `openemr`.`layout_options`  SET uor = 1 WHERE `form_id` = 'DEM' AND `group_id` = '5';
-- Traduciendo... 
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE `constant_name` = 'Patient Finder'
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Buscador de Pacientes'
	);