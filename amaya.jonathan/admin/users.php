<?php

require_once __DIR__ . "/../../php/functions.php";

$users = file_get_json(__DIR__ . "/../../data/users.json");







function showUserPage($user) {

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

        <h2>Edit User</h2>

        <form>

            <div class="form-control">
                <label for="user-name">Name</label>
                <input type="text" id="user-name" name="name" value="$name">
            </div>

            <div class="form-control">
                <label for="user-type">Type</label>
                <input type="text" id="user-type" name="type" value="$type">
            </div>

            <div class="form-control">
                <label for="user-email">Email</label>
                <input type="email" id="user-email" name="email" value="$email">
            </div>

            <div class="form-control">
                <label for="user-classes">Classes</label>
                <input type="text" id="user-classes" name="classes" value="$classes">
            </div>

            <button type="button" class="sophea-button">
                Save Changes
            </button>

        </form>

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

        if(isset($_GET['id']) && ctype_digit((string)$_GET['id']) && isset($users[(int)$_GET['id']])) {
            showUserPage($users[(int)$_GET['id']]);
        } else {

        ?>
        <h2>Sophea User Directory</h2>

            <nav class="nav">
                <ul>

            <?php

            for($i=0;$i<count($users);$i++){
                echo "<li>
                    <a href='/IXD608OL1FA26/amaya.jonathan/admin/users.php?id=$i'>{$users[$i]->name}</a>
                    
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