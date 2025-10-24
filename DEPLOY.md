# Production Deployment Guide

## Deployment Options

### Option 1: Traditional Linux Server (VPS/Cloud)

#### Using Gunicorn + Nginx

1. **Install dependencies on server:**
```bash
sudo apt update
sudo apt install python3-pip python3-venv nginx postgresql
```

2. **Set up the application:**
```bash
cd /var/www/
git clone your-repo sound_test-app
cd sound_test-app
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
pip install gunicorn
```

3. **Configure environment variables:**
```bash
nano .env
# Add your production database credentials
```

4. **Create systemd service file:**
```bash
sudo nano /etc/systemd/system/sound_test.service
```

Add:
```ini
[Unit]
Description=Sound Test Flask App
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/sound_test-app
Environment="PATH=/var/www/sound_test-app/venv/bin"
ExecStart=/var/www/sound_test-app/venv/bin/gunicorn --workers 3 --bind 127.0.0.1:5000 app:app

[Install]
WantedBy=multi-user.target
```

5. **Start the service:**
```bash
sudo systemctl start sound_test
sudo systemctl enable sound_test
```

6. **Configure Nginx:**
```bash
sudo nano /etc/nginx/sites-available/sound_test
```

Add:
```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:5000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    location /static {
        alias /var/www/sound_test-app/static;
    }
}
```

7. **Enable the site:**
```bash
sudo ln -s /etc/nginx/sites-available/sound_test /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Option 2: Docker Deployment

Create `Dockerfile`:
```dockerfile
FROM python:3.11-slim

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libpq-dev gcc && \
    rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["gunicorn", "--bind", "0.0.0.0:5000", "--workers", "3", "app:app"]
```

Create `docker-compose.yml`:
```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "5000:5000"
    environment:
      - DB_HOST=db
      - DB_NAME=sound_test_db
      - DB_USER=postgres
      - DB_PASSWORD=secure_password
    depends_on:
      - db

  db:
    image: postgres:15
    environment:
      - POSTGRES_DB=sound_test_db
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=secure_password
    volumes:
      - postgres_data:/var/lib/postgresql/data

volumes:
  postgres_data:
```

Run with:
```bash
docker-compose up -d
```

### Option 3: Cloud Platforms

#### Heroku
```bash
# Install Heroku CLI
heroku create your-app-name
heroku addons:create heroku-postgresql:mini
git push heroku main
```

#### AWS (Elastic Beanstalk)
#### Google Cloud (App Engine)
#### DigitalOcean App Platform
#### Railway.app

## Security Checklist

- [ ] Change default passwords
- [ ] Use HTTPS (SSL/TLS)
- [ ] Set up firewall rules
- [ ] Configure CORS properly
- [ ] Add rate limiting
- [ ] Implement authentication for admin routes
- [ ] Regular security updates
- [ ] Database backups