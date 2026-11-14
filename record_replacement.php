<?php
include "auth.php";
require_role("technician");
include "db.php";

$tid = $_SESSION["id"];

$stmt = $conn->prepare(
    "SELECT r.id, r.partDescription, e.type, e.brand, e.serialNumber
     FROM SparePartRequest r
     JOIN Intervention i ON r.interventionId = i.id
     JOIN Equipment e ON i.equipmentId = e.id
     WHERE i.technicianId = ? AND r.status = 'Approved'
       AND NOT EXISTS (SELECT 1 FROM Replacement rp WHERE rp.sparePartRequestId = r.id)
     ORDER BY r.id DESC"
);
$stmt->bind_param("i", $tid);
$stmt->execute();
$ready = $stmt->get_result();

$parts = [];
$options = [];
while ($r = $ready->fetch_assoc()) {
    $parts[$r["id"]] = $r["partDescription"];
    $options[] = $r;
}

$stmt = $conn->prepare(
    "SELECT rp.partDescription, rp.replacementDate, rp.verified, e.type, e.brand, e.serialNumber
     FROM Replacement rp
     JOIN SparePartRequest r ON rp.sparePartRequestId = r.id
     JOIN Intervention i ON r.interventionId = i.id
     JOIN Equipment e ON i.equipmentId = e.id
     WHERE rp.technicianId = ?
     ORDER BY rp.id DESC"
);
$stmt->bind_param("i", $tid);
$stmt->execute();
$history = $stmt->get_result();

$pageTitle = "Record a replacement";
include "layout_top.php";
?>
<a class="back" href="technician_dashboard.php">← Back to dashboard</a>
<h2>Record a replacement</h2>
<p class="sub">Save the part that was installed after the manager's approval.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Replacement recorded.</p>
<?php endif; ?>
<?php if (isset($_GET["error"])): ?>
    <p class="msg">Could not save the replacement. Check the date and try again.</p>
<?php endif; ?>

<?php if (count($options) === 0): ?>
    <p class="empty">No approved request is waiting for a replacement.</p>
<?php else: ?>
<form class="form-card" method="POST" action="save_replacement.php">
    <label>Approved request</label>
    <select name="sparePartRequestId" id="requestSelect" required>
        <option value="">-- Select a request --</option>
        <?php foreach ($options as $o): ?>
            <option value="<?php echo $o["id"]; ?>">
                <?php echo htmlspecialchars($o["type"] . " " . $o["brand"] . " - " . $o["serialNumber"] . " : " . $o["partDescription"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Part installed</label>
    <textarea name="partDescription" id="partDescription" placeholder="Describe the part that was actually installed..." required></textarea>

    <label>Replacement date</label>
    <input type="date" name="replacementDate" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>

    <div class="check-row">
        <input type="checkbox" name="verified" id="verified" value="1">
        <label for="verified">I verified that the installation works</label>
    </div>

    <button type="submit">Save replacement</button>
</form>
<script>
    const parts = <?php echo json_encode($parts); ?>;
    const select = document.getElementById("requestSelect");
    const desc = document.getElementById("partDescription");
    select.addEventListener("change", () => {
        desc.value = parts[select.value] || "";
    });
</script>
<?php endif; ?>

<h3 class="section">My replacements</h3>
<?php if ($history->num_rows === 0): ?>
    <p class="empty">No replacement recorded yet.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Part installed</th><th>Date</th><th>Verified</th></tr>
        <?php while ($h = $history->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($h["type"] . " " . $h["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($h["serialNumber"]); ?></small></td>
            <td><?php echo htmlspecialchars($h["partDescription"]); ?></td>
            <td><?php echo $h["replacementDate"]; ?></td>
            <td>
                <?php if ($h["verified"]): ?>
                    <span class="status approved">Verified</span>
                <?php else: ?>
                    <span class="status pending">Not verified</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>