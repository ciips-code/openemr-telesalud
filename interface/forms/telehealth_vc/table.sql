-- -
-- Telesalud Video Consultations Data Table
-- Table structure for table `telehealth_vc`
-- -
DROP table if exists telehealth_vc;
CREATE TABLE `telehealth_vc` (
  `pc_eid` INT(11) UNSIGNED NOT NULL,
  `success` BOOL,
  `message` VARCHAR(1024),
  `data_id` VARCHAR(1024),
  `valid_from` VARCHAR(1024),
  `valid_to` VARCHAR(1024),
  `patient_url` VARCHAR(1024),
  `medic_url` VARCHAR(1024),
  `url` VARCHAR(1024),
  `medic_secret` VARCHAR(1024),
  `evolution` LONGTEXT NULL,
  `encounter` BIGINT(20) NULL DEFAULT 0,
  `created` DATETIME NOT NULL,
  `updated` TIMESTAMP NOT NULL
);
ALTER TABLE `telehealth_vc`
ADD PRIMARY KEY (`pc_eid`);
-- Telesalud Video Consultations Files Table.
-- Table structure for table `telehealth_vc_files`
--
DROP table if exists `telehealth_vc_files`;
-- -
-- Telesalud Video Consultations Settins Table 
-- -
DROP table if exists telehealth_vc_config;
CREATE TABLE `telehealth_vc_config` (
  `smtp_server` VARCHAR(1024),
  `smtp_user` VARCHAR(1024),
  `smtp_password` VARCHAR(1024),
  `smtp_port` VARCHAR(1024),
  `smtp_ssl_verify_peer` VARCHAR(1024),
  `smtp_ssl_verify_peer_name` VARCHAR(1024),
  `vc_api_url` VARCHAR(1024),
  `vc_api_token` VARCHAR(1024)
);
INSERT INTO `telehealth_vc_config`
VALUES (
    'c1031.cloud.wiroos.net',
    'mails.sending@lugaronline.com',
    'iEr1OnI4CK',
    '587',
    'true',
    'true',
    'https://meet.telesalud.iecs.org.ar:32443/api/videoconsultation?',
    '1|OB00LDC8eGEHCAhKMjtDRUXu9buxOm2SREHzQqPz'
  );
-- -
-- Telesalud Video Consultations Tpics Table 
-- -
DROP table if exists telehealth_vc_topic;
CREATE TABLE `telehealth_vc_topic` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic` VARCHAR(30) NULL,
  `description` VARCHAR(255) NULL,
  `value` VARCHAR(10) NULL,
  PRIMARY KEY (`id`) USING BTREE
);
-- 
-- tabla de logs
-- 
drop table if exists telehealth_vc_log;
CREATE TABLE telehealth_vc_log (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timestamp` TIMESTAMP NOT NULL,
  `data_id` VARCHAR(255) NOT NULL,
  `status` VARCHAR(30) NOT NULL,
  `response` longtext NULL,
  PRIMARY KEY (`id`) USING BTREE
);
-- -   
INSERT INTO `telehealth_vc_topic` (`topic`, `description`, `value`)
VALUES (
    'medic-set-attendance',
    'El médico ingresa a la videoconsulta',
    '-'
  ),
  (
    'medic-unset-attendance',
    'El médico cierra la pantalla de videoconsulta',
    '>'
  ),
  (
    'videoconsultation-started',
    'Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
',
    '-'
  ),
  (
    'videoconsultation-finished',
    'El médico presiona el botón Finalizar consulta
',
    '-'
  ),
  (
    'patient-set-attendance',
    'El paciente anuncia su presencia',
    '@'
  );
-- Documents categories
delete from categories
where name = 'Teleconsultas';
delete from categories
where name = 'Video Consultation';
insert into categories (id, value, name, parent, aco_spec, lft, rght)
VALUES (
    31,
    '',
    'Teleconsultas',
    1,
    'patients|docs',
    52,
    56
  );