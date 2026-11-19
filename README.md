
# TP6 Integrador – PetCare

**Estudiante:** Gómez Daiana  
**Institución:** ISFDyT N.º 24  
**Materia:** Prácticas Profesionalizantes  
**Proyecto:** Sistema de gestión veterinaria PetCare

## 1. Descripción del proyecto

PetCare es una aplicación web desarrollada en PHP y MySQL para gestionar información de una veterinaria.

Permite administrar clientes, mascotas, veterinarios y consultas médicas mediante una interfaz web.

## 2. Funcionalidades

- Registro e inicio de sesión de usuarios.
- Gestión de sesiones.
- Cambio de contraseña.
- Administración de usuarios y asignación de roles.
- Alta, baja, modificación y consulta de clientes.
- Alta, baja, modificación y consulta de mascotas.
- Alta, baja, modificación y consulta de veterinarios.
- Registro, modificación y eliminación de consultas médicas.
- Visualización y filtrado del historial de consultas.
- Control de acceso según el rol del usuario.

## 3. Roles y permisos

| Funcionalidad | Administrador | Veterinario | Usuario |
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

- PHP
- MySQL
- HTML
- CSS
- Bootstrap
- PDO para la conexión a la base de datos
- XAMPP para el entorno local
- Visual Studio Code
- Git y GitHub para el control de versiones
- InfinityFree para el alojamiento web

## 5. Base de datos

La base de datos se denomina `petcare` en el entorno local.

Tablas principales:

- `clientes`
- `mascotas`
- `veterinarios`
- `consultas`
- `usuarios`

Se utilizan claves primarias y foráneas para relacionar los registros.

## 6. Seguridad

El sistema incorpora autenticación de usuarios mediante sesiones PHP y control de permisos según roles.

Las contraseñas de los usuarios se almacenan mediante hashes.

Los datos de conexión a MySQL se mantienen en un archivo de configuración independiente, excluido del repositorio público.

## 7. Ejecución local
## 7. Ejecución local

1. Instalar XAMPP y activar Apache y MySQL.
2. Colocar el proyecto en `C:\xampp\htdocs\TP6_PetCare_Daiana_Gomez\`.
3. Crear una base de datos llamada `petcare` desde phpMyAdmin o MySQL Workbench.
4. Importar el archivo `database/petcare_entrega_completo.sql`, que contiene las cinco tablas, sus relaciones y datos ficticios de prueba.
5. Crear el archivo `config/credenciales.php` con los datos de conexión correspondientes al entorno local.
6. Verificar que el puerto de MySQL coincida con el configurado en XAMPP (en el entorno de desarrollo se utilizó el puerto 3307).
7. Acceder a la aplicación desde:

http://localhost/TP6_PetCare_Daiana_Gomez/

**Importante:** el archivo SQL de entrega está destinado a una instalación nueva, ya que reemplaza las tablas existentes. No debe importarse sobre la base de datos del sistema publicado.

## 8. Publicación web

El proyecto se encuentra publicado en InfinityFree.

**Aplicación web:**

https://daiana.rf.gd/TP6_PetCare_Daiana_Gomez/

## 9. Repositorio GitHub

https://github.com/natidai/TP6_PetCare_Daiana_Gomez

El repositorio permite consultar el código fuente y el historial de desarrollo del proyecto.

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

Para facilitar la evaluación del sistema PetCare, se crearon tres cuentas de prueba en la aplicación publicada en InfinityFree, cada una con su rol correspondiente.

| Rol | Correo electrónico |
|---|---|
| Administrador | administrador.prueba@ejemplo.com |
| Veterinario | veterinario.prueba@ejemplo.com |
| Usuario común | usuario.prueba@ejemplo.com |

**Aplicación web:** https://daiana.rf.gd/TP6_PetCare_Daiana_Gomez/

Las tres cuentas permiten comprobar el inicio de sesión, la gestión de sesiones y las restricciones de acceso según los permisos asignados.

**Contraseñas:** las credenciales de acceso de estas cuentas se proporcionarán al docente por separado, junto con la entrega del trabajo práctico.

### Instalación local

El archivo `database/petcare_entrega_completo.sql` permite crear las cinco tablas e incorporar datos ficticios y usuarios de prueba para una instalación nueva.

Las contraseñas de los usuarios incluidos en el SQL son independientes de las utilizadas en la aplicación publicada y se proporcionarán al docente junto con las instrucciones de evaluación.

## 12. Autora

**Gómez Daiana**

ISFDyT N.º 24 – 2026
