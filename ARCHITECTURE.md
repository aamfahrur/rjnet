# 🏗️ RT/RW Net Management System - Architecture Documentation

## 📁 Project Structure

```
rjnet/
├── app/
│   ├── Actions/                         # Single-purpose action classes
│   ├── Console/Kernel.php               # Task scheduler (Billing, Monitoring, Cleanup)
│   ├── DTOs/                            # Data Transfer Objects (Spatie Laravel Data)
│   ├── Enums/                           # PHP 8.2+ Enums (10 enums)
│   │   ├── ConnectionType.php           # PPPoE, Hotspot, Static IP, DHCP
│   │   ├── CustomerStatus.php           # Active, Suspended, Isolated, Terminated
│   │   ├── InvoiceStatus.php            # Pending, Paid, Partial, Overdue, Cancelled
│   │   ├── NotificationChannel.php      # Email, Telegram, WhatsApp, System
│   │   ├── NodeType.php                 # Router, Switch, OLT, ODP, ONU, POP
│   │   ├── PaymentGateway.php           # iPaymu, Duitku, Midtrans
│   │   ├── PaymentMethod.php            # Bank Transfer, VA, E-Wallet, QRIS
│   │   ├── RouterStatus.php             # Online, Offline, Maintenance, Error
│   │   ├── TicketPriority.php           # Low, Medium, High, Critical (SLA)
│   │   └── TicketStatus.php             # Open, InProgress, WaitingCustomer, Resolved
│   ├── Events/                          # Event-driven architecture (6 events)
│   ├── Exceptions/                      # Custom exceptions (MikrotikConnectionException)
│   ├── Helpers/                         # Global helper functions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                   # Admin panel controllers (8 controllers)
│   │   │   ├── API/                     # REST API controllers
│   │   │   ├── Auth/                    # Authentication controllers
│   │   │   └── Customer/                # Customer panel controllers
│   │   ├── Middleware/                  # Custom middleware
│   │   ├── Requests/                    # Form Request Validation
│   │   └── Resources/                   # API Resources (JSON transformation)
│   ├── Jobs/                            # Queue Jobs (6 jobs)
│   │   ├── AutoSuspendOverdueCustomers.php
│   │   ├── CollectRouterMetrics.php
│   │   ├── GenerateMonthlyInvoices.php
│   │   ├── SendBillingReminders.php
│   │   ├── SendTelegramNotification.php
│   │   └── SyncPPPoEToMikrotik.php
│   ├── Listeners/                       # Event Listeners
│   ├── Models/                          # Eloquent Models (25 models)
│   ├── Notifications/                   # Notification classes
│   ├── Observers/                       # Model Observers
│   ├── Policies/                        # Authorization Policies
│   ├── Repositories/                    # Repository Pattern (BaseRepository)
│   ├── Services/                        # Business Logic Layer
│   │   ├── Mikrotik/                    # Mikrotik integration services
│   │   │   ├── BaseMikrotikService.php  # Abstract base (connect, query, logging)
│   │   │   ├── PPPoEService.php         # PPPoE management
│   │   │   ├── HotspotService.php       # Hotspot management
│   │   │   ├── QueueService.php         # Queue/Bandwidth management
│   │   │   ├── RouterMonitoringService.php # Health monitoring
│   │   │   └── MikrotikServiceFactory.php  # Service factory
│   │   ├── Billing/
│   │   │   └── BillingService.php       # Invoice generation, reminders, auto-suspend
│   │   ├── Payment/
│   │   │   ├── PaymentGatewayInterface.php  # Strategy interface
│   │   │   ├── PaymentService.php       # Payment orchestrator
│   │   │   └── Drivers/
│   │   │       ├── IPaymuDriver.php     # iPaymu implementation
│   │   │       ├── DuitkuDriver.php     # Duitku implementation
│   │   │       └── MidtransDriver.php   # Midtrans implementation
│   │   ├── Customer/
│   │   │   └── CustomerService.php      # Customer lifecycle management
│   │   ├── Ticket/
│   │   │   └── TicketService.php        # Ticket/SLA management
│   │   ├── Notification/                # Notification service
│   │   ├── Topology/                    # Network topology service
│   │   └── Monitoring/                  # NOC monitoring service
│   ├── Traits/                          # Reusable traits
│   │   ├── HasTransaction.php           # DB transaction wrapper
│   │   ├── Loggable.php                 # Standardized logging
│   │   └── Cacheable.php                # Cache helpers
│   └── ValueObjects/                    # Immutable domain objects
│       ├── Money.php                    # Currency-safe money handling
│       ├── NetworkSpeed.php             # Speed unit conversion
│       ├── GeoCoordinate.php            # GPS with distance calculation
│       ├── MacAddress.php               # MAC address normalization
│       └── IPv4Address.php              # IP validation & subnet check
├── config/
│   ├── rjnet.php                        # Application-specific config
│   └── services.php                     # Third-party credentials
├── database/
│   ├── migrations/                      # 12 migration files
│   └── seeders/DatabaseSeeder.php       # Roles, permissions, default data
├── resources/
│   └── react-tailwindcss/               # Trezo Admin React Frontend
└── routes/
    ├── web.php                          # Web routes (Inertia)
    ├── api.php                          # REST API (mobile, callback)
    └── console.php                      # Artisan commands
```

