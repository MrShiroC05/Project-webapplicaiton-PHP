<?php include '../Connection/connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Race</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <script src="../assets/js/navigation.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Create race</div>
            <h1>New Race</h1>
        </header>

        <section class="panel">
            <form action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="form-row">
                    <label>Race Name</label>
                    <input type="text" name="race_name" required>
                </div>

                <div class="form-row">
                    <label>Race Description</label>
                    <textarea name="race_description" rows="4"></textarea>
                </div>

                <div class="form-row">
                    <label>Race Image</label>
                    <input type="file" name="race_image" accept="image/*">
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Add Race</button>
                    <a href="./listRace.php" class="secondary-btn">Back</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
