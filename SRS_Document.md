# Software Requirements Specification (SRS)
# AI-Powered Travel Management Platform

**Version:** 2.0  
**Date:** May 2026  
**Prepared by:** Project Development Team  
**Document Type:** Software Requirements Specification (SRS)

---

## Executive Summary

> **Project Name: AI-Powered Travel Management Platform**  
> *"One platform to plan, book, and explore the world."*

**AI-Powered Travel Management Platform** is a full-stack, intelligent web application that transforms the way travellers discover destinations, plan itineraries, and manage their end-to-end travel experience. The system combines an AI Trip Planner, real-time booking engine, guide management, expense tracker, AI chatbot, and a comprehensive admin control panel into one unified platform.

The platform serves three distinct user personas — **Travellers**, **Guides**, and **Administrators** — and is built on a modern, scalable tech stack powered by Laravel, MongoDB, Razorpay, and integrated AI services.

---

## 1. Introduction

### 1.1 Purpose
This document defines the complete Software Requirements for AI-Powered Travel Management Platform. It covers all functional modules, system architecture, data models, security policies, and non-functional requirements that form the foundation of the platform. It is intended for use by the development team, project evaluators, and academic reviewers.

### 1.2 Project Name & Branding

| Attribute | Details |
|---|---|
| **Official Name** | AI-Powered Travel Management Platform |
| **Short Name / Brand** | TravelMate |
| **Tagline** | *One platform to plan, book, and explore the world.* |
| **Platform Type** | AI-Powered Smart Travel & Booking Web Application |
| **Domain** | Travel Technology (TravelTech) |
| **Category** | SaaS / Consumer Web Platform |

> **Why "AI-Powered Travel Management Platform"?**  
> This name clearly communicates what the system does in full — an **Artificial Intelligence driven platform** covering the **complete lifecycle of travel management**: planning, booking, guiding, expense tracking, and administration. It is ideal for college project reports, SRS submissions, viva presentations, and professional demos.

### 1.3 Scope
The AI-Powered Travel Management Platform is a web-based application built using the Laravel framework (PHP 8.2+). It integrates Razorpay for payment processing, Google OAuth for social login, and AI-driven services for trip planning and chat assistance. The platform caters to:
- General users (travellers) seeking to book and plan trips
- Local travel guides seeking to register and receive bookings
- Platform administrators managing content, users, bookings, and guide approvals

### 1.4 Definitions & Acronyms

| Term | Definition |
|---|---|
| **SRS** | Software Requirements Specification |
| **AI** | Artificial Intelligence |
| **RBAC** | Role-Based Access Control |
| **MVC** | Model-View-Controller (architecture pattern) |
| **API** | Application Programming Interface |
| **OTP** | One-Time Password |
| **SaaS** | Software as a Service |
| **MVP** | Minimum Viable Product |

---

## 2. Overall Description

### 2.1 Product Perspective
VoyageIQ operates as a standalone web platform accessible via any modern web browser. It does not require client-side installation. The system follows a monolithic MVC architecture (Laravel) with RESTful API endpoints for AJAX interactions and third-party integrations.

### 2.2 User Classes and Roles

The system supports **three primary user roles**:

#### 🧳 Role 1: Traveller (Customer)
- Self-registers via Email/Password or Google Social Login
- Browses destinations, packages, and hotels
- Creates, manages, and pays for bookings
- Uses the AI Trip Planner (free estimate; premium AI generation via one-time payment of ₹99)
- Manages travel expenses and itineraries
- Maintains a wishlist of favourite destinations and packages
- Submits reviews and ratings
- Interacts with the AI Chatbot for travel assistance
- Views transaction history and booking confirmations
- Earns loyalty points with a tiered reward system (Bronze → Silver → Gold → Diamond)

