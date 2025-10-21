 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful 🎉</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #402ae7ff, #4f3be4ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 40px;
            text-align: center;
            max-width: 450px;
            background: #fff;
            animation: fadeIn 1s ease-in-out;
        }
        .check-icon {
            font-size: 70px;
            color: #28a745;
            margin-bottom: 20px;
            animation: pop 0.6s ease;
        }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .btn-custom {
            background-color: #402ae7ff;
            border: none;
            color: #fff;
            font-weight: 500;
            border-radius: 30px;
            padding: 10px 25px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #402ae7ff;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="check-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h2 class="mb-3">Thank You! 🎉</h2>
    <p>Your order has been placed successfully.</p>
    <p class="text-muted">You’ll receive a confirmation email shortly.</p>
    <a href="ecommerce.php" class="btn btn-custom mt-3">
        <i class="fa-solid fa-arrow-left"></i> Continue Shopping
    </a>
</div>

</body>
</html>
