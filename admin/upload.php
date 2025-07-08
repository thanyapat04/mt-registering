<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อัปโหลดข้อมูลพนักงาน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f0f2f5;
            padding: 50px;
        }
        .ui.container {
            max-width: 500px;
        }
    </style>
</head>
<body>

<div class="ui container">
    <h2 class="ui dividing header">อัปโหลดข้อมูลพนักงาน</h2>

    <form class="ui form" action="import.php" method="post" enctype="multipart/form-data">
        <div class="field">
            <label>เลือกไฟล์ (.csv)</label>
            <input type="file" name="datafile" accept=".csv" required>
            <button type="button" class="ui red button" onclick="clearFile()">
            <i class="trash alternate outline icon"></i> ล้างไฟล์
        </button>
        </div>

        <div class="inline fields">
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="mode" value="replace" checked>
                    <label>แทนที่ข้อมูลเดิม</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="mode" value="append">
                    <label>เพิ่มข้อมูล</label>
                </div>
            </div>
        </div>

        <button class="ui primary button" type="submit">
            <i class="upload icon"></i> อัปโหลด
        </button>
        <a href="detail.php" class="ui button">ย้อนกลับ</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
<script>
    $('.ui.radio.checkbox').checkbox();
</script>

</body>
</html>
