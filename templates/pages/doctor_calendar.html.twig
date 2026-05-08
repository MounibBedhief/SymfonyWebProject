<?php
require_once '../backend/autoloader.php';
session_start();

// Development fallback (remove when login is fully integrated)
if (empty($_SESSION['doctor_id'])) {
  $_SESSION['doctor_id'] = 1;
  $_SESSION['role'] = 'doctor';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor' || !isset($_SESSION['doctor_id'])) {
  header("Location: ../login_signup/login-register.php");
  exit;
}

try {
  $db = ConnexionDB::getInstance();
  $doctorId = $_SESSION['doctor_id'];

  // Fetch appointments joined with patient data
  $stmt = $db->prepare("
        SELECT A.appointment_id, A.appointment_date, A.appointment_time, A.status, A.reason, A.notes, P.name as patient_name
        FROM Appointment A
        INNER JOIN Patient P ON A.patient_id = P.id
        WHERE A.doctor_id = ? AND A.status != 'Cancelled'
    ");
  $stmt->execute([$doctorId]);
  $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Format for JS calendar
  $dbEvents = [];
  foreach ($appointments as $appt) {
    $startTime = substr($appt['appointment_time'], 0, 5); // Format to HH:MM

    // Calculate an end time (defaulting to 1 hour after start time)
    $endObj = new DateTime($appt['appointment_date'] . ' ' . $startTime);
    $endObj->modify('+1 hour');
    $endTime = $endObj->format('H:i');

    // Map database status to JS calendar status ('planned', 'completed')
    $jsStatus = ($appt['status'] === 'Completed') ? 'completed' : 'planned';

    $dbEvents[] = [
      'id' => 'db_' . $appt['appointment_id'], // Prefix to identify it as a DB record
      'title' => !empty($appt['reason']) ? $appt['reason'] : 'Consultation',
      'type' => 'consultation',
      'status' => $jsStatus,
      'date' => $appt['appointment_date'],
      'start' => $startTime,
      'end' => $endTime,
      'patient' => $appt['patient_name'],
      'notes' => $appt['notes'] ?? ''
    ];
  }
} catch (Exception $e) {
  $dbEvents = []; // Fallback to empty if DB fails
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor Calendar</title>
  <link rel="stylesheet" href="doctor_calendar.css" />
</head>

<body>
  <div class="calendar-page">
    <header class="calendar-header">
      <div>
        <h1>Doctor Calendar</h1>
        <p>Manage tasks and consultations efficiently</p>
      </div>

      <div class="view-switcher">
        <button class="view-btn active" data-view="month">Month</button>
        <button class="view-btn" data-view="week">Week</button>
        <button class="view-btn" data-view="day">Day</button>
      </div>
    </header>

    <section class="calendar-toolbar card">
      <div class="nav-group">
        <button id="prevBtn" class="btn btn-light">◀ Prev</button>
        <button id="todayBtn" class="btn btn-primary">Today</button>
        <button id="nextBtn" class="btn btn-light">Next ▶</button>
      </div>

      <h2 id="calendarTitle"></h2>

      <button id="openModalBtn" class="btn btn-success">+ Add Event</button>
    </section>

    <section class="legend card">
      <span><i class="dot consultation"></i> Consultation</span>
      <span><i class="dot task"></i> Task</span>
      <span><i class="dot completed"></i> Completed</span>
    </section>

    <section class="calendar-container card">
      <div id="calendarGrid"></div>
    </section>
  </div>

  <!-- Modal -->
  <div id="eventModal" class="modal hidden">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Add Event</h3>
        <button id="closeModalBtn" class="icon-btn" type="button">✕</button>
      </div>

      <form id="eventForm">
        <input type="hidden" id="eventId" />

        <div class="form-row">
          <label for="eventTitle">Title</label>
          <input id="eventTitle" type="text" required />
        </div>

        <div class="form-row two-cols">
          <div>
            <label for="eventType">Type</label>
            <select id="eventType" required>
              <option value="consultation">Consultation</option>
              <option value="task">Task</option>
            </select>
          </div>
          <div>
            <label for="eventStatus">Status</label>
            <select id="eventStatus" required>
              <option value="planned">Planned</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <label for="eventDate">Date</label>
          <input id="eventDate" type="date" required />
        </div>

        <div class="form-row two-cols">
          <div>
            <label for="eventStart">Start Time</label>
            <input id="eventStart" type="time" required />
          </div>
          <div>
            <label for="eventEnd">End Time</label>
            <input id="eventEnd" type="time" required />
          </div>
        </div>

        <!-- Show only when type = consultation -->
        <div class="form-row" id="patientRow">
          <label for="eventPatient">Patient Name</label>
          <input id="eventPatient" type="text" placeholder="Required for consultations" />
        </div>

        <div class="form-row">
          <label for="eventNotes">Notes</label>
          <textarea id="eventNotes" rows="3" placeholder="Extra details..."></textarea>
        </div>

        <p id="formError" class="form-error"></p>

        <div class="modal-actions">
          <button type="button" id="deleteEventBtn" class="btn btn-danger hidden">Delete</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    window.DB_EVENTS = <?= json_encode($dbEvents) ?>;
  </script>
  <script src="doctor_calendar.js"></script>
</body>

</html>
