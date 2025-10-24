# PostgreSQL Sound Test Application - Installation Guide

## Prerequisites

- Python 3.7 or higher
- PostgreSQL 12 or higher
- pip (Python package installer)

## Step 1: Install PostgreSQL

### Windows

1. Download PostgreSQL from https://www.postgresql.org/download/windows/
2. Run the installer (recommended: PostgreSQL 15 or 16)
3. During installation:
   - Set a password for the postgres user (remember this!)
   - Default port: 5432
   - Install pgAdmin 4 (GUI tool - optional but helpful)
4. Verify installation:
   ```cmd
   psql --version
   ```

### Linux (Ubuntu/Debian)

```bash
# Install PostgreSQL
sudo apt update
sudo apt install postgresql postgresql-contrib

# Start PostgreSQL service
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Switch to postgres user
sudo -i -u postgres
psql
```

## Step 2: Set Up PostgreSQL Database

### Option A: Using Command Line

```bash
# Login to PostgreSQL (will prompt for password)
psql -U postgres

# Or on Linux, you might need:
sudo -u postgres psql
```

Then in the PostgreSQL prompt:
```sql
-- Create a database (optional - our script will do this)
CREATE DATABASE sound_test_db;

-- Create a user with password (optional but recommended for production)
CREATE USER sound_test_user WITH PASSWORD 'regular_user_6782';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE sound_test_db TO sound_test_user;

-- Exit
\q
```

### Option B: Using pgAdmin (GUI)

1. Open pgAdmin 4
2. Connect to your PostgreSQL server
3. Right-click "Databases" → "Create" → "Database"
4. Name it `sound_test_db`

## Step 3: Set Up the Project

### Create Project Structure

Create this directory structure:
```
sound_test-app/
├── app.py
├── database.py
├── config.py
├── requirements.txt
├── .env
├── .env.example
├── templates/
│   ├── sound_test.html
│   └── responses.html
└── static/
    └── style.css
```

## Step 4: Configure Environment Variables

Copy the example environment file:
```bash
cp .env.example .env
```

Edit `.env` with your PostgreSQL credentials:
```env
DB_NAME=sound_test_db
DB_USER=postgres
DB_PASSWORD=your_actual_password
DB_HOST=localhost
DB_PORT=5432
```

## Step 5: Install Python Dependencies

Create and activate a virtual environment:

### Windows
```cmd
python -m venv venv
venv\Scripts\activate
```

### Linux
```bash
python3 -m venv venv
source venv/bin/activate
```

Install packages:
```bash
pip install -r requirements.txt
```

## Step 6: Initialize the Database

Run the database initialization script:
```bash
python database.py
```

You should see:
```
Database 'sound_test_db' created successfully!
Database tables initialized successfully!
```

## Step 7: Run the Application

Start the Flask server:
```bash
python app.py
```

You should see:
```
 * Running on http://0.0.0.0:5000
 * Running on http://127.0.0.1:5000
```

## Step 8: Access the Application

Open your browser and visit:
- **Sound Test Form:** http://localhost:5000
- **View Responses:** http://localhost:5000/responses
- **Health Check:** http://localhost:5000/health

## Testing the Database Connection

You can verify the database connection using psql:

```bash
# Connect to the database
psql -U postgres -d sound_test_db

# View tables
\dt

# View the schema
\d responses

# Check data
SELECT * FROM responses;

# Exit
\q
```

## Troubleshooting

### Issue: "psycopg2" installation fails

**Solution:** Try installing build dependencies:

**Windows:**
- Install Microsoft C++ Build Tools
- Or use: `pip install psycopg2-binary` (already in requirements.txt)

**Mac:**
```bash
brew install postgresql
pip install psycopg2-binary
```

**Linux:**
```bash
sudo apt-get install libpq-dev python3-dev
pip install psycopg2-binary
```

**Solution:** Reset your PostgreSQL password:

```bash
# Linux
sudo -u postgres psql
ALTER USER postgres PASSWORD 'new_password';

# Windows
psql -U postgres
ALTER USER postgres PASSWORD 'new_password';
```

Then update your `.env` file with the new password.

## Production Deployment Considerations

### 1. Use Environment Variables

Never hardcode credentials. Always use environment variables in production.

### 2. Update PostgreSQL Configuration

For production, edit `postgresql.conf`:
```
max_connections = 100
shared_buffers = 256MB
```

### 3. Set Up Connection Pooling

For better performance, consider using `pgbouncer` or SQLAlchemy with connection pooling.

### 4. Regular Backups

Set up automated backups:
```bash
pg_dump -U postgres sound_test_db > backup.sql
```

## Next Steps

- Customize the questionnaire fields in `templates/sound_test.html`
- Add authentication for the `/responses` admin page
- Set up automated backups
- Configure a production WSGI server (Gunicorn, uWSGI)
- Set up a reverse proxy (Nginx, Apache)

## Stopping the Application

Press `Ctrl+C` in the terminal running the Flask app.

To deactivate the virtual environment:
```bash
deactivate
```

To stop PostgreSQL:
```bash

# Linux
sudo systemctl stop postgresql

# Windows
services.msc (stop the PostgreSQL service)
```