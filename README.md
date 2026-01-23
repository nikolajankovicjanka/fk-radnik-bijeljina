# Vue 3 + TypeScript + Vite - Laravel 10 - Filament - Admin

Moderna web aplikacija za FK Radnik Bijeljina s admin panelom za upravljanje sadržajem.
Frontend: Vue 3 + TypeScript + Vite (brza razvojna iskustva)
Backend: Laravel 10 REST API
Admin Panel: Filament PHP (moderni admin interface)
State Management: Pinia (Vue store)
Routing: Vue Router
UI Components: Tailwind CSS + custom dizajn
Database: MySQL

Frontend (Vue 3)
-cd frontend
-npm install
-npm run dev

Backend (Laravel 10)

-cd backend
-composer install
-cp .env.example .env
-php artisan key:generate
-php artisan migrate --seed
-php artisan serve
