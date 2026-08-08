#  Portfolio Management System

A web-based **Portfolio Management System** developed as an academic project.

The system is designed to help users manage multiple Demat accounts and view their holdings in an organized manner. It also provides portfolio information and IPO news managed by an administrator.

>  **Project Status:** Under Development

---

## 📌 About the Project

Managing investments across multiple Demat accounts can make it difficult to keep track of holdings and portfolio information in one place.

The **Portfolio Management System** aims to provide a centralized platform where users can manage their Demat accounts and view the companies and holdings associated with them.

The system also provides an **IPO News** section where users can view IPO-related information managed by an administrator.

---

## Objectives

The main objectives of this project are to:

- Provide a centralized system for managing multiple Demat accounts.
- Allow users to add and remove their Demat accounts.
- Allow users to view holdings associated with each Demat account.
- Provide a summarized view of the user's portfolio.
- Allow users to search for companies already present in their portfolio.
- Provide IPO-related news in one place.
- Allow administrators to manage company and IPO news information.

---

##  Features

###  User

- [x] User registration
- [x] User login
- [x] User logout
- [x] Multiple Demat account support
- [x] Add Demat account
- [x] Remove Demat account
- [ ] View Demat account holdings
- [ ] Portfolio summary
- [ ] Company search
- [ ] View IPO news

### Administrator

- [ ] Administrator authentication
- [ ] Manage companies
- [ ] Manage IPO news
- [ ] Add IPO news
- [ ] Edit IPO news
- [ ] Delete IPO news

---

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML | Structure and frontend |
| CSS | Styling and UI |
| JavaScript | Client-side interactions |
| PHP | Backend development |
| MySQL | Database management |
| XAMPP | Local development environment |
| Git | Version control |
| GitHub | Source code management |

---

## Project Structure

```text
portfolio-management-system/
│
├── assets/
│   ├── css/
│   └── js/
│
├── config/
│   └── database.example.php
│
├── database/
│   └── portfolio_management.sql
│
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── add_demat.php
├── edit_demat.php
├── delete_demat.php
│
├── .gitignore
└── README.md