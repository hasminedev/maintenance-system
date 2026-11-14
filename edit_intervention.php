<?php
include "auth.php";
require_role("technician");
include "db.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM Intervention WHERE id = ? AND technicianId = ? AND status <> 'Resolved'");
$stmt->bind_param("ii", $id, $_SESSION["id"]);
$stmt->execute();
$iv = $stmt->get_result()->fetch_assoc();

if (!$iv) {
    header("Location: my_interventions.php");
    exit;
}

$equipment = $conn->query("SELECT id, type, brand, serialNumber, owner FROM Equipment ORDER BY type, brand");
$map = [];
$currentLabel = "";
while ($e = $equipment->fetch_assoc()) {
    $label = $e["type"] . " " . $e["brand"] . " - " . $e["serialNumber"] . " (" . $e["owner"] . ")";
    $map[$label] = $e["id"];
    if ($e["id"] == $iv["equipmentId"]) {
        $currentLabel = $label;
    }
}

$standard = ["Hardware", "Software", "Network", "Power", "Peripheral"];
$isOther = !in_array($iv["failureType"], $standard, true);

$pageTitle = "Edit intervention";
include "layout_top.php";
?>
<a class="back" href="my_interventions.php">← Back to my interventions</a>
<h2>Edit intervention</h2>
<p class="sub">Update the details of this ongoing job.</p>

<?php if (isset($_GET["error"])): ?>
    <p class="msg">Could not save. Check the equipment, the date and the description.</p>
<?php endif; ?>

<form class="form-card" id="editForm" method="POST" action="update_intervention.php">
    <input type="hidden" name="id" value="<?php echo $iv["id"]; ?>">

    <label>Equipment</label>
    <input type="text" id="equipSearch" list="equipList" autocomplete="off"
           value="<?php echo htmlspecialchars($currentLabel); ?>" required>
    <datalist id="equipList">
        <?php foreach ($map as $label => $eid): ?>
            <option value="<?php echo htmlspecialchars($label); ?>"></option>
        <?php endforeach; ?>
    </datalist>
    <input type="hidden" name="equipmentId" id="equipmentId" value="<?php echo $iv["equipmentId"]; ?>">

    <label>Original ticket ID (if any)</label>
    <input type="text" name="ticketReferenceId" value="<?php echo htmlspecialchars($iv["ticketReferenceId"] ?? ""); ?>">

    <label>Failure type</label>
    <select name="failureType" id="failureType" required>
        <?php foreach ($standard as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php echo ($iv["failureType"] === $opt) ? "selected" : ""; ?>><?php echo $opt; ?></option>
        <?php endforeach; ?>
        <option value="Other" <?php echo $isOther ? "selected" : ""; ?>>Other</option>
    </select>
    <input type="text" name="otherFailure" id="otherFailure" maxlength="50"
           value="<?php echo $isOther ? htmlspecialchars($iv["failureType"]) : ""; ?>"
           placeholder="Type the failure type..." style="display:<?php echo $isOther ? 'block' : 'none'; ?>">

    <label>Failure date</label>
    <input type="date" name="failureDate" value="<?php echo $iv["failureDate"]; ?>" max="<?php echo date('Y-m-d'); ?>" required>

    <label>Description</label>
    <textarea name="description" required><?php echo htmlspecialchars($iv["description"] ?? ""); ?></textarea>

    <button type="submit">Save changes</button>
</form>

<script>
    const items = <?php echo json_encode($map); ?>;
    const search = document.getElementById("equipSearch");
    const hiddenId = document.getElementById("equipmentId");

    search.addEventListener("input", () => {
        hiddenId.value = items[search.value] || "";
    });
    document.getElementById("editForm").addEventListener("submit", (e) => {
        if (!hiddenId.value) {
            e.preventDefault();
            alert("Please choose a piece of equipment from the suggestions list.");
        }
    });

    const typeSelect = document.getElementById("failureType");
    const other = document.getElementById("otherFailure");
    other.required = typeSelect.value === "Other";
    typeSelect.addEventListener("change", () => {
        const isOther = typeSelect.value === "Other";
        other.style.display = isOther ? "block" : "none";
        other.required = isOther;
        if (!isOther) other.value = "";
    });
</script>
<?php include "layout_bottom.php"; ?>