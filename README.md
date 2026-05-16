# Proctor Calling System

A comprehensive web application for managing student attendance, faculty coordination, and proctor management.

## Features

- **Admin Panel**: Master data management (colleges, departments, divisions, semesters, subjects, slots)
- **Faculty Panel**: Mark attendance and manage student records
- **Proctor Panel**: Monitor attendance, generate defaulter lists, and call parents
- **HOD Panel**: Access college data and student registrations
- **Dashboard**: Real-time statistics and analytics
- **Parent Notification**: Automatic SMS/message system for absent students
- **Excel Reports**: Generate monthly attendance reports

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MongoDB
- **Server**: Apache/Nginx
- **Additional**: Font Awesome Icons, Chart.js for statistics

## Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MongoDB Server
- Composer
- Apache/Nginx Server
- Visual Studio Code (or any code editor)

### Step 1: Install MongoDB

**Windows/Mac:**
```bash
# Download from https://www.mongodb.com/try/download/community
# Follow installation wizard
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt-get update
sudo apt-get install -y mongodb
sudo systemctl start mongodb
```

### Step 2: Install PHP MongoDB Extension

**Using Composer (Recommended):**
```bash
composer require mongodb/mongodb
```

**Manual Installation:**
- Linux:
  ```bash
  sudo pecl install mongodb
  echo "extension=mongodb.so" | sudo tee -a /etc/php/7.4/apache2/php.ini
  ```
- Windows: Download `.dll` from PECL and add to `php.ini`

### Step 3: Clone Repository

```bash
git clone https://github.com/sanjanamethe3/Proctor-Calling-System.git
cd Proctor-Calling-System
```

### Step 4: Setup Project Structure

```bash
# Create necessary directories
mkdir -p public/{css,js,images}
mkdir -p backend/{api,controllers,models,config}
mkdir -p uploads/{college-photos,documents}
```

### Step 5: Configure PHP Configuration

Update `config/database.php` with your MongoDB connection details:

```php
$mongoUri = 'mongodb://localhost:27017';
$databaseName = 'proctor_calling_system';
```

### Step 6: Start Servers

**MongoDB:**
```bash
mongod
```

**PHP Server (Development):**
```bash
cd Proctor-Calling-System
php -S localhost:8000
```

**Or use Apache:**
```bash
sudo systemctl start apache2
```

### Step 7: Access Application

```
http://localhost:8000
# or
http://localhost/Proctor-Calling-System
```

## Default Login Credentials

### Admin
- **Email**: admin@gmail.com
- **Password**: admin1234

### Faculty
- **Email**: faculty@gmail.com
- **Password**: faculty1234

### Proctor
- **Email**: proctor@gmail.com
- **Password**: proctor1234

### HOD
- **Email**: hod@gmail.com
- **Password**: hod1234

## Project Structure

```
Proctor-Calling-System/
├── public/
│   ├── index.html
│   ├── login.html
│   ├── dashboard.html
│   ├── faculty-panel.html
│   ├── proctor-panel.html
│   ├── hod-panel.html
│   ├── css/
│   │   ├── styles.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── dashboard.js
│   │   ├── faculty.js
│   │   ├── proctor.js
│   │   ├── auth.js
│   │   └── api-client.js
│   └── images/
├── backend/
│   ├── config/
│   │   └── database.php
│   ├── api/
│   │   ├── auth.php
│   │   ├── colleges.php
│   │   ├── students.php
│   │   ├── attendance.php
│   │   └── reports.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CollegeController.php
│   │   └── AttendanceController.php
│   └── models/
│       ├── User.php
│       ├── College.php
│       └── Attendance.php
├── uploads/
├── composer.json
├── .htaccess
└── README.md
```

## Usage Guide

### Admin Panel
1. Login with admin credentials
2. Access Masters section to add:
   - Colleges
   - Departments
   - Divisions
   - Semesters
   - Subjects
   - Slots
3. Register students
4. View dashboard statistics

### Faculty Panel
1. Login with faculty credentials
2. Select college, department, semester, division, subject, and slot
3. Mark attendance (Click = Present, Double-click = Absent)
4. Auto-save functionality

### Proctor Panel
1. Login with proctor credentials
2. Select department and division
3. View attendance records
4. Generate defaulter list (< 75% attendance)
5. Call parent with stored contact numbers
6. Assign status and send notifications
7. Export monthly reports to Excel

### HOD Panel
1. Login with HOD credentials
2. Select college
3. View all student registrations
4. Access attendance reports
5. Monitor department performance

## API Endpoints

### Authentication
- `POST /backend/api/auth.php` - Login
- `POST /backend/api/auth.php?action=logout` - Logout

### Colleges
- `GET /backend/api/colleges.php` - List all colleges
- `POST /backend/api/colleges.php` - Add college
- `PUT /backend/api/colleges.php` - Update college
- `DELETE /backend/api/colleges.php` - Delete college

### Students
- `GET /backend/api/students.php` - List students
- `POST /backend/api/students.php` - Register student
- `PUT /backend/api/students.php` - Update student

### Attendance
- `GET /backend/api/attendance.php` - Get attendance records
- `POST /backend/api/attendance.php` - Mark attendance
- `GET /backend/api/reports.php` - Generate reports

## Database Schema

### Collections

**users**
```json
{
  "_id": ObjectId,
  "email": String,
  "password": String (hashed),
  "name": String,
  "role": String (admin, faculty, proctor, hod),
  "college_id": ObjectId,
  "created_at": Date
}
```

**colleges**
```json
{
  "_id": ObjectId,
  "name": String,
  "mobile": String,
  "email": String,
  "website": String,
  "address": String,
  "photo_url": String,
  "created_at": Date
}
```

**students**
```json
{
  "_id": ObjectId,
  "name": String,
  "mobile": String,
  "parent_mobile": String,
  "gender": String,
  "college_id": ObjectId,
  "department_id": ObjectId,
  "semester": String,
  "created_at": Date
}
```

**attendance**
```json
{
  "_id": ObjectId,
  "student_id": ObjectId,
  "subject_id": ObjectId,
  "date": Date,
  "status": String (present/absent),
  "marked_by": ObjectId,
  "created_at": Date
}
```

## Features Implemented

- ✅ Multi-panel authentication system
- ✅ Role-based access control
- ✅ Master data management
- ✅ Student registration
- ✅ Attendance marking (single & double-click)
- ✅ Auto-save functionality
- ✅ Defaulter list generation
- ✅ Parent notification system
- ✅ Excel report generation
- ✅ Real-time statistics dashboard
- ✅ Responsive design
- ✅ Secure password hashing
- ✅ Session management

## Troubleshooting

### MongoDB Connection Error
```
Solution: Ensure MongoDB server is running (mongod)
```

### PHP MongoDB Extension Not Found
```
Solution: Install via Composer or manually as shown in Step 2
```

### Permission Denied on Upload Folder
```bash
chmod 755 uploads/
```

### Port 8000 Already in Use
```bash
php -S localhost:8001
```

## Future Enhancements

- SMS Gateway Integration (Twilio/AWS SNS)
- Email notifications
- Advanced analytics and charts
- Mobile app (React Native/Flutter)
- AI-powered attendance prediction
- Real-time chat between faculty and students
- Video call integration
- Blockchain-based attendance verification

## Support & Contribution

For issues, suggestions, or contributions, please open an issue or contact the development team.

## License

MIT License - Feel free to use this project for educational purposes.

---

**Version**: 1.0.0  
**Last Updated**: 2026-05-16
