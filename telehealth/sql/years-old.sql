DELETE FROM lang_definitions 
WHERE
    definition LIKE '%años de edad%'

INSERT INTO lang_definitions (`cons_id`, `lang_id`, `definition`)
VALUES (
		(
			SELECT lc.cons_id
			FROM lang_constants AS lc
			WHERE constant_name = ' years old'
				AND lc.cons_id NOT IN (
					SELECT DISTINCT cons_id
					FROM lang_definitions ld
					WHERE ld.lang_id = 4
				)
			LIMIT 1
		), 4, ' años de edad'
	);
 
SELECT 
    *
FROM
    lang_definitions
WHERE
    definition LIKE '%años de edad%'