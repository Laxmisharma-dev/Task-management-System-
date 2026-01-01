# TaskFlow - Task Management System

A modern, responsive task management application built with PHP, MySQL, and Bootstrap.

## 🚀 Features

### 👤 User Features
- ✅ **Task Management**: Create, edit, delete, and mark tasks as complete
- ✅ **Smart Organization**: Set priorities, categories, and due dates
- ✅ **Productivity Tools**: Real-time notifications and overdue alerts
- ✅ **Account Management**: Profile updates and secure password changes
- ✅ **Dashboard Analytics**: Statistics and velocity tracking

### 🔐 Admin Features
- ✅ **Global Task Control**: View, edit, and delete any user's tasks
- ✅ **User Management**: View all users and delete accounts
- ✅ **Task Assignment**: Assign tasks to users with full control
- ✅ **Advanced Analytics**: Reports, charts, and user activity tracking
- ✅ **System Monitoring**: Task completion rates and overdue tracking

## 📁 Project Structure

```
taskflow/
├── index.html                 # Landing page with login/signup modals
├── dashboard.html             # User dashboard (main interface)
├── admin_dashboard.html       # Admin dashboard (management interface)
├── config.php                 # Database configuration
├── api/                       # Backend API endpoints
│   ├── login.php             # User authentication
│   ├── register.php          # User registration
│   ├── admin_login.php       # Admin authentication
│   ├── check_auth.php        # User session validation
│   ├── check_admin_auth.php  # Admin session validation
│   ├── logout.php            # Session termination
│   ├── get_tasks.php         # Retrieve user tasks
│   ├── create_task.php       # Create new tasks
│   ├── update_task_status.php # Update/delete tasks
│   ├── get_profile.php       # Get user profile data
│   ├── update_profile.php    # Update profile/password
│   ├── get_all_users.php     # Admin: list all users
│   ├── get_all_tasks_admin.php # Admin: list all tasks
│   ├── admin_update_task.php # Admin: modify any task
│   ├── admin_delete_task.php # Admin: remove any task
│   ├── admin_create_task.php # Admin: assign tasks to users
│   ├── admin_delete_user.php # Admin: remove users
│   ├── admin_get_reports.php # Admin: generate reports
│   ├── create_admin.php      # Helper: create admin accounts
│   └── setup_database.php    # Database initialization
├── sql/                      # Database schemas
│   ├── database.sql          # Complete database schema
│   └── admin_table.sql       # Admin table definition
└── docs/                     # Documentation
    └── README_BACKEND.md     # Backend API documentation
```

## 🛠️ Installation & Setup

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP)
- **Web Browser** (Chrome, Firefox, Safari, Edge)

### Quick Setup

1. **Start XAMPP Services**
   - Launch XAMPP Control Panel
   - Start Apache and MySQL services

2. **Database Setup**
   ```bash
   # Visit in browser:
   http://localhost/work/api/setup_database.php
   ```

3. **Create Admin Account** (Optional)
   ```bash
   # Visit in browser:
   http://localhost/work/api/create_admin.php?name=Admin&email=admin@example.com&password=admin123
   ```

4. **Access Application**
   ```bash
   # Main application:
   http://localhost/work/index.html

   # Direct user dashboard (after login):
   http://localhost/work/dashboard.html

   # Direct admin dashboard (after admin login):
   http://localhost/work/admin_dashboard.html
   ```

## 🔐 User Roles

### Regular Users
- **Login**: Through landing page modal
- **Dashboard**: Full task management interface
- **Permissions**: Manage only their own tasks
- **Features**: Profile management, notifications, analytics

### Admin Users
- **Login**: Through landing page admin modal
- **Dashboard**: Complete system management interface
- **Permissions**: Full access to all tasks and users
- **Features**: User management, global analytics, task assignment

## 📊 Database Schema

### Tables
- **`users`** - User accounts and profiles
- **`admin`** - Administrator accounts
- **`tasks`** - Task data with relationships

### Key Relationships
- Tasks belong to users (user_id foreign key)
- Admins can access all tasks and users
- Tasks support priorities, categories, and due dates

## 🎨 UI/UX Features

### Responsive Design
- **Mobile-first** approach with Bootstrap 5
- **Adaptive layouts** for all screen sizes
- **Touch-friendly** interface elements

### Interactive Components
- **Modal forms** for login, signup, task creation
- **Toast notifications** for user feedback
- **Real-time updates** without page refresh
- **Loading states** and smooth transitions

### Modern Styling
- **Clean Bootstrap design** with custom styling
- **Color-coded priorities** and status indicators
- **Professional typography** and spacing
- **Consistent branding** throughout

## 🔧 API Endpoints

### Authentication
- `POST /api/login.php` - User login
- `POST /api/register.php` - User registration
- `POST /api/admin_login.php` - Admin login
- `GET /api/check_auth.php` - Validate user session
- `GET /api/check_admin_auth.php` - Validate admin session
- `POST /api/logout.php` - End session

### Task Management
- `GET /api/get_tasks.php` - Get user's tasks
- `POST /api/create_task.php` - Create new task
- `POST /api/update_task_status.php` - Update/delete tasks

### User Management
- `GET /api/get_profile.php` - Get profile data
- `POST /api/update_profile.php` - Update profile/password

### Admin APIs
- `GET /api/get_all_users.php` - List all users
- `GET /api/get_all_tasks_admin.php` - List all tasks
- `POST /api/admin_update_task.php` - Modify any task
- `POST /api/admin_delete_task.php` - Delete any task
- `POST /api/admin_create_task.php` - Assign tasks
- `POST /api/admin_delete_user.php` - Remove users
- `GET /api/admin_get_reports.php` - Generate reports

## 🛡️ Security Features

- **Password Hashing** - bcrypt encryption
- **SQL Injection Protection** - Prepared statements
- **Session Management** - Secure PHP sessions
- **Input Validation** - Client and server-side validation
- **Role-based Access** - User/Admin permission separation
- **CSRF Protection** - Secure form submissions

## 🚀 Deployment

### Local Development
1. Ensure XAMPP is running
2. Place files in `htdocs/work/` directory
3. Access via `http://localhost/work/`

### Production Deployment
1. Upload all files to web server
2. Update `config.php` with production database credentials
3. Run database setup script
4. Configure proper file permissions
5. Set up SSL certificate for HTTPS

## 📝 Development Notes

### File Organization
- **Frontend**: HTML files in root directory
- **Backend**: PHP APIs in `/api/` folder
- **Database**: SQL schemas in `/sql/` folder
- **Documentation**: Guides in `/docs/` folder
- **Configuration**: `config.php` in root

### Code Quality
- **Consistent naming** conventions
- **Error handling** with proper logging
- **Input sanitization** on all user inputs
- **Modular structure** for maintainability

## 🐛 Troubleshooting

### Common Issues

**"Database connection failed"**
- Ensure MySQL is running in XAMPP
- Check credentials in `config.php`
- Run `setup_database.php` first

**"Invalid email or password"**
- Verify admin account exists
- Use `create_admin.php` to create admin
- Check password hashing compatibility

**"Page not loading"**
- Confirm Apache is running
- Check file permissions
- Verify all files are uploaded

## 📄 License

This project is open-source and available under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make changes and test thoroughly
4. Submit a pull request

## 📞 Support

For issues or questions:
- Check the `/docs/` folder for detailed documentation
- Review browser console for JavaScript errors
- Check PHP error logs for backend issues

---

**Built with ❤️ using PHP, MySQL, Bootstrap, and modern web technologies.**
