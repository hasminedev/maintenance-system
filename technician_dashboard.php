<?php
include "auth.php";
require_role("technician");
$pageTitle = "Technician Dashboard";
include "layout_top.php";
?>
<h2>Technician Dashboard</h2>
<p class="sub">What would you like to do?</p>
<div class="grid">
    <a class="tile" href="log_intervention.php">
        <h3>Log an intervention</h3>
        <p>Record a new repair or maintenance job.</p>
    </a>
    <a class="tile" href="my_interventions.php">
        <h3>My interventions</h3>
        <p>Follow the jobs you are handling.</p>
    </a>
    <a class="tile" href="request_part.php">
        <h3>Request a spare part</h3>
        <p>Send a request to the manager.</p>
    </a>
    <a class="tile" href="record_replacement.php">
        <h3>Record a replacement</h3>
        <p>Save the part that was installed.</p>
    </a>
</div>
<?php include "layout_bottom.php"; ?>