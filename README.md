# ระบบลงทะเบียนการประชุม (Meeting Registration System)

โปรเจกต์นี้เป็นเว็บแอปพลิเคชันที่สร้างด้วย PHP สำหรับใช้เป็นระบบลงทะเบียนเข้าร่วมการประชุม โดยมีฟังก์ชันหลักคือการแสดงรายละเอียดการประชุม, ให้ผู้เข้าร่วมลงทะเบียนด้วยรหัสพนักงาน, และมีระบบหลังบ้านสำหรับเจ้าหน้าที่เพื่อแก้ไขข้อมูลการประชุม

ข้อมูลการลงทะเบียนทั้งหมดจะถูกส่งไปยัง Google Apps Script เพื่อบันทึกลงใน Google Sheets แบบเรียลไทม์

## การใช้งานหลัก

  * **แสดงรายละเอียดการประชุม**: หัวข้อ, วันที่, เวลา, และสถานที่ของการประชุมบนหน้าแรก
  * **ลงทะเบียนด้วยรหัสพนักงาน**: ผู้ใช้สามารถกรอกรหัสพนักงานเพื่อดึงข้อมูลจากฐานข้อมูล SQLite และทำการลงทะเบียน
  * **ลงทะเบียนด้วยตนเอง**: ในกรณีที่ไม่พบรหัสพนักงานในระบบ ผู้ใช้สามารถกรอกข้อมูลเพื่อลงทะเบียนด้วยตนเองได้
  * **เชื่อมต่อกับ Google Sheets**: ทุกการลงทะเบียนที่สำเร็จจะถูกส่งข้อมูลไปยัง Google Apps Script Endpoint
  * **ระบบจัดการหลังบ้าน**:
      * มีหน้าล็อกอินสำหรับเจ้าหน้าที่ (`/admin`)
      * เจ้าหน้าที่สามารถแก้ไขรายละเอียดของการประชุมได้
      * รหัสผ่านถูกจัดเก็บอย่างปลอดภัยด้วยการ Hashing
  * **การจัดการฐานข้อมูล**: ระบบจะดาวน์โหลดไฟล์ฐานข้อมูล SQLite จาก URL ที่กำหนดใน Environment Variable

## โครงสร้างโปรเจกต์ (Structure)

```
├── Dockerfile            # install php เนื่องจาก Render ไม่สามารถรัน php ได้
├── admin
│   ├── detail.php           # หน้าแก้ไขรายละเอียดการประชุม
│   ├── index.html           # redirect ไปยังหน้า login
│   └── login.php            # หน้าล็อกอินสำหรับเจ้าหน้าที่
├── download_db.php       # ดาวน์โหลด DB 
├── index.html            # หน้าแรก แสดงข้อมูลการประชุมและฟอร์มลงทะเบียน
├── register.php          # ประมวลผลการลงทะเบียนโดยค้นหาจาก DB
├── register_manual.php   # ประมวลผลการลงทะเบียนโดยผู้เข้าร่วมประชุมกรอกข้อมูลเอง
└── router.php            # Router สำหรับ PHP built-in server
```

## การตั้งค่าและการติดตั้ง (Setup and Installation)

### ข้อกำหนดเบื้องต้น (Prerequisites)

  * Docker และ Docker Compose
  * (หรือ) PHP 8.0+ พร้อม extension `pdo_sqlite` สำหรับการรันบนเครื่องโดยตรง

### การกำหนดค่า (Configuration)

1.  **Environment Variable: `DB_URL`** หรือ URL สำหรับดาวน์โหลดไฟล์ฐานข้อมูล SQLite (`.db`) ที่มีตาราง `employee`, `schedule`, และ `users`
2.  **Google Apps Script URL**: URL ของ Web App ที่สร้างจาก Google Apps Script ซึ่งฮาร์ดโค้ดไว้ในไฟล์ `register.php` และ `register_manual.php` ซึ่งจำเป็นต้องแก้ไข URL ให้เป็นของเราเอง

#### ขั้นตอนการตั้งค่า

1.  **สร้าง Environment Variable**
    สร้าง Environment Variable ชื่อ DB_URL ของโปรเจกต์บน Render และกำหนด Value เป็น URL สำหรับดาวน์โหลดฐานข้อมูลของเราใน 

2.  **แก้ไข Google Apps Script URL**
    เปิดไฟล์ `register.php` และ `register_manual.php` และแก้ไขตัวแปร `$url` ให้เป็น URL ของเราเอง:

    ```php
    $url = "https://script.google.com/macros/s/YOUR_APPS_SCRIPT_ID/exec";
    ```
    
### การรันโปรเจกต์ผ่าน PHP Built-in Server (สำหรับพัฒนา)

1.  **ตั้งค่า Environment Variable**:

    ```bash
    # บน Linux/macOS
    export DB_URL="https://your-public-url.com/path/to/RegisterForm.db"

    # บน Windows (Command Prompt)
    set DB_URL="https://your-public-url.com/path/to/RegisterForm.db"
    ```

2.  **เริ่ม Server**:

    ```bash
    php -S localhost:8000 
    ```

3.  **เข้าใช้งาน**:

      * หน้าลงทะเบียน: [http://localhost:8000](https://www.google.com/search?q=http://localhost:8000)
      * หน้าสำหรับเจ้าหน้าที่: [http://localhost:8000/admin](https://www.google.com/search?q=http://localhost:8000/admin)

## โครงสร้างฐานข้อมูล (Database Schema)

ไฟล์ฐานข้อมูล SQLite (`RegisterForm.db`) ควรมีโครงสร้างตารางดังนี้:

  * **`employee`**: ตารางข้อมูลพนักงาน

      * `emp_id` (TEXT, PRIMARY KEY): รหัสพนักงาน
      * `emp_name` (TEXT): ชื่อ-นามสกุล
      * `position` (TEXT): ตำแหน่ง
      * `sec_short` (TEXT): ส่วนงานย่อ
      * `cc_name` (TEXT): ชื่อศูนย์ต้นทุน

  * **`schedule`**: ตารางข้อมูลรายละเอียดการประชุม (มี 1 record)

      * `id` (INTEGER, PRIMARY KEY): ID ของรายการ (ใช้ค่าเป็น 1)
      * `topic` (TEXT): หัวข้อการประชุม
      * `date` (TEXT): วันที่ (เช่น 30 มิถุนายน 2568)
      * `start_time` (TEXT): เวลาเริ่ม (HH:MM)
      * `end_time` (TEXT): เวลาสิ้นสุด (HH:MM)
      * `room` (TEXT): ห้องประชุม
      * `floor` (TEXT): ชั้น
      * `building` (TEXT): อาคาร/สถานที่

  * **`users`**: ตารางข้อมูลผู้ใช้งานระบบ (สำหรับเจ้าหน้าที่)

      * `username` (TEXT, UNIQUE): ชื่อผู้ใช้
      * `password` (TEXT): รหัสผ่าน (เก็บในรูปแบบ Hashed ด้วย `password_hash`)
