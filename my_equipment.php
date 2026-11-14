<?php
include "auth.php";
require_role("employee");
include "db.php";

$stmt = $conn->prepare(
    "SELECT e.id, e.type, e.brand, e.serialNumber,
            i.status, i.failureDate, i.resolutionDate,
            COALESCE(NULLIF(TRIM(i.failureType), ''), 'Not specified') AS issue
     FROM Equipment e
     LEFT JOIN Intervention i ON i.equipmentId = e.id
     WHERE LOWER(TRIM(e.owner)) = LOWER(TRIM(?))
     ORDER BY e.id, i.failureDate DESC"
);
$stmt->bind_param("s", $_SESSION["name"]);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My equipment status";
include "layout_top.php";
?>
<a class="back" href="employee_dashboard.php">← Back to dashboard</a>
<h2>My equipment status</h2>
<p class="sub">Follow the repair status of your equipment.</p>

<?php if ($result->num_rows === 0): ?>
    <p class="empty">No equipment is registered under your name.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Issue</th><th>Date</th><th>Status</th></tr>
        <?php while ($r = $result->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"]); ?></small></td>
            <?php if ($r["status"] === null): ?>
                <td colspan="2"><small>No intervention</small></td>
                <td><span class="status resolved">Working</span></td>
            <?php else: ?>
                <td><?php echo htmlspecialchars($r["issue"]); ?></td>
                <td><?php echo $r["failureDate"]; ?>
                    <?php if ($r["resolutionDate"]): ?>
                        <br><small>Resolved: <?php echo $r["resolutionDate"]; ?></small>
                    <?php endif; ?></td>
                <td><?php $cls = ($r["status"] === "Resolved") ? "resolved" : "progress"; ?>
                    <span class="status <?php echo $cls; ?>"><?php echo htmlspecialchars($r["status"]); ?></span></td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>