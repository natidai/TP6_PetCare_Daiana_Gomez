
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

1. Instalar y ejecutar XAMPP.
2. Colocar el proyecto en `C:\xampp\htdocs\TP6_PetCare_Daiana_Gomez`.
3. Crear la base de datos `petcare` en MySQL.
4. Importar la estructura de las tablas.
5. Configurar `config/credenciales.php` con los datos de conexión locales.
6. Acceder desde el navegador a:

   http://localhost/TP6_PetCare_Daiana_Gomez/

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

## 11. Autora

**Gómez Daiana**

ISFDyT N.º 24 – 2026
