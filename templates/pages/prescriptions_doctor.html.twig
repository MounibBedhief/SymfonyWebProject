<?php
session_start();

if (empty($_SESSION['doctor_id']) && empty($_SESSION['user_id'])) {
    header('Location: ../login_signup/login-register.php?force_login=1');
    exit;
}

$uploadSuccess = '';
$uploadError = '';

try {
    require_once '../backend/autoloader.php';
    $repo = new Repository_database();

  $repo->markPastScheduledAppointmentsAsCompleted();

    $doctorId = !empty($_SESSION['doctor_id']) ? (int) $_SESSION['doctor_id'] : (int) $_SESSION['user_id'];
    $result = $repo->getAllPrescriptionsForDoctor($doctorId);
    $prescriptions = is_array($result) ? $result : [];

    $db = ConnexionDB::getInstance();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_pdf'])) {
      $appointmentId = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;

      if ($appointmentId <= 0) {
        $uploadError = 'Invalid appointment.';
      } elseif (!isset($_FILES['prescription_pdf']) || $_FILES['prescription_pdf']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = 'Please select a valid PDF file.';
      } else {
        $file = $_FILES['prescription_pdf'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType = mime_content_type($file['tmp_name']);
        $allowedMime = ['application/pdf', 'application/x-pdf'];

        if ($extension !== 'pdf' || !in_array($mimeType, $allowedMime, true)) {
          $uploadError = 'Only PDF files are allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
          $uploadError = 'PDF size must be less than 5MB.';
        } else {
          $ownershipStmt = $db->prepare('SELECT appointment_id FROM Appointment WHERE appointment_id = ? AND doctor_id = ?');
          $ownershipStmt->execute([$appointmentId, $doctorId]);
          $ownedAppointment = $ownershipStmt->fetch(PDO::FETCH_ASSOC);

          if (!$ownedAppointment) {
            $uploadError = 'You can only upload for your own appointments.';
          } else {
            $uploadDir = __DIR__ . '/../uploads/prescriptions';
            if (!is_dir($uploadDir)) {
              mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
            $targetPath = $uploadDir . '/' . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
              $updateStmt = $db->prepare('UPDATE Appointment SET prescription_path = ? WHERE appointment_id = ? AND doctor_id = ?');
              if ($updateStmt->execute([$newFileName, $appointmentId, $doctorId])) {
                $uploadSuccess = 'PDF uploaded successfully.';
              } else {
                $uploadError = 'Upload succeeded but database update failed.';
              }
            } else {
              $uploadError = 'Failed to save uploaded PDF.';
            }
          }
        }
      }
    }

    $stmt = $db->prepare("SELECT name FROM Doctor WHERE id = ?");
    $stmt->execute([$doctorId]);
    $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);
    $doctorName = $doctorData ? $doctorData['name'] : 'Doctor';

    $result = $repo->getAllPrescriptionsForDoctor($doctorId);
    $prescriptions = is_array($result) ? $result : [];
} catch (Exception $e) {
    $prescriptions = [];
    $error_db = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>TeleMed | Online Healthcare</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="prescriptions_doctor.css" />
    <script src="prescriptions_doctor.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>

  <body>
    <div class="body">
      <header class="navbar">
        <div class="logo">
          Health
          <span>Connect</span>
        </div>
        <nav class="nav-links">
          <a href="../homepage/connected.php">Home</a>
        </nav>

        <div class="nav-actions">
          <a href="../view profile/doctor-profile.php" class="profile-account me-3">
            <img src="https://img.freepik.com/free-photo/female-doctor-hospital-with-stethoscope_23-2148827774.jpg" alt="Profile" class="profile-avatar">
            <span class="profile-name"><?= htmlspecialchars($doctorName) ?></span>
          </a>
          <a href="../login_signup/logout.php" class="btn-logout">Logout</a>
          <a href="../doctor_calendar/doctor_calendar.php" class="btn">Calendar</a>
        </div>
      </header>

      <div class="container mt-5">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h3>Medical prescriptions</h3>
          </div>
          <div class="card-body">
            <?php if (!empty($uploadSuccess)): ?>
              <div class="alert alert-success" role="alert"><?= htmlspecialchars($uploadSuccess) ?></div>
            <?php endif; ?>
            <?php if (!empty($uploadError)): ?>
              <div class="alert alert-danger" role="alert"><?= htmlspecialchars($uploadError) ?></div>
            <?php endif; ?>

            <div class="row g-3 mb-4 align-items-end">
              <div class="col-md-4">
                <label class="form-label small text-muted">Rechercher par nom du patient :</label>
                <input type="text" id="patientSearch" class="form-control" placeholder="Ex: John Doe">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Date de début :</label>
                <input type="date" id="minDate" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Date de fin :</label>
                <input type="date" id="maxDate" class="form-control">
              </div>
              <div class="col-md-2">
                <button id="btnFilter" class="btn btn-primary w-100">Filtrer</button>
              </div>
            </div>

            <div class="table-responsive p-3">
              <table id="prescriptionTable" class="table table-hover align-middle" style="width:100%">
                <thead class="table-light">
                  <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Medical resume</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                      <td><?= htmlspecialchars($prescription['date_cons']) ?></td>
                      <td><?= htmlspecialchars($prescription['patient_name']) ?></td>
                      <td><?= htmlspecialchars($prescription['medical_resume']) ?></td>
                      <td>
                        <?php
                          $status = $prescription['status_cons'];
                          $badgeClass = 'bg-secondary';

                          switch ($status) {
                              case 'Completed':
                                  $badgeClass = 'bg-success';
                                  break;
                              case 'Scheduled':
                                  $badgeClass = 'bg-primary';
                                  break;
                              case 'Cancelled':
                              case 'Canceled':
                                  $badgeClass = 'bg-danger';
                                  break;
                              case 'No-Show':
                                  $badgeClass = 'bg-warning text-dark';
                                  break;
                          }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                      </td>
                      <td class="text-end">
                        <?php if (!empty($prescription['prescription_path'])): ?>
                          <a href="../uploads/prescriptions/<?= htmlspecialchars($prescription['prescription_path']) ?>" class="btn btn-sm btn-outline-success" target="_blank" rel="noopener noreferrer">PDF</a>
                        <?php else: ?>
                          <form method="POST" enctype="multipart/form-data" class="pdf-upload-form d-inline-flex align-items-center gap-2">
                            <input type="hidden" name="appointment_id" value="<?= (int) $prescription['appointment_id'] ?>">
            
            <input type="file" name="prescription_pdf" id="file-<?= $prescription['appointment_id'] ?>" 
                   accept="application/pdf" class="custom-file-input" required>
            
            <label for="file-<?= $prescription['appointment_id'] ?>" class="btn btn-sm btn-outline-primary mb-0">
                Choisir...
            </label>

            <button type="submit" name="upload_pdf" class="btn btn-sm btn-primary">
                Send PDF
            </button>
                          </form>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
