-- Eliminar orientacion sexuals
UPDATE `admin_devopenemr`.`layout_options` SET `uor` = '0' WHERE (`form_id` = 'DEM') and (`field_id` = 'sexual_orientation') and (`seq` = '120');
