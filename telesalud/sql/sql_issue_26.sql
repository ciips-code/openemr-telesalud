-- mostrar teleconsulta activa
SELECT cal.pc_eid,
    cal.pc_aid,
    cal.pc_pid,
    cal.pc_title,
    pc_hometext,
    pc_startTime,
    pc_endTime,
    vcdata.*
FROM `openemr_postcalendar_events` as cal
    inner join tsalud_vc as vcdata on cal.pc_eid = vcdata.pc_eid
    INNER JOIN patient_data AS p ON cal.pc_pid = p.id
    INNER join users as u on cal.pc_aid = u.id
where pc_eventDate = current_date()
    and CURRENT_TIME BETWEEN cal.pc_startTime and cal.pc_endTime
    and cal.pc_catid = 16
    and u.username = 'yois-admin-openemr'
    and cal.pc_pid = 6 -- 
    -- limpiar tablas de video consultas y calendarioß
    --
    TRUNCATE TABLE tsalud_vc;
    TRUNCATE TABLE openemr_postcalendar_events;