<?php
include "auth.php";
require_role("employee");
$pageTitle = "Employee Dashboard";
include "layout_top.php";
?>
<h2>Employee Dashboard</h2>
<p class="sub">Follow the status of your equipment.</p>
<div class="grid">
    <a class="tile" href="my_equipment.php">
        <h3>My equipment status</h3>
        <p>See where your repair stands.</p>
    </a>
</div>
<?php include "layout_bottom.php"; ?>