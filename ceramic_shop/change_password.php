<?php
session_start();
// Security check: Only logged-in admins can see this
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user = $_SESSION['admin_user'];

if (isset($_POST['update_security'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $question = mysqli_real_escape_string($conn, $_POST['security_question']);
    $answer = mysqli_real_escape_string($conn, $_POST['security_answer']);

    if ($new_pass === $confirm_pass) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $sql = "UPDATE admins SET 
                password = '$hashed_pass', 
                security_question = '$question', 
                security_answer = '$answer' 
                WHERE username = '$user'";

        if (mysqli_query($conn, $sql)) {
            $success = "Security settings updated successfully!";
        } else {
            $error = "Error updating database.";
        }
    } else {
        $error = "Passwords do not match!";
    }
}

// Fetch current question to show in the form
$res = mysqli_query($conn, "SELECT security_question FROM admins WHERE username='$user'");
$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Security Settings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav style="background: #4e342e; padding: 15px; text-align: center;">
        <a href="admin.php" style="color: white; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
    </nav>

    <div class="product-card" style="margin: 40px auto; width: 400px; padding: 30px; text-align: left;">
        <h2 style="text-align: center;">Update Admin Security</h2>
        
        <?php if(isset($success)) echo "<p style='color:green; font-weight:bold;'>$success</p>"; ?>
        <?php if(isset($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

        <form method="POST">
            <h4 style="margin-bottom:5px;">Change Password</h4>
            <input type="password" name="new_password" placeholder="New Password" required style="width:95%; padding:10px; margin-bottom:10px;"><br>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required style="width:95%; padding:10px; margin-bottom:20px;"><br>

            <h4 style="margin-bottom:5px;">Security Question (For Recovery)</h4>
            <input type="text" name="security_question" placeholder="e.g., My first pet's name" 
                   value="<?php echo $row['security_question']; ?>" required style="width:95%; padding:10px; margin-bottom:10px;"><br>
            
            <input type="text" name="security_answer" placeholder="Your Secret Answer" required style="width:95%; padding:10px; margin-bottom:20px;"><br>

            <button type="submit" name="update_security" class="btn" style="width: 100%;">Save Security Settings</button>
        </form>
    </div>
</body>
</html>