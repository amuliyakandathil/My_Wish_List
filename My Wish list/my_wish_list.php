<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wishlist_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add new wish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $activity = $_POST['activity'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO wishes (activity, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $activity, $status);
    $stmt->execute();
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Update wish status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE wishes SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Delete wish
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM wishes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wish List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>My Wish List</h1>
        
        <div class="intro">
            <p>These are the activities I would like to accomplish in the near future. 
            This list helps me track my goals and aspirations.</p>
            <p>Each activity can be marked as "Yet to Start", "Almost Done", or "Done" 
            to help monitor my progress.</p>
        </div>

        <div class="add-wish">
            <h2>Add New Activity</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="activity">Activity:</label>
                    <input type="text" id="activity" name="activity" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Yet to Start">Yet to Start</option>
                        <option value="Almost Done">Almost Done</option>
                        <option value="Done">Done</option>
                    </select>
                </div>
                <button type="submit">Add Activity</button>
            </form>
        </div>

        <div class="wish-list">
            <h2>My Activities</h2>
            <table>
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM wishes ORDER BY created_at DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['activity']) . "</td>";
                        echo "<td>
                                <form method='POST' action='' class='status-form'>
                                    <input type='hidden' name='action' value='update'>
                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                    <select name='status' onchange='this.form.submit()'>
                                        <option value='Yet to Start'" . ($row['status'] == 'Yet to Start' ? ' selected' : '') . ">Yet to Start</option>
                                        <option value='Almost Done'" . ($row['status'] == 'Almost Done' ? ' selected' : '') . ">Almost Done</option>
                                        <option value='Done'" . ($row['status'] == 'Done' ? ' selected' : '') . ">Done</option>
                                    </select>
                                </form>
                            </td>";
                        echo "<td>
                                <a href='?action=delete&id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this activity?\")' class='delete-btn'>Delete</a>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>