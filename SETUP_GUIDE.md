# BEACONET-mini Setup Guide

## Project Overview
BEACONET-mini is a Laravel-based web application for posting and finding lost items on an interactive map. It features user authentication, location-based item posting, found item notifications, and an admin panel.

## Features Implemented

### 1. Authentication
- Simple registration (no email verification required)
- Login with email and password
- Logout functionality
- Session management

### 2. User Interface
- **Dashboard**: Interactive map using Leaflet.js
  - Right-click on map to set location for lost item
  - View all lost items as circular markers
  - Click markers to see item details
  - Post new lost items with image upload

### 3. Lost Item Management
- Post lost items with:
  - Title (required)
  - Description (max 255 characters)
  - Image upload
  - GPS coordinates via map selection
  - Location name (optional)
- View user's own lost items
- Delete lost items
- Track item status (lost, found, resolved)

### 4. Found Item Reporting
- Click on lost item markers to view details
- Report item as found with:
  - Message describing where/how it was found
  - Photo of the found item
  - Automatic notification to original owner

### 5. Notifications System
- Real-time notifications when someone reports finding your item
- Notification dashboard showing:
  - Unread notifications
  - Reporter name and message
  - Photo of found item
  - Mark as read/delete functionality

### 6. User Settings
- **Appearance**: Dark/Light mode toggle
- **Profile**: Change username
- **Security**: Change password with current password verification
- **Notifications**: Enable/disable notifications toggle

### 7. Admin Panel
- **Admin Account**: 
  - Email: admin@email.com
  - Password: admin@123123123
- **Dashboard**: View statistics
  - Total users count
  - Lost items count
  - Found reports count
  - Items successfully found
- **User Management**: View all users, delete users (except admins)
- **Item Management**: View and delete lost items
- **Report Management**: View and delete found reports

## Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- SQLite (default database)
- Node.js & NPM (optional, for frontend assets)

### Step-by-Step Setup

1. **Navigate to project directory**:
   ```
   cd c:\Users\melch\tong_padua_tacus\BEACONET-mini
   ```

2. **Install dependencies**:
   ```
   composer install
   ```

3. **Create environment file** (if not exists):
   ```
   copy .env.example .env
   ```

4. **Generate application key**:
   ```
   php artisan key:generate
   ```

5. **Create SQLite database file**:
   ```
   touch database/database.sqlite
   ```
   Or in PowerShell:
   ```
   New-Item -Path database\database.sqlite -ItemType File -Force
   ```

6. **Run migrations**:
   ```
   php artisan migrate --force
   ```

7. **Seed database** (creates admin user):
   ```
   php artisan db:seed
   ```

8. **Create storage link** (for image uploads):
   ```
   php artisan storage:link
   ```

9. **Start the development server**:
   ```
   php artisan serve
   ```

10. **Access the application**:
    - Open browser to `http://127.0.0.1:8000`
    - Default admin user: admin@email.com / admin@123123123

## Database Structure

### Tables
- **users**: User accounts with role (user/admin)
- **lost_items**: Posted lost items with GPS coordinates
- **found_reports**: Reports of found items
- **notifications**: Notifications for item owners
- **user_preferences**: User settings (theme, notifications)

## File Structure
```
BEACONET-mini/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LostItemController.php
│   │   │   ├── FoundReportController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── AdminController.php
│   │   │   └── Auth/RegisterController.php
│   │   └── Middleware/
│   │       └── IsAdmin.php
│   └── Models/
│       ├── User.php
│       ├── LostItem.php
│       ├── FoundReport.php
│       ├── Notification.php
│       └── UserPreference.php
├── database/
│   ├── database.sqlite
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── welcome.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── users.blade.php
│   │   ├── lost-items.blade.php
│   │   └── found-reports.blade.php
│   ├── settings/
│   │   └── index.blade.php
│   └── notifications/
│       └── index.blade.php
├── routes/
│   └── web.php
└── bootstrap/
    └── app.php
```

## API Endpoints

