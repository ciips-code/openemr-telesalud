DROP table if exists  tsalud_vc_topic;
CREATE TABLE `tsalud_vc_topic` (
    `topic` VARCHAR(30) NULL,
    `description` VARCHAR(255) NULL,
    `value` VARCHAR(10) NULL
);

INSERT INTO `tsalud_vc_topic` VALUES
('medic-set-attendance','El médico ingresa a la videoconsulta','-');
INSERT INTO `tsalud_vc_topic` VALUES
('medic-unset-attendance','El médico cierra la pantalla de videoconsulta','>');
INSERT INTO `tsalud_vc_topic` VALUES
('videoconsultation-started','Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
','-');
INSERT INTO `tsalud_vc_topic` VALUES
('videoconsultation-finished','El médico presiona el botón Finalizar consulta
','-');
INSERT INTO `tsalud_vc_topic` VALUES
('patient-set-attendance','El paciente anuncia su presencia','@');