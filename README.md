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
    เข้าไปที่ Manage ของ Web Service ที่สร้างบน Render และเลือก Environment
    สร้าง Environment Variable ชื่อ DB_URL และกำหนด Value เป็น URL สำหรับดาวน์โหลดฐานข้อมูลของเรา 

3.  **แก้ไข Google Apps Script URL**
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


---



# Meeting Registration System

This project is a PHP-based web application designed for meeting registration and attendance management. It allows participants to register for a meeting using their employee ID, while administrators can manage meeting details through a secure backend system.

All registration records are automatically sent to Google Apps Script and stored in Google Sheets in real time.

## Key Features

* **Display Meeting Information**

  * Show meeting topic, date, time, and location on the main registration page.

* **Employee ID Registration**

  * Participants can enter their employee ID to retrieve their information from the SQLite database and complete registration.

* **Manual Registration**

  * If an employee ID is not found in the database, participants can manually enter their information and register.

* **Google Sheets Integration**

  * Successful registrations are automatically submitted to a Google Apps Script endpoint and recorded in Google Sheets.

* **Admin Management System**

  * Secure admin login page (`/admin`)
  * Edit meeting details through the administration panel
  * Passwords are securely stored using PHP password hashing

* **Database Management**

  * Automatically downloads the SQLite database file from a URL specified in an environment variable.


## Project Structure

```text
├── Dockerfile              # Installs PHP since Render does not natively support PHP applications
├── admin
│   ├── detail.php          # Meeting details management page
│   ├── index.html          # Redirects to login page
│   └── login.php           # Admin login page
├── download_db.php         # Downloads the SQLite database
├── index.html              # Main page displaying meeting information and registration form
├── register.php            # Handles registration using employee data from the database
├── register_manual.php     # Handles manual registration
└── router.php              # Router for PHP built-in server
```


## Setup and Installation

### Prerequisites

* Docker and Docker Compose
* OR PHP 8.0+ with the `pdo_sqlite` extension installed


## Configuration

### 1. Environment Variable: `DB_URL`

Provide a publicly accessible URL that points to the SQLite database file (`.db`). The database must contain the following tables:

* `employee`
* `schedule`
* `users`

### 2. Google Apps Script URL

The Google Apps Script Web App URL is hardcoded in both `register.php` and `register_manual.php`. Replace it with your own deployed Apps Script URL.

```php
$url = "https://script.google.com/macros/s/YOUR_APPS_SCRIPT_ID/exec";
```


## Configuration Steps

### Step 1: Create the Environment Variable

In your Render Web Service dashboard:

1. Navigate to **Manage → Environment**
2. Create a new environment variable named:

```text
DB_URL
```

3. Set its value to the public URL where your SQLite database file can be downloaded.

### Step 2: Update the Google Apps Script URL

Open the following files:

* `register.php`
* `register_manual.php`

Replace the existing URL with your own Google Apps Script Web App URL:

```php
$url = "https://script.google.com/macros/s/YOUR_APPS_SCRIPT_ID/exec";
```


## Running the Project with PHP Built-in Server (Development)

### 1. Set the Environment Variable

**Linux/macOS**

```bash
export DB_URL="https://your-public-url.com/path/to/RegisterForm.db"
```

**Windows (Command Prompt)**

```cmd
set DB_URL="https://your-public-url.com/path/to/RegisterForm.db"
```

### 2. Start the Server

```bash
php -S localhost:8000
```

### 3. Access the Application

* Registration page:

  * http://localhost:8000

* Admin page:

  * http://localhost:8000/admin


## Database Schema

The SQLite database file (`RegisterForm.db`) should contain the following tables.

### `employee`

Stores employee information.

| Column    | Type      | Description             |
| --------- | --------- | ----------------------- |
| emp_id    | TEXT (PK) | Employee ID             |
| emp_name  | TEXT      | Employee full name      |
| position  | TEXT      | Position/Job title      |
| sec_short | TEXT      | Department abbreviation |
| cc_name   | TEXT      | Cost center name        |


### `schedule`

Stores meeting details. This table is expected to contain a single record.

| Column     | Type         | Description                        |
| ---------- | ------------ | ---------------------------------- |
| id         | INTEGER (PK) | Record ID (typically set to 1)     |
| topic      | TEXT         | Meeting topic                      |
| date       | TEXT         | Meeting date (e.g., June 30, 2025) |
| start_time | TEXT         | Start time (HH:MM)                 |
| end_time   | TEXT         | End time (HH:MM)                   |
| room       | TEXT         | Meeting room                       |
| floor      | TEXT         | Floor                              |
| building   | TEXT         | Building or venue                  |


### `users`

Stores administrator login credentials.

| Column   | Type          | Description                             |
| -------- | ------------- | --------------------------------------- |
| username | TEXT (UNIQUE) | Username                                |
| password | TEXT          | Password hashed using `password_hash()` |


## Deployment Notes

* The application is designed to run on Render using Docker.
* The SQLite database is not stored within the application repository.
* On startup, the application downloads the latest database file from the URL specified in `DB_URL`.
* Registration data is recorded externally via Google Apps Script and Google Sheets.
* Administrator passwords should always be stored using PHP's `password_hash()` function for security.
