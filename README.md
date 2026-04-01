# Sales Management Web

## Tech stack
- Playwright (E2E Testing, API Tesing)
- PostgreSQL (Database)
- Docker (Environment setup)
- PHP
- CSS Tailwind
- Allure Report

## Hướng dẫn chạy Web Demo
1. Tải về và cài đặt Docker Desktop: `https://www.docker.com/`
2. Mở Terminal và dùng lệnh git clone repo về: `https://github.com/pow267/Sales-Management-Web`
3. Dùng lệnh docker compose để Build and Run Demo Web: `docker compose -d --build`

## Hướng dẫn dùng Playwright
1. Di chuyển vào thư mục playwright: `cd automation_test`
2. Cài đặt dependency: `npm install`
3. Chạy test ( sau khi chạy hết bộ test sẽ tự xuất Allure Report ): `npm run full-test`
4. Xem lại báo cáo: `npm run report`

## 5. Database
- Reset database: npm run reset
- Seed database: npm run seed


## Scope kiểm thử
- E2E testing cho các luồng CRUD
- Authentication flow
- UI & validation testing
- Verify dữ liệu bằng SQL sau các thao tác
- API testing

## Features
- Page Object Model (POM)
- Multi-browser testing
- SQL data verification
- Allure Report

