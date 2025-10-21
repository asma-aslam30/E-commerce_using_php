<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/ProductModel.php';

$productModel = new ProductModel($connection);

$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalProducts = $productModel->getTotalProducts();
$products = $productModel->getAllProducts($limit, $offset);
$totalPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Products</title>
    <?php include('../layout/header_links.php'); ?>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include('../layout/sidebar.php'); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include('../layout/header.php'); ?>

                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">All Products</h3>
                        <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
                    </div>

                    <?php if (!empty($products) && count($products) > 0): ?>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Price (PKR)</th>
                                    <th>Hot Sale</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = $offset + 1;
                                foreach ($products as $row): 
                                ?>
                                    <tr>
                                        <td><?= $count++; ?></td>
                                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                                        <td><?= htmlspecialchars($row['product_description']); ?></td>
                                        <td><?= number_format($row['product_price'], 2); ?></td>
                                        <td>
                                            <?= $row['isHotSale'] ? '<span class="badge badge-danger">Yes</span>' : '<span class="badge badge-secondary">No</span>'; ?>
                                        </td>
                                        <td>
                                            <?= $row['isActive'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-warning">Inactive</span>'; ?>
                                        </td>
                                        <td><?= $row['product_created_at']; ?></td>
                                        <td><?= $row['product_created_by']; ?></td>
                                        <td class="d-flex justify-content-around">
                                            <a href="edit_product.php?id=<?= $row['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <?php if ($row['isActive']): ?>
                                                <a href="../Controller/ProductController.php?action=deactivate&id=<?= $row['product_id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to block this product?');">
                                                   Block
                                                </a>
                                            <?php else: ?>
                                                <a href="../Controller/ProductController.php?action=activate&id=<?= $row['product_id']; ?>" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Are you sure you want to activate this product?');">
                                                   Activate
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center mt-4">
                            <p class="text-muted">No products found.</p>
                        </div>
                    <?php endif; ?>

                    <nav>
                        <ul class="pagination d-flex justify-content-center mb-5">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>

                <?php include('../layout/footer.php'); ?>
                <?php include('../layout/footer_links.php'); ?>
            </div>
        </div>
    </div>
</body>
</html>
