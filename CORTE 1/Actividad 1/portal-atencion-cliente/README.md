# Portal de Atencion al Cliente

Sistema web integral de atencion al cliente que opera 24 horas al dia, 7 dias a la semana. Permite a los clientes registrar solicitudes de forma autonoma, a los agentes gestionar y dar seguimiento a los casos, y a la administracion analizar la informacion mediante reportes e inteligencia artificial.

---

## Indice

1. Descripcion general del sistema
2. Objetivos
3. Arquitectura del sistema
4. Tecnologias utilizadas
5. Requisitos para ejecutar
6. Instalacion paso a paso
7. Credenciales de prueba
8. Modulos del sistema
9. Base de datos
10. Flujo de funcionamiento
11. Seguridad implementada
12. Integracion con Inteligencia Artificial
13. Estructura completa del proyecto
14. Guia para personalizar y extender el sistema
15. ERS - Especificaciones de Requerimientos de Software
16. Historias de Usuario

---

## 1. Descripcion general del sistema

Este portal web fue disenado para empresas que requieren un canal digital de atencion al cliente disponible las 24 horas. Los clientes pueden registrar sus solicitudes en cualquier momento sin depender de horarios de atencion presencial. El sistema almacena toda la informacion en una base de datos MySQL, permite a los agentes gestionar los casos con seguimiento detallado, y genera reportes mensuales con indicadores para la toma de decisiones administrativas.

El sistema diferencia claramente entre tres tipos de usuarios:

- **Clientes**: registran solicitudes y consultan su estado.
- **Agentes de atencion**: gestionan, filtran y dan seguimiento a las solicitudes.
- **Administradores**: supervisan el sistema, gestionan usuarios y generan reportes.

---

## 2. Objetivos

### Objetivo general

Disenar e implementar un portal web de atencion al cliente que permita recibir, registrar, gestionar, almacenar y reportar las solicitudes realizadas por los clientes, garantizando disponibilidad 24/7, integridad de la informacion y herramientas de analisis para la toma de decisiones.

### Objetivos especificos

- Implementar una interfaz web accesible para que los clientes registren solicitudes a cualquier hora.
- Disenar una base de datos relacional en MySQL que almacene la informacion de forma persistente y segura.
- Desarrollar un modulo de administracion para gestionar solicitudes con estados y seguimientos.
- Implementar un modulo de reportes que genere estadisticas e indicadores mensuales.
- Integrar inteligencia artificial (Google Gemini) para analizar datos y generar recomendaciones de negocio.
- Establecer mecanismos de autenticacion, control de acceso y validacion de datos.

---

## 3. Arquitectura del sistema

El sistema utiliza una arquitectura de tres capas (3-tier), estandar para aplicaciones web:

### Diagrama de Arquitectura

```
  +=========================================================================+
  |                     PRESENTACION (Frontend)                             |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  |   Bootstrap 5     |  |   JavaScript      |  |   HTML5 / CSS3    |   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  [ Paginas PHP: Login | Panel | Formularios | Reportes | IA ]         |
  +========================================+================================+
                                           |
                                      HTTP/HTTPS
                                           |
                                           v
  +========================================+================================+
  |                      LOGICA (Backend)                                  |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  | Autenticacion     |  | Validacion        |  | Sesiones          |   |
  |  | y Control Acceso  |  | de Datos          |  | y CSRF            |   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  | Generacion de     |  | Integracion       |  | Consultas         |   |
  |  | Reportes          |  | IA (Gemini)       |  | PDO Prepared      |   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  +========================================+================================+
                                           |
                                           v
  +=========================================================================+
  |                         DATOS (MySQL)                                   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  | usuarios          |  | solicitudes       |  | categorias        |   |
  |  +-------------------+  +-------------------+  +-------------------+   |
  |  +-------------------+  +-------------------+                          |
  |  | seguimientos      |  | reportes_mensuales|                          |
  |  +-------------------+  +-------------------+                          |
  +=========================================================================+
```

### Descripcion por Capa

| Capa | Componente | Tecnologia | Funcion |
|------|-----------|------------|---------|
| **Presentacion** | Bootstrap 5 | CSS Framework | Diseno responsivo y componentes UI |
| **Presentacion** | JavaScript | ES6+ | Interactividad, validacion del lado del cliente |
| **Presentacion** | PHP Templates | PHP 8.x | Generacion dinamica de paginas HTML |
| **Logica** | auth.php | PHP | Autenticacion, sesiones, control de acceso |
| **Logica** | funciones.php | PHP | Funciones auxiliares del sistema |
| **Logica** | ia_service.php | PHP + API | Integracion con Google Gemini + fallback local |
| **Logica** | PDO | PHP Extension | Consultas preparadas seguras a la BD |
| **Datos** | MySQL 8.x | InnoDB | Almacenamiento relacional con integridad referencial |
| **Datos** | 5 tablas | SQL | usuarios, categorias, solicitudes, seguimientos, reportes_mensuales |

