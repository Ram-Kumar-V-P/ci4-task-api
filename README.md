# 📌 Task Management API – CodeIgniter 4

A production-grade **REST API** built with CodeIgniter 4 + MySQL, implementing:

- **JWT Authentication**
- **Task CRUD**
- **Filtering, Pagination & Full-Text Search**
- **Multi-User Task Assignment**
- **Comments & Attachments**
- **Activity Logging**
- **Email Notifications**

---

## 🚀 Installation & Setup

### 1. Clone the project
```bash
git clone <your-repo-url> ci4-task-api
cd ci4-task-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
Copy `.env.example` to `.env` and update:
```ini
app.baseURL = 'http://localhost:8080/'

database.default.hostname = 127.0.0.1
database.default.database = ci4_task_api
database.default.username = root
database.default.password = root
database.default.DBDriver  = MySQLi
database.default.port      = 3306

JWT_SECRET = 'CHANGE_THIS_TO_LONG_RANDOM_SECRET'
JWT_TTL_MINUTES = 120
```

### 4. Create database & run migrations

#### If running locally:
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE ci4_task_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run all migrations to create tables
php spark migrate

# Run specific seeder to load sample data
php spark db:seed DevSeeder   # loads sample users and tasks

# Additional Migration Commands:
# - Roll back all migrations
php spark migrate:rollback

# - Refresh migrations (rollback and re-run)
php spark migrate:refresh

# - Status of migrations
php spark migrate:status

# Additional Seeder Commands:
# - Run all seeders
php spark db:seed

# - Run a specific seeder
php spark db:seed UserSeeder
php spark db:seed TaskSeeder
```

#### If running with Docker:
```bash
# Direct commands without entering the container:
docker exec -it ci4-task-api-container php spark migrate
docker exec -it ci4-task-api-container php spark db:seed DevSeeder

# OR, alternatively, enter the container's shell first:
docker exec -it ci4-task-api-container /bin/bash
# Then run the commands inside the container:
php spark migrate
php spark db:seed DevSeeder
# Exit the container when done
exit
```

### 5. Serve application

#### Option 1: Using PHP's built-in server
```bash
php spark serve
```

#### Option 2: Using Docker Compose
```bash
# Start all services (web, database, phpmyadmin)
docker-compose up -d

# Run migrations and seeders
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DevSeeder

# Access the applications:
# - API: http://localhost:8080
# - PHPMyAdmin: http://localhost:8081 (credentials: root/rootpassword)
```

The Docker setup includes:
- Web API (port 8080)
- MySQL Database (port 3307)
- PHPMyAdmin (port 8081)

---

## 🔑 Authentication

All protected endpoints require `Authorization: Bearer <JWT>` header.

| Endpoint | Method | Description |
|---------|--------|-------------|
| `/auth/register` | POST | Register new user |
| `/auth/login` | POST | Login & get JWT |
| `/auth/me` | GET | Get current user info |

**Register – Request:**
```json
{ "name": "Alice", "email": "alice@example.com", "password": "Alice@123" }
```

**Success Response (201):**
```json
{ "status": "success", "data": { "id": 2 } }
```

**Validation Failure (422):**
```json
{ "status": "fail", "message": "Validation failed", "errors": { "email": "The email field must contain a unique value." } }
```

---

## 📝 Task Endpoints

| Endpoint | Method | Description |
|---------|--------|-------------|
| `/tasks` | GET | List tasks (filter/search/paginate) |
| `/tasks` | POST | Create task |
| `/tasks/{id}` | GET | Get task details |
| `/tasks/{id}` | PUT | Update task |
| `/tasks/{id}` | DELETE | Delete task |
| `/tasks/{id}/assignees` | POST | Assign to multiple users |
| `/tasks/{id}/assignees` | GET | List task assignees |
| `/tasks/{id}/comments` | GET/POST | List or add comments |
| `/tasks/{id}/attachments` | GET/POST | List or upload attachments |
| `/tasks/attachments/{id}` | GET | Download attachment |

---

### 🔍 Filtering, Search & Pagination
`GET /tasks?status=pending&priority=high&due_from=2025-09-01&due_to=2025-09-30&search=release&limit=10&offset=0`

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
      { "id": 11, "title": "Prepare Release", "status": "pending", "priority": "high" }
    ],
    "total": 5,
    "limit": 10,
    "offset": 0
  }
}
```

---

### ➕ Create Task
`POST /tasks`
```json
{
  "title": "Prepare Release",
  "description": "Ship v1",
  "status": "pending",
  "priority": "high",
  "due_date": "2025-09-20"
}
```

**Success (201):**
```json
{ "status": "success", "data": { "id": 11 } }
```

---

### 👥 Assign Users
`POST /tasks/11/assignees`
```json
{ "user_ids": [2, 3] }
```

**Success:**
```json
{ "status": "success", "data": { "task_id": 11, "assigned_to": [2, 3] } }
```

---

### 💬 Add Comment
`POST /tasks/11/comments`
```json
{ "content": "Looks good" }
```

---

### 📎 Attach File
`POST /tasks/11/attachments`
**Multipart Form:** field `file`

---

## ⚠️ Error Handling Summary

| Scenario | Status Code | Example |
|---------|-------------|---------|
| Success | 200/201 | `{ "status": "success", "data": {...} }` |
| Validation Failure | 422 | `{ "status": "fail", "message": "Validation failed", "errors": {...} }` |
| Auth Failure | 401 | `{ "status": "fail", "message": "Missing or invalid Authorization header" }` |
| Not Found | 404 | `{ "status": "fail", "message": "Task not found" }` |
| Server Error | 500 | `{ "status": "error", "message": "Internal server error" }` |
