<?php
include "auth.php";
require_role("manager");
include "db.php";

$id = (int)($_POST["id"] ?? 0);
$decision = $_POST["decision"] ?? "";

if (in_array($decision, ["Approved", "Rejected"], true)) {
    $stmt = $conn->prepare(
        "UPDATE SparePartRequest
         SET status = ?, approvalDate = CURDATE(), managerId = ?
         WHERE id = ? AND status = 'Pending'"
    );
    $stmt->bind_param("sii", $decision, $_SESSION["id"], $id);
    $stmt->execute();
}

header("Location: pending_requests.php?done=1");
exit;
?>