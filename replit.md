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
- **Database**: MySQL with dedicated database `terapiae_terapia` for data persistence
- **Session Management**: Standard PHP sessions for user state management
- **Form Processing**: Server-side validation and sanitization of user inputs

### Data Storage Solutions
- **Primary Database**: MySQL database named `terapiae_terapia`
- **Database Schema**: Designed to handle appointments, client information, service types, and availability schedules
- **Data Security**: Secure credential management with dedicated database user access

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

## External Dependencies

### Database Connection
- **MySQL Server**: Remote database hosting with connection credentials
- **Database Name**: `terapiae_terapia`
- **Database User**: `terapiae_terapia` with appropriate permissions

### Web Hosting
- **Domain**: terapiaebemestar.com.br
- **Hosting Environment**: PHP-enabled web server with MySQL database support

### Frontend Libraries
- **CSS Framework**: Custom CSS with CSS Grid and Flexbox for responsive layouts
- **JavaScript**: Vanilla JavaScript for form validation, phone formatting, and calendar functionality
- **Font System**: System fonts (Segoe UI, Tahoma, Geneva, Verdana) for optimal performance

### Communication Features
- **WhatsApp Integration**: Phone number collection formatted for WhatsApp communication
- **Email System**: Contact form and appointment confirmation via email
- **Virtual Meeting**: Integration capability for online therapy session links

### Security Considerations
- **Input Validation**: Both client-side and server-side validation for all forms
- **Data Sanitization**: Protection against SQL injection and XSS attacks
- **Secure Authentication**: Protected administrative access with secure credential management