<?php
include "auth.php";
require_login();
include "db.php";

$tables = ["technician" => "Technician", "manager" => "Manager", "employee" => "Employee"];
$table = $tables[$_SESSION["role"]];

$stmt = $conn->prepare("SELECT name, email FROM $table WHERE id = ?");
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$initial = strtoupper(substr($user["name"], 0, 1));
$role = ucfirst($_SESSION["role"]);

$pageTitle = "My account";
include "layout_top.php";
?>
<a class="back" href="dashboard.php">← Back to dashboard</a>
<h2>My account</h2>
<p class="sub">Your personal information.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Password changed successfully.</p>
<?php endif; ?>

<div class="form-card account-card">
    <div class="avatar"><?php echo htmlspecialchars($initial); ?></div>
    <div class="account-name">
        <?php echo htmlspecialchars($user["name"]); ?>
        <span class="badge"><?php echo $role; ?></span>
    </div>
    <div class="info-row"><span>Full name</span><strong><?php echo htmlspecialchars($user["name"]); ?></strong></div>
    <div class="info-row"><span>Email</span><strong><?php echo htmlspecialchars($user["email"]); ?></strong></div>
    <div class="info-row"><span>Role</span><strong><?php echo $role; ?></strong></div>
</div>

<div class="form-card account-card">
    <h3>Security</h3>
    <div class="info-row"><span>Password</span><strong>••••••••</strong></div>
    <a class="btn-link" href="change_password.php">Change password</a>
</div>
<?php include "layout_bottom.php"; ?>