# Orbix Market - Premium Website Templates Marketplace

A modern, responsive marketplace for premium website templates built with HTML, CSS, JavaScript, Bootstrap, PHP, and MySQL.

## 🚀 Features

- **Modern Design**: Clean, professional interface with glass morphism effects
- **Responsive Layout**: Works perfectly on all devices
- **Interactive Components**: Dynamic filtering, favorites, cart functionality
- **AI Assistant**: Interactive AI mascot for customer support
- **SEO Optimized**: Clean URLs and semantic HTML structure
- **Security**: Secure authentication and data handling
- **Performance**: Optimized assets and database queries

## 📁 Project Structure

```
orbix/
├── assets/
│   ├── css/
│   │   └── style.css           # Main stylesheet
│   ├── js/
│   │   ├── app.js              # Main application
│   │   └── components/         # Modular components
│   │       ├── header.js       # Header interactions
│   │       ├── template-interactions.js
│   │       ├── filter-interactions.js
│   │       └── ai-mascot.js    # AI assistant
│   └── images/                 # Static images
├── config/
│   └── database.php            # Database configuration
├── database/
│   └── orbix_market.sql        # Database schema
├── includes/
│   ├── header.php              # Header template
│   ├── footer.php              # Footer template
│   ├── template-cards.php      # Template cards
│   └── service-cards.php       # Service cards
├── public/
│   └── index.php               # Main entry point
├── vendor/                     # Third-party libraries
├── .htaccess                   # URL rewriting & security
└── README.md                   # This file
```

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Tailwind CSS
- **Backend**: PHP 8.0+, MySQL 8.0+
- **Server**: Apache (XAMPP recommended)
- **Icons**: Remix Icons
- **Fonts**: Google Fonts (Inter, Pacifico)

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache server with mod_rewrite enabled
- XAMPP (recommended for local development)

## 🚀 Installation & Setup

### 1. XAMPP Setup

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL services
3. Open phpMyAdmin (http://localhost/phpmyadmin)

### 2. Database Setup

1. Create a new database named `orbix_market`
2. Import the SQL file: `database/orbix_market.sql`
3. Update database credentials in `config/database.php` if needed

### 3. File Deployment

1. Copy the project folder to XAMPP's `htdocs` directory:
   ```
   C:\xampp\htdocs\orbix\  (Windows)
   /Applications/XAMPP/htdocs/orbix/  (macOS)
   ```

2. Set proper permissions (Linux/macOS):
   ```bash
   chmod -R 755 /path/to/orbix
   chmod -R 777 /path/to/orbix/assets/images
   ```

### 4. Configuration

1. Edit `config/database.php`:
   ```php
   const DB_HOST = 'localhost';
   const DB_NAME = 'orbix_market';
   const DB_USER = 'root';
   const DB_PASS = '';  // Default XAMPP password
   ```

2. Update `APP_URL` in the same file:
   ```php
   const APP_URL = 'http://localhost/orbix';
   ```

### 5. Access the Application

- Main site: `http://localhost/orbix/`
- Admin panel: `http://localhost/orbix/admin/` (to be implemented)

## 🎨 Design Features

### CSS Architecture
- **CSS Variables**: Consistent color scheme and spacing
- **Glass Morphism**: Modern frosted glass effects
- **Animations**: Smooth transitions and hover effects
- **Responsive Design**: Mobile-first approach

### JavaScript Architecture
- **OOP Structure**: Modular component-based architecture
- **Event Handling**: Efficient event delegation
- **Local Storage**: Client-side data persistence
- **AJAX**: Dynamic content loading

## 🔧 Development

### Adding New Templates

1. Add template data to `includes/template-cards.php`
2. Update database with new entries
3. Add corresponding images to `assets/images/`

### Customizing Styles

1. Edit CSS variables in `assets/css/style.css`:
   ```css
   :root {
     --primary-color: #FF5F1F;
     --secondary-color: #1f2937;
   }
   ```

### Adding New Components

1. Create new JavaScript file in `assets/js/components/`
2. Follow the existing class structure
3. Include in `includes/footer.php`

## 🔒 Security Features

- **Input Validation**: PHP and JavaScript validation
- **SQL Injection Protection**: PDO prepared statements
- **XSS Prevention**: Output sanitization
- **CSRF Protection**: Token-based form protection
- **Secure Headers**: Security headers via .htaccess

## 📱 Responsive Design

- **Mobile First**: Optimized for mobile devices
- **Breakpoints**: 
  - Mobile: < 768px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px

## 🚀 Performance Optimization

- **Asset Minification**: Compressed CSS and JS
- **Image Optimization**: WebP format support
- **Caching**: Browser and server-side caching
- **CDN**: External libraries via CDN

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check XAMPP MySQL service is running
   - Verify database credentials in `config/database.php`

2. **URL Rewriting Issues**:
   - Ensure mod_rewrite is enabled in Apache
   - Check .htaccess file permissions

3. **CSS/JS Not Loading**:
   - Verify file paths in includes
   - Check server permissions

## 📈 Future Enhancements

- [ ] User authentication system
- [ ] Payment gateway integration
- [ ] Advanced search functionality
- [ ] Real-time chat support
- [ ] Mobile app (React Native)
- [ ] Multi-language support

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Credits

- **Design**: Modern marketplace design principles
- **Icons**: Remix Icons
- **Fonts**: Google Fonts
- **Framework**: Custom PHP architecture

## 📞 Support

For support and questions:
- Email: support@orbixmarket.com
- Documentation: Check this README
- Issues: Create a GitHub issue

---

**Built with ❤️ for the design community**