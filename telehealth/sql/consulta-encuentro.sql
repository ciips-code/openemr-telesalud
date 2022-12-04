SELECT 
    fcn.*,
    lo_category.title AS category_title,
    lo_category.notes AS category_code
FROM
    `form_clinical_notes` fcn
        LEFT JOIN
    list_options lo_category ON lo_category.option_id = fcn.clinical_notes_category
        LEFT JOIN
    list_options lo_type ON lo_type.option_id = fcn.clinical_notes_type
WHERE
    fcn.`form_id` = 1 AND fcn.`pid` = 10
        AND fcn.`encounter` = 251
GROUP BY fcn.id