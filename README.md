# Sistema de Gestión de Biblioteca

## Descripción
Aplicación web en PHP con Programación Orientada a Objetos y MySQL para gestionar libros, usuarios y préstamos de una biblioteca.

## Requisitos
- XAMPP con Apache y MySQL
- PHP 7.4 o superior
- Navegador web

## Instalación y configuración
1. Coloca la carpeta del proyecto en: `C:\xampp\htdocs\library-management-system`
2. Inicia Apache y MySQL desde XAMPP.
3. Abre phpMyAdmin en: `http://localhost/phpmyadmin`
4. Importa el archivo `biblioteca.sql` para crear la base de datos `biblioteca`.
5. Abre la aplicación en: `http://localhost/library-management-system/`

## Configuración de conexión
El archivo `classes/Database.php` usa las credenciales por defecto de XAMPP:
- host: `localhost`
- base de datos: `biblioteca`
- usuario: `root`
- contraseña: vacía

Si tu instalación de XAMPP usa otra contraseña, ajústala en ese archivo.

## Funcionalidades implementadas
- Crear, editar y eliminar libros
- Crear, editar y eliminar usuarios
- Registrar préstamos de libros
- Registrar devoluciones de libros
- Mostrar préstamos activos con información de libro y usuario
- Uso de PDO para la conexión a la base de datos
- Transacciones para garantizar consistencia al prestar y devolver libros

## Estructura del proyecto
- `index.php`: interfaz y control de acciones
- `classes/Database.php`: conexión PDO
- `classes/Libro.php`: modelo de libro
- `classes/Usuario.php`: modelo de usuario
- `classes/Prestamo.php`: modelo de préstamo
- `classes/Biblioteca.php`: lógica de negocio y CRUD
- `biblioteca.sql`: script de base de datos

## Uso
1. Agrega libros desde la sección de libros.
2. Agrega usuarios desde la sección de usuarios.
3. Desde la lista de libros, usa “Prestar” para asignar un préstamo a un usuario.
4. En la sección de préstamos, usa “Devolver” para cerrar el préstamo.

## Capturas de pantalla

Coloca las imágenes en una carpeta como `docs/capturas/` con estos nombres para que se muestren en el README:

![Vista de libros](docs/capturas/libros.png)

![Vista de usuarios](docs/capturas/usuarios.png)

![Vista de préstamos](docs/capturas/prestamos.png)

