<?php
include "auth.php";
require_role("manager");
include "db.php";

$base = "SELECT r.id, r.partDescription, r.requestDate, r.status, r.approvalDate,
                e.type, e.brand, e.serialNumber, t.name AS technician
         FROM SparePartRequest r
         JOIN Intervention i ON r.interventionId = i.id
         JOIN Equipment e ON i.equipmentId = e.id
         JOIN Technician t ON i.technicianId = t.id";

$pending = $conn->query($base . " WHERE r.status = 'Pending' ORDER BY r.id ASC");
$history = $conn->query($base . " WHERE r.status <> 'Pending' ORDER BY r.id DESC LIMIT 20");

$pageTitle = "Spare part requests";
include "layout_top.php";
?>
<a class="back" href="manager_dashboard.php">← Back to dashboard</a>
<h2>Spare part requests</h2>
<p class="sub">Approve or reject the requests sent by technicians.</p>

<?php if (isset($_GET["done"])): ?>
    <p class="alert-ok">Decision saved.</p>
<?php endif; ?>

<h3 class="section">Pending</h3>
<?php if ($pending->num_rows === 0): ?>
    <p class="empty">No pending request.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Part</th><th>Technician</th><th>Requested</th><th>Decision</th></tr>
        <?php while ($r = $pending->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"]); ?></small></td>
            <td><?php echo htmlspecialchars($r["partDescription"]); ?></td>
            <td><?php echo htmlspecialchars($r["technician"]); ?></td>
            <td><?php echo $r["requestDate"]; ?></td>
            <td>
                <form method="POST" action="decide_request.php" class="actions">
                    <input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
                    <button type="submit" name="decision" value="Approved" class="btn-small">Approve</button>
                    <button type="submit" name="decision" value="Rejected" class="btn-small btn-reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>

<h3 class="section">Recent decisions</h3>
<?php if ($history->num_rows === 0): ?>
    <p class="empty">No decision yet.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Part</th><th>Technician</th><th>Decided</th><th>Status</th></tr>
        <?php while ($r = $history->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"]); ?></small></td>
            <td><?php echo htmlspecialchars($r["partDescription"]); ?></td>
            <td><?php echo htmlspecialchars($r["technician"]); ?></td>
            <td><?php echo $r["approvalDate"]; ?></td>
            <td><span class="status <?php echo strtolower($r["status"]); ?>"><?php echo htmlspecialchars($r["status"]); ?></span></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>