# Money Tracker API

A simple RESTful API for managing users, wallets, and transactions — built with Laravel 12.

## Setup

```bash
git clone https://github.com/LoydNgei/Backend_Assessment.git
cd Backend_Assessment

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate
```

## API Endpoints

Base URL: `/api/v1`

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/users` | Create a user |
| `GET` | `/users/{id}` | View user profile (wallets, balances, total balance) |
| `POST` | `/wallets` | Create a wallet |
| `GET` | `/wallets/{id}` | View wallet (balance, transactions) |
| `POST` | `/transactions` | Add a transaction (income or expense) |

### Request Bodies

**Create User**
```json
{ "name": "Jane Doe", "email": "jane@example.com" }
```

**Create Wallet**
```json
{ "user_id": 1, "name": "Personal" }
```

**Add Transaction**
```json
{ "wallet_id": 1, "type": "income", "amount": 5000, "description": "Salary" }
```

## Running Tests

```bash
php artisan test
```
