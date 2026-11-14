<?php
include "auth.php";
require_role("manager");
include "db.php";

$result = $conn->query("SELECT type, brand, serialNumber, owner, department, acquisitionDate FROM Equipment ORDER BY type, brand");

$pageTitle = "Equipment";
include "layout_top.php";
?>
<a class="back" href="manager_dashboard.php">← Back to dashboard</a>
<h2>Equipment</h2>
<p class="sub">The IT equipment registered in the system.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Equipment added.</p>
<?php endif; ?>
<?php if (isset($_GET["error"])): ?>
    <p class="msg">
        <?php echo $_GET["error"] === "exists" ? "An equipment with this serial number already exists." : "Type, brand and serial number are required."; ?>
    </p>
<?php endif; ?>

<form class="form-card" method="POST" action="save_equipment.php">
    <h3 style="color:var(--blue);margin-bottom:14px;">Add equipment</h3>
    <label>Type</label>
    <input type="text" name="type" placeholder="e.g. Laptop, Printer, Server" required>
    <label>Brand</label>
    <input type="text" name="brand" required>
    <label>Serial number</label>
    <input type="text" name="serialNumber" required>
    <label>Owner</label>
    <input type="text" name="owner" placeholder="Full name of the employee using it">
    <label>Department</label>
    <input type="text" name="department">
    <label>Acquisition date</label>
    <input type="date" name="acquisitionDate" max="<?php echo date('Y-m-d'); ?>">
    <button type="submit">Add equipment</button>
</form>

<h3 class="section">Registered equipment</h3>
<input type="text" id="search" placeholder="Search by type, brand, serial number, owner..." style="margin-bottom:16px;">

<?php if ($result->num_rows === 0): ?>
    <p class="empty">No equipment registered yet.</p>
<?php else: ?>
<div class="table-wrap">
    <table id="equipTable">
        <tr><th>Type</th><th>Brand</th><th>Serial number</th><th>Owner</th><th>Department</th><th>Acquired</th></tr>
        <?php while ($r = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($r["type"]); ?></td>
            <td><?php echo htmlspecialchars($r["brand"]); ?></td>
            <td><?php echo htmlspecialchars($r["serialNumber"]); ?></td>
            <td><?php echo htmlspecialchars($r["owner"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($r["department"] ?? ""); ?></td>
            <td><?php echo $r["acquisitionDate"]; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<script>
    document.getElementById("search").addEventListener("input", function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll("#equipTable tr").forEach((row, i) => {
            if (i === 0) return;
            row.style.display = row.textContent.toLowerCase().includes(q) ? "" : "none";
        });
    });
</script>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>