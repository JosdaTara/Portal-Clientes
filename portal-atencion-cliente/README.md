# Portal de Atencion al Cliente

Portal web de atencion al cliente 24/7, desarrollado con PHP, MySQL y XAMPP. Permite recibir, gestionar, almacenar y analizar las solicitudes realizadas por los clientes de una empresa.

## Funcionalidades principales

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

## Tecnologias utilizadas

- **PHP 8.x** - Lenguaje de servidor
- **MySQL 8.x** - Base de datos relacional
- **Apache 2.4** - Servidor web (via XAMPP)
- **HTML5 / CSS3 / JavaScript** - Frontend
- **Bootstrap 5** - Framework CSS responsivo
- **Google Gemini API** - Inteligencia artificial
- **PDO** - Conexion segura a la base de datos

## Requisitos

- XAMPP (Apache + MySQL + PHP)
- Navegador web moderno
- Cuenta en Google AI Studio (para API key de Gemini, opcional)

## Instalacion

1. Clonar o descargar el repositorio en `C:\xampp\htdocs\portal-atencion-cliente`
2. Iniciar Apache y MySQL desde el XAMPP Control Panel
3. Abrir phpMyAdmin (`http://localhost/phpmyadmin`)
4. Importar el archivo `portal_atencion_cliente.sql` para crear la base de datos y datos de prueba
5. (Opcional) Configurar la API key de Google Gemini en `config/api_key.php`:
   - Copiar `config/api_key.example.php` como `config/api_key.php`
   - Obtener una API key gratis en https://aistudio.google.com/apikey
   - Pegarla en la constante `GEMINI_API_KEY`
6. Acceder al portal en `http://localhost/portal-atencion-cliente/`

## Credenciales de prueba

| Usuario | Contrasena | Rol |
|---------|-----------|-----|
| admin@portal.com | admin123 | Administrador |
| carlos.ruiz@portal.com | admin123 | Agente |
| maria.lopez@email.com | admin123 | Cliente |

## Estructura del proyecto

```
portal-atencion-cliente/
  config/                  Configuracion y conexion a BD
  includes/                Funciones compartidas, auth, IA, header, footer
  auth/                    Login, logout, registro
  modulo_cliente/          Panel del cliente, solicitudes, historial, IA cliente
  modulo_atencion/         Gestion de solicitudes por agentes
  modulo_reportes/         Reportes mensuales y estadisticas
  modulo_ia/               Analisis inteligente y consultas IA
  modulo_admin/            Gestion de usuarios y categorias
  assets/css/              Estilos personalizados
  assets/js/               Funcionalidades JavaScript
  portal_atencion_cliente.sql  Script de la base de datos
```

## Base de datos

El sistema utiliza 5 tablas principales:

- **usuarios** - Clientes, agentes y administradores
- **categorias** - Tipos de solicitudes
- **solicitudes** - Registro de todas las solicitudes
- **seguimientos** - Historial de acciones por solicitud
- **reportes_mensuales** - Reportes generados para referencia

## Licencia

Proyecto academico - Universidad Tecnologica de San Jose del Tunar (UTS)