#### 🧭 Role 2: Guide (Travel Guide / Local Expert)
- Applies to become a guide during signup by entering a **secret registration key** provided by the admin
- Application is placed in **pending** status until admin reviews it
- Upon admin **approval**, the guide can log in and access the Guide Dashboard
- Guides **cannot** log in while their application is pending or rejected
- The Guide Dashboard displays:
  - Profile summary (specialty, experience, phone)
  - All bookings assigned to them by admin
  - Upcoming trips (sorted by check-in date)
  - Trip completion statistics

#### 🛡️ Role 3: Administrator (Admin / Super Admin)
- Full access to the Admin Dashboard
- Manages users (view, activate/deactivate accounts)
- Manages all bookings (view, process refunds, assign guides)
- Manages content (destinations, packages — add, edit, toggle status, update pricing)
- Handles support tickets (view and send replies)
- Moderates reviews (flag inappropriate content)
- **Reviews and approves/rejects guide applications**
- Views platform-wide analytics and revenue charts

---

## 3. System Features (Functional Requirements)

### 3.1 Authentication & Authorization Module
| Feature | Description |
|---|---|
| Email/Password Registration | Standard signup with password strength meter and confirmation |
| Google OAuth Login | One-click login using Google account via Laravel Socialite |
| Role Selection on Signup | Users choose between "Traveller" or "Guide" during registration |
| Guide Secret Key Validation | Guide applicants must enter the admin-issued key `(hidden)` to apply |
| Guide Approval Workflow | Guide accounts start as `pending`; admin approves/rejects before login is allowed |
| Login Redirect Logic | Admins → Admin Dashboard; Approved Guides → Guide Dashboard; Users → User Dashboard |
| Session Management | Laravel Breeze handles secure session generation and token regeneration |

### 3.2 AI Trip Planner & Itinerary Management
| Feature | Description |
|---|---|
| Free Cost Estimator | Public calculator that estimates trip cost without registration |
| AI Itinerary Generator | Logged-in users fill a form (origin, destination, days, budget, interests) and the AI generates a full day-by-day itinerary |
| One-Time Premium Unlock | First itinerary plan costs ₹99; all future generations are permanently free for that user |
| Package/Destination Free AI | Users who book a package or destination get a complimentary AI Trip Planner session |
| Itinerary Management | View, edit, replan, and delete saved itineraries |
| PDF Export | Download a formatted PDF of any paid/unlocked itinerary |
| Public Sharing | Share itineraries via unique public links |

### 3.3 Destination & Package Management
| Feature | Description |
|---|---|
| Destination Catalogue | Browse filterable list of destinations with images, descriptions, and categories |
| Package Catalogue | Browse curated travel packages with pricing, duration, and includes |
| Detail Pages | Individual pages for each destination/package with reviews, ratings, and booking option |
| Wishlist | Save destinations or packages with a single click for later reference |

### 3.4 Booking & Payment System
| Feature | Description |
|---|---|
| Book Destinations | Users can directly book a destination (type: `destination`) |
| Book Packages | Users can book a travel package (type: `package`) |
| Book Hotels | After a booking, users can browse and book recommended hotels |
| Guide Booking | Option to add a local guide to any booking |
| Promo Code Support | Apply promotional discount codes at checkout |
| Razorpay Integration | Secure online payment gateway; card details never stored on server |
| Booking Confirmation | Unique booking reference generated; confirmation page and PDF |
| Booking Cancellation | Users can cancel eligible bookings |
| Admin Refund | Admins can process refunds on cancelled bookings |

### 3.5 My Bookings Page (Separated Display)
| Section | What It Shows |
|---|---|
| Premium Plans | AI itinerary plans purchased; includes a "View Plan" button |
| Packages & Destinations | Regular travel bookings; includes a free "AI Trip Planner" button for that destination |

### 3.6 Expense Tracker
- Users can log and track spending tied to specific itineraries
- Dashboard shows real-time spending vs. budget with progress bars

### 3.7 AI Chatbot
- Integrated conversational chatbot interface
- Users send messages and receive AI-driven travel assistance
- Chat history stored per user session

