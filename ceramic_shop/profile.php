<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status_message = "";

// --- HANDLE DATA UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Update Basic Info
    if (isset($_POST['update_profile'])) {
        $new_name = mysqli_real_escape_string($conn, $_POST['name']);
        $new_email = mysqli_real_escape_string($conn, $_POST['email']);

        $update_sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $new_name; 
            $_SESSION['user_email'] = $new_email; // Store for forgot_password.php pre-fill
            $status_message = "ARCHIVE UPDATED SUCCESSFULLY";
        }
    }
}

// --- FETCH CURRENT DATA ---
$query = "SELECT name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ensure session email is set for the forgot password flow
$_SESSION['user_email'] = $user['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TSC — Member Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root { 
            --gold: #d4af37; 
            --neon-gold: rgba(212, 175, 55, 0.8);
            --bg: #0a0a0a; 
            --panel: #121212; 
        }

        body { 
            background: var(--bg); color: #fff; font-family: 'Inter', sans-serif; 
            margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh;
        }

        .profile-card {
            background: var(--panel); padding: 40px; width: 100%; max-width: 450px; 
            text-align: center; border: 1px solid var(--gold);
            box-shadow: 0 0 15px var(--neon-gold), inset 0 0 10px rgba(212, 175, 55, 0.1);
        }

        h1 { font-family: 'Cinzel', serif; color: var(--gold); letter-spacing: 4px; font-size: 1.1rem; margin-bottom: 30px; }
        h2 { font-family: 'Cinzel', serif; color: var(--gold); font-size: 0.8rem; letter-spacing: 2px; margin: 20px 0; opacity: 0.8; }
        
        .info-group { margin-bottom: 20px; text-align: left; }
        .label { font-size: 0.6rem; color: #777; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
        
        input[type="text"], input[type="email"] {
            width: 100%; background: transparent; border: none; border-bottom: 1px solid #333;
            color: #fff; font-family: 'Inter', sans-serif; font-size: 0.9rem; padding: 8px 0; outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }
        input:focus { border-bottom-color: var(--gold); }

        .status { font-size: 0.65rem; color: var(--gold); margin-bottom: 20px; letter-spacing: 1px; font-weight: bold; }

        .btn-container { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
        .btn {
            background: transparent; border: 1px solid #444; color: #fff;
            padding: 12px; font-size: 0.7rem; letter-spacing: 2px; cursor: pointer;
            text-decoration: none; transition: 0.3s; text-transform: uppercase;
            display: block; box-sizing: border-box;
        }
        .btn-save { background: var(--gold); color: #000; border: none; font-weight: bold; width: 100%; }
        .btn:hover { border-color: var(--gold); color: var(--gold); }
        
        .hidden { display: none; }
        hr { border: 0; border-top: 1px solid #222; margin: 30px 0; }
    </style>
</head>
<body>

<div class="profile-card">
    <h1>Member Profile</h1>
    
    <?php if($status_message): ?>
        <div class="status"><?php echo $status_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div id="display-mode">
            <div class="info-group">
                <div class="label">Full Name</div>
                <div style="font-size: 1rem;"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Email Address</div>
                <div style="font-size: 1rem;"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <button type="button" class="btn" style="width:100%" onclick="toggleEdit('display-mode', 'edit-mode')">Edit Details</button>
        </div>

        <div id="edit-mode" class="hidden">
            <div class="info-group">
                <div class="label">Full Name</div>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="info-group">
                <div class="label">Email Address</div>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="btn-container">
                <button type="submit" name="update_profile" class="btn btn-save">Save Details</button>
                <button type="button" class="btn" onclick="toggleEdit('edit-mode', 'display-mode')">Cancel</button>
            </div>
        </div>
    </form>

    <hr>

    <h2>Security Credentials</h2>
    <div id="pass-display">
        <a href="forgot_password.php" class="btn" style="width:100%">Change Password</a>
    </div>

    <div class="btn-container">
        <a href="index.php" class="btn" style="margin-top:20px; border-color:var(--gold)">Return to Archive</a>
    </div>
</div>

<script>
function toggleEdit(hideId, showId) {
    document.getElementById(hideId).classList.add('hidden');
    document.getElementById(showId).classList.remove('hidden');
}
</script>

<?php include 'global_footer.php'; ?>
</body>
</html>