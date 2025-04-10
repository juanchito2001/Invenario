<?php
session_start();

// sesión y rol
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Conexion a bd
$conn = new mysqli("localhost", "root", "", "gestion_proyectos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'add':
            case 'edit':
                $username = $conn->real_escape_string($_POST['username']);
                $email = $conn->real_escape_string($_POST['email']);
                $password = $conn->real_escape_string($_POST['password']);
                $rol = $conn->real_escape_string($_POST['rol']);
                
                if ($_POST['action'] === 'add') {
                    $stmt = $conn->prepare("INSERT INTO usuarios (username, email, password, rol) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $username, $email, $password, $rol);
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $conn->prepare("UPDATE usuarios SET username = ?, email = ?, password = ?, rol = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $username, $email, $password, $rol, $id);
                }
                $stmt->execute();
                $stmt->close();
                break;
        }
    }
}

// Obtener usuarios
$users = [];
$result = $conn->query("SELECT id, username, email, password, rol FROM usuarios");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <!-- Botón para abrir el sidebar -->
    <button class="toggle-btn" id="toggleSidebar">☰</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
        <h2><span class="brand label-text">Variedades</span> <span class="label-text">CH</span></h2>
            <button class="close-btn" id="closeSidebar">✖</button>
        </div>
        <ul>
        <li><a href="#"><i class="fas fa-home"></i> <span class="label-text">Dashboard</span></a></li>
<li><a href="admin.php"><i class="fas fa-user-cog"></i> <span class="label-text">Usuarios</span></a></li>
<li><a href="administrar_productos.php"><i class="fas fa-wrench"></i> <span class="label-text">Administrar Insumos</span></a></li>
<li><a href="#"><i class="fas fa-copy"></i> <span class="label-text">Páginas</span></a></li>
<li><a href="#"><i class="fas fa-chart-line"></i> <span class="label-text">Gráficos</span></a></li>
<li><a href="#"><i class="fas fa-table"></i> <span class="label-text">Usuarios</span></a></li>
<li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span class="label-text">Cerrar sesión</span></a></li>

        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="main-content" id="mainContent">
        <div class="topbar">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar usuario..." id="searchInput">
                <select id="roleFilter">
                    <option value="">Todos los roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="comprador">Comprador</option>
                </select>
            </div>
            <div class="user">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Lista de Usuarios</h3>
                <button class="add-user" id="addUserBtn">
                    <i class="fas fa-plus"></i> Agregar Usuario
                </button>
            </div>
            
            <div class="table-container">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>ID <i class="fas fa-sort" data-column="0"></i></th>
                            <th>Usuario <i class="fas fa-sort" data-column="1"></i></th>
                            <th>Email <i class="fas fa-sort" data-column="2"></i></th>
                            <th>Contraseña</th>
                            <th>Rol <i class="fas fa-sort" data-column="4"></i></th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr data-id="<?php echo $user['id']; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>••••••</td>
                            <td>
                                <span class="role-badge <?php echo $user['rol'] === 'administrador' ? 'admin' : ($user['rol'] === 'vendedor' ? 'seller' : 'buyer'); ?>">
                                    <?php echo htmlspecialchars($user['rol']); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="edit" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <button disabled><i class="fas fa-angle-double-left"></i></button>
                <button disabled><i class="fas fa-angle-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button><i class="fas fa-angle-right"></i></button>
                <button><i class="fas fa-angle-double-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Modal para usuarios -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            <h2 id="modalTitle">Agregar Nuevo Usuario</h2>
            <form id="userForm" method="POST" action="admin.php">
                <input type="hidden" id="userId" name="id">
                <input type="hidden" name="action" id="formAction" value="add">
                
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select id="role" name="rol" required>
                        <option value="">Seleccionar rol</option>
                        <option value="administrador">Administrador</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="comprador">Comprador</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel" id="cancelBtn">Cancelar</button>
                    <button type="submit" class="save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal confirm-modal" id="confirmModal">
        <div class="modal-content">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar este usuario?</p>
            <form id="deleteForm" method="POST" action="admin.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                <div class="form-actions">
                    <button type="button" class="cancel" id="cancelDelete">Cancelar</button>
                    <button type="submit" class="delete">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin_script.js"></script>
</body>
</html>