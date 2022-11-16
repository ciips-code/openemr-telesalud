DELETE FROM openemr_postcalendar_events 
WHERE
    pc_pid = 10;
SELECT 
    *
FROM
    openemr_postcalendar_events
WHERE
    pc_pid = 10
ORDER BY pc_eid DESC
LIMIT 10;

