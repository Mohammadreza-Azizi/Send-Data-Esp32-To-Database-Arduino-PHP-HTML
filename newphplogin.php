<?php
// Check if the login form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted username and password
    $username = $_POST['frm']['username'];
    $password = $_POST['frm']['password'];

    // Connect to the MySQL database
    $mysqli = new mysqli('****', '****', '****', '****');

    // Check for connection errors
    if ($mysqli->connect_errno) {
        die('Failed to connect to MySQL: ' . $mysqli->connect_error);
    }

    // Prepare the SQL query to check if the user's login credentials are valid
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query returned a valid result
    if ($result->num_rows === 1) {
        // Start a PHP session and store information about the logged-in user
        session_start();
        $_SESSION['username'] = $username;

        // Redirect the user to a protected page
        header('Location: index1.php ');
        exit;
    } else {
        // Handle the error (e.g., display an error message to the user)
          header("Location: index.php?error=Incorect User name or password");

    }

    // Close the database connection
    $mysqli->close();
}
?>
