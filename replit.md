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

## Recent Updates (October 2025)

### Sistema de Imagens para Serviços (October 13, 2025)
- **Implementação Completa**: Sistema de imagens para categorias de serviços com upload seguro
- **Banco de Dados**: Adicionada coluna `image_url VARCHAR(500)` à tabela services
- **Imagens Stock**: 3 imagens profissionais salvas em `assets/images/services/`
  - Consulta Inicial: Consultório acolhedor de terapia
  - Sessão de Acompanhamento: Pessoa em sessão de terapia
  - Terapia de Casal: Aconselhamento para casais
- **Página de Serviços** (pages/services.php):
  - Cards com imagens em destaque (250px altura, object-fit: cover)
  - Efeito hover com zoom suave nas imagens
  - Layout responsivo com .service-image e .service-content
- **Painel Admin** (admin/services.php):
  - **Upload Seguro de Imagens**: Input file para upload direto do computador
  - Validação MIME real com finfo (impede upload de arquivos maliciosos)
  - Formatos aceitos: JPG, PNG, WebP | Tamanho máximo: 5MB
  - Preview instantâneo da imagem selecionada
  - Thumbnail 80x60px na listagem de serviços
  - Placeholder visual quando não há imagem
  - Segurança: Extensão derivada do MIME validado, nome único gerado
- **Benefício**: Interface visual atraente + upload seguro e fácil de usar

### Redirecionamento Automático para Painel Admin (October 13, 2025)
- **Problema Resolvido**: Erro 404 ao acessar `/admin/` diretamente
- **Solução Implementada**: Criado arquivo `admin/index.php` com redirecionamento automático
- **URLs Funcionais**:
  - `/admin/` → redireciona automaticamente para `/?page=admin`
  - `/?page=admin` → acesso direto ao painel
  - `/index.php?page=admin` → acesso direto ao painel
- **Benefício**: Acesso mais intuitivo ao painel administrativo

### Página "Agendar" Mobile-First Otimizada (October 10, 2025)
- **Problema Resolvido**: Corrigida responsividade da página calendar.php para mobile Android 15
- **HTML Limpo**: Removidos TODOS os inline styles, substituídos por classes CSS semânticas e reutilizáveis
- **Novas Classes CSS**:
  - Progress Indicator: `.progress-steps`, `.progress-step`, `.progress-number`, `.progress-label`
  - Calendar: `.calendar-month-year`, `.calendar-weekday`, `.calendar-grid-2col`
  - Cards: `.card-header`, `.card-title`, `.card-title-time`, `.card-description`, `.card-active`
  - Estados: `.success-box`, `.warning-box`, `.error-box`, `.empty-state-box`
  - Time periods: `.time-period-title`
  - Legend: `.legend-color-available`, `.legend-color-free`, `.legend-color-unavailable`
  - Date selection: `.selected-date-box`
- **Responsividade Completa**:
  - **Desktop (>768px)**: Layout 2 colunas (calendário | horários), elementos com tamanhos padrão
  - **Tablet (≤768px)**: Layout 1 coluna, fontes reduzidas, boxes ajustados
  - **Mobile (≤480px)**: Elementos compactos, touch targets adequados (34-45px), fontes legíveis (0.7-1rem)
- **Media Queries**: Implementadas para 768px (tablet) e 480px (mobile) com ajustes específicos
- **Benefícios**: Interface otimizada para mobile, código limpo e manutenível, performance melhorada

### Layout Centralizado Responsivo (October 10, 2025)
- **Desktop Layout**: Implementado layout com largura máxima (1200px) centralizado horizontalmente
- **Estrutura Corrigida**: Adicionado `<div class="container">` em header, main e footer de todas as páginas
- **Páginas Atualizadas**:
  - Cliente: home.php, calendar.php, booking.php, confirmation.php, services.php, google_meet_tutorial.php
  - Admin: dashboard.php
- **Responsividade**: Layout se adapta automaticamente - centralizado em desktop (>1200px), largura total em mobile
- **CSS Mantido**: Utiliza classe `.container` existente (max-width: 1200px, margin: 0 auto, padding: 0 20px)
- **Benefícios**: Melhor leitura em telas grandes, conteúdo não esticado na largura total da tela