select  concat_ws(p.fname,p.lname) as patient, log.*, vc.* from telehealth_vc_log log 
inner join telehealth_vc vc on log.data_id=vc.data_id
inner join openemr_postcalendar_events e on vc.pc_eid=e.pc_eid
inner join patient_data p on e.pc_pid=p.id
inner join users u on e.pc_aid=u.id
where log.status='videoconsultation-finished'
order by log.timestamp desc;