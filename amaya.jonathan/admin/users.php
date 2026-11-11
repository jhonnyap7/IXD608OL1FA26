<?php

require_once __DIR__ . "/../../php/functions.php";

$users = file_get_json(__DIR__ . "/../../data/users.json");

$filename = __DIR__ . "/../../data/users.json";

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    in_array(
        $_GET['action'] ?? '',
        ['update', 'create', 'delete'],
        true
    )
) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (
    (
        $_GET['action'] === 'delete' &&
        $id !== false &&
        $id !== null &&
        isset($users[$id])
    ) ||
    (
        isset(
            $_POST['user-name'],
            $_POST['user-type'],
            $_POST['user-email'],
            $_POST['user-classes']
        ) &&
        (
            $_GET['action'] === 'create' ||
            (
                $_GET['action'] === 'update' &&
                $id !== false &&
                $id !== null &&
                isset($users[$id])
            )
        )
    )
) {
        switch ($_GET['action']) {
            case 'update':
            $users[$id]->name = trim($_POST['user-name']);
            $users[$id]->type = trim($_POST['user-type']);
            $users[$id]->email = trim($_POST['user-email']);

            $users[$id]->classes = array_map(
                'trim',
                explode(',', $_POST['user-classes'])
        );

        $json = json_encode(
        $users,
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($filename, $json, LOCK_EX) === false) {
                    throw new RuntimeException('Unable to save user data.');
                }

                header('Location: /IXD608OL1FA26/amaya.jonathan/admin/users.php?id=' . $id, true, 303);
                exit;

        case 'create':

                $new_user = (object)[
                    'name' => trim($_POST['user-name']),
                    'type' => trim($_POST['user-type']),
                    'email' => trim($_POST['user-email']),
                    'classes' => array_map(
                        'trim',
                        explode(',', $_POST['user-classes'])
                    )
                ];

                $users[] = $new_user;

                $id = count($users) - 1;

                $json = json_encode(
                    $users,
                    JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
                );

        if (file_put_contents($filename, $json, LOCK_EX) === false) {
                    throw new RuntimeException('Unable to save user data.');
                }

                header('Location: /IXD608OL1FA26/amaya.jonathan/admin/users.php?id=' . $id, true, 303);
                exit;
                            case 'delete':

                array_splice($users, $id, 1);

                $json = json_encode(
                    $users,
                    JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
                );

        if (file_put_contents($filename, $json, LOCK_EX) === false) {
                    throw new RuntimeException('Unable to save user data.');
                }

                header('Location: /IXD608OL1FA26/amaya.jonathan/admin/users.php', true, 303);
                exit;
    }
}
}
$empty_user = (object)[
    'name' => '',
    'type' => '',
    'email' => '',
    'classes' => []
];

function showUserPage($user, $id) {
    $action = ($id === 'new') ? 'create' : 'update';
    $title = ($id === 'new') ? 'Create User' : 'Edit User';
    $button_text = ($id === 'new') ? 'Create User' : 'Save Changes';

    $classes = implode(", ", $user->classes);

    $name = htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($user->type, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8');
    $classes = htmlspecialchars($classes, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
    <nav class="nav nav-crumbs">
        <ul>
            <li><a href="/IXD608OL1FA26/amaya.jonathan/admin/users.php">Back to User List</a></li>
        </ul>
    </nav>

    <div class="sophea-user-editor">

        <h2>$title</h2>

    <form method="post" action="/IXD608OL1FA26/amaya.jonathan/admin/users.php?id=$id&action=$action">
        <div class="form-grid">
            <div class="form-control">
                <label for="user-name">Name</label>
                <input type="text" id="user-name" name="user-name" value="$name">
            </div>

            <div class="form-control">
                <label for="user-type">Type</label>
                <input type="text" id="user-type" name="user-type" value="$type">
            </div>

            <div class="form-control">
                <label for="user-email">Email</label>
                <input type="email" id="user-email" name="user-email" value="$email">
            </div>

            <div class="form-control">
                <label for="user-classes">Classes</label>
                <input type="text" id="user-classes" name="user-classes" value="$classes">
            </div>

            </div>

            <button type="submit" class="sophea-button">
                $button_text
            </button>

        </form>
HTML;

if ($id !== 'new') {
    echo <<<HTML
        <form method="post"
              action="/IXD608OL1FA26/amaya.jonathan/admin/users.php?id=$id&action=delete"
              onsubmit="return confirm('Are you sure you want to delete this user?');">

            <button type="submit" class="sophea-button">
                Delete User
            </button>

        </form>
HTML;
}

echo <<<HTML

    </div>
HTML;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SOPHEA | User Management</title>

<?php include __DIR__ . "/../../parts/meta.php"; ?>
<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-grid .form-control {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-grid input {
        width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

    <header class="navbar">
        <div class="container display-flex">
            <div class="flex-none">
                <h1>SOPHEA | User Management</h1>
            </div>
            <div class="flex-stretch"></div>
            <nav class="nav nav-flex flex-none">
                <ul>
                    <li><a href="/IXD608OL1FA26/amaya.jonathan/admin/users.php">Manage Users</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">

        <div class="card soft">
            
            <?php

        if (($_GET['id'] ?? '') === 'new') {
    showUserPage($empty_user, 'new');
        } elseif (
    isset($_GET['id']) &&
    ctype_digit((string)$_GET['id']) &&
    isset($users[(int)$_GET['id']])
    ) {
    showUserPage($users[(int)$_GET['id']], (int)$_GET['id']);
        } else {

        ?>
        <h2>Sophea User Directory</h2>
        <a href="/IXD608OL1FA26/amaya.jonathan/admin/users.php?id=new" class="sophea-button">
            + Create New User
        </a>

            <nav class="nav">
                <ul>

            <?php

            for ($i = 0; $i < count($users); $i++) {
    $name = htmlspecialchars(
        $users[$i]->name,
        ENT_QUOTES,
        'UTF-8'
    );

    echo "<li>
        <a href='/IXD608OL1FA26/amaya.jonathan/admin/users.php?id=$i'>$name</a>
    </li>";
}

            ?>
                </ul>
             </nav>

             <?php } ?>

        </div>

    </div>

</body>
</html>