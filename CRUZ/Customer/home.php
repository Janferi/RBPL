<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee & Work Space</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .background-image {
            background-image: url('../image/backgroundhome.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            position: relative;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.4);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 30px;
            display: flex;
            align-items: center;
        }

        .logo img {
            top: 50px;
            width: 200px;
        }

        .logo-text {
            margin-left: 10px;
        }

        .cart-icon {
            position: absolute;
            top: 70px;
            right: 100px;
        }

        h1 {
            font-size: 100px;
            font-weight: bold;
            color: rgb(29, 31, 33);
        }

        h2 {
            font-size: 60px;
            font-weight: bold;
            color: rgb(33, 36, 39);
        }

        .h4 {
            font-size: 40px;
            font-weight: bold;
            color: rgb(33, 36, 39);
        }

        .btn {
            font-size: 20px;
            font-weight: bold;
            color: rgb(33, 36, 39);
        }
    </style>
</head>

<body>
    <div class="background-image">
        <div class="overlay">
            <div class="logo">
                <img src="../image/logocruz.png" alt="Cruz Coffee & Work Space logo">
            </div>
            <a href="keranjang.php">
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart fa-2x text-dark"></i>
                </div>
            </a>
            <div class="text-center mt-5">
                <h1 class=" font-weight-bold text-black">Good Place</h1>
                <h2 class=" font-weight-bold text-black mt-0">Good Friend</h2>
                <p class="h4 text-black mt-0">Coffee & Work Space</p>
                <a href="halamanmenu.php"><button class="btn btn-warning mt-2 px-4 py-2 font-weight-bold rounded-pill">Order here</button></a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>