<?php
include "auth.php";
require_role("technician");
include "db.php";

$equipment = $conn->query("SELECT id, type, brand, serialNumber, owner FROM Equipment ORDER BY type, brand");
$map = [];
while ($e = $equipment->fetch_assoc()) {
    $label = $e["type"] . " " . $e["brand"] . " - " . $e["serialNumber"] . " (" . $e["owner"] . ")";
    $map[$label] = $e["id"];
}

$pageTitle = "Log an intervention";
include "layout_top.php";
?>
<a class="back" href="technician_dashboard.php">← Back to dashboard</a>
<h2>Log an intervention</h2>
<p class="sub">Record a job you are taking charge of.</p>

<?php if (isset($_GET["saved"])): ?>
    <p class="alert-ok">Intervention saved successfully.</p>
<?php endif; ?>

<form class="form-card" id="interventionForm" method="POST" action="save_intervention.php">
    <label>Equipment</label>
    <input type="text" id="equipSearch" list="equipList" autocomplete="off"
           placeholder="Type a name, brand, serial number or owner..." required>
    <datalist id="equipList">
        <?php foreach ($map as $label => $id): ?>
            <option value="<?php echo htmlspecialchars($label); ?>"></option>
        <?php endforeach; ?>
    </datalist>
    <input type="hidden" name="equipmentId" id="equipmentId">

    <label>Original ticket ID (if any)</label>
    <input type="text" name="ticketReferenceId" placeholder="Leave empty if no ticket">

    <label>Failure type</label>
    <select name="failureType" id="failureType" required>
        <option value="">-- Select failure type --</option>
        <option>Hardware</option>
        <option>Software</option>
        <option>Network</option>
        <option>Power</option>
        <option>Peripheral</option>
        <option>Other</option>
    </select>
    <input type="text" name="otherFailure" id="otherFailure" maxlength="50"
           placeholder="Type the failure type..." style="display:none">

    <label>Failure date</label>
    <input type="date" name="failureDate" value="<?php echo date('Y-m-d'); ?>" required>

    <label>Description</label>
    <textarea name="description" placeholder="Describe the problem..." required></textarea>

    <button type="submit">Save intervention</button>
</form>

<script>
    const items = <?php echo json_encode($map); ?>;
    const search = document.getElementById("equipSearch");
    const hiddenId = document.getElementById("equipmentId");

    search.addEventListener("input", () => {
        hiddenId.value = items[search.value] || "";
    });

    document.getElementById("interventionForm").addEventListener("submit", (e) => {
        if (!hiddenId.value) {
            e.preventDefault();
            alert("Please choose a piece of equipment from the suggestions list.");
        }
    });

    const typeSelect = document.getElementById("failureType");
    const other = document.getElementById("otherFailure");

    typeSelect.addEventListener("change", () => {
        const isOther = typeSelect.value === "Other";
        other.style.display = isOther ? "block" : "none";
        other.required = isOther;
        if (!isOther) other.value = "";
    });
</script>
<?php include "layout_bottom.php"; ?>