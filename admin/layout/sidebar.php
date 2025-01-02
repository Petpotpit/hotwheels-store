<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons for icons -->
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="../image/logo.png" type="image/x-icon">

  <style>
    /* Custom Styling */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    /* Sidebar styles */
    .sidebar {
      transition: all 0.3s ease;
      width: 250px; /* Set fixed width */
      height: 100vh;
      background-color: #1e293b;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
    }
    .sidebar .logo-details {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }
    .sidebar .logo-name {
      font-size: 20px;
      font-weight: 600;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      position: relative;
      padding: 10px;
      margin: 10px 0;
    }
    .sidebar ul li a {
      display: flex;
      align-items: center;
      color: #ffffff;
      text-decoration: none;
      padding: 10px;
      transition: background-color 0.3s ease;
    }
    .sidebar ul li a:hover {
      background-color: #4f46e5;
      color: #ffffff;
    }
    .sidebar ul li a .links_name {
      margin-left: 10px;
      font-size: 16px;
    }
    /* Home Section */
    .home-section {
      margin-left: 250px;
      padding: 20px;
      min-height: 100vh;
    }
    /* Dropdown menu for mobile */
    @media (max-width: 768px) {
      .sidebar {
        width: 0;
      }
      .sidebar.open {
        width: 250px;
      }
      .home-section {
        margin-left: 0;
      }
    }
  </style>
</head>

<body class="bg-gray-100">
  <!-- Sidebar -->
  <div id="sidebar" class="sidebar">
    <div class="logo-details">
      <div class="logo-name text-white text-lg">Dashboard</div>
      <button id="toggle-sidebar" class="text-white text-3xl">
        <i class="bx bx-menu"></i>
      </button>
    </div>
    
    <!-- Sidebar Menu -->
    <ul>
      <li>
        <a href="../admin/dashboard.php" class="flex items-center">
          <i class="bx bx-grid-alt text-xl"></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>

      <!-- Manajemen Produk-->
      <li>
        <a href="../admin/manajemen_produk.php" class="flex items-center">
          <i class="bx bx-package text-xl"></i>
          <span class="links_name">Manajemen Produk</span>
        </a>
      </li>

      <!-- Manajemen Pesanan -->
      <li>
        <a href="../admin/manajemen_pesanan.php" class="flex items-center">
          <i class="bx bx-cart text-xl"></i>
          <span class="links_name">Manajemen Pesanan</span>
        </a>
      </li>

      <!-- Data Pelanggan -->
      <li>
        <a href="../admin/data_pelanggan.php" class="flex items-center">
          <i class="bx bx-group text-xl"></i>
          <span class="links_name">Data Pelanggan</span>
        </a>
      </li>

      <!-- Promosi -->
      <li>
        <a href="../admin/promosi.php" class="flex items-center">
          <i class="bx bx-tag text-xl"></i>
          <span class="links_name">Promosi</span>
        </a>
      </li>

      <!-- Laporan Penjualan -->
      <li>
        <a href="../admin/laporan_penjualan.php" class="flex items-center">
          <i class="bx bx-line-chart text-xl"></i>
          <span class="links_name">Laporan Penjualan</span>
        </a>
      </li>

      <!-- Manajemen Stock -->
      <li>
        <a href="../admin/manajemen_stock.php" class="flex items-center">
          <i class="bx bx-box text-xl"></i>
          <span class="links_name">Manajemen Stock</span>
        </a>
      </li>

      <!-- Manajemen Pengguna -->
      <li>
        <a href="../admin/manajemen_pengguna.php" class="flex items-center">
          <i class="bx bx-user text-xl"></i>
          <span class="links_name">Manajemen Pengguna</span>
        </a>
      </li>

      <!-- Logout -->
      <li>
        <a href="../logout.php" class="flex items-center">
          <i class="bx bx-log-out text-xl"></i>
          <span class="links_name">Logout</span>
        </a>
      </li>
    </ul>
  </div>

  <!-- Home Section
  <div class="home-section">
    <div class="container-fluid"> -->
      <!-- Content goes here -->
      <!-- <h1>Welcome to the Admin Dashboard</h1>
      <p>This is where your admin content will be displayed.</p>
    </div>
  </div> -->

  <script>
    // Toggle Sidebar
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    toggleSidebar.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });

    // Dropdown menu for mobile
    const dropdowns = document.querySelectorAll('.sidebar .relative');
    dropdowns.forEach(dropdown => {
      dropdown.addEventListener('click', () => {
        const menu = dropdown.querySelector('.dropdown-menu');
        menu.classList.toggle('hidden');
      });
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>



<!-- <script>
    let sidebar = document.querySelector(".sidebar");
    let closeBtn = document.querySelector("#btn");
    let dropdownBtns = document.querySelectorAll(".dropdown-btn");

    // Toggle Sidebar
    closeBtn.addEventListener("click", () => {
      sidebar.classList.toggle("open");
      menuBtnChange();
    });

    function menuBtnChange() {
      if (sidebar.classList.contains("open")) {
        closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
      } else {
        closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      }
    }

    // Toggle Dropdown
    dropdownBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        let parent = btn.parentElement;
        parent.classList.toggle("open");
      });
    });
  </script>  kkkkkkkkk kasdnuh s jbjb bdsabj asdjasbd  jada9qwdajb jbiabdua w ajbdjbu9 ononadw jba jbosuwisfnreuciee nnjb jdbjfw8 djbfifba jbsd iab dw  jbdaj bwaj jb awabi ajbdjbu9a dasdb adadbw adadad
  d asd ashbddbadbjadbjabdajbdajdbja jbdjsdbjsbdjw wwbsh -->