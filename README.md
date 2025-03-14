# News Aggregator API

A Laravel-based RESTful API that aggregates news from multiple sources including NewsAPI, The Guardian, and The New York Times.

## Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Docker Setup](#docker-setup)
4. [Configuration](#configuration)
5. [Database Setup](#database-setup)
6. [Running the Application](#running-the-application)
7. [Available Commands](#available-commands)
8. [API Endpoints](#api-endpoints)
9. [Troubleshooting](#troubleshooting)

## Requirements

- Docker and Docker Compose
- PHP 8.2+
- Composer
- Git

## Installation

1. Clone the repository:
```bash
git clone <https://github.com/robinbedi786/backend-laravel-challenge/>
cd laravel-assignment
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Install dependencies:
```bash
composer install
```

## Docker Setup

1. Build and start the containers:
```bash
docker-compose up -d --build
```

2. Verify containers are running:
```bash
docker-compose ps
```

The following containers should be running:
- `news_app` (Laravel Application)
- `news_nginx` (Nginx Web Server)
- `news_db` (MySQL Database)
- `news_redis` (Redis Cache)

## Configuration

1. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

2. Configure API keys in `.env`:
```env
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYT_API_KEY=your_nyt_key
```

## Database Setup

1. Run migrations:
```bash
docker-compose exec app php artisan migrate
```

2. Seed the database (if needed):
```bash
docker-compose exec app php artisan db:seed
```

## Running the Application

The application should be accessible at:
- API: `http://localhost:8000/api`
- Swagger Documentation: `http://localhost:8000/api/documentation`

## Available Commands

### General Laravel Commands

1. Artisan List:
```bash
docker-compose exec app php artisan list
```

2. Cache Commands:
```bash
# Clear application cache
docker-compose exec app php artisan cache:clear

# Clear configuration cache
docker-compose exec app php artisan config:clear

# Clear route cache
docker-compose exec app php artisan route:clear

# Clear view cache
docker-compose exec app php artisan view:clear
```

3. Database Commands:
```bash
# Fresh migration
docker-compose exec app php artisan migrate:fresh

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed
```

4. Development Commands:
```bash
# Generate API documentation
docker-compose exec app php artisan l5-swagger:generate

# Start Laravel Tinker
docker-compose exec app php artisan tinker

# List all routes
docker-compose exec app php artisan route:list
```

### Custom Application Commands

1. Fetch News Articles:
```bash
docker-compose exec app php artisan news:fetch
```

## API Endpoints

### Authentication
- POST `/api/auth/register` - Register new user
- POST `/api/auth/login` - User login
- POST `/api/auth/logout` - User logout

### News
- GET `/api/articles` - Get all articles
- GET `/api/articles/{id}` - Get specific article
- GET `/api/articles/search` - Search articles

### User Preferences
- GET `/api/preferences` - Get user preferences
- POST `/api/preferences` - Update user preferences

## Troubleshooting

### Common Issues and Solutions

1. 502 Bad Gateway:
```bash
# Clear configuration cache
docker-compose exec app php artisan config:clear

# Restart the app container
docker-compose restart app
```

2. Database Connection Issues:
```bash
# Verify database connection
docker-compose exec app php artisan db:monitor

# Check database status
docker-compose exec db mysql -u laravel -psecret -e "SHOW DATABASES;"
```

3. Redis Connection Issues:
```bash
# Switch to file cache driver in .env
CACHE_DRIVER=file
CACHE_STORE=file

# Restart the app
docker-compose restart app
```

### Maintenance Commands

1. Put Application in Maintenance Mode:
```bash
docker-compose exec app php artisan down
```

2. Bring Application Back Online:
```bash
docker-compose exec app php artisan up
```

3. Check Application Status:
```bash
docker-compose exec app php artisan about
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
