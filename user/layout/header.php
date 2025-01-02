<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotwheels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="shortcut icon" href="https://omgsymbol.com/download/x/06/original/logo.jpg" type="image/x-icon">

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Spartan:wght@100;200;300;400;500;600;700;800;900&display=swap");
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Spartan", sans-serif;
        }
        h1 {
            font-size: 50px;
            line-height: 64px;
            color: #222;
        }
        h2 {
            font-size: 46px;
            line-height: 54px;
            color: #222;
        }
        h4 {
            font-size: 20px;
            color: #222;
        }
        h6 {
            font-weight: 700;
            font-size: 12px;
        }
        p {
            font-size: 16px;
            color: #465b52;
            margin: 15px 0 20px 0;
        }
        .section-p1 {
            padding: 40px 80px;
        }
        .section-m1 {
            padding: 40px 0px;
        }
        button.normal {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 30px;
            color: black;
            background-color: #fff;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            outline: none;
            transition: 0.2s;
        }
        body {
            width: 100%;
        }
        .logo {
            width: 60px;
        }
        #header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 80px;
            background-color: #E3E6F3;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.09);
            z-index: 999;
            position: sticky;
            top: 0;
            left: 0;
        }
        #navbar {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #navbar li {
            list-style: none;
            padding: 0 20px;
            position: relative;
        }
        #navbar li a {
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            color: rgb(12, 11, 11);
            transition: 0.3s ease;
        }
        #navbar li a:hover {
            color: #088178;
        }
        #navbar li a:hover,
        #navbar li a.active {
            color: #088178;
        }
        #navbar li a.active::after,
        #navbar li a:hover::after {
            content: "";
            width: 30%;
            height: 2px;
            background: #088178;
            position: absolute;
            bottom: -4px;
            left: 20px;
        }
        #mobile {
            display: none;
        }
        @media (max-width: 768px) {
            #navbar {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 0;
                right: 0;
                background-color: #E3E6F3;
                width: 100%;
                height: 100vh;
                align-items: center;
                justify-content: center;
                z-index: 999;
            }
            #navbar.active {
                display: flex;
            }
            #mobile {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #bar {
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
    <section id="header">
        <a href="#">
            <img src="../image/logo.png" class="logo" alt="Toko Hotwheels Logo">
        </a>

        <div>
            <ul id="navbar">
                <li><a class="active" href="../user/home.php">Home</a></li>
                <li><a href="../user/kategori.php">Produk</a></li>
                <li><a href="../user/contact.php">Contact</a></li>
                <li><a href="../user/akun.php">Akun</a></li>
                <li id="lg-bag"><a href="../user/keranjang.php"><i class="fas fa-shopping-cart"></i></a></li>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i></a>
                <!--  <a href="#" id="close"><img src="https://omgsymbol.com/download/x/06/original/logo.jpg" alt="Close" width="25px"></a> -->
            </ul>
        </div>
        <div id="mobile">
        <li><a class="active" href="../user/home.php">Home</a></li>
            <li><a href="../user/kategori.php">Produk</a></li>
            <li><a href="../user/contact.php">Contact</a></li>
            <li><a href="../user/akun.php">Akun</a></li>
            <li id="lg-bag"><a href="../user/keranjang.php"><i class="fas fa-shopping-cart"></i></a></li>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i></a>   
            <img src="https://omgsymbol.com/download/x/06/original/logo.jpg" width="25px" id="bar">
        </div>
    </section>
    <script>
        const bar = document.getElementById("bar")
        const close = document.getElementById("close")
        const nav = document.getElementById("navbar")

        if (bar) {
            bar.addEventListener("click", () => {
                nav.classList.add("active")
            })
        }
        if (close) {
            close.addEventListener("click", () => {
                nav.classList.remove("active")
            })
        }
    </script>
</body>
</html>
