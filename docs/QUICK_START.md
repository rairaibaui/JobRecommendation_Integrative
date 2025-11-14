# ⚡ Quick Start - 5 Minutes to Production

Get your AI-powered Job Portal running in 5 simple steps.

---

## 1️⃣ Install Dependencies (2 minutes)

```powershell
# Install PHP packages (includes OpenAI client)
composer install

# Install frontend assets
npm install && npm run build
```

---

## 2️⃣ Configure Environment (1 minute)

```powershell
# Copy environment file
Copy-Item .env.example .env

# Generate app key
php artisan key:generate
```

**Edit `.env` and add your OpenAI key:**
```env
OPENAI_API_KEY=sk-your-key-here
```

Get your key: https://platform.openai.com/api-keys

---

## 3️⃣ Setup Database (30 seconds)

```powershell
# Create SQLite database
New-Item database/database.sqlite -ItemType File -Force

# Run migrations
php artisan migrate --force

# Link storage for file uploads
php artisan storage:link
```

---

## 4️⃣ Start Queue Worker (Required!)

**Open a new terminal and run:**
```powershell
php artisan queue:work --tries=3
```

⚠️ **Keep this running!** This processes business permit validations.

---

## 5️⃣ Start Server (30 seconds)

```powershell
php artisan serve
```

Visit: **http://localhost:8000**

---

## ✅ Test It Works

1. Register as **Employer** with a business permit (use a real Philippine DTI/SEC/Barangay permit)
2. Wait ~30 seconds
3. Check email for verification result
4. Login and try posting a job

---

## 🎯 Portfolio Features Active

✅ **AI Business Permit Verification** (GPT-4o Vision)  
✅ **Background Queue Processing**  
✅ **Email Notifications**  
✅ **Verified-Only Job Posting**  
✅ **Admin CLI Tools**

---

## 📊 Quick Commands

```powershell
# Check employer verification status
php artisan check:employer-validation

# Manually approve an employer
php artisan validate:manual approve --user-id=1

# List all employers
php artisan users:list --type=employer

# Monitor queue jobs
php artisan queue:monitor
```

---

## 🚨 Common Issues

### "OpenAI key not configured"
```powershell
php artisan config:clear
```

### "Jobs not processing"
Make sure queue worker is running:
```powershell
php artisan queue:work --tries=3
```

### "File upload failed"
```powershell
php artisan storage:link
```

---

## 🎓 For Full Documentation

See **PRODUCTION_SETUP.md** for:
- Production deployment
- Windows service setup
- Cost estimation
- Troubleshooting guide

---

**That's it! Your AI-powered job portal is live.** 🚀

Perfect for showcasing in your portfolio and resume.
