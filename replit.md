# Terapia e Bem Estar - Online Therapy Appointment System

## Overview
"Terapia e Bem Estar" is a web-based appointment scheduling system for Dr. Daniela Lima, an online psychologist specializing in Cognitive Behavioral Therapy. The platform allows clients to view availability, book sessions, and manage appointments, while providing an administrative panel for the therapist to manage bookings, availability, and client information. The system aims to streamline the booking process for online therapy sessions and enhance client-therapist interaction.

## User Preferences
Preferred communication style: Simple, everyday language.

## System Architecture

### UI/UX Decisions
The design emphasizes a minimalist, calming aesthetic using a neutral color palette (sage green, light blue, beige, off-white). It employs a mobile-first responsive design approach, ensuring accessibility and optimal experience across all devices. The user interface features clean, accessible forms with real-time validation, Brazilian phone number formatting, and a clear, direct copywriting style throughout the booking flow.

### Technical Implementations
- **Frontend**: HTML, CSS (with CSS Grid and Flexbox for responsiveness), and vanilla JavaScript for dynamic elements. Uses system fonts for performance.
- **Backend**: PHP for server-side logic, processing, and session management.
- **Database**: PostgreSQL (Replit-hosted) for all data persistence, utilizing environment variables for secure connection.
- **Security**: Includes client-side and server-side input validation, data sanitization to prevent SQL injection and XSS, and secure authentication for administrative access with bcrypt password hashing.

### Feature Specifications
- **Appointment Scheduling**: Interactive calendar for clients to select and book available time slots.
- **Service Management**: Supports multiple therapy services with customizable pricing and duration.
- **Client Management**: Comprehensive client information collection and storage.
- **Administrative Panel**: A secure backend interface for managing appointments, client data, and availability.
- **Automated Confirmations & Reminders**: System sends automated booking confirmations and 24-hour appointment reminders via email.
- **Google Meet Integration**: Provides step-by-step tutorials for both clients and the therapist on using Google Meet for online consultations.
- **Booking Flow Redesign**: A 3-step mobile-first booking process with clear visual feedback, simplified forms, and redesigned payment options (Pix, Bank Transfer).

### System Design Choices
- **Responsive Layouts**: Implemented a centralized, responsive layout with a maximum width for desktop, adapting seamlessly to mobile devices.
- **Email System**: Robust SMTP-based email system with production-grade configuration (SSL/TLS, anti-spam headers) for reliable communication.
- **Session Management**: Standard PHP sessions are used for maintaining user state.

## External Dependencies

- **Database**: PostgreSQL (Replit-hosted) with connection credentials managed via environment variables.
- **Email Service**: SMTP server (srv96.prodns.com.br) for sending emails, configured with SSL/TLS and secure authentication.
- **Virtual Meeting Platform**: Google Meet for online therapy sessions.
- **Web Hosting**: PHP-enabled web server with database support (terapiaebemestar.com.br).