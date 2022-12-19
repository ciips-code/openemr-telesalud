# Instalación Plataforma de telesalud Todo en una basada en OpemEMR  v7.0.0

Seguir los pasos de instalación del siguiente enlace. https://github.com/ciips-code/openemr-telesalud/blob/main/Documentation/INSTALL
## Ejecutar los siguientes archivos via sql dump
    - Listas y traducciones: /telehealth/sql/ops-openemr-data-upgrade.sql
    - Configuración Documentos: /telehealth/sql/documentos-teleconsulta.sql
    - Actualizaciones tablas: /telehealth/sql/ops-openemr-upgrade.sql

## Configurar sistema
    -    Administración / Formularios / Formularios de administración
        - Desactivar todos los formularios de la vista/encuentro que no sean los siguientes:
            - Plan de atención	inhabilitado	
            - Clinical Instructions	inhabilitado
            - Nota Clínica	Activado 
            - Eye Exam	inhabilitado 
            - Hoja de tarifas	inhabilitado 
            - Functional and Cognitive Status	inhabilitado 
            - Group Attendance Form	inhabilitado 
            - Diversas opciones de facturación HCFA	inhabilitado 
            - Formulario Nueva Visita	inhabilitado 
            - New Group Encounter Form	Activado 
            - Observation	inhabilitado 
            - Orden de Procedimiento	inhabilitado 
            - Revisión de los sistemas	inhabilitado 
            - Revisión de chequeos de sistemas	inhabilitado 
            - SOEP	Activado 
            - Dictado	inhabilitado 
            - Signos Vitales	Activado
## Variables de entornos archivo .env
Estas variables deben configurarse para que el módulo de teleconsulta funcione correctamente.
################################
# CONFIGURACION API SERVIDOR DE VIDEO CONSULTAS
################################
# URL Servidor
VC_API_URL='https://meet.telesalud.iecs.org.ar'
# Endpoint 
VC_API='/api/videoconsultation?'
# Endpoint de datos
VC_API_DATA='/api/videoconsultation/data?'
# Puerto
VC_API_PORT=NRO DE PUERTO
# Token access
VC_API_TOKEN=TOKEN DE ACCESO
################################
# CONFIGURACION SERVIDOR DE BASE DE DATOS
################################
# DATA BASE CONNECTIONS SETTING
OPS_DB_HOST=HOST DE BASE DE DATOS
OPS_DB_PASSWORD=CONTRASEÑA DE BASE DE DATOS
OPS_DB_USER=USUARIO DE BASE DE DATOS
OPS_DB_DATABASE=NOMBRE DE BASE DE DATOS