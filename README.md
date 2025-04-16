![](Aspose.Words.da2fbb18-004a-4a09-8a66-40d876837536.001.png)

[**FACULTAD ](https://www.iua.edu.ar/)**REGIONAL ROSARIO ![](Aspose.Words.da2fbb18-004a-4a09-8a66-40d876837536.002.png)**

Cátedra de: ENTORNOS GRÁFICOS 

**Trabajo Práctico Año 2025** 

**Introducción** 

Dado que la carrera Ingeniería en Sistemas de Información, tiende a la definición de un espacio interdisciplinario e integrador de aprendizajes, y está constituida por un conjunto de materias cuya finalidad es la de crear a lo largo de la carrera un espacio de estudio interdisciplinario y fortalecer los conceptos fundamentales de la profesión, que permitan a los estudiantes conocer las características del trabajo ingenieril, partiendo desde los problemas básicos de los Sistemas de Información;  se pretende a través de este desarrollo web, lograr un espacio de articulación vertical dentro del área de Desarrollo de Software, en la cual está enmarcada la asignatura. Luego, el trabajo práctico a desarrollar se basa en el realizado en el ciclo lectivo 2023 por los alumnos de la cátedra de Algoritmos y Estructura de Datos. 

En este caso, el trabajo para la cátedra de Entornos Gráficos consiste en la presentación de un Sitio Web en funcionamiento, subido a un servidor web y un informe detallado sobre el desarrollo del mismo. 

La minuta de relevamiento que se presenta en este documento le dará al estudiante una noción general de aquello que se trabajará durante el cuatrimestre de cursado, además de los contenidos que deberá ir adquiriendo a medida que avanza el proceso de desarrollo permitiendo profundizar y aplicar los conocimientos que se adquieren durante la cursada al resolver un caso concreto de la realidad. 

**Resultado de Aprendizaje**  

Se espera que al finalizar el trabajo los alumnos adquieran la capacidad de: 

*Construir  sitios  web  según  las  pautas  generales  de  accesibilidad,  usabilidad  y considerando las buenas prácticas de desarrollo de software para concebir soluciones tecnológicas que permitan resolver situaciones organizacionales.*  

**Descripción General** 

El trabajo práctico consiste en el desarrollo de un Sitio Web que permita gestionar los descuentos y/o promociones en los locales de un reconocido shopping de la ciudad de Rosario.  

De ahora en más, en el contexto de este sitio, serán sinónimos: ofertas, descuentos, promociones.  

El software fue en realidad desarrollado para implementarse mediante una Web APP, por lo  cual  realizaremos un sitio  web totalmente responsivo  que pueda  ser utilizado en diferentes dispositivos manejados por los usuarios. 

El sitio tendrá 4 niveles de usuarios distintos:  

- Administrador,  
- Dueños de locales  
- Clientes y 
- Usuarios no registrados. 

Cada uno de los usuarios excepto el administrador deberán poder registrarse en el sitio web.  

Cuando la categoría de usuario es la de Dueños de Locales, dicho registro deberá ser autorizado por el Administrador. 

En relación con la categoría de Clientes, éstos podrán registrarse y luego debe llegarles a su casilla de correo un enlace con el cual lograr validar su registro. 

El **administrador** es quien gestiona las ofertas de todos los locales del shopping de acuerdo a la política comercial del centro comercial, aprobando o denegando dichas ofertas que son propuestas por los locales.  

El **administrador** podrá crear los locales y también aprobar las cuentas de los dueños de locales para que accedan al sistema. Éste deberá aprobar o denegar las promociones que se ofrecen en cada local, también podrá crear novedades destinadas a los clientes del shopping.  

Las  novedades  pueden  ir  dirigidas  a  distintas  categorías  de  clientes,  y  expirarán automáticamente pasado un intervalo de tiempo, y contendrán un texto descriptivo de la novedad.  

Por último, el **administrador** podrá monitorear la utilización de los descuentos en los locales del shopping mediante reportes gerenciales brindados por el sistema a desarrollar. 

Los **Dueños** de los locales ingresarán al sistema las promociones de sus locales, y luego el **Administrador** será el encargado de “aprobarlas” o “denegarlas” (esto en función de si cumplen o no con la política comercial global del shopping).  

Los **Dueños** de locales por su parte serán los encargados de “aceptar” las solicitudes de descuento generadas por un cliente o “rechazar” las mismas.  

Cada *promoción* u oferta tendrá ciertos atributos como: el *rango de fechas vigente* de la oferta, la *categoría de cliente* que puede acceder a dicha oferta, el *día de la semana* que está vigente dicha oferta y el *texto descriptivo* de la oferta (por ej.: ‘20% pago contado’, ‘80% de descuento en la segunda unidad’, ‘2x1 para mismo producto’, etc.).  

Respecto del *día de la semana*, se indica, de lunes a domingo, se debe definir como válida la oferta el o los días correspondientes y como no válidas aquellos que no se puede aplicar la oferta. (siempre dentro del rango de fechas de la vigencia de la oferta).  

Los **Dueños** de locales, por último, podrán monitorear el uso de sus promociones por parte de los **Clientes** mediante un reporte brindado por el sitio.  

Los **Clientes** para acceder al sistema de ofertas del shopping, deberán primero crear su cuenta en el sistema registrándose.  

Para ello usarán un mail como usuario y una contraseña a elección.  

Los **Clientes** podrán consultar los locales y sus promociones a través del software, dicha consulta podrán hacerla estando o no registrados.  

Cuando un **Cliente** decide comprar en un local del shopping, deberá ante todo registrarse en el sitio web lo cual le dará acceso a las promociones. Luego, debe ingresar el código del local al sistema (cada local tiene asignado un código único dentro del sistema) y luego elegir la promoción deseada (el sistema mostrará al cliente sólo las promociones que estén vigentes, que correspondan a un día válido y considerando también la categoría del cliente).  

Cada **Cliente** tiene una categoría (‘Inicial’, ‘Medium’, ‘Premium’) que le es asignada automáticamente por el sistema en función del uso en el tiempo de las promociones del shopping y de la cantidad de promocione  utilizadas. Siempre que se registra el cliente posee  la  categoría  de Inicial,  luego  al  comenzar  a  comprar  en  el  shopping  con  las promociones irá creciendo en categoría.  

Debe definirse un rango de promociones que el cliente haya tomado para cambiar de categoría. El rango queda a criterio de los desarrolladores del sitio web. 

De acuerdo con la categoría a la que pertenece un cliente, podrá acceder a determinadas promociones.  

Respecto de las categorías de los clientes (donde un cliente en un momento dado puede ser ‘Inicial’ o ‘Medium’ o ‘Premium’), las promociones, cuando se crean, tienen asociada una categoría de cliente, donde el cliente puede acceder a las promociones de su categoría o inferior.  Por ejemplo, si un cliente es categoría ‘Premium’, puede acceder a todas las promociones del shopping; si un cliente es ‘Medium’ podrá acceder a las promociones de su categoría y a las promociones para clientes ‘Iniciales’; y si un cliente es ‘Inicial’ solo podrá acceder a las promociones dirigidas a clientes iniciales.  

Por  último,  los  **Clientes**  pueden  visualizar  en  cualquier  momento  a  las  novedades publicadas por el administrador y que estén vigentes en el shopping, también dependiendo de su categoría (es decir, una novedad dirigida a clientes de categoría ‘Inicial’ es vista por todos los clientes; una novedad dirigida a clientes ‘Medium’ es vista por esta categoría más los ‘Premium’; y una novedad dirigida a clientes ‘Premium’, puede ser vista solo por clientes ‘Premium’). 

**Requisitos Mínimos del Sitio Web** 

El sitio web debe cumplir como mínimo con los siguientes requisitos según la categoría de usuario: 

**Administrador:** 

Deberá poder:  

- crear, editar y eliminar locales.  
- validar cuentas de dueños de locales.  
- aprobar o denegar una solicitud de descuento de un local.  
- crear, editar y eliminar novedades del shopping.  
- ver reportes acerca de la utilización de los descuentos.  

**Dueños de los locales** 

Podrán: 

- crear y eliminar descuentos en su propio local. No se permite la edición para evitar 

consideraciones de publicidad engañosa. En caso de cometer errores en la carga, deberá eliminar la promoción. 

- aceptar o rechazar una solicitud de descuento de un cliente.  
- ver la cantidad de clientes que usaron un descuento.  

**Clientes**  

Como cliente, podrá: 

- registrarse en el sistema para acceder a las ofertas del shopping.  
- buscar descuentos en los locales del shopping.  
- ingresar el código de un local y elegir un descuento disponible.  
- ver las novedades del shopping. 

**Usuarios no Registrados:** 

Podrán: 

- Visualizar todas las promociones de todos los locales del shopping para todas las categorías de clientes. 
- Poder acceder al email de contacto para poder comunicarse con el administrador del sitio web 

Como se detalla en párrafos anteriores estos requisitos son mínimos luego pueden ser ampliados por los desarrolladores según consideren necesario. 

**Reglas de negocio**  



|# |Descripción |
| - | - |
|1 |Cada local tendrá un código único, numérico y secuencial que lo identifica dentro del sistema |
|2 |Cada promoción o descuento tendrá un código único, numérico y secuencial que lo identifica dentro del sistema |
|3 |Cada descuento de un local tiene días de la semana en que se aplica. Se indica de lunes a domingo, definiendo como válido el día que corresponde aplicar la oferta y el resto como no válidos.  |
|4 |Cada descuento tiene asignado una categoría mínima de cliente a la que pueden acceder |
|5 |Las categorías de clientes son: ‘Inicial’, ‘Medium’, ‘Premium’. |
|6 |Cada  cliente  solo  podrá  ver  los  descuentos  a  los  que  tiene  acceso  según  su categoría o inferior. Por ejemplo, si un cliente es categoría ‘Premium’ puede acceder a todas las promociones del shopping; si un cliente es ‘Medium’ podrá acceder a las promociones de su categoría y a las promociones para clientes ‘Iniciales’;  y  si  un  cliente  es  ‘Inicial’  solo  podrá  acceder  a  las  promociones dirigidas a clientes iniciales |
|7 |La categoría del cliente será Inicial al registrarse en el sitio. |
|8 |El cliente subirá de categoría dependiendo de la cantidad de ofertas o promociones que haya adquirido efectivamente en los locales del shopping, en cuyo caso queda para el desarrollador del sitio definir dicha cantidad. |
|9 |Un cliente puede utilizar un descuento vigente al que tenga acceso una sola vez como máximo. |
|10 |La conversión  de categoría de cliente se hace automáticamente por parte del sistema luego de determinada cantidad de compras del cliente (aceptación del descuento  por  parte  del  local).  El  sistema  determinará  si  hay  un  cambio  de categoría evaluando el comportamiento del cliente en el último semestre. |
|11 |Sólo se conocerá el detalle de los clientes que usaron los descuentos, pero no los detalles de la transacción de compra del cliente. |
|12 |Las novedades expirarán automáticamente pasado un intervalo de tiempo. |
|13 |Los clientes pueden acceder en cualquier momento a las novedades publicadas por el administrador y que estén vigentes en el shopping, dependiendo de su categoría (es decir, una novedad dirigida a clientes de categoría ‘Inicial’ es vista por todos los clientes; una novedad dirigida a clientes ‘Medium’ es vista por esta categoría más los ‘Premium’; y una novedad dirigida a clientes ‘Premium’, puede ser vista solo por clientes ‘Premium’ |
|14 |Los usuarios no registrados en el sitio pueden visualizar todas las promociones publicadas en el shopping. Podrán además realizar diferentes filtros para contribuir a una correcta visualización. |

**Modelo de Datos Mínimo** 

![](Aspose.Words.da2fbb18-004a-4a09-8a66-40d876837536.003.jpeg)

El modelo de datos presentado es el mínimo indispensable para desarrollar el sitio web, luego los desarrolladores tienen la  posibilidad de incrementar la cantidad de tablas, agregar bases de datos adicionales y atributos según las necesidades de implementación. 
