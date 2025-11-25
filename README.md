# Bank Simulation

A full-stack **banking simulation** built with **PHP**, **MySQL**, and **HTML**, featuring user login, account management, and transaction handling.  
The system supports multiple account holders, each with multiple bank accounts and full transaction history stored in a SQL database.

---

## Features

- Create and log in as an account holder
- Create multiple bank accounts per user
- View all accounts belonging to a logged-in account holder
- Perform transactions:
  - **Deposit**
  - **Withdraw**
  - **Transfer** between accounts
- View complete transaction history for each account
- Server-side validation (insufficient funds, invalid receivers, etc.)
- MySQL database with referential integrity
- Secure queries using prepared statements (prevents SQL injection)
- Session handling for persistent user state

---

## How the System Works

### 1. Login / Create Account
Users begin on the login page, where they can:

- Log in using an existing username and password  
- Create a new account holder (username + unique password)

Passwords must be unique—duplicate passwords are rejected.

---

### 2. Accounts Overview
After logging in, users reach the **accounts page** where they can:

- View a list of all their existing accounts  
- Create new bank accounts (optional initial deposit)  
- Click an account to open its transaction history  

Each account displays:

- **Account ID**
- **Holder name**
- **Balance**

---

### 3. Transaction Page
Selecting an account opens its **transaction page**, showing:

- Current account balance  
- Full list of past transactions  
- A form to create a new transaction

Supported transaction types:

#### **Deposit**
- Increase balance
- `from_account_id = NULL`, `to_account_id = account`

#### **Withdraw**
- Only allowed if balance is sufficient
- Decreases balance  
- `from_account_id = account`, `to_account_id = NULL`

#### **Transfer**
- Requires:
  - Valid receiver account ID  
  - Receiver cannot be the same as sender  
  - Enough funds  
- Moves money between two accounts  
- Both accounts update their balance  

Each transaction records:
- Type  
- From account  
- To account  
- Amount  
- Timestamp  
- Optional description  

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/Vincent010525/Bank-Simulation.git
cd Bank-Simulation
```

---

## Database Setup

### 2. Import the SQL file

Inside the project, there is an exported SQL file:

```
bank.sql
```

Import it into your MySQL server (phpMyAdmin, MySQL CLI, or other tools).

**phpMyAdmin:**
- Go to *Import*
- Select `bank.sql`
- Press *Go*

This will create the tables:

- `account_holders`
- `accounts`
- `transactions`

---

## Environment Configuration

Before running the application, you may have to configure your `.env` file.

A template is included:

```
.env
```

Edit the `.env` file with your database settings:

```
DB_HOST=localhost
DB_USER=root
DB_PASS=yourpassword
DB_NAME=bank
```

The PHP code will automatically load these values to connect to the database.

---

## Running the Project

You can run the system using:

### XAMPP / MAMP / WAMP
1. Move the project folder into `htdocs/` (XAMPP) or equivalent
2. Start **Apache** and **MySQL**
3. Visit:

```
http://localhost/Bank-Simulation/index.html
```

---

## Project Structure

```
/root
 ├── index.html                 # Takes user to the login page
 ├── login.html                 # Login + create account page
 ├── accounts.html              # Displays accounts + create new account
 ├── transactions.html          # Transaction page with history
 ├── show-accounts.php          # Handles login, account creation, account listing
 ├── show-transactions.php      # Handles transactions and transaction listing
 ├── bank.sql                   # Full database export
 └── .env                       # Environment variable template
```

---

## Future Improvements

- Password hashing for improved security
- User-friendly error messages and validation
- More advanced styling and layout
- Pagination for large transaction histories
- Admin dashboard for overseeing all users
- API version for external applications

---

## Author

**Vincent Bejbom**