---

## 4. Tecnologias utilizadas

| Tecnologia | Version | Funcion |
|-----------|---------|---------|
| PHP | 8.x | Backend / logica de servidor |
| MySQL | 8.x | Base de datos relacional |
| Apache | 2.4 | Servidor web (via XAMPP) |
| HTML5 | - | Estructura de paginas |
| CSS3 | - | Estilos visuales |
| JavaScript | ES6+ | Interactividad del cliente |
| Bootstrap | 5.x | Framework CSS responsivo |
| Google Gemini API | - | Inteligencia artificial |
| PDO | - | Conexion segura a la base de datos |

---

## 5. Requisitos para ejecutar

- **XAMPP** (Apache 2.4 + MySQL 8.x + PHP 8.x) instalado y funcionando
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexion a internet (para cargar Bootstrap y los iconos desde CDN)
- Cuenta en Google AI Studio (opcional, para API key de Gemini)

---

## 6. Instalacion paso a paso

### Paso 1: Clonar el repositorio

`ash
git clone https://github.com/JosdaTara/Portal-Clientes.git
`

Copiar la carpeta portal-atencion-cliente dentro de C:\xampp\htdocs\.

### Paso 2: Iniciar XAMPP

Abrir el XAMPP Control Panel y dar clic en **Start** tanto en Apache como en MySQL.

### Paso 3: Crear la base de datos

1. Abrir http://localhost/phpmyadmin
2. Ir a la pestana **SQL**
3. Pegar el contenido del archivo portal_atencion_cliente.sql
4. Dar clic en **Continuar**

### Paso 4: Configurar la API key de Gemini (opcional)

1. Copiar config/api_key.example.php como config/api_key.php
2. Obtener una API key gratis en https://aistudio.google.com/apikey
3. Pegarla en config/api_key.php:

`php
define('GEMINI_API_KEY', 'AIzaSy...');
`

### Paso 5: Acceder al portal

`
http://localhost/portal-atencion-cliente/
`

---

## 7. Credenciales de prueba

| Email | Contrasena | Rol |
|-------|-----------|-----|
| admin@portal.com | admin123 | Administrador |
| carlos.ruiz@portal.com | admin123 | Agente |
| maria.lopez@email.com | admin123 | Cliente |

---

## 8. Modulos del sistema

### Modulo de Cliente
- Registro de solicitudes (consultas, reclamos, soporte, sugerencias, administrativas)
- Consulta de estado por numero de caso
- Historial de solicitudes propias
- Asistente IA para resolver dudas sobre sus casos

### Modulo de Atencion / Administracion
- Gestion y filtrado de todas las solicitudes
- Actualizacion de estados (Pendiente, En proceso, Atendida, Cerrada)
- Registro de seguimientos y observaciones
- Dashboard con metricas de desempeno

### Modulo de Reportes
- Reporte mensual con indicadores clave
- Solicitudes por categoria, prioridad y estado
- Tiempo promedio de atencion
- Tendencias diarias y distribucion por hora

### Modulo de Inteligencia Artificial
- Analisis automatico de datos con Google Gemini
- Consultas personalizadas sobre patrones y tendencias
- Recomendaciones accionables para la toma de decisiones
- Modo fallback con analisis local cuando no hay conexion a la IA

### Modulo de Administracion
- Gestion de usuarios (CRUD)
- Gestion de categorias de solicitudes
- Control de roles y permisos

---

## 9. Base de datos

El sistema utiliza 5 tablas principales:

- **usuarios** - Clientes, agentes y administradores
- **categorias** - Tipos de solicitudes
- **solicitudes** - Registro de todas las solicitudes
- **seguimientos** - Historial de acciones por solicitud
- **reportes_mensuales** - Reportes generados para referencia

### Relaciones

`
usuarios (1) ----< (N) solicitudes
categorias (1) ----< (N) solicitudes
solicitudes (1) ----< (N) seguimientos
usuarios (1) ----< (N) seguimientos
`

---

## 10. Flujo de funcionamiento

1. El cliente registra una solicitud y recibe un numero de caso unico.
2. Un agente revisa, cambia el estado y registra seguimientos.
3. El cliente consulta el estado de su caso en cualquier momento.
4. La administracion genera reportes mensuales con indicadores.
5. La IA analiza los datos y genera recomendaciones de negocio.

---

