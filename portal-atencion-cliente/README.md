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

```
+---------------------------------------------------+
|          CAPA DE PRESENTACION (Frontend)          |
|                                                   |
|   HTML5 + CSS3 + JavaScript + Bootstrap 5         |
|   Plantillas PHP (vistas del sistema)             |
+--------------------------+------------------------+
                           | HTTP/HTTPS
                           v
+---------------------------------------------------+
|          CAPA DE LOGICA (Backend)                 |
|                                                   |
|   PHP 8.x (lenguaje de servidor)                 |
|   Funciones de validacion, autenticacion          |
|   Generacion de reportes e integracion IA         |
|   Consultas preparadas via PDO                    |
+--------------------------+------------------------+
                           | SQL (consultas preparadas)
                           v
+---------------------------------------------------+
|          CAPA DE DATOS (MySQL)                    |
|                                                   |
|   MySQL 8.x (motor InnoDB)                       |
|   Base de datos: portal_atencion_cliente           |
|   Almacenamiento persistente de toda la info      |
+---------------------------------------------------+
```

**Flujo de una peticion:**

1. El navegador del usuario envia una peticion HTTP al servidor Apache.
2. Apache procesa la peticion y ejecuta el archivo PHP correspondiente.
3. El script PHP aplica la logica de negocio, valida datos, autentica usuarios.
4. Se ejecutan consultas SQL contra MySQL usando PDO con consultas preparadas.
5. Los resultados se renderizan en HTML y se devuelven al navegador.

**Por que esta arquitectura:**

- Es nativa de XAMPP (Apache + MySQL + PHP integrados).
- Permite separacion clara entre presentacion, logica y datos.
- Es ampliamente documentada y mantenida por la comunidad.
- Es escalable: puede migrarse a un servidor VPS o hosting en el futuro.

---

## 4. Tecnologias utilizadas

| Tecnologia | Version | Funcion | Justificacion |
|-----------|---------|---------|---------------|
| PHP | 8.x | Backend / logica de servidor | Nativo de XAMPP, amplia comunidad, facil integracion con MySQL |
| MySQL | 8.x | Base de datos relacional | Incluido en XAMPP, soporta relaciones, transacciones e integridad referencial |
| Apache | 2.4 | Servidor web | Incluido en XAMPP, configura rutas y archivos PHP |
| HTML5 | - | Estructura de paginas | Estandar para contenido web |
| CSS3 | - | Estilos visuales | Diseno responsivo y presentacion |
| JavaScript | ES6+ | Interactividad del cliente | Validaciones en formulario, dinamismo de interfaz |
| Bootstrap | 5.x | Framework CSS | Diseno responsivo rapido, componentes listos |
| Google Gemini API | - | Inteligencia artificial | Analisis de datos, recomendaciones de negocio, tier gratuito |
| PDO | - | Capa de acceso a BD | Abstraccion de base de datos, consultas preparadas (seguridad) |
| phpMyAdmin | - | Administracion visual de MySQL | Incluido en XAMPP, facilita diseno y mantenimiento de la BD |

---

## 5. Requisitos para ejecutar

### Requisitos minimos

- **XAMPP** (Apache 2.4 + MySQL 8.x + PHP 8.x) instalado y funcionando
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexion a internet (para cargar Bootstrap y los iconos de Bootstrap Icons desde CDN)

### Requisitos opcionales

