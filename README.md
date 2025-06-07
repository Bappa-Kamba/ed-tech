# EduLearn 🎓

**EduLearn** is an AI-powered EdTech platform built with Laravel and Vue.js. It allows administrators to upload lesson materials, while students can read content and interact with an AI assistant to ask questions and receive helpful answers.

This project was created as part of a 7-day assessment and includes AI integration via Hugging Face for real-time educational assistance.

---

## ✨ Features

- Admin authentication and dashboard
- Upload and manage lesson materials
- Student login and access to lessons
- Ask AI: Students can ask questions related to any lesson
- AI answers powered by Hugging Face's DeepSeek R1 model
- Session and queue management via PostgreSQL
- Responsive UI with Vue.js and Blade templates

---

## 🧠 AI Integration

The platform integrates with **DeepSeek R1**, hosted on Hugging Face via [Hyperbolic's API Router](https://router.huggingface.co/hyperbolic/v1/chat/completions).

- **Model Used**: `deepseek-chat`
- **API Provider**: Hugging Face (via Hyperbolic)
- **Endpoint**: `https://router.huggingface.co/hyperbolic/v1/chat/completions`

Make sure you have a valid `HYPERBOLIC_API_KEY` stored in your `.env` file for this to work.

---

## 🧰 Tech Stack

| Layer        | Tech                        |
|--------------|-----------------------------|
| Backend      | Laravel 11                  |
| Frontend     | Vue 3 + Blade               |
| AI API       | Hugging Face (DeepSeek R1)  |
| Database     | PostgreSQL                  |
| Auth System  | Laravel Breeze              |
| Session/Queue| Database                    |

---

## 🚀 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/edulearn.git
cd edulearn

2. Install PHP Dependencies

composer install

3. Install JavaScript Dependencies

npm install && npm run dev

4. Configure Environment Variables

Create a .env file and fill in values similar to:

APP_NAME="EduLearn"
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ed-tech
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

HYPERBOLIC_API_KEY=your_huggingface_api_key

    Note: Your .env file should also include a valid APP_KEY generated using:

php artisan key:generate

5. Run Migrations

php artisan migrate

6. (Optional) Seed the Database

You can optionally add some test data using seeders.

php artisan db:seed

- Note: Two user accounts admin@ed-tech.com and student@ed-tech.com will be available upon seeding. Use the admin account i order to create lessons.
'password' is the passkey for both accounts.

🏃 Running the Application
Start the Laravel server:

php artisan serve

Start the Vite dev server:

npm run dev

Then visit http://localhost:8000 in your browser.
```

## 📁 Project Structure

├── app/
├── resources/
│   ├── js/         ← Vue.js components
│   └── views/      ← Blade templates
├── routes/
│   └── web.php     ← Main route definitions
├── database/
│   ├── migrations/
│   └── seeders/
├── .env            ← Environment configuration
├── package.json    ← JavaScript dependencies
├── vite.config.js  ← Vite configuration
├── composer.json   ← PHP dependencies

## NOTE:
This project is for educational/demo purposes and does not yet carry a license. Contact the author if you'd like to reuse parts of the project.

## 🙌 Acknowledgements
- DeepSeek R1 Model
- Laravel
- Vue.js
- Hugging Face
