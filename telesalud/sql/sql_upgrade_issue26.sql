-- Respuesta Servicio de creacion de video consulta (SCV)
DROP TABLE IF EXISTS tsalud_vc;
CREATE TABLE `tsalud_vc` (
    `pc_eid` INT (11) UNSIGNED NOT NULL,
    `success` BOOL,
    `message` VARCHAR (1024),
    `data_id` VARCHAR (1024),
    `data_valid_from` VARCHAR (1024),
    `data_valid_to` VARCHAR (1024),
    `data_patient_url` VARCHAR (1024),
    `data_medic_url` VARCHAR (1024),
    `data_data_url` VARCHAR (1024),
    `medic_secret` VARCHAR (1024)
);
DROP TABLE IF EXISTS tsalud_vc_notify;
CREATE TABLE `tsalud_vc_notify` (
    `pc_eid` INT (11) UNSIGNED NOT NULL,
    `vc_secret` VARCHAR (1024),
    `vc_medic_secret` VARCHAR (1024),
    `vc_status` VARCHAR (1024),
    `vc_medic_attendance_date` VARCHAR (1024),
    `vc_patient_attendance_date` VARCHAR (1024),
    `vc_start_date` VARCHAR (1024) NULL,
    `vc_finish_date` VARCHAR (1024),
    `vc_extra` VARCHAR (1024),
    `topic` VARCHAR (1024)
);
-- Video consultas desde y hacia servidor de Jitsi
-- DROP TABLE IF EXISTS tsalud_vc_calendar;
-- CREATE TABLE `tsalud_vc_calendar` (
--     `pc_eid` INT (11) UNSIGNED NOT NULL,
--     `appointment_date` VARCHAR (1024),
--     `days_before_expiration` INT,
--     `medic_name` VARCHAR (1024),
--     `patient_name` VARCHAR (1024),
--     `extra` VARCHAR (1024)
-- );
ALTER TABLE `openemr`.`tsalud_vc`
ADD PRIMARY KEY (`pc_eid`);