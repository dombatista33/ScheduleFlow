# Terapia e Bem Estar - Online Therapy Appointment System

## Overview

This is a web-based appointment scheduling system for "Terapia e Bem Estar" (terapiaebemestar.com.br), designed for Dr. Daniela Lima, an online psychologist specializing in Cognitive Behavioral Therapy. The application provides a complete booking system where clients can view available appointment slots, schedule sessions, and manage their bookings. The system includes both client-facing functionality and an administrative panel for the therapist to manage appointments, availability, and client information.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Technology Stack**: HTML, CSS, JavaScript with mobile-first responsive design
- **Design Philosophy**: Minimalist, calming aesthetic using neutral colors (sage green, light blue, beige, off-white)
- **User Interface**: Clean, accessible forms with real-time validation and Brazilian phone number formatting
- **Responsive Design**: Mobile-first approach ensuring optimal experience across all devices

### Backend Architecture
- **Server Technology**: PHP for server-side processing and business logic
- **Database**: PostgreSQL (Replit-hosted) for data persistence
- **Session Management**: Standard PHP sessions for user state management
- **Form Processing**: Server-side validation and sanitization of user inputs
- **Automated Tasks**: Daily cron-like workflow for sending appointment reminders via email

### Data Storage Solutions
- **Primary Database**: PostgreSQL (Replit-hosted) with environment variable DATABASE_URL
- **Database Schema**: Five main tables:
  - `admin_users`: Administrator accounts with password hashing
  - `appointments`: Scheduled therapy sessions with client and service references
  - `clients`: Client information including contact details
  - `services`: Available therapy services with pricing and duration
  - `time_slots`: Available appointment slots by date and time
- **Data Security**: Password hashing with bcrypt, secure credential management via environment variables

### Authentication and Authorization
- **Admin Access**: Secure login system for Dr. Daniela Lima to access administrative panel
- **Client Sessions**: Session-based tracking for appointment booking process
- **Role-based Access**: Separation between client-facing features and administrative functions

### Core Features
- **Appointment Scheduling**: Interactive calendar system showing available time slots
- **Service Management**: Multiple therapy services (Initial Consultation, Follow-up Sessions, Couples Therapy)
- **Client Management**: Complete client information collection and storage
- **Administrative Panel**: Backend interface for managing appointments, availability, and client data
- **Confirmation System**: Automated booking confirmation with payment and virtual meeting room information
- **Email Reminders**: Automated email reminder system that sends notifications 24 hours before appointments
- **Google Meet Integration**: Complete tutorials for both clients and therapist on how to access online consultations via Google Meet on mobile devices

## External Dependencies

### Database Connection
- **MySQL Server**: Remote database hosting with connection credentials
- **Database Name**: `terapiae_terapia`
- **Database User**: `terapiae_terapia` with appropriate permissions

### Web Hosting
- **Domain**: terapiaebemestar.com.br
- **Hosting Environment**: PHP-enabled web server with MySQL database support

## Recent Updates (October 2025)

### Email System Configuration with Production SMTP (October 8, 2025)
- **SMTP Server Configuration**: Configured production SMTP server (srv96.prodns.com.br) with port 465 for secure email delivery
- **SSL/TLS Implementation**: Implemented implicit SSL/TLS encryption using port 465 with proper certificate verification
- **Anti-Spam Headers**: Added comprehensive email headers to prevent spam filtering:
  - Message-ID generation with unique timestamps
  - Return-Path configuration
  - Proper Date headers in RFC format
  - Content-Transfer-Encoding optimization
- **Authentication System**: Configured AUTH LOGIN with base64 encoding for secure SMTP authentication
- **Fallback Mechanism**: Implemented fallback SMTP connection for certificate verification issues
- **Email Logging**: Enhanced logging system to track successful and failed email deliveries
- **Environment Variables**: Secure credential management using Replit Secrets:
  - SMTP_HOST: srv96.prodns.com.br
  - SMTP_PORT: 465
  - EMAIL_USERNAME: contato@terapiaebemestar.com.br
  - EMAIL_PASSWORD: (stored securely)