- Cuenta en Google AI Studio (https://aistudio.google.com) para obtener una API key gratuita de Gemini y activar las funcionalidades de IA.

---

## 6. Instalacion paso a paso

### Paso 1: Clonar o copiar el proyecto

```bash
git clone https://github.com/JosdaTara/Portal-Clientes.git
```

Copiar la carpeta `portal-atencion-cliente` dentro de `C:\xampp\htdocs\`.

La ruta final debe ser: `C:\xampp\htdocs\portal-atencion-cliente\`

### Paso 2: Iniciar XAMPP

Abrir el XAMPP Control Panel y dar clic en **Start** tanto en Apache como en MySQL.

### Paso 3: Crear la base de datos

1. Abrir el navegador y acceder a `http://localhost/phpmyadmin`
2. Ir a la pestana **SQL**
3. Pegar todo el contenido del archivo `portal_atencion_cliente.sql`
4. Dar clic en **Continuar** o **Go** para ejecutar las sentencias

Esto creara la base de datos `portal_atencion_cliente` con 5 tablas y datos de prueba.

### Paso 4: Configurar la API key de Gemini (opcional)

1. Copiar el archivo `config/api_key.example.php` como `config/api_key.php`
2. Abrir `config/api_key.php` con un editor de texto
3. Reemplazar `TU_API_KEY_AQUI` con una API key real de Google Gemini
4. Para obtener la key gratis:
   - Ir a https://aistudio.google.com
   - Crear una cuenta Google (no requiere tarjeta de credito)
   - Ir a https://aistudio.google.com/apikey
   - Crear una nueva API key
   - Copiarla y pegarla en el archivo

Si no se configura la API key, el sistema funciona igualmente con un modo de analisis local basado en reglas.

### Paso 5: Acceder al portal

Abrir el navegador y acceder a:

```
http://localhost/portal-atencion-cliente/
```

Se mostrara la pantalla de inicio de sesion.

---

## 7. Credenciales de prueba

El script SQL crea tres usuarios de prueba. Todas las contrasenas son `admin123`:

| Email | Contrasena | Rol | Descripcion |
|-------|-----------|-----|-------------|
| admin@portal.com | admin123 | Administrador | Acceso total: gestionar usuarios, categorias, solicitudes, reportes e IA |
| carlos.ruiz@portal.com | admin123 | Agente | Gestionar solicitudes, registrar seguimientos, ver reportes e IA |
| maria.lopez@email.com | admin123 | Cliente | Registrar solicitudes, consultar estado, usar asistente IA |

Si se necesitan recrear las contrasenas, ejecutar desde PHP:

```php
echo password_hash('admin123', PASSWORD_BCRYPT);
```

Y pegar el hash resultante en la tabla `usuarios` de la base de datos.

---

## 8. Modulos del sistema

### 8.1 Modulo de Autenticacion (`auth/`)

Funcionalidades:
- **Inicio de sesion**: formulario con validacion de email y contrasena. Utiliza `password_verify()` para comparar contrasenas hasheadas con bcrypt.
- **Registro de clientes**: formulario con validacion de campos, verificacion de email duplicado y creacion de cuenta con rol de cliente.
- **Cierre de sesion**: destruye la sesion y redirige al login.
- **Control de sesiones**: regeneracion de ID de sesion al autenticarse para prevenir session fixation.

Archivos:
- `auth/login.php` - Formulario y logica de inicio de sesion
- `auth/registrar.php` - Formulario y logica de registro de clientes
- `auth/logout.php` - Cierre de sesion

### 8.2 Modulo de Cliente (`modulo_cliente/`)

Funcionalidades:
- **Panel principal**: muestra resumen con total de solicitudes, abiertas y resueltas. Enlaces rapidos a las acciones principales.
- **Registrar solicitud**: formulario con categoria, asunto, descripcion y prioridad. Genera un numero de caso unico automaticamente (formato: CASO-AAAA-NNNNN).
- **Consultar solicitud**: busca por numero de caso y muestra detalles con historial de seguimiento. Los clientes solo ven sus propias solicitudes.
- **Historial**: listado de todas las solicitudes del cliente con filtros por estado y ordenamiento.
- **Asistente IA**: permite al cliente hacer preguntas sobre sus propias solicitudes o sobre como usar el portal.

Archivos:
- `modulo_cliente/index.php` - Panel principal del cliente
- `modulo_cliente/registrar_solicitud.php` - Formulario de registro
- `modulo_cliente/consultar_solicitud.php` - Busqueda por numero de caso
- `modulo_cliente/historial.php` - Listado de solicitudes
- `modulo_cliente/ia_cliente.php` - Asistente IA para clientes

### 8.3 Modulo de Atencion (`modulo_atencion/`)

Funcionalidades:
- **Gestion de solicitudes**: listado de todas las solicitudes con filtros por estado, categoria, prioridad y busqueda por texto. Las solicitudes pendientes se resaltan visualmente.
- **Detalle de solicitud**: muestra toda la informacion del caso, datos del cliente, historial de seguimientos y formulario para registrar nuevas acciones.
- **Cambio de estado**: permite cambiar entre estados con validacion de transiciones (por ejemplo, no se puede pasar de Cerrada a Pendiente directamente).
- **Registro de seguimientos**: cada accion queda registrada con fecha, usuario responsable, comentario y cambio de estado.

Archivos:
- `modulo_atencion/index.php` - Panel de gestion con filtros
- `modulo_atencion/detalle_solicitud.php` - Detalle y seguimiento de una solicitud

### 8.4 Modulo de Reportes (`modulo_reportes/`)

Funcionalidades:
- **Reporte mensual**: seleccion de mes y anio para generar el reporte.
- **Indicadores clave**: total de solicitudes, pendientes, en proceso, atendidas, cerradas, tasa de resolucion y tiempo promedio de atencion.
- **Distribucion por categoria**: barras de progreso mostrando la proporcion de cada tipo de solicitud.
- **Distribucion por prioridad**: visualizacion de solicitudes por nivel de urgencia.
- **Tendencia diaria**: tabla con la cantidad de solicitudes recibidas por dia del mes.
- **Exportacion**: boton para imprimir el reporte directamente desde el navegador.

Archivo:
- `modulo_reportes/index.php` - Dashboard de reportes

### 8.5 Modulo de Inteligencia Artificial

#### Para agentes y administradores (`modulo_ia/`)

- **Analisis Automatico**: ejecuta un analisis completo de todos los datos del portal. Muestra metricas, graficas de distribucion y el analisis generado por Gemini con hallazgos y recomendaciones.
- **Consultar IA**: formulario de preguntas personalizadas con 5 sugerencias predefinidas. La IA recibe todos los datos de solicitudes y responde con analisis contextualizado.

#### Para clientes (`modulo_cliente/ia_cliente.php`)

- **Consultar mis casos**: la IA analiza las solicitudes propias del cliente y responde sobre estados, proximos pasos, etc.
- **Ayuda general**: responde preguntas sobre como usar el portal, que categoria elegir, como consultar el estado, etc.

Archivos:
- `modulo_ia/index.php` - Dashboard de analisis IA
- `modulo_ia/consultar.php` - Consultas personalizadas IA
- `modulo_cliente/ia_cliente.php` - Asistente IA para clientes

### 8.6 Modulo de Administracion (`modulo_admin/`)

Funcionalidades:
- **Gestion de usuarios**: crear, editar y eliminar/desactivar usuarios. Asignar roles (cliente, agente, administrador) y estados (activo, inactivo).
- **Gestion de categorias**: crear, editar, activar y desactivar categorias de solicitudes. Muestra la cantidad de solicitudes asociadas a cada categoria.

Archivos:
- `modulo_admin/usuarios.php` - CRUD de usuarios
- `modulo_admin/categorias.php` - CRUD de categorias

---

## 9. Base de datos

### Nombre de la base de datos

`portal_atencion_cliente`

### Tablas y campos

#### tabla: usuarios

Almacena todos los usuarios del sistema (clientes, agentes y administradores).

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id_usuario | INT AUTO_INCREMENT PRIMARY KEY | Identificador unico |
| nombre | VARCHAR(100) NOT NULL | Nombre del usuario |
| apellido | VARCHAR(100) NOT NULL | Apellido del usuario |
| email | VARCHAR(150) NOT NULL UNIQUE | Correo electronico (login) |
| telefono | VARCHAR(20) NULL | Numero de telefono |
| contrasena | VARCHAR(255) NOT NULL | Hash bcrypt de la contrasena |
| rol | ENUM('cliente', 'agente', 'administrador') | Rol del usuario en el sistema |
| estado_cuenta | ENUM('activo', 'inactivo') | Estado de la cuenta |
| fecha_registro | DATETIME DEFAULT CURRENT_TIMESTAMP | Fecha de creacion |
| ultimo_acceso | DATETIME NULL | Ultimo inicio de sesion |

#### tabla: categorias

Define los tipos o categorias de solicitudes disponibles.

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id_categoria | INT AUTO_INCREMENT PRIMARY KEY | Identificador unico |
| nombre | VARCHAR(100) NOT NULL UNIQUE | Nombre de la categoria |
| descripcion | TEXT NULL | Descripcion de la categoria |
| estado | ENUM('activa', 'inactiva') | Estado de la categoria |

Categorias predefinidas:
1. Consulta general - Preguntas sobre productos o servicios
2. Reclamo - Queja formal sobre un servicio o producto
3. Soporte tecnico - Solicitud de ayuda tecnica
4. Sugerencia - Propuesta de mejora
5. Solicitud administrativa - Cambios en datos, facturacion, etc.

#### tabla: solicitudes

Tabla principal que almacena cada solicitud registrada por un cliente.

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id_solicitud | INT AUTO_INCREMENT PRIMARY KEY | Identificador unico |
| numero_caso | VARCHAR(20) NOT NULL UNIQUE | Numero unico (CASO-AAAA-NNNNN) |
| id_cliente | INT NOT NULL (FK) | Cliente que registro la solicitud |
| id_categoria | INT NOT NULL (FK) | Categoria de la solicitud |
| asunto | VARCHAR(200) NOT NULL | Breve resumen |
| descripcion | TEXT NOT NULL | Detalle completo del caso |
| prioridad | ENUM('baja', 'media', 'alta', 'urgente') | Nivel de prioridad |
| estado | ENUM('pendiente', 'en_proceso', 'atendida', 'cerrada') | Estado actual |
| fecha_creacion | DATETIME DEFAULT CURRENT_TIMESTAMP | Fecha de registro |
| fecha_actualizacion | DATETIME ON UPDATE CURRENT_TIMESTAMP | Ultima modificacion |
| fecha_cierre | DATETIME NULL | Fecha de cierre |

#### tabla: seguimientos

Registra cada accion o actualizacion realizada sobre una solicitud.

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id_seguimiento | INT AUTO_INCREMENT PRIMARY KEY | Identificador unico |
| id_solicitud | INT NOT NULL (FK) | Solicitud asociada |
| id_usuario | INT NOT NULL (FK) | Usuario que registro el seguimiento |
| comentario | TEXT NOT NULL | Observacion o accion realizada |
| estado_anterior | ENUM(...) NULL | Estado previo al cambio |
| estado_nuevo | ENUM(...) NULL | Estado nuevo (si hubo cambio) |
| fecha_seguimiento | DATETIME DEFAULT CURRENT_TIMESTAMP | Fecha del seguimiento |

#### tabla: reportes_mensuales

Almacena los reportes generados mensualmente para referencia historica.

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id_reporte | INT AUTO_INCREMENT PRIMARY KEY | Identificador unico |
| mes | TINYINT NOT NULL | Mes (1-12) |
| anio | SMALLINT NOT NULL | Anio |
| total_solicitudes | INT DEFAULT 0 | Total del mes |
| total_pendientes | INT DEFAULT 0 | Pendientes |
| total_en_proceso | INT DEFAULT 0 | En proceso |
| total_atendidas | INT DEFAULT 0 | Atendidas |
| total_cerradas | INT DEFAULT 0 | Cerradas |
| tiempo_promedio_horas | DECIMAL(10,2) NULL | Horas promedio de atencion |
| fecha_generacion | DATETIME | Fecha de generacion del reporte |
| generado_por | INT NULL (FK) | Usuario que genero el reporte |

### Relaciones

```
usuarios (1) ----< (N) solicitudes     (un cliente tiene muchas solicitudes)
categorias (1) ----< (N) solicitudes   (una categoria clasifica muchas solicitudes)
solicitudes (1) ----< (N) seguimientos (una solicitud tiene muchos seguimientos)
usuarios (1) ----< (N) seguimientos    (un usuario registra muchos seguimientos)
usuarios (1) ----< (N) reportes_mensuales (un usuario genera reportes)
```

### Sentencia SQL para crear la base de datos

El archivo `portal_atencion_cliente.sql` contiene todas las sentencias CREATE TABLE, los INSERT de datos de prueba y las relaciones. Se puede importar directamente desde phpMyAdmin.

---

## 10. Flujo de funcionamiento

### Flujo 1: Cliente registra una solicitud

1. El cliente accede al portal y se autentica.
2. Navega a "Nueva Solicitud".
3. Completa el formulario: categoria, asunto, descripcion, prioridad.
4. El sistema valida los datos en el servidor.
5. Se genera un numero de caso unico (ej: CASO-2026-00001).
6. Se inserta el registro en la tabla solicitudes con estado "pendiente".
7. Se registra el primer seguimiento automatico.
8. El cliente recibe confirmacion con su numero de caso.

### Flujo 2: Agente atiende una solicitud

1. El agente accede al panel de gestion.
2. Visualiza las solicitudes con filtros (estado, categoria, prioridad, busqueda).
3. Selecciona una solicitud para ver el detalle.
4. Revisa los datos del caso y el historial de seguimientos.
5. Cambia el estado de "pendiente" a "en proceso".
6. Registra un seguimiento con sus observaciones.
7. Al resolver el caso, cambia a "atendida" o "cerrada".
8. Registra el seguimiento final.

### Flujo 3: Generacion de reporte mensual

1. El administrador accede al modulo de reportes.
2. Selecciona el mes y anio a consultar.
3. El sistema ejecuta consultas SQL agregadas sobre la tabla solicitudes.
4. Se muestran los indicadores: total, estados, categorias, prioridades, tiempo promedio.
5. Se muestra la tendencia diaria del mes.
6. El administrador puede imprimir el reporte.

### Flujo 4: Analisis con IA

1. El usuario (agente, administrador o cliente) accede al modulo de IA.
2. El sistema recopila los datos relevantes de MySQL.
3. Se construye un prompt con los datos y la pregunta del usuario.
4. Se envia a la API de Google Gemini.
5. Gemini responde con analisis y recomendaciones.
6. Si no hay conexion a la API, se genera un analisis local basado en reglas.

---

## 11. Seguridad implementada

### Autenticacion y contrasenas

- Las contrasenas se almacenan hasheadas con `password_hash()` usando bcrypt (algoritmo por defecto de PHP).
- Nunca se almacenan contrasenas en texto plano.
- Se utiliza `password_verify()` para comparar contrasenas.
- La sesion se regenera al autenticarse (`session_regenerate_id(true)`) para prevenir session fixation.

### Proteccion contra inyeccion SQL

- Todas las consultas a la base de datos utilizan PDO con consultas preparadas (prepared statements).
- Nunca se concatenan variables del usuario directamente en sentencias SQL.
- Ejemplo de uso seguro:

```php
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);
```

### Proteccion contra XSS (Cross-Site Scripting)

- Toda salida de datos al navegador se escapa con `htmlspecialchars()` usando la funcion `e()`.
- Esto previene que scripts maliciosos se inyecten en las paginas.

### Proteccion contra CSRF (Cross-Site Request Forgery)

- Todos los formularios incluyen un token CSRF oculto.
- El token se genera con `bin2hex(random_bytes(32))`.
- Se valida en el servidor antes de procesar cualquier formulario POST.

### Control de acceso

- Las funciones `requiereAutenticacion()` y `requiereRol()` verifican que el usuario este logueado y tenga el rol adecuado antes de acceder a cada pagina.
- Los clientes solo pueden ver sus propias solicitudes.
- Los agentes y administradores ven todas las solicitudes.
- Solo los administradores pueden gestionar usuarios y categorias.

### Integridad de datos

- Las llaves foranesas (FOREIGN KEY) en MySQL garantizan la integridad referencial.
- Los ENUM restringen los valores permitidos para estados, prioridades y roles.
- Los campos NOT NULL obligan a completar informacion esencial.

---

## 12. Integracion con Inteligencia Artificial

### Proveedor: Google Gemini

Se utiliza la API de Google Gemini (modelo gemini-3.6-flash) por las siguientes razones:

- Tier gratuito generoso: 15 solicitudes por minuto, 1 millon de tokens por dia.
- No requiere tarjeta de credito.
- Modelo rapido y preciso para analisis de texto.
- Facil integracion via HTTP/REST.

### Como funciona

1. **Recopilacion de datos**: el sistema consulta MySQL para obtener estadisticas de solicitudes (total, por estado, categoria, prioridad, tendencias, clientes frecuentes).

2. **Construccion del prompt**: se arma un prompt que incluye todos los datos de la empresa y la pregunta del usuario.

3. **Consulta a Gemini**: se envia el prompt a la API de Gemini mediante una peticion HTTP POST con cURL.

4. **Respuesta**: Gemini devuelve un analisis con hallazgos y recomendaciones que se muestra al usuario.

5. **Fallback local**: si no hay API key configurada o no hay conexion, el sistema genera un analisis local usando reglas predefinidas basadas en los datos.

### Configuracion

1. Obtener API key gratis en https://aistudio.google.com/apikey
2. Copiar `config/api_key.example.php` como `config/api_key.php`
3. Pegar la key:

```php
define('GEMINI_API_KEY', 'AIzaSy...');
```

4. El modelo se configura en la misma constante:

```php
define('GEMINI_MODEL', 'gemini-3.6-flash');
```

### Preguntas sugeridas para la IA

**Para agentes/administradores:**
- Cuales son los principales problemas reportados por los clientes?
- Que categorias deberian tener mas personal asignado?
- Como podemos reducir el tiempo promedio de atencion?
- Que tendencias se observan en las solicitudes del ultimo mes?
- Que acciones inmediatas recomiendas para mejorar la satisfaccion?

**Para clientes:**
- Cual es el estado de todas mis solicitudes?
- Que deberia hacer con mi proxima solicitud?
- Como puedo registrar una solicitud en el portal?
- Cuales son las categorias disponibles y cual debo elegir?
- Como puedo consultar el estado de mi solicitud?

---

## 13. Estructura completa del proyecto

```
portal-atencion-cliente/
|
+-- config/
|   +-- conexion.php               Conexion a MySQL via PDO
|   +-- api_key.php                API key de Gemini (NO se sube a git)
|   +-- api_key.example.php        Plantilla de configuracion (si se sube)
|
+-- includes/
|   +-- auth.php                   Funciones de autenticacion y control de acceso
|   +-- funciones.php              Funciones auxiliares (formateo, badges, flash messages)
|   +-- ia_service.php             Servicio de IA (Gemini + fallback local)
|   +-- header.php                 Cabecera HTML comun (navbar, sesiones)
|   +-- footer.php                 Pie de pagina comun (Bootstrap JS)
|
+-- auth/
|   +-- login.php                  Inicio de sesion
|   +-- logout.php                 Cierre de sesion
|   +-- registrar.php              Registro de nuevos clientes
|
+-- modulo_cliente/
|   +-- index.php                  Panel principal del cliente
|   +-- registrar_solicitud.php    Formulario de registro de solicitudes
|   +-- consultar_solicitud.php    Busqueda por numero de caso
|   +-- historial.php              Listado de solicitudes propias
|   +-- ia_cliente.php             Asistente IA para clientes
|
+-- modulo_atencion/
|   +-- index.php                  Panel de gestion de solicitudes
|   +-- detalle_solicitud.php      Detalle y seguimiento de solicitudes
|
+-- modulo_reportes/
|   +-- index.php                  Reporte mensual e indicadores
|
+-- modulo_ia/
|   +-- index.php                  Dashboard de analisis con IA
|   +-- consultar.php              Consultas personalizadas a la IA
|
+-- modulo_admin/
|   +-- usuarios.php               Gestion de usuarios (CRUD)
|   +-- categorias.php             Gestion de categorias (CRUD)
|
+-- assets/
|   +-- css/
|   |   +-- estilo.css             Estilos personalizados del portal
|   +-- js/
|       +-- app.js                 Funcionalidades JavaScript
|
+-- index.php                      Punto de entrada (redireccionador)
+-- portal_atencion_cliente.sql    Script SQL de la base de datos
+-- README.md                      Este archivo
+-- .gitignore                     Archivos excluidos del repositorio
```

---

## 14. Guia para personalizar y extender el sistema

### Cambiar la apariencia

Los estilos personalizados estan en `assets/css/estilo.css`. Los colores del navbar, cards y botones se pueden modificar ahi. El framework Bootstrap 5 permite cambiar colores mediante clases predefinidas (bg-primary, bg-success, etc.).

### Agregar nuevas categorias

Desde el panel de administracion (modulo_admin/categorias.php) se pueden crear, editar, activar y desactivar categorias sin modificar codigo.

### Agregar nuevos campos a las solicitudes

1. Agregar la columna en MySQL con `ALTER TABLE solicitudes ADD COLUMN ...`
2. Actualizar el formulario en `modulo_cliente/registrar_solicitud.php`
3. Actualizar el listado en `modulo_atencion/index.php` y `modulo_cliente/historial.php`
4. Actualizar las consultas SQL en `modulo_ia/` y `modulo_reportes/`

### Cambiar o agregar un modelo de IA

Editar `includes/ia_service.php` y modificar la funcion `consultarGemini()`. Cambiar la URL de la API y el formato del payload segun la documentacion del proveedor elegido.

### Migrar a un servidor en produccion

1. Subir todos los archivos a un hosting con PHP y MySQL.
2. Crear la base de datos en el servidor y ejecutar el SQL.
3. Actualizar `config/conexion.php` con las credenciales del servidor.
4. Configurar HTTPS para proteger las sesiones.
5. Actualizar las rutas absolutas en el codigo (reemplazar `/portal-atencion-cliente/` por la ruta real).
6. Asegurar que `config/api_key.php` no sea accesible publicamente.

### Formato del numero de caso

El numero de caso se genera automaticamente con el formato `CASO-AAAA-NNNNN`. Para cambiar el formato, editar la funcion `generarNumeroCaso()` en `includes/funciones.php`.

---

## Informacion adicional

- **Disponibilidad**: el portal esta disenado para funcionar 24/7. En un servidor local depende de que XAMPP este activo. En un hosting profesional, estaria disponible permanentemente.
- **Escalabilidad**: la arquitectura modular permite agregar nuevos modulos sin modificar los existentes.
- **Mantenimiento**: el codigo esta organizado por responsabilidades, facilitando la busqueda y modificacion de funcionalidades.
- **Compatibilidad**: funciona en cualquier navegador moderno (Chrome, Firefox, Edge, Safari) y es responsivo para dispositivos moviles gracias a Bootstrap 5.

---

Proyecto academico - Universidad Tecnologica de San Jose del Tunar (UTS)
