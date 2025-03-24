<?php
session_start();

$complaintsFile = 'data/complaints.json';
$complaints = file_exists($complaintsFile) ? json_decode(file_get_contents($complaintsFile), true) : [];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $complaint = trim($_POST['complaint']);

    if (!empty($complaint)) {
        $newComplaint = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $_SESSION['user_name'],
            'complaint' => $complaint,
            'date' => date('Y-m-d H:i:s'),
        ];

        $complaints[] = $newComplaint;
        file_put_contents($complaintsFile, json_encode($complaints, JSON_PRETTY_PRINT));
        $message = "Your complaint has been submitted successfully.";
    } else {
        $message = "Please write something for the complaint.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaints - click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .complaint-container {
            text-align: center;
            margin-top: 50px;
        }
        .complaint-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .complaint-list {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f4f4f4;
        }
        .complaint-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .complaint-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <div class="complaint-container">
        <h1>Submit a Complaint or Suggestion</h1>
        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"> <?php echo $message; ?> </p>
        <?php endif; ?>
        
        <form method="POST" class="complaint-form">
            <textarea name="complaint" placeholder="Write your complaint or suggestion here..." required></textarea><br><br>
            <button type="submit">Submit Complaint</button>
        </form>
    </div>

    <div class="complaint-list">
        <h2>All Complaints</h2>
        <?php if (!empty($complaints)): ?>
            <?php foreach ($complaints as $comp): ?>
                <div class="complaint-item">
                    <strong><?php echo htmlspecialchars($comp['user_name']); ?></strong>
                    <em>(<?php echo $comp['date']; ?>)</em>
                    <p><?php echo nl2br(htmlspecialchars($comp['complaint'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No complaints submitted yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