### Authentication
- `GET /` - Home page
- `GET /login` - Login form
- `POST /login` - Login
- `GET /register` - Register form
- `POST /register` - Create account
- `POST /logout` - Logout

### User Dashboard
- `GET /dashboard` - Main dashboard with map

### Lost Items
- `GET /lost-items` - Get all lost items (JSON)
- `POST /lost-items` - Create new lost item
- `GET /lost-items/{id}` - Get item details
- `DELETE /lost-items/{id}` - Delete own lost item

### Found Reports
- `POST /found-reports` - Report item as found
- `PATCH /found-reports/{id}/accept` - Accept found report
- `PATCH /found-reports/{id}/reject` - Reject found report

### Notifications
- `GET /notifications` - Get all notifications
- `GET /notifications/unread` - Get unread notifications count
- `PATCH /notifications/{id}/read` - Mark as read
- `PATCH /notifications/mark-all/read` - Mark all as read
- `DELETE /notifications/{id}` - Delete notification

### Settings
- `GET /settings` - Settings page
- `GET /settings/preferences` - Get user preferences (JSON)
- `PATCH /settings/theme` - Update theme
- `PATCH /settings/notifications` - Update notification settings
- `PATCH /settings/profile` - Update profile
- `PATCH /settings/password` - Change password

### Admin
- `GET /admin` - Admin dashboard
- `GET /admin/users` - Manage users
- `DELETE /admin/users/{id}` - Delete user
- `GET /admin/lost-items` - Manage items
- `DELETE /admin/lost-items/{id}` - Delete item
- `GET /admin/found-reports` - Manage reports
- `DELETE /admin/found-reports/{id}` - Delete report

## Technologies Used
- **Backend**: Laravel 12
- **Database**: SQLite
- **Frontend**: HTML5, CSS3, JavaScript
- **Maps**: Leaflet.js (OpenStreetMap)
- **Authentication**: Laravel built-in Auth
- **File Storage**: Local storage with public symbolic link

## Security Features
- CSRF protection on all forms
- Password hashing (bcrypt)
- User authorization checks
- Admin-only middleware
- Authentication guards

## Testing the Application

### Test User Registration
1. Click "Register" on home page
2. Fill in name, email, password
3. Click "Register"
4. Automatically logged in and redirected to dashboard

### Test Posting Lost Item
1. In dashboard, fill in item title and description
2. Right-click on map to set location (a marker appears)
3. Optional: Upload image
4. Click "Post Item"
5. Item appears as marker on map for all users

### Test Finding Item
1. Click on any item marker on the map
2. Modal shows item details and reporter name
3. Click "Found this item?"
4. Enter message about finding it
5. Optional: Upload photo of found item
6. Submit report
7. Original poster receives notification

### Test Admin Panel
1. Login as admin@email.com / admin@123123123
2. Access `/admin` to see dashboard
3. View users, items, and reports management pages
4. Delete users or items as needed

### Test Settings
1. Click "Settings" in dashboard
2. Toggle dark/light mode
3. Update username
4. Change password
5. Toggle notifications

## Common Issues & Solutions

### 1. Composer Installation Issues
If `composer install` fails:
```
composer install --prefer-source --no-interaction
```

### 2. Database Migrations Failed
Reset and remigrate:
```
php artisan migrate:refresh --seed
```

### 3. Storage Link Missing
Create storage link for image uploads:
```
php artisan storage:link
```

### 4. Permission Denied on Files
Windows users may need to check file permissions

### 5. Port 8000 Already in Use
Use different port:
```
php artisan serve --port=8001
```

## Future Enhancements
- Email notifications
- Advanced map filters
- Search functionality
- User profiles and ratings
- Social sharing
- Mobile app
- Push notifications
- Real-time chat with reporters
- Geofencing for location-based alerts

## Support
For issues or questions, check the Laravel documentation:
- https://laravel.com/docs
- https://leafletjs.com/

---

**Developed**: January 2026
**Framework**: Laravel 12
**Database**: SQLite
