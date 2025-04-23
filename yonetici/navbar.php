
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- Marka / Logo -->
    <a class="navbar-brand" href="dashboard.php">
      Yönetim Paneli
    </a>
    <!-- Mobil Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Navbar İçerikleri -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        
        <!-- Yönetici İşlemleri Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminOperationsDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Yönetici İşlemleri
          </a>
          <ul class="dropdown-menu" aria-labelledby="adminOperationsDropdown">
            <li>
              <a class="dropdown-item" href="qr-tarat.php">
                <i class="bi bi-graph-up me-2"></i>QR KOD OKUT!
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="yoneticiler.php">
                <i class="bi bi-people me-2"></i>Yöneticiler
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="yonetici-ekle.php">
                <i class="bi bi-person-plus me-2"></i>Yönetici Ekle
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="grafikler.php">
                <i class="bi bi-graph-up me-2"></i>Grafikler
              </a>
            </li>
          </ul>
        </li>
        
        <!-- Menü İşlemleri Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="menuOperationsDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Menü İşlemleri
          </a>
          <ul class="dropdown-menu" aria-labelledby="menuOperationsDropdown">
            <li>
              <a class="dropdown-item" href="menu.php">
                <i class="bi bi-list-ul me-2"></i>Menüyü Görüntüle
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="menu-ekle.php">
                <i class="bi bi-plus-circle me-2"></i>Menüye Ekle
              </a>
            </li>
          </ul>
        </li>
        
        <!-- Kampanya İşlemleri Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="campaignOperationsDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Kampanya İşlemleri
          </a>
          <ul class="dropdown-menu" aria-labelledby="campaignOperationsDropdown">
            <li>
              <a class="dropdown-item" href="kampanyalar.php">
                <i class="bi bi-megaphone me-2"></i>Kampanyalar
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="kampanya-ekle.php">
                <i class="bi bi-plus-square me-2"></i>Kampanya Ekle
              </a>
            </li>
          </ul>
        </li>
        
        <!-- Kupon İşlemleri Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="couponOperationsDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Kupon İşlemleri
          </a>
          <ul class="dropdown-menu" aria-labelledby="couponOperationsDropdown">
            <li>
              <a class="dropdown-item" href="kuponlar.php">
                <i class="bi bi-megaphone me-2"></i>Kuponlar
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="kupon-ekle.php">
                <i class="bi bi-plus-square me-2"></i>Kupon Ekle
              </a>
            </li>
          </ul>
        </li>
        
        <!-- Kullanıcı İşlemleri Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userOperationsDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Kullanıcı İşlemleri
          </a>
          <ul class="dropdown-menu" aria-labelledby="userOperationsDropdown">
            <li>
              <a class="dropdown-item" href="kullanicilar.php">
                <i class="bi bi-graph-up me-2"></i>Kullanıcılar
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="kullanici-ekle.php">
                <i class="bi bi-envelope me-2"></i>Kullanıcı Ekle
              </a>
            </li>
          </ul>
        </li>
        
        <!-- Çıkış -->
        <li class="nav-item">
          <a class="nav-link" href="cikis.php">
            <i class="bi bi-box-arrow-right me-2"></i>Çıkış
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
