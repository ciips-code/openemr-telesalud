SELECT 
round((d.size/1024 ),0) as s_Kilobytes,
round(((d.size/1024)/1024),2) as s_Megabytes,  
  d.* FROM admin_devopenemr.documents d order by size ;