<?php
include "auth.php";
require_role("manager");
include "db.php";

$filter = $_GET["status"] ?? "all";

$sql = "SELECT i.ticketReferenceId, i.status, i.failureType, i.failureDate,
               i.resolutionDate, i.description,
               e.type, e.brand, e.serialNumber, e.owner, t.name AS technician
        FROM Intervention i
        JOIN Equipment e ON i.equipmentId = e.id
        LEFT JOIN Technician t ON i.technicianId = t.id";
if ($filter === "progress") {
    $sql .= " WHERE i.status <> 'Resolved'";
} elseif ($filter === "resolved") {
    $sql .= " WHERE i.status = 'Resolved'";
}
$sql .= " ORDER BY i.failureDate DESC, i.id DESC";
$result = $conn->query($sql);

$pageTitle = "All interventions";
include "layout_top.php";
?>
<a class="back" href="manager_dashboard.php">← Back to dashboard</a>
<h2>All interventions</h2>
<p class="sub">Every job handled by the technicians.</p>

<div class="filters">
    <a href="all_interventions.php" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
    <a href="all_interventions.php?status=progress" class="<?php echo $filter === 'progress' ? 'active' : ''; ?>">In progress</a>
    <a href="all_interventions.php?status=resolved" class="<?php echo $filter === 'resolved' ? 'active' : ''; ?>">Resolved</a>
</div>

<?php if ($result->num_rows === 0): ?>
    <p class="empty">No intervention found.</p>
<?php else: ?>
<div class="table-wrap">
    <table>
        <tr><th>Equipment</th><th>Failure</th><th>Technician</th><th>Date</th><th>Status</th></tr>
        <?php while ($r = $result->fetch_assoc()): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r["type"] . " " . $r["brand"]); ?></strong><br>
                <small><?php echo htmlspecialchars($r["serialNumber"] . " · " . $r["owner"]); ?></small></td>
            <td><?php echo htmlspecialchars($r["failureType"]); ?><br>
                <small><?php echo htmlspecialchars($r["description"]); ?></small>
                <?php if ($r["ticketReferenceId"]): ?>
                    <br><small>Ticket: <?php echo htmlspecialchars($r["ticketReferenceId"]); ?></small>
                <?php endif; ?></td>
            <td><?php echo htmlspecialchars($r["technician"] ?? "—"); ?></td>
            <td><?php echo $r["failureDate"]; ?>
                <?php if ($r["resolutionDate"]): ?>
                    <br><small>Resolved: <?php echo $r["resolutionDate"]; ?></small>
                <?php endif; ?></td>
            <td><?php $cls = ($r["status"] === "Resolved") ? "resolved" : "progress"; ?>
                <span class="status <?php echo $cls; ?>"><?php echo htmlspecialchars($r["status"]); ?></span></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php endif; ?>
<?php include "layout_bottom.php"; ?>