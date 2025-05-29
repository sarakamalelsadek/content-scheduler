# content-scheduler

A simplified content scheduling system built with Laravel and Vue.js to allow users to create, schedule, and manage posts across multiple social platforms.

## 🚀 Features

- 🔐 User authentication (Laravel Sanctum)
- 📝 Post creation, editing, and deletion
- ⏰ Schedule posts for future publication
- 📊 Post analytics dashboard (per platform, success rate, etc.)
- 💡 Rate limiting (max 10 scheduled posts per day)
- ✅ Platform validation (character limits, image rules, etc.)
- 🔄 Laravel command/job to publish due posts
- ⚙️ Manage active platforms per user

---

## 🛠️ Tech Stack

- Backend: **Laravel 10**
- Authentication: **Laravel Sanctum**
- Database: **MySQL**
- File Storage: **Laravel Filesystem (Public disk)**

---

## ⚙️ Installation

```bash
git clone https://github.com/sarakamalelsadek/content-scheduler.git
cd content-scheduler

# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure migration and seeding data
php artisan migrate --seed

# Start the server
php artisan serve

# run schedule
php artisan queue:work

# start the queue
php artisan queue:work


# 📬 Postman Collection

You can test the API easily using our ready-made Postman collection.

📁 GO TO THIS FOLDER IN OWR PROJECT (Postman/ContentScheduler.postman_collection.json)

### How to Use

1. Open Postman
2. Click on "Import"
3. Select the downloaded `.json` file
4. Set your environment variables (`server`, `token`)
5. Start testing the API endpoints!

## Thank You

