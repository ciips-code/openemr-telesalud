SELECT 
    vc.data_id,
    e.pc_eid,
    e.pc_pid,
    IFNULL((SELECT 
                    f.id
                FROM
                    forms AS f
                WHERE
                    f.form_name = 'Telehealth Video Consultations'
                LIMIT 1),
            0) AS formID
FROM
    telehealth_vc AS vc
        INNER JOIN
    openemr_postcalendar_events e ON vc.pc_eid = e.pc_eid
WHERE
    data_id = 'f14a83764a1b65fa2af0b5163048648f4cf75f86'
;