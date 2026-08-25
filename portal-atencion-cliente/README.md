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

`
+---------------------------------------------------+
|          CAPA DE PRESENTACION (Frontend)          |
|   HTML5 + CSS3 + JavaScript + Bootstrap 5         |
|   Plantillas PHP (vistas del sistema)             |
+--------------------------+------------------------+
                           | HTTP/HTTPS
                           v
+---------------------------------------------------+
|          CAPA DE LOGICA (Backend)                 |
|   PHP 8.x (lenguaje de servidor)                 |
|   Funciones de validacion, autenticacion          |
|   Generacion de reportes e integracion IA         |
|   Consultas preparadas via PDO                    |
+--------------------------+------------------------+
                           | SQL
                           v
+---------------------------------------------------+
|          CAPA DE DATOS (MySQL)                    |
|   MySQL 8.x (motor InnoDB)                       |
|   Base de datos: portal_atencion_cliente           |
+---------------------------------------------------+
`

---

## 4. Tecnologias utilizadas

| Tecnologia | Funcion |
|-----------|---------|---------|
| PHP | Backend / logica de servidor |
| MySQL | Base de datos relacional |
| Apache | Servidor web (via XAMPP) |
| HTML5 | Estructura de paginas |
| CSS3 | Estilos visuales |
| JavaScript | Interactividad del cliente |
| Bootstrap | Framework CSS responsivo |
| Google Gemini API | Inteligencia artificial |
| PDO | Conexion segura a la base de datos |

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

`
portal-atencion-cliente/
  config/                  Configuracion y conexion a BD
  includes/                Funciones compartidas, auth, IA
  auth/                    Login, logout, registro
  modulo_cliente/          Panel del cliente, solicitudes, IA
  modulo_atencion/         Gestion de solicitudes
  modulo_reportes/         Reportes y estadisticas
  modulo_ia/               Analisis inteligente
  modulo_admin/            Gestion de usuarios y categorias
  assets/css/              Estilos
  assets/js/               JavaScript
  portal_atencion_cliente.sql  Script de la BD
`

---

## 14. Guia para extender el sistema

- **Nuevas categorias**: desde modulo_admin/categorias.php
- **Nuevos campos**: ALTER TABLE + actualizar formularios y consultas
- **Cambiar modelo de IA**: editar funcion consultarGemini() en ia_service.php
- **Migrar a produccion**: subir a hosting, actualizar rutas, configurar HTTPS

---

Proyecto academico - Universidad Tecnologica de San Jose del Tunar (UTS)
