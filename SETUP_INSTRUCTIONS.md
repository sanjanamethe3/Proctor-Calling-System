# Proctor Calling System - Setup Instructions

## Quick Start Guide

### Step 1: Prerequisites Installation

#### Windows

1. **PHP 7.4+**
   - Download from [php.net](https://www.php.net/downloads)
   - Add PHP to system PATH

2. **MongoDB Community Edition**
   - Download from [MongoDB Community](https://www.mongodb.com/try/download/community)
   - Install using MSI installer
   - Start MongoDB service

3. **Composer**
   - Download from [getcomposer.org](https://getcomposer.org/download/)

#### Linux (Ubuntu/Debian)

```bash
# Update package list
sudo apt-get update

# Install PHP and extensions
sudo apt-get install -y php7.4 php7.4-cli php7.4-dev php7.4-json php7.4-curl

# Install MongoDB
wget -qO - https://www.mongodb.org/static/pgp/server-5.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/5.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-5.0.list
sudo apt-get update
sudo apt-get install -y mongodb-org

# Start MongoDB
sudo systemctl start mongod

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

#### macOS

```bash
# Install Homebrew if not installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php@7.4

# Install MongoDB
brew tap mongodb/brew
brew install mongodb-community

# Start MongoDB
brew services start mongodb-community

# Install Composer
brew install composer
```

### Step 2: PHP MongoDB Extension Installation

#### Using Composer (Recommended)

```bash
cd Proctor-Calling-System
composer install
```

#### Manual Installation

**Linux:**
```bash
sudo pecl install mongodb
echo "extension=mongodb.so" | sudo tee -a /etc/php/7.4/apache2/php.ini
sudo systemctl restart apache2
```

**Windows:**
1. Download DLL from [PECL MongoDB](https://pecl.php.net/package/mongodb)
2. Extract to PHP extensions folder
3. Add to php.ini: `extension=php_mongodb.dll`
4. Restart Apache/PHP server

### Step 3: Project Setup

1. **Clone Repository**
   ```bash
   git clone https://github.com/sanjanamethe3/Proctor-Calling-System.git
   cd Proctor-Calling-System
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Create Necessary Directories**
   ```bash
   mkdir -p public/uploads/{college-photos,reports}
   mkdir -p backend/logs
   chmod 755 public/uploads
   ```

4. **Configure Database**
   - Edit `backend/config/database.php`
   - Update MongoDB URI if needed (default: mongodb://localhost:27017)

5. **Create Default Users in MongoDB**
   ```javascript
   // MongoDB console
   use proctor_calling_system
   
   db.users.insertMany([
     {
       "email": "admin@gmail.com",
       "password": "$2y$10$HASH_OF_admin1234",
       "name": "Administrator",
       "role": "admin",
       "created_at": new Date()
     },
     {
       "email": "faculty@gmail.com",
       "password": "$2y$10$HASH_OF_faculty1234",
       "name": "Faculty Member",
       "role": "faculty",
       "created_at": new Date()
     },
     {
       "email": "proctor@gmail.com",
       "password": "$2y$10$HASH_OF_proctor1234",
       "name": "Proctor",
       "role": "proctor",
       "created_at": new Date()
     },
     {
       "email": "hod@gmail.com",
       "password": "$2y$10$HASH_OF_hod1234",
       "name": "Head of Department",
       "role": "hod",
       "created_at": new Date()
     }
   ])
   ```

### Step 4: Running the Application

#### Option 1: Built-in PHP Server (Development)

```bash
cd Proctor-Calling-System
php -S localhost:8000
```

Access: http://localhost:8000

#### Option 2: Apache Server

1. Copy project to Apache webroot:
   ```bash
   sudo cp -r Proctor-Calling-System /var/www/html/
   ```

2. Create Apache config (Linux):
   ```bash
   sudo nano /etc/apache2/sites-available/proctor.conf
   ```

   Add:
   ```apache
   <VirtualHost *:80>
       ServerName proctor.local
       DocumentRoot /var/www/html/Proctor-Calling-System/public
       
       <Directory /var/www/html/Proctor-Calling-System/public>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Enable site and rewrite module:
   ```bash
   sudo a2ensite proctor.conf
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

4. Add to hosts file:
   ```bash
   sudo nano /etc/hosts
   # Add: 127.0.0.1 proctor.local
   ```

5. Access: http://proctor.local

#### Option 3: Nginx

1. Create Nginx config:
   ```bash
   sudo nano /etc/nginx/sites-available/proctor
   ```

   Add:
   ```nginx
   server {
       listen 80;
       server_name proctor.local;
       root /var/www/html/Proctor-Calling-System/public;
       index index.html index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

2. Enable and reload:
   ```bash
   sudo ln -s /etc/nginx/sites-available/proctor /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```

### Step 5: Initial Configuration

1. **Access Login Page**
   - URL: http://localhost:8000
   - Click Login button

2. **Admin Credentials**
   - Email: admin@gmail.com
   - Password: admin1234

3. **Add Master Data**
   - Navigate to Masters section
   - Add colleges, departments, divisions, semesters, subjects, and slots

4. **Register Students**
   - Go to Student Registration
   - Add student details with parent contact information

## Troubleshooting

### MongoDB Connection Error

**Error:** "Failed to connect to MongoDB"

**Solution:**
```bash
# Check if MongoDB is running
mongod --version
sudo systemctl status mongod

# Start MongoDB
sudo systemctl start mongod
```

### PHP MongoDB Extension Not Found

**Error:** "Class 'MongoDB\Client' not found"

**Solution:**
```bash
# Install via Composer
composer require mongodb/mongodb

# Or install manually
sudo pecl install mongodb
```

### Permission Denied

**Error:** "Permission denied" in uploads folder

**Solution:**
```bash
chmod 755 public/uploads
chown -R www-data:www-data public/uploads
```

### Port Already in Use

**Error:** "Address already in use"

**Solution:**
```bash
# Use different port
php -S localhost:8001

# Or find and kill process
lsof -i :8000
kill -9 <PID>
```

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | admin1234 |
| Faculty | faculty@gmail.com | faculty1234 |
| Proctor | proctor@gmail.com | proctor1234 |
| HOD | hod@gmail.com | hod1234 |

## File Structure

```
Proctor-Calling-System/
├── public/
│   ├── index.html
│   ├── login.html
│   ├── dashboard.html
│   ├── css/
│   ├── js/
│   └── uploads/
├── backend/
│   ├── config/
│   │   └── database.php
│   ├── api/
│   │   ├── auth.php
│   │   ├── colleges.php
│   │   ├── students.php
│   │   └── attendance.php
│   └── logs/
├── composer.json
├── .htaccess
└── README.md
```

## Next Steps

1. Configure email notifications (update SMTP settings)
2. Setup SMS gateway (Twilio/AWS SNS)
3. Configure backup system
4. Set up monitoring and logging
5. Deploy to production server

## Support

For issues or questions, please refer to:
- [MongoDB Documentation](https://docs.mongodb.com/)
- [PHP Documentation](https://www.php.net/docs.php)
- [Project Issues](https://github.com/sanjanamethe3/Proctor-Calling-System/issues)

---

**Last Updated:** 2026-05-16
