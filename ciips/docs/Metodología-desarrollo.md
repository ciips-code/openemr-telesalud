### Metodología de Desarrollo

Con el fin de poder lograr un orden en el proceso de desarrollo, se está definiendo como metodología utilizar el modelo alternativo [Gitflow](https://www.atlassian.com/es/git/tutorials/comparing-workflows/gitflow-workflow). 

Esto significa que las ramas que estarán creadas en el repositorio en Github mantedrán un propósito que será el siguiente:

**Main**: Rama dentro del repositorio que mantendrá la versión estable del proyecto. Será la rama que se recomendará para desplegar proyectos en ambientes de producción. 

**Develop**: Rama dentro del repositorio que será utilizada como base para los desarrollos, la misma servirá para aceptar los pull request y realizar los unittest

**Hotfix**: Cuando se reporte un error que se considere critico sobre una funcionalidad especifica (parche/actualización) se categorizará como hotfix. Para esto se abrirá primeramente un [Issues](https://github.com/ciips-code/openemr-telesalud/issues) en el repositorio explicando detalladamente que es el error, cual es el comportamiento esperado y como se puede reproducir el error. Una vez publicado, Github le generará un N# de Issues, este número servirá para crear el hotfix. Para esto se creará una nueva rama indicando primeramente que el un hotfix y a la cual se le asignará como nombre la palabra issue seguindamente con el simbolo numeros (#) y el número del Issues en github, por ejemplo:
`hotfix/issues #25`

**Bugfix**: Cuando se reporte una acción sobre el código fuente, un cambio especifico o un conjunto de cambios para corregir un defecto en el código (de cualquier tipo) se categorizará como bugfix. Se realizará el mismo procedimiento que se haría con un hotfix en donde primero se abrirará un Issues en Github y luego se procederá crear una rama bugfix con el número de Issues. El nombre de la rama debería quedar algo así como: 
`hotfix/issues #26`

**Feature**: Los features serán todas aquellas nuevas funcionalidades o requirimientos que sean aceptadas para el proyecto. Al igual que con los casos anteriores, se abrirán Issues en Github y en base a esta númeracíon se creará la rama, ejemplo:
`feature/issues #4`

## Trello
Se esta utilizando la herramienta de [Trello](https://trello.com/b/xVT3UM0I/ts-all-in-one) para poder trabajar una especie de metodología ágil customizada. El proyecto esta organizado de la siguiente manera:

**Lista de tareas**: Se muestras todos los posibles temas que se deberían abordar o que se discutirán para el proyecto. Esta lista no dispara ningún desarrollo y sirve como un borrador de hoja de ruta.

**Análisis Funcional**: Una vez aceptado un tarea de la lista de tareas se procede a realizar todo el analisis funcioanal, levantamiento de requierimientos, observaciones y todo aquello que se necesita para tener un requerimientos listo para adopción en el desarrollo.

**Backlog de desarrollo**: Lista de requerimientos aceptados y documentados listos para ser adoptados en desarrollo y/o documentación (en el caso de que sea algo configurable).

**En Desarrollo**: Tareas que estan en desarrollo, es importante que todas las tareas que estén en desarrollo se les proceda abrirar una [Issues](https://github.com/ciips-code/openemr-telesalud/issues) en Github y el número de Issue esté indicado en la **Descripción** de la tarjeta.

**En prueba**: Una vez finalizado el desarrollo, se procede a realizar todo el proceso de QA.

**Hecho**: Una vez superada las pruebas QA se indica la tarea como realiada y se procede a crear el Pull Request para enviar los datos de la rama `develop` a `main`.