INSERT INTO lang_constants (cons_id,constant_name) VALUES
(13479, 'Video Consultation Links')
(13480, 'Professional')
(13481, 'Clinical data')
(13482, 'Search for ICD11 problem')
(13483, 'Unassigned')
(13484, 'Acute (active)')
(13485, 'Chronic (passive)')
(13486, 'Invalid')
;

INSERT INTO lang_definitions(cons_id, lang_id, definition) VALUES
(13479, 4, 'Accesos a la video consulta')
(13480, 4, 'Profesional')
(13481, 4, 'Dato clínico')
(13482, 4, 'Buscar problema CIE-11')
(13483, 4, 'Sin Asignar')
(13484, 4, 'Agudo (activo)')
(13485, 4, 'Crónico (pasivo)')
(13486, 4, 'Inválido')
;

UPDATE list_options
SET title='Unassigned'
WHERE list_id='outcome' AND option_id='0';
UPDATE list_options
SET title='Acute (active)'
WHERE list_id='outcome' AND option_id='6';
UPDATE list_options
SET title='Chronic (passive)'
WHERE list_id='outcome' AND option_id='7';
UPDATE list_options
SET title='Invalid'
WHERE list_id='outcome' AND option_id='8';