### 3.8 User Dashboard Analytics
The user dashboard displays four key metric cards:
1. **Premium Plans Bought** — total AI plans purchased
2. **Total Spent** — sum of all paid bookings and expenses
3. **Packages Bought** — total packages/destinations booked
4. **Upcoming Trip** — name and date of next scheduled booking

A pie chart shows **Expenditure Breakdown** (Tour Package Amount / Packages Bought / Premium Plans Bought) and a line chart shows **Activity Over Time** (monthly spending).

### 3.9 Social & Loyalty Features
| Feature | Description |
|---|---|
| Reviews & Ratings | 5-star ratings with written reviews on destinations/hotels; visible on admin dashboard in table format |
| Helpful Votes | Users can mark others' reviews as helpful |
| Notifications | Travel notifications displayed in dashboard and notification bell |
| Loyalty Points | Points earned per booking; tiered levels: Bronze → Silver → Gold → Diamond |
| Wishlist | Saved destinations and packages accessible from profile |

### 3.10 Customer Support & Ticketing
- Contact form on the Contact Us page
- Admin panel for viewing and replying to support tickets

### 3.11 Contact Page — Meet The Team
The Contact page features a dedicated **"Meet The Builders"** section above the contact form, showcasing the development team with:
- Profile photo, name, role, and bio for each member
- Direct social profile links: **LinkedIn, Instagram, Twitter/X, Facebook**
- Hover animations and branded member accent colors

### 3.12 Direct Booking & Transit Hub
Integrated external partner links for:
- **Flight Booking** → MakeMyTrip
- **IRCTC Railway** → Indian Railways
- **Bus Services** → RedBus
- **Hotels** → Agoda / Booking.com
- **Cab / Taxi** → Rapido / Uber

### 3.13 Emergency SOS Module
- Fixed SOS button on the user dashboard (bottom-left corner)
- On activation, alerts authorities with user location and sends SOS

---

## 4. Database Entities (Data Model)

The system uses **MongoDB** as the primary database (via `mongodb/laravel-mongodb`).

| Model | Purpose |
|---|---|
| `User` | Core account: name, email, password, role (`user`/`guide`), guide_status, guide_specialty, guide_phone, guide_experience |
| `UserProfile` | Extended profile: avatar, bio, nationality, travel interests, loyalty points, loyalty level |
| `Destination` | Travel destinations: name, city, state, country, description, image, status, category |
| `Package` | Travel packages: title, description, price, duration, inclusions, destination reference |
| `Hotel` | Hotel listings: name, location, star rating, price per night |
| `Booking` | All bookings: user_id, package_id, booking_type (`user`/`guide`/`itinerary`/`destination`), payment_status, booking_status, guide_id, check_in, total_amount, razorpay IDs |
| `Transaction` | Payment records linked to bookings |
| `Itinerary` | AI-generated trip plans: user_id, destination_id, days array, budget, is_paid, preferences, status |
| `Expense` | Trip expenses: user_id, itinerary_id, amount, category, expense_date |
| `Review` | User reviews: user_id, reviewable_type/id, rating (1–5), body, is_flagged |
| `Wishlist` | Saved items: user_id, destination_id or package_id |
| `SupportTicket` | Support requests: subject, message, status, admin replies |
| `ChatMessage` | AI chatbot history: user_id, message, response |
| `Promotion` | Discount codes: code, discount_type, value, expiry |
| `TravelNotification` | Alerts per user: title, message, is_read |
| `LoyaltyPoint` | Points per transaction: user_id, points, reason |

---

