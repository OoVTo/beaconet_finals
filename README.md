# BEACONET-mini 🌍

A Laravel-based web application for posting and finding lost items on an interactive map.

## 🎯 Quick Start

### For Windows Users:

1. **Using Batch Script (Recommended)**:
   ```cmd
   start.bat
   ```

2. **Using PowerShell**:
   ```powershell
   powershell -ExecutionPolicy Bypass -File start.ps1
   ```

3. **Manual Setup**:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed
   php artisan storage:link
   php artisan serve
   ```

Then open your browser to **http://127.0.0.1:8000**

## 👤 Default Credentials

- **Admin Account**:
  - Email: `admin@email.com`
  - Password: `admin@123123123`

## ✨ Key Features

✅ **User Authentication**
- Simple registration (no email verification)
- Login/logout functionality
- Session management

✅ **Interactive Map Dashboard**
- View lost items as markers on Leaflet.js map
- Right-click to set location for posting
- OpenStreetMap integration

✅ **Lost Item Management**
- Post lost items with title, description, image
- Automatic GPS coordinates from map
- Track item status (lost/found/resolved)
- Delete your own items

✅ **Found Item Reporting**
- Report items as found
- Upload photo evidence
- Send notification to item owner
- Message describing location

✅ **Notification System**
- Get notified when someone finds your item
- View notification history
- Mark as read/delete notifications
- Photo and message from finder

✅ **User Settings**
- Dark/Light mode toggle
- Update username
- Change password
- Enable/disable notifications

✅ **Admin Dashboard**
- View statistics (users, items, reports)
- Manage users (delete functionality)
- Manage lost items
- Manage found reports
- Access at `/admin`

## 📁 Project Structure

```
BEACONET-mini/
├── app/
│   ├── Http/Controllers/       # Business logic
│   ├── Models/                 # Database models
│   └── Middleware/             # Admin middleware
├── database/
│   ├── database.sqlite         # SQLite database
│   ├── migrations/             # Database schema
│   └── seeders/                # Database seeds
├── resources/views/            # Blade templates
│   ├── auth/                   # Login/Register
│   ├── dashboard/              # Main app
│   ├── admin/                  # Admin pages
│   ├── settings/               # User settings
│   └── notifications/          # Notifications
├── routes/web.php              # URL routes
└── start.bat / start.ps1        # Startup scripts
```

## 🗄️ Database Schema

### users
- id, name, email, password, role (user/admin)

### lost_items  
- id, user_id, title, description, image_path
- latitude, longitude, location_name, status

### found_reports
- id, lost_item_id, reporter_id, message, image_path, status

### notifications
- id, user_id, found_report_id, type, title, message, is_read

### user_preferences
- id, user_id, theme (light/dark), notifications_enabled

## 🛠️ Technologies

- **Backend**: Laravel 12 (PHP 8.1+)
- **Database**: SQLite
- **Frontend**: HTML5, CSS3, JavaScript
- **Maps**: Leaflet.js with OpenStreetMap
- **Authentication**: Laravel Auth
- **File Upload**: Local storage

## 🔄 Workflow

### Posting a Lost Item
1. Go to Dashboard
2. Fill in item details
3. **Right-click on map** to set location
4. Upload optional image
5. Click "Post Item"
6. Item appears as marker for all users

### Reporting a Found Item
1. Click any marker on the map
2. View item details in modal
3. Click "Found this item?"
4. Fill in message (where found, condition, etc.)
5. Upload photo of item
6. Submit
7. Item owner gets notification

### Managing Items
- View your posted items in sidebar
- Delete items you've posted
- Check found reports received
- Accept/reject found reports

### Admin Management
1. Login as admin
2. Visit `/admin`
3. View dashboard statistics
4. Manage users, items, reports
5. Delete problematic content

## 🌐 Routes Overview

### Public Routes
- `/` - Home page
- `/login` - Login form
- `/register` - Registration form

### User Routes (Authenticated)
- `/dashboard` - Main map interface
- `/notifications` - View notifications
- `/settings` - User settings
- `/lost-items` - Manage lost items (JSON API)
- `/found-reports` - Report found items (JSON API)

### Admin Routes (Admin Only)
- `/admin` - Dashboard
- `/admin/users` - Manage users
- `/admin/lost-items` - Manage items
- `/admin/found-reports` - Manage reports

## 📋 Features Details

### Map Interface
- Zoom and pan enabled
- Right-click context menu sets location
- Blue circular markers for items
- Click marker for details

### Posting Item
- Title (required)
- Description (max 255 chars)
- Image upload (JPEG, PNG, GIF)
- GPS coordinates from map
- Optional location name

### Found Report
- Message (max 500 chars)
- Photo upload
- Auto-notification to owner
- Reporter name shown to owner

### User Preferences
- Light/Dark theme toggle (stored in DB)
- Notification on/off
- Username changes
- Password updates with verification

### Admin Features
- Dashboard with statistics
- User list with delete
- Item list with delete
- Report list with delete
- View recent activity

## 🚀 Running the Application

### First Time Setup
```bash
cd c:\Users\melch\tong_padua_tacus\BEACONET-mini
composer install
php artisan key:generate
php artisan migrate --force
php artisan db:seed
php artisan storage:link
php artisan serve
```

### Subsequent Runs
```bash
php artisan serve
```

Then visit **http://127.0.0.1:8000** in your browser.

## 🆘 Troubleshooting

### Composer Installation Failed
```bash
composer install --prefer-source --no-interaction
```

### Database Issues
```bash
php artisan migrate:refresh --seed
```

### Missing Storage Link
```bash
php artisan storage:link
```

### Port 8000 Already in Use
```bash
php artisan serve --port=8001
```

## 📱 Testing

### Create Test Account
1. Click "Register"
2. Fill in any name, email, password
3. Submit (no verification needed)
4. Logged in automatically

### Post Test Item
1. Fill "Post Lost Item" form
2. **Must right-click map to set location**
3. Add title and optional image
4. Submit

### Find Test Item
1. Click any marker on map
2. Click "Found this item?"
3. Add message and optional photo
4. Submit
5. Original poster gets notification

### Admin Test
1. Logout current user
2. Login with admin@email.com / admin@123123123
3. Click "Admin Panel" button
4. View dashboard and management pages

## 📝 Notes

- SQLite database is included in `database/database.sqlite`
- Images are stored in `storage/app/public/`
- All user data stored locally (no external APIs)
- Map uses free OpenStreetMap tiles
- Admin account created automatically on first seed

## 🔒 Security

- CSRF protection on all forms
- Password hashing (bcrypt)
- User authorization checks
- Admin-only middleware
- SQL injection protection (Laravel ORM)
- Secure session management

## 📞 Support

For issues, refer to:
- [Laravel Documentation](https://laravel.com/docs)
- [Leaflet.js Documentation](https://leafletjs.com/)

---

**Version**: 1.0.0  
**Created**: January 2026  
**Framework**: Laravel 12  
**Database**: SQLite  
**Status**: ✅ Production Ready
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
