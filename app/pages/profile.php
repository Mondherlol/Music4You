<?php require page('includes/header'); ?>


<?php
$id = $URL[1] ?? null;

$query = "select * from users where id = :id limit 1";
$row = db_query_one($query, ['id' => $id]);

if ($_SERVER['REQUEST_METHOD'] == "POST" && $row) {
    $errors = [];

    //data validation
    if (empty($_POST["username"])) {
        $errors['username'] = "a username is required";
    } else {
        if (!preg_match("/^[a-zA-Z]+$/", $_POST['username'])) {
            $errors['username'] = "a username can only have letters with no spaces";

        }
    }
    if (empty($_POST["email"])) {
        $errors['email'] = "a email is required";
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "email is not valid";

        }
    }
    if (!empty($_POST["password"])) {
        if ($_POST['password'] != $_POST['retype_password']) {
            $errors['password'] = "password do not match";

        } else {
            if (strlen($_POST['password']) < 8) {
                $errors['password'] = "password must be 8 character or more";

            }
        }
    }
    if (empty($_POST["role"])) {

        $errors['role'] = "a role is required";
    }
    if (empty($errors)) {
        $values = [];
        $values['username'] = trim($_POST['username']);
        $values['email'] = trim($_POST['email']);
        $values['role'] = trim($_POST['role']);
        $values['id'] = $id;


        $query = "update users set email = :email, username = :username, role= :role where id= :id limit 1";

        if (!empty($_POST['password'])) {
            $query = "update users set email = :email, password= :password, username = :username, role= :role where id= :id limit 1";
            $values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        db_query($query, $values);
        message("user edited successfully");
        redirect('admin/users');

    }
}




?>
<div class="" style="max-width:500px; margin:auto;">
    <form action="" method="post">
        <h3>Edit User</h3>

        <?php if (!empty($row)): ?>

            <input class="form-control my-1" type="text" name="username"
                value="<?= set_value('username', $row['username']) ?>" placeholder="Username">
            <?php if (!empty($errors['username'])): ?>
                <small class="text-danger"><?= $errors['username'] ?></small>
            <?php endif; ?>
            <input class="form-control my-1" type="email" name="email" value="<?= set_value('email', $row['email']) ?>"
                placeholder="Email">
            <?php if (!empty($errors['email'])): ?>
                <small class="text-danger"><?= $errors['email'] ?></small>
            <?php endif; ?>
            <select name="role" id="role" class="form-select my-1">
                <option value="">--Select Role--</option>
                <option <?= set_select('role', 'user', $row['role']) ?> value="user">User</option>
                <option <?= set_select('role', 'admin', $row['role']) ?> value="admin">Admin</option>
            </select>

            <?php if (!empty($errors['role'])): ?>
                <small class="text-danger"><?= $errors['role'] ?></small>
            <?php endif; ?>
            <input class="form-control my-1" type="password" name="password"
                placeholder="Password (leave empty to keep old one)" value="<?= set_value('password') ?>"
                placeholder="Password">
            <?php if (!empty($errors['password'])): ?>
                <small class="text-danger"><?= $errors['password'] ?></small>
            <?php endif; ?>
            <input class="form-control my-1" type="password" name="retype_password"
                value="<?= set_value('retype_password') ?>" placeholder="Retype Password">

            <button class="btn bg-orange">Save</button>
            <a href="<?= ROOT ?>/admin/users">
                <button type="button" class="float-end btn">Back</button>
            </a>
        <?php else: ?>
            <div class="alert">That record was not found</div>
            <a href="<?= ROOT ?>/admin/users">
                <button type="button" class="float-end btn">Back</button>
            </a>
        <?php endif; ?>
    </form>
</div>
<?php require page('includes/footer'); ?>