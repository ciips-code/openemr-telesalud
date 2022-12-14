
INSERT INTO lang_constants (constant_name)
VALUES ('Copy patient link');
INSERT INTO lang_definitions (`cons_id`, `lang_id`, `definition`)
VALUES (
		(
			SELECT lc.cons_id
			FROM lang_constants AS lc
			WHERE constant_name = 'Copy patient link'
				AND lc.cons_id NOT IN (
					SELECT DISTINCT cons_id
					FROM lang_definitions ld
					WHERE ld.lang_id = 4
				)
			LIMIT 1
		), 4, 'Copiar enlace del paciente'
	);