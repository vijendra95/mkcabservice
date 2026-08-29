<?php
// Booking form handler: forwards the request to WhatsApp.
require_once __DIR__ . '/includes/config.php';

$trip = trim($_POST['trip'] ?? 'Outstation One-Way');
$pickup = trim($_POST['pickup'] ?? '');
$drop = trim($_POST['drop'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$msg = "Hello MK Cab Service, I'd like to book a cab.\n"
     . "Trip: $trip\n"
     . "Pickup: $pickup\n"
     . ($drop !== '' ? "Drop: $drop\n" : '')
     . "Date: $date $time\n"
     . "Name: $name\n"
     . "Mobile: $phone";

header('Location: ' . wa_link($msg));
exit;