## 🗄️ Database Schema (15 Tables)

```
users                    ─── Spatie Permission (roles, permissions, pivot tables)
├── customers            ─── Customer data, status, documents
│   ├── customer_addresses ─── GPS coordinates, address hierarchy
│   └── customer_documents ─── KTP, contracts, photos
├── router_groups        ─── Router grouping by location
├── routers              ─── Mikrotik router configs, status
│   └── router_logs      ─── API command audit trail
├── internet_packages    ─── Package definitions with QoS
├── subscriptions        ─── Customer ↔ Package ↔ Router
├── pppoe_accounts       ─── PPPoE credentials per router
├── hotspot_accounts     ─── Hotspot credentials per router
├── invoices             ─── Billing records
│   └── invoice_items    ─── Line items
├── payments             ─── Payment transactions
│   ├── payment_gateways ─── Gateway configurations
│   └── payment_logs     ─── Transaction logs
├── tickets              ─── Support tickets with SLA
│   ├── ticket_replies   ─── Threaded replies
│   └── ticket_attachments ─── File uploads
├── network_nodes        ─── Topology nodes (React Flow)
├── network_links        ─── Topology edges
├── network_events       ─── NOC alerts
├── traffic_logs         ─── Bandwidth utilization
├── router_metrics       ─── CPU, RAM, disk, sessions
├── online_sessions      ─── PPPoE/Hotspot online users
├── notifications        ─── User notifications
├── billing_reminders    ─── Reminder tracking
├── login_histories      ─── Security audit
└── scheduled_jobs       ─── Job tracking
```

## 🔄 Design Patterns Used

| Pattern | Where | Purpose |
|---------|-------|---------|
| **Repository** | `BaseRepository` | Data access abstraction |
| **Strategy** | `PaymentGatewayInterface` + Drivers | Multi-payment gateway |
| **Factory** | `MikrotikServiceFactory` | Centralized service creation |
| **Service Layer** | All `Services/` classes | Business logic isolation |
| **DTO** | `Spatie Laravel Data` | Type-safe data transfer |
| **Value Object** | `Money`, `NetworkSpeed`, etc. | Immutable domain concepts |
| **Observer** | Model Observers | Model lifecycle hooks |
| **Event-Driven** | Events + Listeners + Jobs | Decoupled async processing |
| **Thin Controllers** | All controllers | No business logic in controllers |
| **Form Request** | `StoreCustomerRequest`, etc. | Validated input contracts |
| **API Resource** | `CustomerResource` | Consistent JSON shapes |

## ⏰ Scheduler Cron Jobs

```
00:05  → Generate Monthly Invoices
00:10  → Mark Overdue Invoices
00:15  → Auto-Suspend Overdue Customers (7-day grace)
06:00  → Auto-Unsuspend Paid Customers
07:00  → Send Reminders (H-7, H-3, H-1)
*/5    → Collect Router Metrics (CPU, RAM, traffic)
Daily  → Clean activity logs, prune sessions
```

## 🚀 Getting Started

1. `composer install`
2. `cp .env.example .env` → Configure database, Redis, payment gateways
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan db:seed` → Creates roles, admin user, packages
6. `php artisan reverb:start` → WebSocket server
7. `php artisan horizon` → Queue worker dashboard
8. `npm --prefix resources/react-tailwindcss install && npm --prefix resources/react-tailwindcss run dev`

### Default Credentials
- **Admin:** admin@rjnet.id / password
- **Teknisi:** teknisi@rjnet.id / password
