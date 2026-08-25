## 15. ERS - Especificaciones de Requerimientos de Software

### 15.1 Vision del Proyecto

El **Portal de Atencion al Cliente** nace como proyecto academico en la Universidad Tecnologica de San Jose del Tunar (UTS), curso de Desarrollo de Aplicaciones Empresariales, 6to semestre. Su proposito es demostrar las competencias adquiridas en desarrollo web full-stack, bases de datos, seguridad informatica e integracion de inteligencia artificial.

**Vision actual (academica)**:

El sistema opera como prototipo funcional en entorno local (XAMPP), demostrando los fundamentos de una plataforma real de atencion al cliente. Permite validar la arquitectura, los flujos de negocio y la integracion con IA en un contexto controlado.

**Vision futura (expansion a produccion)**:

El portal esta disenado para escalarse a una solucion empresarial real. Las siguientes mejoras estan contempladas para futuras iteraciones:

| Fase | Descripcion | Plazo estimado |
|------|-------------|----------------|
| Fase 2 | Despliegue en hosting con dominio propio, HTTPS, base de datos en la nube | 1-2 meses |
| Fase 3 | Notificaciones por email al cambiar estado de solicitud | 2-3 meses |
| Fase 4 | Aplicacion movil nativa (React Native) o PWA | 3-4 meses |
| Fase 5 | Sistema de chat en tiempo real entre cliente y agente | 4-6 meses |
| Fase 6 | Integracion con WhatsApp Business API | 6+ meses |
| Fase 7 | Multi-tenant: multiples empresas en una sola instancia | 6+ meses |

### 15.2 Alcance del Proyecto

#### Alcance actual (Corte 1 - Prototipo funcional)

| Componente | Estado | Descripcion |
|-----------|--------|-------------|
| Autenticacion | Completo | Login, registro, roles (Cliente, Agente, Admin) |
| Gestion de solicitudes | Completo | CRUD con estados, seguimientos, filtros |
| Reportes mensuales | Completo | Indicadores por categoria, prioridad, tiempo |
| Inteligencia Artificial | Completo | Analisis con Gemini API + fallback local |
| Seguridad | Completo | Bcrypt, PDO, XSS, CSRF, control de acceso |
| Base de datos | Completo | 5 tablas, relaciones, datos de prueba |

#### Alcance fuera del proyecto actual (futuro)

- Despliegue en servidor de produccion
- Sistema de notificaciones push/email
- API REST para integracion con aplicaciones moviles
- Chat en tiempo real (WebSockets)
- Sistema de encuestas de satisfaccion post-atencion
- Dashboard administrativo con graficos interactivos (Chart.js / D3.js)
- Exportacion de reportes a PDF y Excel
- Autenticacion con OAuth2 (Google, Microsoft)
- Pruebas automatizadas (PHPUnit, Selenium)
- CI/CD con GitHub Actions

### 15.3 Requisitos Funcionales

#### RF-01: Gestion de Usuarios

| ID | Requisito | Prioridad | Estado |
|----|-----------|-----------|--------|
| RF-01.1 | El sistema debe permitir el registro de nuevos clientes con nombre, email y contrasena | Alta | Cumplido |
| RF-01.2 | El sistema debe autenticar usuarios con email y contrasena | Alta | Cumplido |
| RF-01.3 | El sistema debe diferenciar tres roles: Cliente, Agente, Administrador | Alta | Cumplido |
| RF-01.4 | El administrador debe poder crear, editar y eliminar usuarios | Alta | Cumplido |
| RF-01.5 | El sistema debe cerrar sesion automaticamente tras inactividad | Media | Pendiente |
| RF-01.6 | El sistema debe enviar email de confirmacion al registrarse | Baja | Pendiente |

#### RF-02: Gestion de Solicitudes

| ID | Requisito | Prioridad | Estado |
|----|-----------|-----------|--------|
| RF-02.1 | El cliente debe poder crear solicitudes con titulo, descripcion, categoria y prioridad | Alta | Cumplido |
| RF-02.2 | El sistema debe asignar un numero de caso unico a cada solicitud | Alta | Cumplido |
| RF-02.3 | El cliente debe poder consultar el estado de su solicitud por numero de caso | Alta | Cumplido |
| RF-02.4 | El agente debe poder filtrar solicitudes por estado, categoria, prioridad y fecha | Alta | Cumplido |
| RF-02.5 | El agente debe poder cambiar el estado de una solicitud (Pendiente, En proceso, Atendida, Cerrada) | Alta | Cumplido |
| RF-02.6 | El agente debe poder agregar seguimientos con observaciones | Alta | Cumplido |
| RF-02.7 | El sistema debe validar que un cliente solo vea sus propias solicitudes | Alta | Cumplido |
| RF-02.8 | El cliente debe poder ver el historial completo de seguimientos de su solicitud | Media | Cumplido |

#### RF-03: Reportes

