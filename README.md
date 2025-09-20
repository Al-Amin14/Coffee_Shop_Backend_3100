# Caffe Sync

**Team Members:**  
- AL AMIN | alamin.cse.20220204014@aust.edu
- Shahadat | shahadat.cse.20220204019@aust.edu
- Sourav | sourav.cse.20220204014@aust.edu
- Muhit | muhit.cse.20210104107@aust.edu

**Project Live Link:** https://coffee-sync.vercel.app/  
**Recorded video:** https://drive.google.com/drive/folders/1YuoKuneXEl96fH2dhB8Ny0JkCE8qLcG3?usp=sharing

---

## Table of Contents

1. [Project Description](#1-project-description)
2. [Workflow Overview](#2-workflow-overview)
3. [Main Features](#3-main-features)
4. [Technologies Used](#4-technologies-used)
5. [System Architecture](#5-system-architecture)
6. [Setup Guidelines](#6-setup-guidelines)
    - [Backend](#backend)
    - [Frontend](#frontend)
7. [Running the Application](#7-running-the-application)
8. [Deployment Status & Tests](#8-deployment-status--tests)
9. [Demo Credentials](#9-demo-credentials)
10. [Screenshots](#10-screenshots)
11. [Limitations / Known Issues](#11-limitations--known-issues)
12. [Dockerization](#12-dockerization)
13. [Figma UI](#13-figma-ui)
14. [Appendix: README Highlights](#14-appendix-readme-highlights)

---

## 1. Project Description

A comprehensive system to streamline coffee shop operations: order processing, billing/invoicing, and detailed customer records. Improves service speed, transaction accuracy, and organized data management—ultimately boosting operational efficiency and customer experience.

**Course:** CSE 3100 (Software Development - IV), Section A1 — Fall 2024  
**Instructors: Mr. Md. Zahid Hossain and Mr. Parvez Ahammed**  
**University:** Ahsanullah University of Science and Technology (AUST), Bangladesh

---

## 2. Workflow Overview

**Actors:** Manager, Customer  
**Flow (high-level):**
1. Customer browses products and adds items to cart.  
2. Customer checks out and pays securely via Stripe.  
3. System records order and generates invoice/receipt.  
4. Manager updates inventory, fulfills orders, and monitors analytics.

---

## 3. Main Features

- End‑to‑end order processing (cart → order → payment → receipt)
- Billing & invoice generation
- Customer profiles and purchase history
- Inventory & product management (manager role)
- Role‑based access (manager, customer) with JWT‑secured APIs
- Stripe payment integration
- Reporting dashboards via SQL JOINs across orders/products/customers
- Data integrity using MySQL trigger for email verification status

**Future Enhancements (Optional):**
- AI-powered product suggestions (optional future enhancement).
- PWA for offline cart & resilient checkout.
- Audit logs and staff scheduling.

---

## 4. Technologies Used

- Frontend: React.js (UI)
- Backend: Laravel (PHP)
- Database: MySQL (normalized schema; PK/FK; JOINs; trigger)
- Authentication: JWT tokens
- Payments: Stripe
- Collaboration: GitHub

---

## 5. System Architecture

**Overview:**  
- React.js SPA communicates with Laravel REST APIs.  
- Laravel manages auth (JWT), business logic, and validation.  
- MySQL stores normalized tables (users, products, orders, payments) with PK/FK relationships and JOIN‑based reporting.  
- A MySQL trigger keeps email verification status consistent.  
- Stripe handles payment processing and invoicing.

**Simple diagram (textual):**
```
[React.js SPA]  ⇄  [Laravel API Layer]  ⇄  [MySQL Database]
         │                  │
         └── Stripe SDK ────┘
```

---

## 6. Setup Guidelines

### Backend (Laravel)
```bash
# Clone the repository
git clone <repo-url>
cd backend

# Install dependencies
composer install

# Setup environment variables
cp .env.example .env
php artisan key:generate

# Configure DB and Stripe keys in .env
# Run migrations/seeders
php artisan migrate --seed

# Run backend server
php artisan serve
```

### Frontend (React)
```bash
cd frontend

# Install dependencies
npm install

# Setup environment variables
cp .env.example .env
# e.g., VITE_API_BASE_URL=http://127.0.0.1:8000/api

# Run frontend
npm run dev
```

---

## 7. Running the Application

- Start Laravel backend at http://127.0.0.1:8000  
- Start React frontend (default: http://127.0.0.1:5173)  
- Create a manager account; configure Stripe keys; test a sample order using Stripe test cards.

---

## 8. Deployment Status & Tests

| Component | Is Deployed? | Is Dockerized? | Unit Tests Added? (Optional) | Notes |
|-----------|--------------|----------------|-------------------------------|-------|
| Backend   | YES           | Compose-ready  | No                            | Dockerfile + compose provided below |
| Frontend  | YES           | Compose-ready  | No                            | Dockerfile + compose provided below |

---

## 9. Demo Credentials

- **Live URL:** {https://drive.google.com/drive/folders/1YuoKuneXEl96fH2dhB8Ny0JkCE8qLcG3?usp=sharing}  
- **Customer:** `{rifat@gmail.com}` / `{12345!@#$%a}`  
- **Manager:** `{jihad@gmail.com }` / `{12345!@#$%a}`  
*Security note: rotate demo passwords after grading.*

---

## 10. Screenshots

> These images are placeholders so you can see layout. Replace them with real screenshots from the live site.

- **Dashboard**  
  ![alt text](image-7.png)

- **Menu / Products**  
  ![alt text](image.png)
   ![alt text](image-3.png)

- **Order**  
  ![alt text](image-1.png)
  
- **Cart**  
  ![alt text](image-2.png)

- **Checkout**  
  ![alt text](image-6.png)

- **Contact us**  
  ![alt text](image-4.png)

- **Profile**  
  ![alt text](image-5.png)

- **Login**  
  ![alt text](image-8.png)

- **SignUp**  
  ![alt text](image-9.png)


---

## 11. Limitations / Known Issues

- Payments currently in Stripe test mode.  
- Limited offline support.  
- Basic analytics; can be extended for deeper insights.  
- Role management includes manager & customer; admin tooling can be expanded.  
- Ensure secure handling of any PII and follow best practices.

---

## 12. Dockerization

**Folder Layout (suggested)**
```
/project-root
  ├─ backend/        # Laravel app
  ├─ frontend/       # React app (Vite or CRA)
  └─ deploy/
       ├─ nginx/
       │   └─ default.conf
       └─ compose.yaml
```

**Backend Dockerfile (Laravel)**
```dockerfile
FROM php:8.2-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader  && php artisan key:generate || true  && chown -R www-data:www-data storage bootstrap/cache
```

**Frontend Dockerfile (React + Vite, production build)**
```dockerfile
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
```

**Nginx vhost for Laravel (reverse proxy)**
```nginx
server {
  listen 80;
  server_name localhost;
  root /var/www/html/public;
  index index.php index.html;
  location / { try_files $uri $uri/ /index.php?$query_string; }
  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass backend:9000;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }
}
```

**docker compose (development)**
```yaml
version: "3.9"
services:
  db:
    image: mysql:8
    environment:
      MYSQL_DATABASE: caffe_sync
      MYSQL_USER: app
      MYSQL_PASSWORD: app
      MYSQL_ROOT_PASSWORD: root
    ports: ["3306:3306"]
    volumes: [ "db_data:/var/lib/mysql" ]

  backend:
    build: ../backend
    working_dir: /var/www/html
    env_file: [ "../backend/.env" ]
    volumes: [ "../backend:/var/www/html" ]
    depends_on: [ db ]

  nginx:
    image: nginx:alpine
    ports: ["8080:80"]
    volumes:
      - "../backend:/var/www/html"
      - "./nginx/default.conf:/etc/nginx/conf.d/default.conf"
    depends_on: [ backend ]

  frontend:
    build: ../frontend
    ports: ["5173:80"]
    depends_on: [ nginx ]

  phpmyadmin:
    image: phpmyadmin
    environment: { PMA_HOST: db }
    ports: ["8081:80"]
    depends_on: [ db ]

volumes: { db_data: {} }
```

**Environment (.env)**
```
APP_URL=http://localhost:8080
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=caffe_sync
DB_USERNAME=app
DB_PASSWORD=app

# Stripe keys (test)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

**Frontend .env**
```
VITE_API_BASE_URL=http://localhost:8080/api
```

**Commands**
```bash
docker compose up -d --build
docker compose exec backend php artisan migrate --seed
docker compose exec backend php artisan storage:link
```

---

## 13. Figma UI

https://www.figma.com/design/SvXCRA28QLWCbgFWzwbXhZ/A1_group07_Coffee_Shop?node-id=1-2&p=f&t=VnEvd82xPS75rRiB-0

---

## 14. Appendix: README Highlights

### Project Objective
{readme_objective}

### Collaborators (from README)
- 1-> Md. Shahadat Hossain<br> 
WakaTime badge frontend <br> [![wakatime](https://wakatime.com/badge/user/3e03447a-8791-4526-b6ff-5bcb2cdcbb30/project/eb5a27b1-35c5-4cf9-bf3f-2a936bd9628e.svg)](https://wakatime.com/badge/user/3e03447a-8791-4526-b6ff-5bcb2cdcbb30/project/eb5a27b1-35c5-4cf9-bf3f-2a936bd9628e)<br>
WakaTime badge backend<br>
[![wakatime](https://wakatime.com/badge/user/3e03447a-8791-4526-b6ff-5bcb2cdcbb30/project/eb5a27b1-35c5-4cf9-bf3f-2a936bd9628e.svg)](https://wakatime.com/badge/user/3e03447a-8791-4526-b6ff-5bcb2cdcbb30/project/eb5a27b1-35c5-4cf9-bf3f-2a936bd9628e)
- 2-> AL AMIN JIHAD <br>
 [![wakatime](https://wakatime.com/badge/user/3da02b1c-7095-45c3-94fd-04673f582943/project/8b02b7d0-6130-4336-8031-e5e6d1e77aa0.svg)](https://wakatime.com/badge/user/3da02b1c-7095-45c3-94fd-04673f582943/project/8b02b7d0-6130-4336-8031-e5e6d1e77aa0)<br>
WakaTime badge backend<br>
[![wakatime](https://wakatime.com/badge/user/3da02b1c-7095-45c3-94fd-04673f582943/project/3e34ec2c-b4f8-492b-9ed9-053a89372b4a.svg)](https://wakatime.com/badge/user/3da02b1c-7095-45c3-94fd-04673f582943/project/3e34ec2c-b4f8-492b-9ed9-053a89372b4a)
- 3-> Sourav Deb <br>
 WakaTime badge frontend<br> [![wakatime](https://wakatime.com/badge/user/037d3863-0437-4965-9b1f-18c891f54dbc/project/3c996e3a-04e7-4ad6-b979-53daab6c7b18.svg)](https://wakatime.com/badge/user/037d3863-0437-4965-9b1f-18c891f54dbc/project/3c996e3a-04e7-4ad6-b979-53daab6c7b18)<br>
WakaTime badge backend<br>
[![wakatime](https://wakatime.com/badge/user/037d3863-0437-4965-9b1f-18c891f54dbc/project/13879cff-f64d-4b80-8040-ad7fad70daa4.svg)](https://wakatime.com/badge/user/037d3863-0437-4965-9b1f-18c891f54dbc/project/13879cff-f64d-4b80-8040-ad7fad70daa4)
- 4-> Abdullah Al Muhit <br>
 [![wakatime](https://wakatime.com/badge/user/aaefaa84-9d83-42ba-9869-9891679ad01d/project/9c5a365d-48c0-419f-8bb4-8ceeab8c59bb.svg)](https://wakatime.com/badge/user/aaefaa84-9d83-42ba-9869-9891679ad01d/project/9c5a365d-48c0-419f-8bb4-8ceeab8c59bb)<br>
WakaTime badge backend<br>
[![wakatime](https://wakatime.com/badge/user/aaefaa84-9d83-42ba-9869-9891679ad01d/project/a19440b4-da64-4d0f-b45b-2a6574d77806.svg)](https://wakatime.com/badge/user/aaefaa84-9d83-42ba-9869-9891679ad01d/project/a19440b4-da64-4d0f-b45b-2a6574d77806)

### Target Audience
Coffee shop owners, managers, staff, and customers who benefit from a comprehensive digital solution for daily operations and feedback.

### Core Features (from README, current scope)
- Order Management: Real-time order taking and tracking for customers and baristas.
- Inventory Management: Monitor stock and notify when restocking is needed.
- Sales Analytics: Daily/weekly/monthly reports to aid decisions.
- Employee Management: Manage schedules, roles, and payroll.

### Technology Stack
- Frontend: React.js
- Backend: Laravel (PHP Framework)
- Database: MySQL

*(Note: The AI-powered product suggestion using Gemini is treated as a future/optional enhancement per your latest instruction.)*
