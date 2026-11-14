<?php
include "auth.php";
require_role("manager");
$pageTitle = "Manager Dashboard";
include "layout_top.php";
?>
<h2>Manager Dashboard</h2>
<p class="sub">Supervise maintenance activity.</p>
<div class="grid">
     <a class="tile" href="add_technician.php">
        <h3>Add a technician</h3>
        <p>create technician account.</p>
    </a>
    <a class="tile" href="pending_requests.php">
        <h3>Spare part requests</h3>
        <p>Approve or reject pending requests.</p>
    </a>
    <a class="tile" href="all_interventions.php">
        <h3>All interventions</h3>
        <p>See every job and its status.</p>
    </a>
    <a class="tile" href="equipment.php">
        <h3>Equipment</h3>
        <p>Browse the IT equipment list.</p>
    </a>
</div>
<?php include "layout_bottom.php"; ?>