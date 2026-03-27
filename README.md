# 🍽 Restaurant Ordering System — Laravel

A private, full-featured restaurant ordering platform built with **Laravel 11**, **Bootstrap 5**, and **MySQL**.

---

## ✅ Features

| Area | Details |
|---|---|
| **Auth** | Login / Register with admin approval gate |
| **Menu** | Categories → Items → Add-ons (CRUD) |
| **Orders** | Cart → Checkout → Order tracking |
| **Payment** | Cash on Delivery only |
| **Coupons** | One-time / reusable discount codes (fixed & %) |
| **Printing** | ESC/POS: network TCP, USB, or file output |
| **Profiles** | Address + Google Maps, order history |
| **Newsletter** | Subscribe / unsubscribe + CSV export |
| **Admin Panel** | Full CRUD, status management, sales reports |
| **GDPR** | Data export (JSON) + account anonymisation |
| **SEO Block** | `robots.txt` + `X-Robots-Tag` headers everywhere |
| **Mobile** | Bootstrap 5 responsive layout |

---

## 🚀 Quick Start

### 1. Clone & Install
```bash
git clone <your-repo-url> restaurant
cd restaurant
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure `.env`
```env
APP_URL=https://yourdomain.com
DB_DATABASE=restaurant_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
GOOGLE_MAPS_API_KEY=AIzaSy...
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
# ... etc
```

### 3. Database
```bash
php artisan migrate
php artisan db:seed
```

Default credentials after seeding:
- **Admin:** `admin@yourdomain.com` / `Admin@12345!`
- **Demo user:** `customer@demo.com` / `Customer@123`

> ⚠️ Change all passwords immediately in production!

### 4. Storage Link
```bash
php artisan storage:link
```

### 5. Queue (for email notifications)
```bash
php artisan queue:work --daemon
# Or for production, use Supervisor
```

---

## 🗂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/         LoginController, RegisterController
│   │   ├── Admin/        Dashboard, User, Category, MenuItem,
│   │   │                 Order, Coupon, Newsletter,
│   │   │                 Report, PrinterConfig
│   │   ├── MenuController
│   │   ├── OrderController
│   │   └── ProfileController
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── ApprovedMiddleware.php
│   │   └── NoIndexMiddleware.php
├── Models/
│   ├── User, Category, MenuItem, Addon
│   ├── Order, OrderItem, Coupon
│   ├── NewsletterSubscription
│   ├── PrinterConfig, GdprRequest
├── Services/
│   └── PrinterService.php     # ESC/POS receipt generation
└── Notifications/
    ├── OrderConfirmed
    ├── OrderStatusUpdated
    ├── AccountApproved
    └── AdminNewUserRegistration
```

---

## 🗄 Database Schema

| Table | Purpose |
|---|---|
| `users` | Customers & admins with role/status/GDPR |
| `categories` | Menu categories with images & ordering |
| `menu_items` | Items with price, allergens, add-ons |
| `addons` | Per-item optional extras with prices |
| `orders` | Full order record with delivery coords |
| `order_items` | Snapshot of items at time of order |
| `coupons` | One-time & reusable discount codes |
| `newsletter_subscriptions` | Mailing list with unsubscribe token |
| `printer_configs` | Network/USB printer settings |
| `gdpr_requests` | Audit trail of data export/delete requests |
| `sessions` | Database session storage |

---

## 🖨 Printer Setup

The system uses **raw ESC/POS** bytes — no CUPS required.

### Network Printer (recommended)
1. Assign a **static IP** to your receipt printer.
2. In Admin → Printer, add: `Type=Network`, `Host=192.168.1.100`, `Port=9100`.
3. Click **Test Print** to verify.

### USB Printer
```bash
# Grant access
sudo chmod a+rw /dev/usb/lp0
# Or permanently via udev rule:
echo 'SUBSYSTEM=="usb", ATTR{idVendor}=="04b8", MODE="0666"' | sudo tee /etc/udev/rules.d/99-printer.rules
```
Then set `Type=USB`, `Device=/dev/usb/lp0`.

### Supported Printers
- Epson TM-T20 / TM-T82 / TM-T88
- Star TSP100 / TSP650
- BIXOLON SRP-350
- Any ESC/POS compatible thermal printer

---

## 🗺 Google Maps Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/).
2. Enable **Maps JavaScript API** and **Places API**.
3. Create an API key and restrict it to your domain.
4. Add to `.env`: `GOOGLE_MAPS_API_KEY=AIzaSy...`

---

## 🔒 Security & GDPR

- All routes are **private** (auth required)
- `robots.txt` blocks all crawlers
- Every response has `X-Robots-Tag: noindex,nofollow`
- User data export available as JSON
- Account deletion anonymises PII, retains order records for accounting
- GDPR consent timestamp stored at registration
- Session cookies only — no tracking

---

## 📧 Email Notifications

| Trigger | Recipient |
|---|---|
| New registration | All admins |
| Account approved | Customer |
| Order placed | Customer |
| Order status change | Customer |

Configure SMTP in `.env` (Mailgun, SMTP, Postmark, etc.).

---

## 🚢 Production Deployment (Nginx example)

```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;
    root /var/www/restaurant/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
    location ~* \.(env|log|git) { deny all; }
}
```

---

## 📋 Admin Credentials Checklist (Before Go-Live)

- [ ] Change admin password from seed default
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure real SMTP credentials
- [ ] Add Google Maps API key with domain restriction
- [ ] Configure printer IP address
- [ ] Set `SESSION_SECURE_COOKIE=true` (requires HTTPS)
- [ ] Run `php artisan config:cache && php artisan route:cache`
- [ ] Set up cron for `php artisan schedule:run`
- [ ] Set up Supervisor for queue workers

---

## 📄 License

Private use. All rights reserved.
