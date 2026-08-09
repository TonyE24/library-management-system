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

    if (isset($_POST['action']) && $_POST['action'] === 'editar_libro') {
        $id = (int) $_POST['libro_id'];
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $isbn = trim($_POST['isbn']);
        $cantidad = (int) $_POST['cantidad'];

        $datos = [
            'titulo' => $titulo,
            'autor' => $autor,
            'isbn' => $isbn,
            'cantidad' => $cantidad
        ];

        $biblioteca->editarLibro($id, $datos);
        header('Location: index.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'editar_usuario') {
        $id = (int) $_POST['usuario_id'];
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);

        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono
        ];

        $biblioteca->editarUsuario($id, $datos);
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
        $action = 'prestar';
    }

    if ($_GET['action'] === 'edit_libro' && isset($_GET['id'])) {
        $action = 'edit_libro';
    }

    if ($_GET['action'] === 'edit_usuario' && isset($_GET['id'])) {
        $action = 'edit_usuario';
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
            <?php if ($action === 'libros'): ?>
                <h2>Libros</h2>

                <h3>Agregar libro</h3>
                <form method="post" action="index.php">
                    <input type="hidden" name="action" value="agregar_libro">
                    <div>
                        <label>Título:</label>
                        <input type="text" name="titulo" required>
                    </div>
                    <div>
                        <label>Autor:</label>
                        <input type="text" name="autor" required>
                    </div>
                    <div>
                        <label>ISBN:</label>
                        <input type="text" name="isbn">
                    </div>
                    <div>
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="1" min="1">
                    </div>
                    <button type="submit">Agregar libro</button>
                </form>

                <h3>Listado de libros</h3>
                <?php $libros = $biblioteca->obtenerLibros(); ?>
                <table border="1" cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $l): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($l['id']); ?></td>
                                <td><?php echo htmlspecialchars($l['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($l['autor']); ?></td>
                                <td><?php echo htmlspecialchars($l['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($l['cantidad']); ?></td>
                                <td>
                                    <a href="index.php?action=prestar&libro_id=<?php echo $l['id']; ?>">Prestar</a>
                                    |
                                    <a href="index.php?action=edit_libro&id=<?php echo $l['id']; ?>">Editar</a>
                                    |
                                    <a href="index.php?action=delete_libro&id=<?php echo $l['id']; ?>" onclick="return confirm('Eliminar libro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'usuarios'): ?>
                <h2>Usuarios</h2>

                <h3>Agregar usuario</h3>
                <form method="post" action="index.php?action=usuarios">
                    <input type="hidden" name="action" value="agregar_usuario">
                    <div>
                        <label>Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div>
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div>
                        <label>Teléfono:</label>
                        <input type="text" name="telefono">
                    </div>
                    <button type="submit">Agregar usuario</button>
                </form>

                <h3>Listado de usuarios</h3>
                <?php $usuarios = $biblioteca->obtenerUsuarios(); ?>
                <table border="1" cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['id']); ?></td>
                                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['telefono']); ?></td>
                                <td>
                                    <a href="index.php?action=edit_usuario&id=<?php echo $u['id']; ?>">Editar</a>
                                    |
                                    <a href="index.php?action=delete_usuario&id=<?php echo $u['id']; ?>" onclick="return confirm('Eliminar usuario?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'edit_libro' && isset($_GET['id'])): ?>
                <?php $libro_id = (int) $_GET['id']; $libro = $biblioteca->buscarLibro($libro_id); ?>
                <h2>Editar libro</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="action" value="editar_libro">
                    <input type="hidden" name="libro_id" value="<?php echo $libro_id; ?>">
                    <div>
                        <label>Título:</label>
                        <input type="text" name="titulo" value="<?php echo htmlspecialchars($libro['titulo'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label>Autor:</label>
                        <input type="text" name="autor" value="<?php echo htmlspecialchars($libro['autor'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label>ISBN:</label>
                        <input type="text" name="isbn" value="<?php echo htmlspecialchars($libro['isbn'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="<?php echo htmlspecialchars($libro['cantidad'] ?? 1); ?>" min="1">
                    </div>
                    <button type="submit">Guardar cambios</button>
                </form>

            <?php elseif ($action === 'edit_usuario' && isset($_GET['id'])): ?>
                <?php $usuario_id = (int) $_GET['id']; $usuario = $biblioteca->buscarUsuario($usuario_id); ?>
                <h2>Editar usuario</h2>
                <form method="post" action="index.php?action=usuarios">
                    <input type="hidden" name="action" value="editar_usuario">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
                    <div>
                        <label>Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label>Teléfono:</label>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                    </div>
                    <button type="submit">Guardar cambios</button>
                </form>

            <?php elseif ($action === 'prestar' && isset($_GET['libro_id'])): ?>
                <?php $libro_id = (int) $_GET['libro_id']; $libro = $biblioteca->buscarLibro($libro_id); ?>
                <h2>Prestar libro: <?php echo htmlspecialchars($libro['titulo'] ?? '—'); ?></h2>

                <form method="post" action="index.php">
                    <input type="hidden" name="action" value="prestar_confirm">
                    <input type="hidden" name="libro_id" value="<?php echo $libro_id; ?>">
                    <div>
                        <label>Usuario:</label>
                        <select name="usuario_id" required>
                            <option value="">-- seleccionar --</option>
                            <?php foreach ($biblioteca->obtenerUsuarios() as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Confirmar préstamo</button>
                </form>

            <?php elseif ($action === 'prestamos'): ?>
                <h2>Préstamos activos</h2>
                <?php $prestamos = $biblioteca->obtenerPrestamosActivos(); ?>
                <table border="1" cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libro</th>
                            <th>Usuario</th>
                            <th>Fecha préstamo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['id']); ?></td>
                                <td><?php echo htmlspecialchars($p['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['fecha_prestamo']); ?></td>
                                <td>
                                    <a href="index.php?action=devolver&prestamo_id=<?php echo $p['id']; ?>">Devolver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <h2>Sección no encontrada</h2>
            <?php endif; ?>
            
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
