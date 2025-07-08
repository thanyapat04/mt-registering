<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อัปโหลดข้อมูล</title>
</head>
<body>
    <h2>อัปโหลดไฟล์พนักงาน (.csv)</h2>
    <form action="import.php" method="post" enctype="multipart/form-data">
        <input type="file" name="datafile" accept=".csv" required><br><br>

        <label><input type="radio" name="mode" value="replace" checked> แทนที่ข้อมูลเดิม</label><br>
        <label><input type="radio" name="mode" value="append"> เพิ่มข้อมูล</label><br><br>

        <button type="submit">อัปโหลด</button>
    </form>
</body>
</html>
