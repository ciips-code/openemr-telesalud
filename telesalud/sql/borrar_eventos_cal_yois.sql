delete from openemr_postcalendar_events where pc_pid=10;
SELECT 
    *
FROM
    openemr_postcalendar_events
ORDER BY pc_eid DESC
LIMIT 10;
