<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['doctor_id']) && empty($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Not authenticated']);
  exit;
}

try {
  require_once '../backend/autoloader.php';
  $db = ConnexionDB::getInstance();

  $data = json_decode(file_get_contents('php://input'), true);
  $appointmentId = $data['appointment_id'] ?? null;
  $newDate = $data['new_date'] ?? null;
  $newTime = $data['new_time'] ?? null;

  if (!$appointmentId || !$newDate || !$newTime) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
  }

  $stmt = $db->prepare("UPDATE Appointment SET appointment_date = ?, appointment_time = ?, status = 'Pending' WHERE appointment_id = ?");
  $result = $stmt->execute([$newDate, $newTime, $appointmentId]);

  if ($result) {
    echo json_encode(['success' => true, 'message' => 'Appointment rescheduled successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
