# Sales Management Web

## Tech stack
- Playwright (E2E Testing)
- PostgreSQL (Database)
- Docker (Environment setup)
- Tailwind
- Automation testing

## Run project
### 1. Cài đặt Docker
Tải và cài Docker Desktop: https://www.docker.com/products/docker-desktop/

### 2. Khởi động hệ thống (Backend + Database)
- docker compose up -d --build
- http://localhost:8080/

### 3. Cài đặt dependencies cho automation test
npm install

### 4. Run test
- cd "automation_test"
- Run test (Native): npm run test
- Run test (Docker): docker compose run --rm playwright

## 5. Report và Database
- Reset database: npm run reset
- Seed database: npm run seed
- Report: npm run report


## Scope kiểm thử
- E2E testing cho các luồng CRUD
- Authentication flow
- UI & validation testing
- Verify dữ liệu bằng SQL sau các thao tác

## Features
- Page Object Model (POM)
- Multi-browser testing
- SQL data verification

