SELECT c.pc_eid, c.pc_catid, c.pc_aid, c.pc_pid,
c.pc_title, c.pc_time, c.pc_eventDate as encounterDate,
c.pc_endDate, c.pc_startTime as encounterTime,
c.pc_endTime, c.pc_duration, CONCAT_WS( p.fname, p.mname, p.lname ) AS
patientFullName, CONCAT_WS( m.fname, m.mname, m.lname ) AS medicFullName
, p.email as patientEmail
,  vc.patient_url as patientEncounterUrl
,  vc.medic_url as medicEncounterUrl
, m.email as medicEmail
FROM
openemr_postcalendar_events AS c 
INNER JOIN patient_data AS p ON
c.pc_pid = p.id 
INNER JOIN users AS m ON c.pc_aid = m.id 
LEFT join tsalud_vc as vc on c.pc_eid =vc.pc_eid

WHERE
c.pc_catid IN (16) and c.pc_eid=1;