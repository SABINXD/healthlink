<?php
// cycle_input.php
$userId = $_SESSION['user_id']; // assuming session-based login
// Save POST inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dates = array_filter($_POST['dates']);
    // Insert each into DB if valid
    $stmt = $pdo->prepare("INSERT INTO menstrual_cycles (user_id, start_date) VALUES (?, ?)");
    foreach ($dates as $date) {
        if ($date) {
            $stmt->execute([$userId, $date]);
        }
    }
    header("Location: dashboard.php"); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <style>
    /* Simple green-blue styled input */
    .date-input { padding: 8px; margin: 5px; border-radius: 4px; border: 1px solid #4CAF50; }
    .btn { background: linear-gradient(to right, #4caf50, #2196f3); color: #fff; padding: 10px; cursor: pointer; border: none; border-radius: 4px; }
    .btn:hover { opacity: 0.9; }
    .fade-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  </style>
</head>
<body class="fade-in">
  <h2>Enter Last 3 Period Start Dates</h2>
  <form method="POST">
    <input type="date" name="dates[]" class="date-input" required><br>
    <input type="date" name="dates[]" class="date-input"><br>
    <input type="date" name="dates[]" class="date-input"><br>
    <button type="submit" class="btn">Save</button>
  </form>
</body>
</html>