## 5. System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        VoyageIQ Platform                        │
├──────────────┬──────────────────────────┬───────────────────────┤
│  Traveller   │        Guide             │     Admin Panel       │
│  Dashboard   │      Dashboard           │   (Super Dashboard)   │
├──────────────┴──────────────────────────┴───────────────────────┤
│                    Laravel MVC Application                       │
│  Controllers / Models / Blade Views / Middleware                │
├──────────────┬──────────────────────────┬───────────────────────┤
│   MongoDB    │     Razorpay API         │   Google OAuth API    │
│  (Database)  │   (Payments)             │   (Social Login)      │
├──────────────┼──────────────────────────┼───────────────────────┤
│ AI Services  │  OpenStreetMap / OpenMet │  DOMPDF (PDF Export) │
│ (Itinerary)  │  (Geolocation/Weather)   │                       │
└──────────────┴──────────────────────────┴───────────────────────┘
```

---

## 6. Technical Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 11 (PHP 8.2+) |
| **Frontend** | Blade Templates, Vanilla CSS, JavaScript (Vite) |
| **Database** | MongoDB (via `mongodb/laravel-mongodb`) |
| **Authentication** | Laravel Breeze + Laravel Socialite (Google OAuth) |
| **Payment Gateway** | Razorpay API |
| **PDF Generation** | DOMPDF (`barryvdh/laravel-dompdf`) |
| **Roles & Permissions** | Custom RBAC + Spatie Laravel Permission |
| **Charts** | Chart.js (Doughnut + Line charts) |
| **Fonts** | Google Fonts — Playfair Display, Outfit, Orbitron, Space Grotesk |
| **Icons** | Font Awesome 6 |
| **Maps/Geo** | OpenStreetMap Nominatim API |
| **Weather** | Open-Meteo API |

---

## 7. Non-Functional Requirements

| Category | Requirement |
|---|---|
| **Security** | Passwords hashed via bcrypt; sensitive routes protected by auth middleware; Razorpay payment handled server-side without storing card data; CSRF protection on all forms |
| **Usability** | Fully responsive interface (desktop & mobile); click-based navigation dropdowns; animated transitions and micro-interactions throughout |
| **Performance** | Destination data cached using Laravel Cache; optimised MongoDB queries with date-range filtering; chart data computed server-side |
| **Reliability** | MVC architecture ensures separation of concerns and maintainability; error logging via Laravel Log facade |
| **Scalability** | MongoDB's document-based schema allows flexible data growth; Laravel supports horizontal scaling |
| **Accessibility** | Semantic HTML elements; descriptive alt text; keyboard-navigable forms |

---

## 8. Guide Registration Workflow (Detailed)

```
User fills signup form
  └── Selects "Guide" role
      └── Enters Secret Key (admin-issued)
          ├── Key INVALID → Validation error shown, cannot proceed
          └── Key VALID →
              ├── Account created with guide_status = "pending"
              ├── User redirected to login page with success message
              └── Admin sees the new request in Admin → Guide Requests
                  ├── Admin clicks "Approve" → guide_status = "approved"
                  │   └── Guide can now log in → redirected to Guide Dashboard
                  └── Admin clicks "Reject" → guide_status = "rejected"
                      └── Guide login is blocked with error message
```

---

## 9. Key URLs & Routes

| Route | Access | Description |
|---|---|---|
| `/` | Public | Home page |
| `/destinations` | Public | Destinations catalogue |
| `/packages` | Public | Packages catalogue |
| `/contact` | Public | Contact page + Team section |
| `/register` | Public | Registration (Traveller or Guide) |
| `/login` | Public | Login |
| `/dashboard` | Traveller | User dashboard |
| `/bookings` | Traveller | My Bookings (separated sections) |
| `/itineraries/create` | Traveller | AI Trip Planner |
| `/guide/dashboard` | Approved Guide | Guide dashboard |
| `/admin/dashboard` | Admin | Admin overview |
| `/admin/guides` | Admin | Guide approval panel |
| `/admin/bookings` | Admin | Booking management |
| `/admin/reviews` | Admin | Review moderation |

---

*Document prepared for academic and professional submission. All features described reflect the current implemented state of the VoyageIQ platform as of May 2026.*