## 11. Seguridad implementada

- **Contrasenas**: hash con bcrypt via password_hash() / password_verify()
- **Inyeccion SQL**: todas las consultas usan PDO con prepared statements
- **XSS**: toda salida se escapa con htmlspecialchars()
- **CSRF**: tokens aleatorios en todos los formularios
- **Control de acceso**: funciones que verifican autenticacion y rol
- **Integridad referencial**: llaves foranesas en MySQL

---

## 12. Integracion con Inteligencia Artificial

Se utiliza la API de **Google Gemini** (modelo gemini-3.6-flash):

- Tier gratuito: 15 req/min, 1M tokens/dia
- No requiere tarjeta de credito
- Analisis de datos de solicitudes y recomendaciones
- Modo fallback local si no hay conexion

### Configuracion

1. Obtener key gratis en https://aistudio.google.com/apikey
2. Crear config/api_key.php desde config/api_key.example.php
3. Pegar la key

---

## 13. Estructura del proyecto

### Arbol de Directorios

```
portal-atencion-cliente/
│
├── config/                          CONFIGURACION DEL SISTEMA
│   ├── conexion.php                 Conexion PDO a MySQL
│   ├── api_key.example.php          Plantilla para API key de Gemini
│   └── api_key.php                  API key real (excluido de git)
│
├── includes/                        FUNCIONES COMPARTIDAS
│   ├── auth.php                     Autenticacion y control de acceso
│   ├── funciones.php                Funciones auxiliares del sistema
│   ├── ia_service.php               Integracion con Google Gemini
│   ├── header.php                   Cabecera HTML comun (navbar)
│   └── footer.php                   Pie de pagina comun
│
├── auth/                            AUTENTICACION
│   ├── login.php                    Formulario y proceso de login
│   ├── logout.php                   Cierre de sesion
│   └── registrar.php                Formulario de registro de clientes
│
├── modulo_cliente/                  PANEL DEL CLIENTE
│   ├── index.php                    Dashboard del cliente
│   ├── registrar_solicitud.php      Formulario nueva solicitud
│   ├── consultar_solicitud.php      Buscar por numero de caso
│   ├── historial.php                Historial de solicitudes propias
│   └── ia_cliente.php               Asistente IA para clientes
│
├── modulo_atencion/                 GESTION DE SOLICITUDES
│   ├── index.php                    Lista con filtros y busqueda
│   └── detalle_solicitud.php        Detalle + seguimientos + cambio estado
│
├── modulo_reportes/                 REPORTES Y ESTADISTICAS
│   └── index.php                    Reporte mensual con indicadores
│
├── modulo_ia/                       INTELIGENCIA ARTIFICIAL
│   ├── index.php                    Dashboard de analisis IA
│   └── consultar.php                Consulta personalizada con IA
│
├── modulo_admin/                    ADMINISTRACION
│   ├── usuarios.php                 Gestion CRUD de usuarios
│   └── categorias.php              Gestion CRUD de categorias
│
├── assets/                          RECURSOS ESTATICOS
│   ├── css/
│   │   └── estilo.css               Estilos personalizados
│   └── js/
│       └── app.js                   Funciones JavaScript
│
├── index.php                        Pagina principal (redirige a login)
├── portal_atencion_cliente.sql      Script SQL de la base de datos
├── README.md                        Documentacion del proyecto
└── .gitignore                       Archivos excluidos de git
```

### Tabla de Archivos por Modulo

| Modulo | Archivos | Funcion Principal |
|--------|----------|-------------------|
| **config/** | 3 archivos | Conexion a BD y API key |
| **includes/** | 5 archivos | Funciones compartidas, header/footer, IA |
| **auth/** | 3 archivos | Login, logout, registro |
| **modulo_cliente/** | 5 archivos | Solicitudes, consulta, historial, IA cliente |
| **modulo_atencion/** | 2 archivos | Gestion y detalle de solicitudes |
| **modulo_reportes/** | 1 archivo | Reportes mensuales |
| **modulo_ia/** | 2 archivos | Analisis y consultas IA |
| **modulo_admin/** | 2 archivos | Usuarios y categorias |
| **assets/** | 2 archivos | CSS y JavaScript |
| **Raiz** | 4 archivos | index.php, SQL, README, .gitignore |

---

## 14. Guia para extender el sistema

- **Nuevas categorias**: desde modulo_admin/categorias.php
- **Nuevos campos**: ALTER TABLE + actualizar formularios y consultas
- **Cambiar modelo de IA**: editar funcion consultarGemini() en ia_service.php
- **Migrar a produccion**: subir a hosting, actualizar rutas, configurar HTTPS

---

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
