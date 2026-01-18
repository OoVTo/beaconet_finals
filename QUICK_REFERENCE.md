# BEACONET-mini - Quick Reference

## 🚀 Start Application

### Windows (Easiest)
```cmd
start.bat
```

### PowerShell
```powershell
powershell -ExecutionPolicy Bypass -File start.ps1
```

### Manual
```bash
cd c:\Users\melch\tong_padua_tacus\BEACONET-mini
php artisan serve
```

Then visit: **http://127.0.0.1:8000**

---

## 👥 Test Accounts

### Admin Account
- Email: `admin@email.com`
- Password: `admin@123123123`
- Access: `/admin`

### Create New Account
1. Click "Register" on home page
2. Fill in name, email, password
3. No verification needed - instantly logged in

---

## 🗺️ User Workflow

### Step 1: Post a Lost Item
```
1. Go to Dashboard
2. Fill "Post Lost Item" form
3. RIGHT-CLICK on map to set location (blue marker appears)
4. Optional: Upload image of item
5. Click "Post Item"
```

### Step 2: View All Items
```
1. See all markers on map (blue circles)
2. Zoom and pan the map
3. Click any marker to see details
```

### Step 3: Report Finding Item
```
1. Click marker of lost item
2. View details in modal
3. Click "Found this item?"
4. Enter message describing where found
5. Optional: Upload photo of item
6. Click "Submit Report"
```

### Step 4: Receive Notification
```
1. Item owner gets notification automatically
2. Can view finder's name, message, and photo
3. Can accept or reject the report
4. View all notifications in `/notifications`
```

---

## ⚙️ User Settings

Access from Dashboard menu → Settings

### Change Theme
- Toggle between Light and Dark mode
- Auto-saves to database

### Update Profile
- Change username
- Click "Update Profile"

### Change Password
- Enter current password
- Enter new password twice
- Confirm to update

### Notification Settings
- Enable/disable notifications
- Affects all received notifications

---

## 👨‍💼 Admin Panel

Access at: `/admin`

### Dashboard
- View statistics
- See recent items and reports
- Quick access links

### User Management
- View all users
- Delete users (except admin)
- See join date and info

### Item Management  
- View all lost items
- Delete inappropriate items
- See status and owner

### Report Management
- View all found reports
- Delete reports as needed
- See pending/accepted/rejected status

---

## 📁 Project Location
```
c:\Users\melch\tong_padua_tacus\BEACONET-mini\
```

### Key Files
- `routes/web.php` - All routes
- `app/Http/Controllers/` - Business logic
- `resources/views/` - HTML templates
- `database/database.sqlite` - SQLite database
- `start.bat` - Windows startup script

---

## 📊 Database

### Stored Data
- **users** - User accounts (name, email, password, role)
- **lost_items** - Posted items (title, description, image, GPS coords)
- **found_reports** - Found reports (message, image, status)
- **notifications** - Notifications to users (message, image, read status)
- **user_preferences** - User settings (theme, notifications enabled)

### Data Files
- Database: `database/database.sqlite`
- Images: `storage/app/public/lost-items/` and `storage/app/public/found-reports/`

---

## 🐛 Troubleshooting

### Application Won't Start
```bash
# Install dependencies
composer install

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Start server
php artisan serve
```

### Database Locked
```bash
php artisan migrate:refresh --seed
```

### Port 8000 in Use
```bash
php artisan serve --port=8001
```

### No Images Showing
```bash
php artisan storage:link
```

### Login Not Working
- Check database exists: `database/database.sqlite`
- Verify admin user created: `php artisan db:seed`
- Try clearing cache: `php artisan cache:clear`

---

## 🔗 Important URLs

| URL | Description |
|-----|-------------|
| `/` | Home page |
| `/login` | Login form |
| `/register` | Registration form |
| `/dashboard` | Main app with map |
| `/notifications` | View notifications |
| `/settings` | User settings |
| `/admin` | Admin dashboard (admin only) |

---

## 📝 Features Summary

✅ User login & registration
✅ Post lost items with GPS location
✅ Interactive map view
✅ Report found items
✅ Automatic notifications
✅ User settings (theme, password, profile)
✅ Admin management panel
✅ Image uploads
✅ Notification history
✅ Dark/Light mode

---

## 🎯 Quick Test

1. **Register** a test account
2. **Post** a lost item (right-click map!)
3. **Create another** account
4. **Report** the item as found
5. **View** notification on first account
6. **Login as admin** and manage users/items

---

## 📞 Support

- See `README.md` for full documentation
- See `SETUP_GUIDE.md` for detailed setup
- See `IMPLEMENTATION_SUMMARY.md` for technical details

---

## ⚡ Performance Tips

- Map loads faster with fewer markers
- Images reduce load time if very large
- SQLite is perfect for small-medium apps
- Local storage doesn't require internet

---

**Created**: January 2026
**Framework**: Laravel 12
**Database**: SQLite
**Version**: 1.0.0

✅ Ready to use!
