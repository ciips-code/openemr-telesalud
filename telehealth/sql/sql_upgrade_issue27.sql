-- Registro de horas en la tabla de notas clínicas

ALTER TABLE form_clinical_notes
ADD COLUMN time TIME DEFAULT NULL AFTER date;
