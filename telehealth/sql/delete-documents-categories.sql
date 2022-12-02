DELETE FROM categories 
WHERE
    id > 1;
     -- Documents categories
DELETE FROM categories 
WHERE
    name = 'Teleconsultas';
DELETE FROM categories 
WHERE
    name = 'Video Consultation';
  insert into categories (id,value,name,parent, aco_spec,lft,rght) 
  VALUES (31,'','Teleconsultas',1,'patients|docs',52,56);