| ID | Requisito | Prioridad | Estado |
|----|-----------|-----------|--------|
| RF-03.1 | El sistema debe generar reportes mensuales con indicadores clave | Alta | Cumplido |
| RF-03.2 | El reporte debe mostrar solicitudes por categoria, prioridad y estado | Alta | Cumplido |
| RF-03.3 | El reporte debe calcular el tiempo promedio de atencion | Alta | Cumplido |
| RF-03.4 | El reporte debe mostrar tendencias diarias y distribucion por hora | Media | Cumplido |
| RF-03.5 | El sistema debe permitir exportar reportes a PDF | Baja | Pendiente |
| RF-03.6 | El sistema debe permitir exportar reportes a Excel/CSV | Baja | Pendiente |

#### RF-04: Inteligencia Artificial

| ID | Requisito | Prioridad | Estado |
|----|-----------|-----------|--------|
| RF-04.1 | El sistema debe analizar automaticamente los datos de solicitudes con IA | Alta | Cumplido |
| RF-04.2 | El usuario debe poder hacer consultas personalizadas sobre patrones y tendencias | Alta | Cumplido |
| RF-04.3 | La IA debe generar recomendaciones accionables para la toma de decisiones | Alta | Cumplido |
| RF-04.4 | El sistema debe funcionar con fallback local cuando no hay conexion a la API | Media | Cumplido |
| RF-04.5 | El cliente debe poder consultar a la IA sobre sus propios casos | Media | Cumplido |

#### RF-05: Seguridad

| ID | Requisito | Prioridad | Estado |
|----|-----------|-----------|--------|
| RF-05.1 | Las contrasenas deben almacenarse con hash bcrypt | Alta | Cumplido |
| RF-05.2 | Todas las consultas SQL deben usar prepared statements | Alta | Cumplido |
| RF-05.3 | Toda salida de datos debe escaparse para prevenir XSS | Alta | Cumplido |
| RF-05.4 | Todos los formularios deben incluir tokens CSRF | Alta | Cumplido |
| RF-05.5 | El sistema debe validar que cada usuario acceda solo a sus datos | Alta | Cumplido |

### 15.4 Requisitos No Funcionales

| ID | Requisito | Metrica | Estado |
|----|-----------|---------|--------|
| RNF-01 | Disponibilidad | El sistema debe estar disponible 24/7 en entorno local | Cumplido |
| RNF-02 | Tiempo de respuesta | Las paginas deben cargar en menos de 3 segundos | Cumplido |
| RNF-03 | Escalabilidad | La arquitectura debe permitir agregar nuevos modulos sin reescribir | Cumplido |
| RNF-04 | Usabilidad | La interfaz debe ser intuitiva y responsiva (Bootstrap 5) | Cumplido |
| RNF-05 | Portabilidad | El sistema debe funcionar en cualquier navegador moderno | Cumplido |
| RNF-06 | Mantenibilidad | El codigo debe seguir estandares PSR y estar documentado | Cumplido |
| RNF-07 | Seguridad | Cumplir con OWASP Top 10 en categorias aplicables | Cumplido |

---

## 16. Historias de Usuario

### HU-01: Registro de Cliente

**Como** cliente nuevo,
**quiero** poder registrarme en el portal con mi nombre, email y contrasena,
**para que** pueda acceder al sistema y registrar solicitudes de atencion.

**Criterios de aceptacion:**
- El formulario solicita nombre completo, email y contrasena
- La contrasena debe tener al menos 6 caracteres
- El email no puede estar ya registrado en el sistema
- Al registrarse, el usuario es redirigido al panel de cliente
- La contrasena se almacena con hash bcrypt

---

### HU-02: Inicio de Sesion

**Como** usuario registrado (cliente, agente o administrador),
**quiero** poder iniciar sesion con mi email y contrasena,
**para que** pueda acceder a las funcionalidades de mi rol.

**Criterios de aceptacion:**
- El sistema valida email y contrasena contra la base de datos
- Si las credenciales son correctas, redirige al panel correspondiente segun el rol
- Si son incorrectas, muestra un mensaje de error sin revelar cual campo fallo
- La sesion se mantiene hasta que el usuario cierre sesion

---

### HU-03: Registro de Solicitud por Cliente

**Como** cliente,
**quiero** poder registrar una solicitud indicando titulo, descripcion, categoria y prioridad,
**para que** mi caso quede registrado y pueda ser atendido por un agente.

**Criterios de aceptacion:**
- El formulario muestra los campos: titulo, descripcion, categoria (select), prioridad (select)
- El sistema asigna automaticamente un numero de caso unico
- La solicitud se guarda con estado "Pendiente"
- El cliente ve confirmacion con su numero de caso
- La solicitud aparece en el historial del cliente

---

### HU-04: Consulta de Estado de Solicitud

**Como** cliente,
**quiero** poder consultar el estado de mi solicitud usando el numero de caso,
**para que** sepa en que etapa se encuentra mi atencion.

