<?php
// include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/RegistrationModels.php';
 

$registration = new RegistrationModel($connection); 
 
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
 
$totalUsers = $registration->getTotalUsers();
$result = $registration->getAllUsers($limit, $offset);
$totalPages = ceil($totalUsers / $limit);





?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
 <style>
    .btn a {
            color: white !important;
            text-decoration: none;  
        }
    
 </style>
</head>
<body>

<div class="d-flex justify-content-between">
    <h2 style="text-align:center; margin: auto;">Welcome to Dashboard</h2>


    <button type="button" class="btn btn-primary p-3"> <a href="add_user.php" class=" ">+ Add User</a></button>
</div>

<br>

<table class="table table-striped ">
 
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Email</th>
      <th scope="col">Phone</th>
      <th scope="col">Designation</th>
      <th scope="col">Created_by</th>
      <th scope="col">Updated_by</th>
      <th scope="col">File</th>
      <th scope="col">Actions</th>
    </tr>
 
  <?php while($row = $result->fetch_assoc()): ?>
 
   <tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td><?= $row['designation'] ?></td>
     <td><?= $row['name'] ?></td>
            <td><?= $row['updated_by'] ?: '—' ?></td>


      <td>
    <?php if (!empty($row['cv_path'])): ?>
        <a href="<?= '../uploads/' . $row['id'] . '/' . basename($row['cv_path']) ?>" 
           class="btn btn-info btn-sm" 
           download="<?= $row['name'] ?>.pdf">
           Download PDF
        </a>
    <?php else: ?>
        
    <?php endif; ?>
</td>
<td>
 <button type="button" class="btn btn-success "> <a class="edit" href="editin_user.php?id=<?= $row['id'] ?>">Edit</a></button>
       <button type="button" class="btn btn-danger">  
        <a href="../Controller/RegisterController.php?action=delete&id=<?= $row['id'] ?>" 
   onclick="return confirm('Delete this user?')">
   Delete
</a>


      </button>
</td>
</tr>
     <?php endwhile; ?>
  
</table>

    <nav>
    <ul class="pagination d-flex justify-content-center mb-5">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<!-- <button type="button" class="btn btn-primary  "> <a href="Logout.php" class="logout">Logout</a></button> -->



<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>


 