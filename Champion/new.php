<?php
include '../Connection/connect.php';

$racesResult = $conn->query("SELECT * FROM race ORDER BY race_name ASC");
$races = $racesResult ? $racesResult->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Champion</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <script src="../assets/js/navigation.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Create champion</div>
            <h1>New Champion</h1>
        </header>

        <section class="panel">
            <form action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="form-row">
                    <label>Champion Name</label>
                    <input type="text" name="champion_name" required>
                </div>

                <div class="form-row">
                    <label>Champion Title</label>
                    <input type="text" name="champion_title">
                </div>

                <div class="form-row">
                    <select name="champion_gender" required>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="O">Other</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Champion Region</label>
                    <select name="champion_region" required>
                        <option value="">Select a region</option>
                        <?php
                        $regionResult = $conn->query("SELECT * FROM region ORDER BY region_name ASC");
                        while ($region = $regionResult->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($region['region_id']) . '">' . htmlspecialchars($region['region_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <label>Champion Story</label>
                    <textarea name="champion_story" rows="4"></textarea>
                </div>

                <div class="form-row">
                    <label>Champion Image</label>
                    <input type="file" name="champion_image" accept="image/*">
                </div>

                <div class="form-row">
                    <label>Champion Races (max 2)</label>
                    <div class="checkbox-group">
                        <?php foreach ($races as $race): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="races[]" value="<?php echo htmlspecialchars($race['race_id']); ?>">
                                <?php echo htmlspecialchars($race['race_name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Add Champion</button>
                    <a href="./listChampion.php" class="secondary-btn">Back</a>
                </div>
            </form>
        </section>
    </div>

    <script>
        const limit = 2;
        const checkboxes = document.querySelectorAll('input[name="races[]"]');
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const checked = Array.from(checkboxes).filter((item) => item.checked);
                if (checked.length > limit) {
                    checkbox.checked = false;
                    alert('You can only select up to 2 races for one champion.');
                }
            });
        });
    </script>
</body>
</html>