- **Reminder Email Optimization** (October 8, 2025):
  - **Subject Line**: Changed from "Lembrete: Sua consulta é amanhã" to "Sua sessão de amanhã (DD/MM/AAAA) - Dra. Daniela Lima" to avoid spam filters
  - **Email Content**: Removed emojis and generic "reminder" language, replaced with professional "Confirmação da Sua Sessão" template
  - **Professional Template**: Added therapist signature, CRP number, and structured appointment details
  - **Layout**: Corporate-style design matching confirmation emails for consistency
- **Files Updated**:
  - `includes/email_system.php` - Complete SMTP implementation with SSL/TLS and anti-spam headers, optimized reminder template

### Booking Instructions Optimization (October 8, 2025)
- **Step-by-Step Instructions**: Redesigned booking flow with clear numbered steps and instructions
- **Date Selection Section**: 
  - Title: "1. Data da Sessão"
  - Instruction: "Selecione a Data Disponível"
  - Description: Clear explanation about calendar availability
  - Warning: Alert about inactive dates without available slots
- **Time Selection Section**:
  - Title: "2. Horário da Sessão"
  - Instruction: "Escolha um Horário Livre"
  - Guidance: Clear explanation about occupied time slots
  - Note: Information about unavailable but visible time slots
- **Files Updated**:
  - `pages/calendar.php` - Enhanced booking instructions with numbered steps

### Fully Responsive Administrative Panel (October 5, 2025)
- **Complete Mobile Responsiveness**: All administrative pages now fully responsive for desktop (>1024px), tablet (768-1024px), and mobile (<768px) devices
- **Hamburger Menu Navigation**: Mobile-friendly navigation with toggle button that shows/hides admin menu on small screens
- **Touch-Friendly Interface**: All inputs, buttons, and selects have minimum 44px height and 16px font size to prevent zoom on iOS and ensure easy touch interaction
- **Responsive Tables**: All data tables convert to card layout on mobile using data-label attributes for optimal mobile viewing
- **Mobile-Optimized Forms**: Form fields stack vertically on mobile with full-width buttons and proper spacing
- **Responsive Calendar**: Weekly calendar view uses horizontal scroll on mobile devices for easy date navigation
- **CSS Architecture**: Mobile-first approach with breakpoints at 768px and 1024px using media queries with !important flags to override inline styles
- **Files Updated**: 
  - `assets/css/style.css` - Complete responsive CSS framework
  - `admin/dashboard.php` - Hamburger menu and responsive header
  - `admin/appointments.php` - Responsive tables and forms
  - `admin/clients.php` - Responsive tables and forms
  - `admin/services.php` - Responsive tables and forms
  - `admin/calendar_manage.php` - Responsive calendar grid
  - `admin/admin_users.php` - Responsive tables and forms

### Tutorial System for Google Meet
- **Client Tutorial**: Created comprehensive step-by-step guide at `/index.php?page=google_meet_tutorial` teaching clients how to download Google Meet app, join consultations, and prepare for online sessions
- **Professional Tutorial**: Created detailed guide at `/admin/google_meet_guide.php` for the therapist, covering how to create meetings, share links, manage sessions, and troubleshoot common issues on mobile devices
- **Menu Integration**: Added "Primeira Consulta" button to all client-facing pages for easy access to the tutorial
- **Admin Access**: Added highlighted tutorial button in the administrative dashboard for quick access by the therapist

### Frontend Libraries
- **CSS Framework**: Custom CSS with CSS Grid and Flexbox for responsive layouts
- **JavaScript**: Vanilla JavaScript for form validation, phone formatting, and calendar functionality
- **Font System**: System fonts (Segoe UI, Tahoma, Geneva, Verdana) for optimal performance

### Communication Features
- **WhatsApp Integration**: Phone number collection formatted for WhatsApp communication
- **Email System**: SMTP-based email system for confirmations and automated 24-hour appointment reminders
- **Virtual Meeting**: Google Meet platform for online therapy sessions with comprehensive tutorials
- **Tutorial Pages**: 
  - Client tutorial (`/index.php?page=google_meet_tutorial`) - Step-by-step guide for clients to join consultations via mobile
  - Professional tutorial (`/admin/google_meet_guide.php`) - Complete guide for therapist to create and manage Google Meet sessions on mobile

### Security Considerations
- **Input Validation**: Both client-side and server-side validation for all forms
- **Data Sanitization**: Protection against SQL injection and XSS attacks
- **Secure Authentication**: Protected administrative access with secure credential management