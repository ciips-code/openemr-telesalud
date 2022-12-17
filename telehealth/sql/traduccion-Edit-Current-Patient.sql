-- agregar definicion Editar paciente actual
insert into lang_definitions (cons_id, lang_id, definition)
values (
(
select cons_id
from lang_constants
where constant_name like 'Edit Current Patient' limit 1
)
,
4, 
'Editar paciente actual'
)
;
-- consulta 
select * from lang_definitions where definition ='Editar paciente actual';
-- fix 
select cons_id
from lang_constants
where constant_name like 'Editar paciente actual' limit 1;