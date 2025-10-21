<?php
include_once '../config/sessioon_check.php';
include_once '../config/connection.php';
include_once '../Models/RegistrationModels.php';

$model = new RegistrationModel($connection);
$user = null;

if (isset($_GET['id'])) {
    $user = $model->getUserById($_GET['id']);
}

if (!$user) {
    die("User not found!");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    
    <style>
        form { width: 700px; margin: 40px auto; background: #f9f9f9; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; margin-bottom:140px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; }
        button { background: #007bff; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
<h2 style="text-align:center; ">Edit User</h2>

<!-- <form action="../Controller/RegisterController.php" method="POST" enctype="multipart/form-data"> -->

<form action="../Controller/RegisterController.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <label>Name:</label>
    <input type="text" name="name" value="<?= $user['name'] ?>"  >

    <label>Email:</label>
    <input type="email" name="email" value="<?= $user['email'] ?>"  >

    <label>Phone:</label>
    <input type="text" name="phone" value="<?= $user['phone'] ?>"  >

    <label>Designation:</label>
    <input type="text" name="designation" value="<?= $user['designation'] ?>"  >

    <label>Upload CV:</label>
    <input type="file" name="cv_path" accept=".pdf">
<?php if(!empty($user['cv_path'])): ?>
    <p>Existing CV: <a href="../Uploads/<?= $user['id'] ?>/<?= $user['cv_path'] ?>" target="_blank"><?= $user['cv_path'] ?></a></p>
<?php endif; ?>
    <button type="submit" name="update_user">Update User</button>
</form>



</body>
</html>
