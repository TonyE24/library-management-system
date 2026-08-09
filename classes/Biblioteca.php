<?php
require_once 'Database.php';
require_once 'Libro.php';
require_once 'Usuario.php';
require_once 'Prestamo.php';

class Biblioteca {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function agregarLibro(Libro $libro) {
        $query = "INSERT INTO libros (titulo, autor, isbn, cantidad) VALUES (:titulo, :autor, :isbn, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':titulo', $libro->getTitulo());
        $stmt->bindValue(':autor', $libro->getAutor());
        $stmt->bindValue(':isbn', $libro->getIsbn());
        $stmt->bindValue(':cantidad', $libro->getCantidad(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function editarLibro($id, $nuevosDatos) {
        $query = "UPDATE libros
                  SET titulo = :titulo, autor = :autor, isbn = :isbn, cantidad = :cantidad
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':titulo', $nuevosDatos['titulo']);
        $stmt->bindValue(':autor', $nuevosDatos['autor']);
        $stmt->bindValue(':isbn', $nuevosDatos['isbn']);
        $stmt->bindValue(':cantidad', $nuevosDatos['cantidad'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function eliminarLibro($id) {
        $query = "DELETE FROM libros WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function obtenerLibros() {
        $query = "SELECT * FROM libros ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarLibro($id) {
        $query = "SELECT * FROM libros WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function agregarUsuario(Usuario $usuario) {
        $query = "INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nombre', $usuario->getNombre());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':telefono', $usuario->getTelefono());

        return $stmt->execute();
    }

    public function editarUsuario($id, $nuevosDatos) {
        $query = "UPDATE usuarios
                  SET nombre = :nombre, email = :email, telefono = :telefono
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nombre', $nuevosDatos['nombre']);
        $stmt->bindValue(':email', $nuevosDatos['email']);
        $stmt->bindValue(':telefono', $nuevosDatos['telefono']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function eliminarUsuario($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function obtenerUsuarios() {
        $query = "SELECT * FROM usuarios ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function prestarLibro($libro_id, $usuario_id) {
        try {
            $this->conn->beginTransaction();

            $queryStock = "SELECT cantidad FROM libros WHERE id = :id FOR UPDATE";
            $stmtStock = $this->conn->prepare($queryStock);
            $stmtStock->bindValue(':id', $libro_id, PDO::PARAM_INT);
            $stmtStock->execute();
            $libro = $stmtStock->fetch();

            if (!$libro || $libro['cantidad'] <= 0) {
                $this->conn->rollBack();
                return false;
            }

            $queryPrestamo = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado)
                              VALUES (:libro_id, :usuario_id, CURDATE(), 'activo')";
            $stmtPrestamo = $this->conn->prepare($queryPrestamo);
            $stmtPrestamo->bindValue(':libro_id', $libro_id, PDO::PARAM_INT);
            $stmtPrestamo->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtPrestamo->execute();

            $queryUpdate = "UPDATE libros SET cantidad = cantidad - 1 WHERE id = :id";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindValue(':id', $libro_id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function devolverLibro($prestamo_id) {
        try {
            $this->conn->beginTransaction();

            $queryPrestamo = "SELECT libro_id, estado FROM prestamos WHERE id = :id FOR UPDATE";
            $stmtPrestamo = $this->conn->prepare($queryPrestamo);
            $stmtPrestamo->bindValue(':id', $prestamo_id, PDO::PARAM_INT);
            $stmtPrestamo->execute();
            $prestamo = $stmtPrestamo->fetch();

            if (!$prestamo || $prestamo['estado'] !== 'activo') {
                $this->conn->rollBack();
                return false;
            }

            $queryUpdatePrestamo = "UPDATE prestamos
                                    SET fecha_devolucion = CURDATE(), estado = 'devuelto'
                                    WHERE id = :id";
            $stmtUpdatePrestamo = $this->conn->prepare($queryUpdatePrestamo);
            $stmtUpdatePrestamo->bindValue(':id', $prestamo_id, PDO::PARAM_INT);
            $stmtUpdatePrestamo->execute();

            $queryUpdateLibro = "UPDATE libros
                                 SET cantidad = cantidad + 1
                                 WHERE id = :id";
            $stmtUpdateLibro = $this->conn->prepare($queryUpdateLibro);
            $stmtUpdateLibro->bindValue(':id', $prestamo['libro_id'], PDO::PARAM_INT);
            $stmtUpdateLibro->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function obtenerPrestamosActivos() {
        $query = "SELECT p.*, l.titulo, u.nombre
                  FROM prestamos p
                  INNER JOIN libros l ON p.libro_id = l.id
                  INNER JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.estado = 'activo'
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}