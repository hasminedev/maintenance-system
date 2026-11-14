<?php
include "auth.php";
require_role("technician");
include "db.php";

$tid = $_SESSION["id"];

$stmt = $conn->prepare(
    "SELECT i.id, i.failureType, e.type, e.brand, e.serialNumber
     FROM Intervention i JOIN Equipment e ON i.equipmentId = e.id
     WHERE i.technicianId = ? AND i.status <> 'Resolved'
     ORDER BY i.id DESC"
);
$stmt->bind_param("i", $tid);
$stmt->execute();
$open = $stmt->get_result();

$stmt = $conn->prepare(
    "SELECT r.partDescription, r.requestDate, r.status, r.approvalDate, e.type, e.brand, e.serialNumber
     FROM SparePartRequest r
     JOIN Intervention i ON r.interventionId = i.id
     JOIN Equipment e ON i.equipmentId = e.id
     WHERE i.technicianId = ?
     ORDER BY r.id DESC"
);
$stmt->bind_param("i", $tid);
$stmt->execute();
$mine = $stmt->get_result();

$pageTitle = "Request a spare part";
include "layout_top.php";
?>
<a class="back" href="technician_dashboard.php">← Back to dashboard</a>
<h2>Request a spare part</h2>
<p class="sub">Send a request to the manager for one of your ongoing interventions.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Request sent to the manager.</p>
<?php endif; ?>
<?php if (isset($_GET["error"])): ?>
    <p class="msg">Could not send the request. Please try again.</p>
<?php endif; ?>

<?php if ($open->num_rows === 0): ?>
    <p class="empty">You have no ongoing intervention. Log one first.</p>
<?php else: ?>
<form class="form-card" method="POST" action="save_part_request.php">
    <label>Intervention</label>
    <select name="interventionId" required>
        <option value="">-- Select an intervention --</option>
        <?php while ($o = $open->fetch_assoc()): ?>
            <option value="<?php echo $o["id"]; ?>">
                <?php echo htmlspecialchars($o["type"] . " " . $o["brand"] . " - " . $o["serialNumber"] . " (" . $o["failureType"] . ")"); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Part needed</label>
    <textarea name="partDescription" placeholder="e.g. 8 GB DDR4 RAM stick, laptop battery..." required></textarea>

    <button type="submit">Send request</button>
</form>
<?php endif; ?>

<h3 class="section">My requests</h3>
<?php if ($mine->num_rows === 0): ?>
    <p class="empty">No request sent yet.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Part</th><th>Requested</th><th>Status</th></tr>
        <?php while ($r = $mine->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"]); ?></small></td>
            <td><?php echo htmlspecialchars($r["partDescription"]); ?></td>
            <td><?php echo $r["requestDate"]; ?>
                <?php if ($r["approvalDate"]): ?><br><small>Decided: <?php echo $r["approvalDate"]; ?></small><?php endif; ?></td>
            <td><span class="status <?php echo strtolower($r["status"]); ?>"><?php echo htmlspecialchars($r["status"]); ?></span></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>