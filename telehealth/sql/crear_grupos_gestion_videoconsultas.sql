-- Grupos
DELETE FROM layout_group_properties 
WHERE
    grp_form_id = 'telehealth_vc';
DELETE FROM layout_group_properties 
WHERE
    grp_form_id = 'LBFtelehealth_vc';
-- INSERT INTO `layout_group_properties` (
--         `grp_form_id`,
       
--         `grp_title`,
--         `grp_mapping`,
--         `grp_columns`,
--         `grp_size`,
--         `grp_last_update`
--     )
-- VALUES (
--         'LBFtelehealth_vc',
       
--         'Datos de video consultas', 
--         'Core',
--         0,
--         0,
--         current_timestamp()
--     )
--     -- ,
--     -- (
--     --     'LBFtelehealth_vc',
--     --     '2',
--     --     'Notificaciones de video consuiltas'
--     -- ),
--     -- (
--     --     'LBFtelehealth_vc',
--     --     '3',
--     --     'Configuracion Video Consultas'
--     -- )
--     ;
    DELETE FROM layout_options 
WHERE
    form_id = 'telehealth_vc';
DELETE FROM layout_options 
WHERE
    form_id = 'LBFtelehealth_vc';
--   -  
--     INSERT INTO layout_options 
-- (
-- form_id, field_id, group_id, title,
-- seq, data_type, uor, fld_length,
-- max_length, list_id, titlecols, datacols,
-- default_value, edit_options, description, fld_rows,
-- list_backup_id, source, conditions, validation) 
-- VALUES 
-- ('LBFtelehealth_vc', 'pc_eid', 1, 'Event ID'
-- , 10, 2, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')
-- ,('LBFtelehealth_vc', 'success', 1, 'Success Satatus'
-- , 20, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'message', 1, 'Message'
-- , 30, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'data_id', 1, 'VC ID'
-- , 40, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'valid_from', 1, 'Valid form'
-- , 50, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'valid_to', 1, 'Valid to'
-- , 60, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'patient_url', 1, 'Patient VC URL'
-- , 70, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'medic_url', 1, 'Medic VC URL'
-- , 80, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'url', 1, 'VC URL'
-- , 90, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '')

-- ,('LBFtelehealth_vc', 'medic_secret', 1, 'Medic Secret'
-- , 100, 1, 1, 0
-- , 0, '', 1, 1
-- , '', '', '', 0
-- , '', 'F', '', '');

SELECT 
    *
FROM
    layout_options
WHERE
    form_id = 'LBFtelehealth_vc'
ORDER BY group_id , seq;

SELECT 
    *
FROM
    layout_group_properties
WHERE
    grp_form_id = 'LBFtelehealth_vc';