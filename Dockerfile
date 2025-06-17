FROM php:8.2-cli

# ติดตั้ง SQLite extension
RUN docker-php-ext-install pdo pdo_sqlite

# คัดลอกไฟล์ทั้งหมดเข้า container
COPY . /app
WORKDIR /app

# เปิด PHP built-in server ที่ port 10000 (Render รองรับ)
CMD ["php", "-S", "0.0.0.0:10000"]
