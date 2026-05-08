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
  $status = $data['status'] ?? null;

  if (!$appointmentId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
  }

  $validStatuses = ['Scheduled', 'Completed', 'Cancelled', 'No-Show', 'Pending'];
  if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
  }

  $stmt = $db->prepare("UPDATE Appointment SET status = ? WHERE appointment_id = ?");
  $result = $stmt->execute([$status, $appointmentId]);

  if ($result) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
