# Iseki Chadet - Character Detection & Record System

## Overview

**Iseki Chadet** (Character Detection) is a web application designed for automated character recognition and record management. It leverages OCR (Optical Character Recognition) technology to detect text from uploaded images and maintains a structured record system for the processed data.

The application combines a robust Laravel backend with a modern frontend, integrating specialized OCR libraries for client-side or server-side text detection.

## Key Features

### 1. Detection Module
*   **Image Upload**: Interface for users to upload images for processing.
*   **OCR Processing**: Integration with `PaddleJS` for optical character recognition.
*   **Result Display**: View detection results (text, confidence) immediately after processing.

### 2. Record Management
*   **Record Tracking**: Maintain a history of all detection activities.
*   **Approval Workflow**: Mechanism for approving specific records.
*   **Export**: Export record data to Excel for external analysis.
*   **Reset**: Ability to clear or reset record data.

### 3. User Management (Admin)
*   **CRUD Operations**: Full Create, Read, Update, Delete functionality for system users.
*   **Authentication**: Secure Login and Register system.

## Technology Stack

### Backend
*   **Framework**: [Laravel 12.x](https://laravel.com)
*   **Language**: PHP ^8.2
*   **Database**: SQLite (Default)
*   **Image Processing**: `intervention/image`
*   **Excel Export**: `phpoffice/phpspreadsheet`

### Frontend
*   **Build Tool**: [Vite](https://vitejs.dev)
*   **Styling**: [Tailwind CSS v4.0](https://tailwindcss.com)
*   **OCR Engine**:
    *   `@paddle-js-models/ocr`
    *   `@arkntools/paddlejs-ocr`
    *   `@paddlejs/paddlejs-core`

## Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd iseki_chadet
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install Node Dependencies**
    ```bash
    npm install
    ```
    *Note: This will install the necessary PaddleJS OCR models and libraries.*

4.  **Environment Configuration**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Configure your database and app settings.

5.  **Key Generation & Migration**
    ```bash
    php artisan key:generate
    php artisan migrate
    ```

6.  **Build Frontend Assets**
    ```bash
    npm run build
    ```

7.  **Run Development Server**
    ```bash
    php artisan serve
    ```
    The application will be accessible at `http://localhost:8000`.

## Usage Guide

1.  **Login/Register**: Access the application by logging in or creating a new account.
2.  **Detect**: Navigate to the "Detect" page to upload an image. The system will process the image using the integrated OCR engine.
3.  **Records**: View the history of detections in the "Record" section. Admins can approve or export these records.
4.  **User Management**: Admin users can manage other users via the "User" menu.

## License

This project is proprietary.
