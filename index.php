<?php
require_once 'classes/Biblioteca.php';

// TODO: Instanciar la clase Biblioteca
$biblioteca = new Biblioteca();

// TODO: Manejar lógica de enrutamiento o acciones (GET/POST)
?>
<?php
// Manejo de acciones (debe ejecutarse antes de cualquier salida)
$action = isset($_GET['action']) ? $_GET['action'] : 'libros';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'agregar_libro') {
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $isbn = trim($_POST['isbn']);
        $cantidad = (int) $_POST['cantidad'];

        $libro = new Libro($titulo, $autor, $isbn, $cantidad);
        $biblioteca->agregarLibro($libro);
        header('Location: index.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'agregar_usuario') {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);

        $usuario = new Usuario($nombre, $email, $telefono);
        $biblioteca->agregarUsuario($usuario);
        header('Location: index.php?action=usuarios');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'prestar_confirm') {
        $libro_id = (int) $_POST['libro_id'];
        $usuario_id = (int) $_POST['usuario_id'];
        $biblioteca->prestarLibro($libro_id, $usuario_id);
        header('Location: index.php?action=prestamos');
        exit;
    }
}

// Acciones tipo GET
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete_libro' && isset($_GET['id'])) {
        $biblioteca->eliminarLibro((int) $_GET['id']);
        header('Location: index.php');
        exit;
    }

    if ($_GET['action'] === 'delete_usuario' && isset($_GET['id'])) {
        $biblioteca->eliminarUsuario((int) $_GET['id']);
        header('Location: index.php?action=usuarios');
        exit;
    }

    if ($_GET['action'] === 'prestar' && isset($_GET['libro_id'])) {
        // mostrar formulario para elegir usuario (se maneja en la vista)
        $action = 'prestar';
    }

    if ($_GET['action'] === 'devolver' && isset($_GET['prestamo_id'])) {
        $biblioteca->devolverLibro((int) $_GET['prestamo_id']);
        header('Location: index.php?action=prestamos');
        exit;
    }

    if ($_GET['action'] === 'prestamos') {
        $action = 'prestamos';
    }

    if ($_GET['action'] === 'usuarios') {
        $action = 'usuarios';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Biblioteca</title>
    <style>
        /* TODO: Agregar estilos CSS */
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { margin-bottom: 20px; background: #eee; padding: 10px; }
        nav a { margin-right: 15px; text-decoration: none; color: #333; }
        .container { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Biblioteca Mini-App</h1>
        
        <nav>
            <a href="index.php">Inicio / Libros</a>
            <a href="index.php?action=usuarios">Usuarios</a>
            <a href="index.php?action=prestamos">Préstamos</a>
        </nav>

        <div id="content">
            <!-- TODO: Mostrar contenido dinámico aquí dependiendo de la sección -->
            
            <h2>Sección Actual</h2>
            <p>Implementar la visualización de datos aquí.</p>
            
            <!-- Ejemplo de estructura para lista -->
            <!-- 
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php // foreach($items as $item): ?>
                    <tr>
                        <td>...</td>
                        <td>...</td>
                        <td>
                            <a href="#">Editar</a>
                            <a href="#">Eliminar</a>
                        </td>
                    </tr>
                    <?php // endforeach; ?>
                </tbody>
            </table> 
            -->
        </div>
    </div>
</body>
</html>
