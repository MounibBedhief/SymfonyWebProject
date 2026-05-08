<?php
session_start();

require_once '../backend/autoloader.php';

$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (empty($doctor_id)) { die('Doctor not found.'); }

try {
    $db = ConnexionDB::getInstance();
    $stmt = $db->prepare("SELECT id, name, specialization, experience, consultation_fee AS fee, hospital, about, rating, reviews, image_path, email, phone, office_place FROM Doctor WHERE id = ? LIMIT 1");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doctor) { die('Doctor not found.'); }
} catch (Exception $e) { die('Error loading doctor profile: ' . $e->getMessage()); }

$docImagePath = !empty($doctor['image_path']) ? $doctor['image_path'] : 'https://cdn-icons-png.flaticon.com/512/3774/3774299.png';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title><?= (strpos($doctor['name'], 'Dr.') === 0) ? htmlspecialchars($doctor['name']) : 'Dr. ' . htmlspecialchars($doctor['name']) ?> | HealthConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="view-profile.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
    <!-- Consistent Navbar -->
    <header class="navbar">
      <div class="logo">Health <span>Connect</span></div>
      <nav class="navigation">
        <a href="../homepage/connected.php">Home</a>
        <a href="../rechercher_docteurs/rechercher_docteurs.php">Specialists</a>
      </nav>
      <div class="header-actions">
        <a href="../book/book.php?id=<?= $doctor['id']; ?>" class="btn-top-book">Book Appointment</a>
      </div>
    </header>

    <main class="profile-v3">
      <div class="page-header">
         <div class="container">
            <h2 class="page-title">Doctor Profile</h2>
         </div>
      </div>

      <div class="container main-layout">
        <div class="profile-card shadow-card">
          <div class="profile-main-info">
            <div class="profile-image-section">
              <img src="<?= htmlspecialchars($docImagePath); ?>" alt="<?= htmlspecialchars($doctor['name']); ?>" class="profile-large-img">
            </div>
            
            <div class="profile-info-section">
              <div class="title-meta">
                <span class="specialty-badge"><?= htmlspecialchars($doctor['specialization']); ?></span>
                <h1><?= (strpos($doctor['name'], 'Dr.') === 0) ? htmlspecialchars($doctor['name']) : 'Dr. ' . htmlspecialchars($doctor['name']) ?></h1>
                <div class="rating-summary">
                  <i class="fas fa-star"></i> <?= htmlspecialchars($doctor['rating']); ?>
                  <span>(<?= htmlspecialchars($doctor['reviews']); ?> reviews)</span>
                </div>
              </div>

              <div class="stats-grid">
                <div class="stat-box">
                  <i class="fas fa-calendar-alt"></i>
                  <div class="stat-text">
                    <label>Experience</label>
                    <strong><?= htmlspecialchars($doctor['experience'] ?: '10+ Years'); ?></strong>
                  </div>
                </div>
                <div class="stat-box">
                  <i class="fas fa-money-bill-wave"></i>
                  <div class="stat-text">
                    <label>Consultation Fee</label>
                    <strong><?= htmlspecialchars($doctor['fee'] ?: '$50'); ?></strong>
                  </div>
                </div>
              </div>

              <div class="cta-section">
                <a href="../book/book.php?id=<?= $doctor['id']; ?>" class="primary-btn">Book Appointment Now</a>
              </div>
            </div>
          </div>

          <div class="profile-details-section">
             <div class="details-cols">
                <div class="details-main">
                  <h3 class="section-title">About Doctor</h3>
                  <p class="description-text"><?= nl2br(htmlspecialchars($doctor['about'] ?: 'A dedicated professional providing top-tier medical assistance.')); ?></p>

                  <h3 class="section-title">Location & Facility</h3>
                  <div class="facility-box">
                    <div class="facility-icon"><i class="fas fa-hospital"></i></div>
                    <div class="facility-info">
                      <h4><?= htmlspecialchars($doctor['hospital'] ?: 'HealthConnect Central'); ?></h4>
                      <p><?= htmlspecialchars($doctor['office_place'] ?: 'Consultation Room'); ?></p>
                    </div>
                  </div>
                </div>

                <div class="details-side">
                  <h3 class="section-title">Contact Information</h3>
                  <div class="contact-list">
                    <div class="contact-item">
                      <i class="fas fa-envelope"></i>
                      <div class="contact-data">
                        <label>Email Address</label>
                        <p><?= htmlspecialchars($doctor['email']); ?></p>
                      </div>
                    </div>
                    <div class="contact-item">
                      <i class="fas fa-phone"></i>
                      <div class="contact-data">
                        <label>Phone Number</label>
                        <p><?= htmlspecialchars($doctor['phone'] ?: 'N/A'); ?></p>
                      </div>
                    </div>
                  </div>
                </div>
             </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="footer">
      <p>&copy; 2026 HealthConnect. All rights reserved.</p>
    </footer>
  </body>
</html>
