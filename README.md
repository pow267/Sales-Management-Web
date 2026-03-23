# Sales Management Web

## Tech stack
- Playwright (E2E Testing)
- PostgreSQL (Database)
- Docker (Environment setup)
- Tailwind

## Run project
### 1. Cài đặt Docker
Tải và cài Docker Desktop:  
https://www.docker.com/products/docker-desktop/
Open project, use cmd:

### 2. Khởi động hệ thống (Backend + Database)
docker compose up -d --build
http://localhost:8080/

### 3. Cài đặt dependencies cho automation test
npm install

### 4. Run test
cd "automation"
npx playwright test --ui

## Scope kiểm thử
- E2E testing cho các luồng CRUD
- Authentication flow
- UI & validation testing
- Verify dữ liệu bằng SQL sau các thao tác

## Features
- Page Object Model (POM)
- Multi-browser testing
- SQL data verification
