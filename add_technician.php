<?php
include "auth.php";
require_role("manager");

$errors = [
    "password" => "Password must have 8+ characters, 1 uppercase and 1 number.",
    "exists"   => "This email is already used by another account.",
    "domain"   => "Only @camlight.cm email addresses are allowed."
];
$err = $_GET["error"] ?? "";

$pageTitle = "Add a technician";
include "layout_top.php";
?>
<a class="back" href="manager_dashboard.php">← Back to dashboard</a>
<h2>Add a technician</h2>
<p class="sub">Create an account for a new technician.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Technician account created.</p>
<?php endif; ?>
<?php if (isset($errors[$err])): ?>
    <p class="msg"><?php echo $errors[$err]; ?></p>
<?php endif; ?>

<form class="form-card" method="POST" action="save_technician.php">
    <label>Full name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Starting password</label>
    <input type="text" name="password" value="Bonjour2026" required>
    <p class="hint">8+ characters · 1 uppercase · 1 number</p>

    <button type="submit">Create technician</button>
</form>
<?php include "layout_bottom.php"; ?>