<?php
include "auth.php";
require_login();

$errors = [
    "current"  => "Your current password is incorrect.",
    "password" => "New password must have 8+ characters, 1 uppercase and 1 number.",
    "mismatch" => "The new passwords do not match."
];
$err = $_GET["error"] ?? "";

$pageTitle = "My account";
include "layout_top.php";
?>
<a class="back" href="account.php">← Back to my account</a>
<h2>Change password</h2>
<p class="sub">Choose a private password that only you know.</p>
<?php if (isset($errors[$err])): ?>
    <p class="msg"><?php echo $errors[$err]; ?></p>
<?php endif; ?>

<form class="form-card" method="POST" action="save_password.php">
    <label>Current password</label>
    <input type="password" name="current" required>

    <label>New password</label>
    <input type="password" name="password" required>
    <p class="hint">8+ characters · 1 uppercase · 1 number</p>

    <label>Confirm new password</label>
    <input type="password" name="confirm" required>

    <button type="submit">Save my password</button>
</form>
<?php include "layout_bottom.php"; ?>