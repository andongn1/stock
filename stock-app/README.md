# Stock App - Symfony 8.1

Conversion of the Laravel `mercuriale-app` stock management application to Symfony 8.1.

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ (or MariaDB 10.11+)
- Symfony CLI (optional, for local server)

## Installation

```bash
# 1. Clone the repository
cd stock-app

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.local.example .env.local
# Edit .env.local with your database credentials:
# DATABASE_URL="mysql://user:password@127.0.0.1:3306/stock?serverVersion=8.0.32&charset=utf8mb4"

# 4. Generate app secret
php bin/console secrets:generate-key APP_SECRET
# Or manually set APP_SECRET in .env.local

# 5. Create database and run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 6. Load fixtures (test data)
php bin/console doctrine:fixtures:load

# 7. Start the server
symfony server:start
# or
php -S localhost:8000 -t public
```

## Default credentials (from fixtures)

- **Admin**: admin@mercuriale.com / password
- **User**: user@mercuriale.com / password

## Testing

```bash
# Run tests
php bin/phpunit

# Check routes
php bin/console debug:router

# Clear cache
php bin/console cache:clear
```

## API Usage

```bash
# Login to get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@mercuriale.com","password":"password"}'

# Response: {"token":"abc123...","user":{...}}

# Use token for API requests
curl http://localhost:8000/api/articles \
  -H "Authorization: Bearer <token>"
```

## Security Note

**IMPORTANT**: If you previously had credentials committed to git (in .env), you MUST change your database password as the old credentials remain in git history.

## Project Structure

```
stock-app/
├── config/           # Configuration files
├── migrations/       # Database migrations
├── public/           # Web root
├── src/
│   ├── Controller/   # Controllers (Web, Api, Auth)
│   ├── Entity/       # Doctrine entities
│   ├── Form/         # Form types
│   ├── Repository/   # Doctrine repositories
│   ├── Security/     # Security (TokenAuthenticator)
│   ├── DataFixtures/ # Test data fixtures
│   └── Command/      # Console commands
├── templates/        # Twig templates
└── tests/            # PHPUnit tests
```
