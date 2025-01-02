<?php
session_start();
require('functions.php');

if (isset($_POST['login'])) {
    // Debugging
    echo "Form submitted<br>";
    echo "Email: " . $_POST['pengguna_email'] . "<br>";
    echo "Password: " . $_POST['pengguna_password'] . "<br>";

    loginPengguna($_POST['pengguna_email'], $_POST['pengguna_password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Hotwheels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900">
    <div class="flex justify-center items-center min-h-screen bg-cover bg-center" style="background-image: url('image/background.png');">
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-xl w-full max-w-md backdrop-filter backdrop-blur-sm">
            <h1 class="text-center text-3xl font-bold text-blue-800 mb-6">Toko Hotwheels</h1>
            <h3 class="text-center text-xl font-semibold text-gray-700 mb-8">Silahkan <b class="text-blue-600">Login</b></h3>

            <form action="" method="post" class="space-y-6">
                <div class="mb-4">
                    <label for="pengguna_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <input type="email" name="pengguna_email" id="pengguna_email" class="w-full py-3 px-4 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 bg-white bg-opacity-80 text-gray-900 placeholder-gray-500" placeholder="Email" required>
                        <i class='bx bx-envelope absolute right-4 top-3 text-blue-500'></i>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="pengguna_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="pengguna_password" id="pengguna_password" class="w-full py-3 px-4 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 bg-white bg-opacity-80 text-gray-900 placeholder-gray-500" placeholder="Password" required>
                        <i class='bx bxs-lock absolute right-4 top-3 text-blue-500'></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <!-- <div class="flex items-center">
                        <input type="checkbox" id="remember_me" name="remember_me" class="h-4 w-4 text-blue-600">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                    </div> -->
                    <a href="#" class="text-sm text-blue-600 hover:underline">Lupa password?</a>
                </div>
                <button type="submit" name="login" class="w-full py-3 px-4 bg-yellow-500 text-blue-900 rounded-full font-semibold hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 transition duration-200">Login</button>
            </form>
            <div class="mt-6 text-center">
                <a href="registrasi.php" class="text-blue-600 hover:underline">Belum punya akun? Daftar di sini</a>
            </div>
            <div class="mt-6">
            <div class="mt-6">
    <h4 class="text-center text-sm text-gray-600 mb-2">Tester Akun</h4>
    <div class="flex justify-center space-x-4">
        <button onclick="showCredentials('user')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            User
        </button>
        <button onclick="showCredentials('admin')" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
            Admin
        </button>
    </div>
</div>

<!-- Pop-up modals -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">User Credentials</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Email: <span class="font-medium">all@gmail.com</span></p>
                <p class="text-sm text-gray-500">Password: <span class="font-medium">lin</span></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeUserModal" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="adminModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Admin Credentials</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Email: <span class="font-medium">alleuy@gmail.com</span></p>
                <p class="text-sm text-gray-500">Password: <span class="font-medium">lin</span></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeAdminModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>        </div>
    </div>

    <script>
function showCredentials(type) {
    const modal = document.getElementById(type + 'Modal');
    modal.classList.remove('hidden');
}

document.getElementById('closeUserModal').addEventListener('click', function() {
    document.getElementById('userModal').classList.add('hidden');
});

document.getElementById('closeAdminModal').addEventListener('click', function() {
    document.getElementById('adminModal').classList.add('hidden');
});
</script> 
</body>
</html>
