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