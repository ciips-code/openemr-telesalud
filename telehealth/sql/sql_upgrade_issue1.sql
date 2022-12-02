-- Issue #1: Modificar Identificación de RRHH 
--
-- Modificar Leyendas:
--
-- Nombre -> Primer nombre /
-- Identificación de impuesto federal-> Tipo de identificación profesional
-- Nro. licencia estatal -> Número de identificación profesoional
-- Provider type-> Tipo de profesional
-- Control de acceso-> Perfiles de acceso
-- 
-- Correcciones 2: 
-- cambiar leyendas: 
-- 'username' -> 'Nombre de usuario'   
-- 'Su palabra clave' -> 'Contraseña de su usuario con perfil administrador' 
-- corregir falta de ortografía de campo 
-- "Numero de identidicación profesoional" 
--
-- Cambiar nompre por Nombre -> Primer nombre
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Primer nombre' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND constant_name = 'First Name';
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Tipo de identificación profesional' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Identificación de impuesto federal';--
-- Nro. licencia estatal -> Número de identificación profesoional
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Número de identificación profesoional' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Nro. licencia estatal';-- 
-- Control de acceso-> Perfiles de acceso
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Perfiles de acceso' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Control de acceso';--
-- Main Menu Role -> Rol menú principal
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Main Menu Role' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Rol menú principal';--
-- Main Menu Role -> Rol menú principal
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Nombre de usuario' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'username';--
-- corregir falta de ortografía de campo "Numero de identidicación profesoional
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = REPLACE ( definition, 'profesoional', 'profesional' ) 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition LIKE '%profesoional%';--
-- Su palabra clave  -> Contraseña de su usuario con perfil administrador
--
UPDATE lang_definitions AS ld,
lang_constants AS lc 
SET definition = 'Contraseña de su usuario con perfil administrador' 
WHERE
	ld.cons_id = lc.cons_id 
	AND lang_id = 4 
	AND definition = 'Su palabra clave';--
-- Provider type-> Tipo de profesional
-- no esta en la carga inicial
--
INSERT INTO lang_definitions ( `cons_id`, `lang_id`, `definition` )
VALUES
	(
		(
		SELECT
			lc.cons_id 
		FROM
			lang_constants AS lc 
		WHERE
			constant_name LIKE '%Provider Type%' 
			AND lc.cons_id NOT IN ( SELECT DISTINCT cons_id FROM lang_definitions ld WHERE ld.lang_id = 4 ) 
			LIMIT 1 
		),
		4,
	'Tipo de profesional' 
	);