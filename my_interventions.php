<?php
include "auth.php";
require_role("technician");
include "db.php";

$stmt = $conn->prepare(
    "SELECT i.id, i.ticketReferenceId, i.status, i.failureType, i.failureDate,
            i.resolutionDate, i.description,
            e.type, e.brand, e.serialNumber, e.owner
     FROM Intervention i
     JOIN Equipment e ON i.equipmentId = e.id
     WHERE i.technicianId = ?
     ORDER BY i.failureDate DESC, i.id DESC"
);
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My interventions";
include "layout_top.php";
?>
<a class="back" href="technician_dashboard.php">← Back to dashboard</a>
<h2>My interventions</h2>
<p class="sub">The jobs you are handling.</p>

<?php if (isset($_GET["resolved"])): ?>
    <p class="alert-ok">Intervention closed successfully.</p>
<?php endif; ?>
<?php if (isset($_GET["updated"])): ?>
    <p class="alert-ok">Intervention updated.</p>
<?php endif; ?>
<?php if (isset($_GET["error"])): ?>
    <p class="msg">Invalid resolution date. It must be between the failure date and today.</p>
<?php endif; ?>

<?php if ($result->num_rows === 0): ?>
    <p class="empty">You have no interventions yet.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr>
            <th>Equipment</th>
            <th>Failure</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($r = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"] . " · " . $r["owner"]); ?></small>
            </td>
            <td>
                <?php echo htmlspecialchars($r["failureType"]); ?><br>
                <small><?php echo htmlspecialchars($r["description"]); ?></small>
                <?php if ($r["ticketReferenceId"]): ?>
                    <br><small>Ticket: <?php echo htmlspecialchars($r["ticketReferenceId"]); ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $r["failureDate"]; ?>
                <?php if ($r["resolutionDate"]): ?>
                    <br><small>Resolved: <?php echo $r["resolutionDate"]; ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php $cls = ($r["status"] === "Resolved") ? "resolved" : "progress"; ?>
                <span class="status <?php echo $cls; ?>"><?php echo htmlspecialchars($r["status"]); ?></span>
            </td>
            <td>
                <?php if ($r["status"] !== "Resolved"): ?>
                    <a class="btn-edit" href="edit_intervention.php?id=<?php echo $r["id"]; ?>">Edit</a>
                <form method="POST" action="resolve_intervention.php">
                    <input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
                    <input type="date" name="resolutionDate"
                           value="<?php echo date('Y-m-d'); ?>"
                           min="<?php echo $r["failureDate"]; ?>"
                           max="<?php echo date('Y-m-d'); ?>" required>
                    <button type="submit" class="btn-small">Close intervention</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>