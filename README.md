# TP6 Integrador – PetCare

**Estudiante:** Gómez Daiana  
**Institución:** ISFDyT N.º 24  
**Materia:** Prácticas Profesionalizantes I  
**Proyecto:** Sistema de gestión veterinaria PetCare  
**Año:** 2026

## 1. Descripción del proyecto

PetCare es una aplicación web desarrollada en PHP y MySQL para gestionar información de una veterinaria. Permite administrar clientes, mascotas, veterinarios y consultas médicas mediante una interfaz web, con acceso diferenciado según el rol de cada usuario.

## 2. Funcionalidades

- Registro e inicio de sesión de usuarios.
- Gestión de sesiones y cierre de sesión.
- Cambio de contraseña.
- Administración de usuarios y asignación de roles.
- Alta, baja, modificación y consulta de clientes.
- Alta, baja, modificación y consulta de mascotas.
- Alta, baja, modificación y consulta de veterinarios.
- Registro, modificación y eliminación de consultas médicas.
- Visualización y filtrado del historial de consultas.
- Control de acceso según el rol del usuario.
- **DaiBot:** asistente virtual de preguntas frecuentes sobre el uso de PetCare, integrado en la interfaz. Ofrece respuestas predefinidas, animación de escritura y acceso al contacto por correo electrónico. No consulta la base de datos ni utiliza servicios externos de inteligencia artificial.

## 3. Roles y permisos

| Funcionalidad | Administrador | Veterinario | Usuario común |
|---|---|---|---|
| Gestionar clientes | Sí | No | No |
| Gestionar mascotas | Sí | Solo consultar | Solo consultar |
| Gestionar veterinarios | Sí | No | No |
| Registrar consultas | Sí | Sí | No |
| Modificar consultas | Sí | Sí | No |
| Eliminar consultas | Sí | No | No |
| Administrar usuarios y roles | Sí | No | No |
| Cambiar su contraseña | Sí | Sí | Sí |

## 4. Tecnologías utilizadas

- PHP.
- MySQL.
- HTML, CSS y JavaScript.
- Bootstrap.
- PDO para la conexión a la base de datos.
- XAMPP para el entorno local.
- Visual Studio Code.
- Git y GitHub para el control de versiones.
- InfinityFree para el alojamiento web.

## 5. Base de datos

La base de datos se denomina `petcare` en el entorno local.

Tablas principales:

- `clientes`
- `mascotas`
- `veterinarios`
- `consultas`
- `usuarios`

Se utilizan claves primarias y foráneas para relacionar los registros.

El archivo `database/petcare_entrega_completo.sql` permite crear las cinco tablas, sus relaciones e incorporar datos ficticios de prueba para una instalación nueva.

## 6. Seguridad

El sistema incorpora autenticación mediante sesiones PHP y control de permisos según roles. Las contraseñas se almacenan mediante hashes.

Los datos de conexión a MySQL se mantienen en un archivo de configuración independiente, excluido del repositorio público. No deben publicarse las credenciales reales del hosting ni del servicio de base de datos.

## 7. Ejecución local

1. Instalar XAMPP y activar Apache y MySQL.
2. Colocar el proyecto en `C:\xampp\htdocs\TP6_PetCare_Daiana_Gomez\`.
3. Crear una base de datos llamada `petcare` desde phpMyAdmin o MySQL Workbench.
4. Importar `database/petcare_entrega_completo.sql`.
5. Crear `config/credenciales.php` con los datos de conexión correspondientes al entorno local, siguiendo la configuración utilizada por el proyecto.
6. Verificar que el puerto de MySQL coincida con el configurado en XAMPP (en el entorno de desarrollo se utilizó el puerto **3307**).
7. Abrir la aplicación en el navegador: <http://localhost/TP6_PetCare_Daiana_Gomez/>.

**Importante:** el archivo SQL de entrega está destinado a una instalación nueva, ya que reemplaza las tablas existentes. **No debe importarse sobre la base de datos del sistema publicado.**

## 8. Publicación web

La aplicación está publicada en InfinityFree:

<https://daiana.rf.gd/TP6_PetCare_Daiana_Gomez/>

## 9. Repositorio GitHub

<https://github.com/natidai/TP6_PetCare_Daiana_Gomez>

El repositorio permite consultar el código fuente y el historial de desarrollo del proyecto mediante commits.

## 10. Pruebas realizadas

Se realizaron pruebas de:

- Registro e inicio de sesión.
- Acceso según roles y permisos.
- Cambio de contraseña.
- Operaciones de gestión de las entidades.
- Consulta y registro de información en MySQL.
- Funcionamiento de la aplicación publicada en InfinityFree.

Se comprobó la conexión con la base de datos del hosting y el registro y eliminación de una consulta médica de prueba.

## 11. Usuarios de prueba para la evaluación

Se crearon tres cuentas de prueba en la aplicación publicada en InfinityFree, una para cada rol:

| Rol | Correo electrónico |
|---|---|
| Administrador | `administrador.prueba@ejemplo.com` |
| Veterinario | `veterinario.prueba@ejemplo.com` |
| Usuario común | `usuario.prueba@ejemplo.com` |

**Las contraseñas de evaluación y las indicaciones para probar cada rol se encuentran en [`USUARIOS_PRUEBA.md`](USUARIOS_PRUEBA.md), incluido en este repositorio.** Las contraseñas allí indicadas deben corresponder a estas cuentas y ser exclusivas para la evaluación.

**Aplicación web:** <https://daiana.rf.gd/TP6_PetCare_Daiana_Gomez/>

### Usuarios de prueba en una instalación local

El script `database/petcare_entrega_completo.sql` incorpora datos ficticios y usuarios de prueba para una instalación nueva. Las contraseñas de esos usuarios pueden ser diferentes de las utilizadas en la aplicación publicada; si es así, deben indicarse también en `USUARIOS_PRUEBA.md` antes de entregar.

## 12. Autora

**Gómez Daiana**  
ISFDyT N.º 24 – 2026
