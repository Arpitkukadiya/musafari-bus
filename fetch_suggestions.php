<?php
include 'config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'], $_POST['type'])) {
    $query = $conn->real_escape_string($_POST['query']);
    $type = $_POST['type'];

    $column = $type === 'source' ? 'source' : 'destination';
    // Match words starting with the input query
    $sql = "SELECT DISTINCT $column FROM buses WHERE $column LIKE '$query%' LIMIT 10";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div>' . htmlspecialchars($row[$column]) . '</div>';
        }
    } else {
        echo '<div>No suggestions found</div>';
    }
}
?>