**Criterios de aceptacion:**
- El cliente ingresa el numero de caso en un campo de busqueda
- El sistema muestra el estado actual (Pendiente, En proceso, Atendida, Cerrada)
- Se muestran los seguimientos registrados por el agente
- Si el numero no existe, se muestra un mensaje informativo
- Solo se muestran solicitudes propias del cliente

---

### HU-05: Gestion de Solicitudes por Agente

**Como** agente de atencion,
**quiero** poder ver todas las solicitudes con filtros por estado, categoria, prioridad y fecha,
**para que** pueda organizar mi trabajo y atender los casos de forma eficiente.

**Criterios de aceptacion:**
- Se muestra una tabla con todas las solicitudes del sistema
- El agente puede filtrar por: estado, categoria, prioridad y rango de fechas
- Los filtros se combinan (AND)
- El agente puede hacer clic en una solicitud para ver su detalle
- Se muestra el numero de total de resultados

---

### HU-06: Actualizacion de Estado y Seguimiento

**Como** agente de atencion,
**quiero** poder cambiar el estado de una solicitud y agregar seguimientos con observaciones,
**para que** el cliente pueda ver el avance de su caso.

**Criterios de aceptacion:**
- Desde el detalle de la solicitud, el agente puede cambiar el estado
- El agente puede agregar seguimientos con texto libre
- Cada seguimiento queda registrado con fecha, hora y nombre del agente
- El historial de seguimientos se muestra en orden cronologico
- El cambio de estado es inmediato y visible para el cliente

---

### HU-07: Generacion de Reportes Mensuales

**Como** administrador,
**quiero** poder generar reportes mensuales con indicadores clave,
**para que** pueda tomar decisiones basadas en datos reales del sistema.

**Criterios de aceptacion:**
- El reporte muestra: total de solicitudes, por categoria, por prioridad, por estado
- Se calcula el tiempo promedio de atencion
- Se muestran tendencias diarias y distribucion por hora
- Los datos son reales y se obtienen de la base de datos
- El reporte se puede visualizar en pantalla

---

### HU-08: Analisis con Inteligencia Artificial

**Como** administrador o agente,
**quiero** poder hacer consultas en lenguaje natural sobre los datos del sistema,
**para que** la IA me proporcione analisis, patrones y recomendaciones.

**Criterios de aceptacion:**
- El usuario escribe una pregunta en un campo de texto
- La IA analiza los datos reales de solicitudes del sistema
- La respuesta incluye analisis, hallazgos y recomendaciones
- Si no hay conexion a la API, se usa el modo fallback local
- El historial de consultas se muestra en la pagina

---

### HU-09: Asistente IA para Clientes

**Como** cliente,
**quiero** poder consultar a la inteligencia artificial sobre mis propios casos,
**para que** obtenga informacion resumida de mi historial de atencion.

**Criterios de aceptacion:**
- El cliente accede a una seccion de IA dentro de su panel
- La IA solo tiene acceso a las solicitudes del cliente actual
- El cliente puede hacer preguntas como "Cuantas solicitudes tengo abiertas?"
- La IA responde con datos reales del historial del cliente
- Se muestran preguntas sugeridas para facilitar la interaccion

---

### HU-10: Administracion de Usuarios

**Como** administrador,
**quiero** poder crear, editar y eliminar usuarios del sistema,
**para que** pueda gestionar quienes tienen acceso al portal.

**Criterios de aceptacion:**
- El administrador ve una lista con todos los usuarios registrados
- Puede crear nuevos usuarios indicando nombre, email, contrasena y rol
- Puede editar la informacion de un usuario existente
- Puede eliminar un usuario (con confirmacion)
- No puede eliminar su propia cuenta de administrador

---

### HU-11: Administracion de Categorias

**Como** administrador,
**quiero** poder gestionar las categorias de solicitudes (crear, editar, eliminar),
**para que** el sistema se adapte a los tipos de atencion que ofrece la empresa.

**Criterios de aceptacion:**
- El administrador ve la lista de categorias activas
- Puede crear nuevas categorias con nombre y descripcion
- Puede editar categorias existentes
- Puede eliminar categorias que no tengan solicitudes asociadas
- Las categorias aparecen en el select del formulario de solicitud

---

### HU-12: Cambio de Contrasena

**Como** usuario registrado,
**quiero** poder cambiar mi contrasena desde mi perfil,
**para que** mantenga mi cuenta segura.

**Criterios de aceptacion:**
- El formulario solicita la contrasena actual y la nueva contrasena
- La contrasena actual se valida contra la base de datos
- La nueva contrasena debe tener al menos 6 caracteres
- Al cambiarla, se cierra la sesion y se redirige al login
- Se muestra confirmacion del cambio exitoso

---

Proyecto academico - Universidad Tecnologica de San Jose del Tunar (UTS)
