# Lelis Realty - Real Estate Management Platform

A complete real estate platform built with classic HTML/CSS/JavaScript frontend and PHP/MySQL backend.

## Project Structure

```
lelis-realty/
├── public/                 # Frontend files
│   ├── index.html          # Homepage
│   ├── browse.html         # Property browsing
│   ├── property-detail.html # Property details
│   ├── sell.html           # Seller submission form
│   ├── admin/              # Admin portal
│   │   ├── login.html      # Admin login
│   │   ├── dashboard.html  # Dashboard
│   │   ├── properties.html # Property management
│   │   ├── sellers.html    # Seller management
│   │   ├── buyers.html     # Buyer management
│   │   ├── agents.html     # Agent management
│   │   ├── appointments.html # Appointment management
│   │   ├── inquiries.html  # Inquiry management
│   │   └── reports.html    # Reports & analytics
│   ├── css/
│   │   └── style.css       # Main stylesheet (dark green + gold theme)
│   └── js/
│       └── main.js         # Frontend JavaScript
├── api/                    # Backend API endpoints
│   ├── config.php          # Database configuration
│   ├── auth.php            # Authentication
│   ├── properties.php      # Property management
│   ├── sellers.php         # Seller submissions
│   └── inquiries.php       # Inquiry management
├── db/
│   └── schema.sql          # MySQL database schema
└── README.md               # This file
```

## Features

### Public Website
- **Homepage**: Hero section, featured properties, services overview, brand story
- **Property Browse**: Search, filter, and browse available properties
- **Property Details**: Full property information, agent contact, appointment booking
- **Seller Submission**: Form for homeowners to submit properties
- **Responsive Design**: Mobile-first, works on all devices

### Admin Portal
- **Authentication**: Secure login for brokers and agents
- **Dashboard**: Overview of properties, sellers, inquiries, appointments
- **Property Management**: Add, edit, delete, and feature properties
- **Seller Management**: Track and approve seller submissions
- **Inquiry Management**: Manage property inquiries from buyers
- **Reports**: Sales performance and analytics
- **Role-Based Access**: Different access levels for admin and agents

## Technology Stack

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with CSS variables and Grid/Flexbox
- **JavaScript (Vanilla)**: No frameworks, pure ES6+ with Fetch API

### Backend
- **PHP 7.4+**: Server-side logic
- **MySQL 5.7+**: Relational database
- **PDO**: Database abstraction layer

### Design
- **Color Scheme**: Dark Green (#1a4d3e) + Gold (#d4a574)
- **Typography**: System fonts for optimal performance
- **Responsive**: Mobile-first, fully responsive design
- **Accessibility**: Semantic HTML, proper ARIA labels

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

### Installation

1. **Clone or extract the project**
   ```bash
   cd lelis-realty
   ```

2. **Create MySQL database**
   ```bash
   mysql -u root -p
   CREATE DATABASE lelis_realty;
   USE lelis_realty;
   SOURCE db/schema.sql;
   ```

3. **Configure database connection**
   Edit `api/config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'password');
   define('DB_NAME', 'lelis_realty');
   ```

4. **Set up web server**
   - Point document root to `public/` directory
   - Ensure `.php` files are executable
   - Enable URL rewriting if needed

5. **Create default admin user**
   ```sql
   INSERT INTO users (username, email, password, role, first_name, last_name, status) 
   VALUES ('admin', 'admin@lelisrealty.com', '$2y$10$...hashed_password...', 'admin', 'Admin', 'User', 'active');
   ```
   
   To hash a password in PHP:
   ```php
   echo password_hash('your_password', PASSWORD_BCRYPT);
   ```

6. **Access the application**
   - Public site: `http://localhost/`
   - Admin portal: `http://localhost/admin/login.html`

## Database Schema

### Core Tables
- **users**: Admin and agent accounts
- **properties**: Property listings
- **property_images**: Property photos
- **sellers**: Seller submissions
- **buyers**: Buyer leads
- **appointments**: Property viewing appointments
- **inquiries**: Property inquiries
- **purchases**: Closed transactions
- **articles**: Blog posts

## API Endpoints

### Authentication
- `POST /api/auth.php?action=login` - User login
- `POST /api/auth.php?action=logout` - User logout
- `GET /api/auth.php?action=status` - Check login status

### Properties
- `GET /api/properties.php?action=list` - List properties with pagination
- `GET /api/properties.php?action=featured` - Get featured properties
- `GET /api/properties.php?action=detail&id=X` - Get property details
- `GET /api/properties.php?action=search` - Search properties
- `POST /api/properties.php?action=create` - Create property (admin only)
- `PUT /api/properties.php?action=update&id=X` - Update property (admin only)
- `DELETE /api/properties.php?action=delete&id=X` - Delete property (admin only)

### Sellers
- `GET /api/sellers.php?action=list` - List seller submissions (admin only)
- `POST /api/sellers.php?action=submit` - Submit property for sale
- `PUT /api/sellers.php?action=update&id=X` - Update seller status (admin only)

### Inquiries
- `GET /api/inquiries.php?action=list` - List inquiries (admin only)
- `POST /api/inquiries.php?action=submit` - Submit property inquiry
- `PUT /api/inquiries.php?action=update&id=X` - Update inquiry status (admin only)

## Security Features

- **Password Hashing**: BCrypt hashing for all passwords
- **SQL Injection Prevention**: PDO prepared statements
- **CSRF Protection**: Session-based validation
- **Input Validation**: Server-side input validation
- **Authentication**: Session-based authentication
- **Role-Based Access Control**: Different permissions by role

## Customization

### Changing Colors
Edit CSS variables in `/public/css/style.css`:
```css
:root {
  --primary-color: #1a4d3e;  /* Dark green */
  --gold-color: #d4a574;      /* Gold accent */
  /* ... other variables ... */
}
```

### Adding Pages
1. Create HTML file in `/public/`
2. Include header navigation and footer
3. Use existing CSS classes and components
4. Link in navigation menu

### API Extensions
1. Create PHP file in `/api/`
2. Include `config.php` for database access
3. Use `response()` function for output
4. Follow existing endpoint patterns

## Deployment

### To Deploy to Web Server

1. **Upload files via FTP/SFTP**
   - Upload all files except `/db/schema.sql`
   - Ensure `.php` files have proper permissions

2. **Database Setup**
   - Create database and import `schema.sql`
   - Update connection credentials in `api/config.php`

3. **Environment Configuration**
   - Set proper file permissions (644 for files, 755 for directories)
   - Ensure database can be accessed from web server
   - Update `BASE_URL` in `api/config.php`

4. **Security**
   - Use HTTPS in production
   - Keep PHP and MySQL updated
   - Regular database backups
   - Monitor error logs

## Testing

### Test Accounts
Login to admin portal with test credentials:
- Username: `admin`
- Password: `password`

### Test Workflows
1. Browse properties as guest
2. Submit property as seller
3. Send inquiry on property detail
4. Log in as admin
5. Review seller submissions
6. Manage properties
7. View inquiries and appointments

## Performance Optimization

- Lazy loading for property images
- Pagination for large datasets
- Caching headers on static assets
- Minified CSS and JS (optional)
- Database indexing on frequently queried columns

## Browser Compatibility

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Android)

## Support & Maintenance

- Monitor error logs regularly
- Check database performance
- Update credentials periodically
- Backup database weekly
- Review security updates for PHP and MySQL

## License

This project is provided as-is for the Lelis Realty Services.

## Contact

For questions or support, contact: `contact@lelisrealty.com`
