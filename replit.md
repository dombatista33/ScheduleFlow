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

### Complete UX/Copywriting Optimization of Booking Flow (October 9, 2025)
- **Complete 3-Step Flow Redesign**: Restructured entire booking experience with UX/copywriting best practices
- **Mobile-First Approach**: All pages optimized for mobile devices first, then scaled up for desktop/tablet
- **Welcoming Copywriting**: Direct, reassuring language throughout the entire journey
- **Visual Feedback System**: 
  - Green highlights for available dates
  - Blue highlights for available time slots
  - Gray for unavailable options
  - Celebratory confirmations with checkmarks and success colors

**Step 1 - Calendar (Find Your Time):**
- Title: "Encontre Seu Horário" (welcoming and direct)
- Subtitle: "Choose the date and time that work best for you. It's quick and simple."
- Clear color-coded instructions with visual highlights (green = available, gray = no slots)
- Celebratory selection confirmation with checkmark icon and "Perfeito!" message
- Improved empty state message: "Ops! Esta data está cheia"

**Step 2 - Booking (Your Information):**
- Title: "Seus Dados" with subtitle "Takes less than 2 minutes"
- Simplified form with only 4 essential fields
- Completely redesigned payment section:
  - Visual cards for Pix (recommended) and Bank Transfer options
  - Clear explanation of how each payment method works
  - **24-Hour Rule HIGHLIGHTED** in yellow box with clock icon
  - Professional gradient designs for each payment option
- Reserved time slot confirmation card showing selected date/time

**Step 3 - Confirmation (All Set):**
- Celebratory header with large checkmark icon and "Tudo Confirmado!"
- Redesigned appointment summary with icon-based layout
- **Clear Next Steps** numbered 1-2-3:
  1. Payment within 24 hours (HIGHLIGHTED in yellow with payment options)
  2. Wait for Google Meet link (24 hours before session)
  3. Prepare for session (with link to tutorial)
- Simplified important information section with icons
- Clean, single-button footer

- **Files Updated**:
  - `pages/calendar.php` - Complete UX/copywriting optimization
  - `pages/booking.php` - Redesigned payment section and form
  - `pages/confirmation.php` - Celebratory confirmation with clear next steps

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