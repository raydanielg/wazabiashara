# Wazabiashara Mobile App

> **Biashara Yako, Mkononi Mwako** — Your Business, In Your Hands

Flutter mobile application for the Wazabiashara SaaS business management platform, built for Tanzanian businesses.

## Project Structure

```
mobile/
├── android/              # Android-specific configuration
├── ios/                  # iOS-specific configuration
├── lib/
│   ├── config/           # App configuration (API URLs, constants)
│   ├── theme/            # App theme & colors (light/dark)
│   ├── routes/           # Route definitions & navigation
│   ├── models/           # Data models (User, Business, Product, Sale, Customer)
│   ├── services/         # API, Storage, Translation services
│   ├── providers/        # State management (Auth, Theme)
│   ├── screens/          # UI screens
│   │   ├── auth/         # Login, Register, OTP
│   │   ├── dashboard/    # Main dashboard with bottom nav
│   │   ├── pos/          # Point of Sale
│   │   ├── products/     # Product management
│   │   ├── sales/        # Sales history
│   │   ├── customers/    # Customer management
│   │   ├── reports/      # Reports & analytics
│   │   └── settings/     # App settings
│   ├── widgets/          # Reusable widgets (StatCard, CustomButton, etc.)
│   ├── utils/            # Utilities (formatting, toasts)
│   └── main.dart         # App entry point
├── assets/
│   ├── images/           # Logo, branding images
│   ├── icons/            # SVG icons
│   ├── fonts/            # Custom fonts (Nunito)
│   ├── lottie/           # Lottie animations
│   └── translations/     # i18n JSON files (en.json, sw.json)
└── pubspec.yaml          # Flutter dependencies
```

## Key Features

- **Bilingual** — English & Swahili with runtime language switching
- **Dark Mode** — Full dark theme support
- **Offline-Ready** — Hive local storage + sync queue architecture
- **Secure** — Secure storage for tokens, biometric auth support
- **Modern UI** — Material 3 design with Wazabiashara brand colors

## Brand Colors

| Color  | Hex       | Usage            |
|--------|-----------|------------------|
| Primary| `#024938` | Main brand       |
| Gold   | `#D4A437` | Accent / CTA     |
| Success| `#10B981` | Positive actions |
| Error  | `#EF4444` | Destructive      |

## Getting Started

```bash
cd mobile
flutter pub get
flutter run
```

## Dependencies

- **State Management:** Provider
- **Networking:** Dio
- **Local Storage:** Hive, SharedPreferences, FlutterSecureStorage
- **UI:** Google Fonts, Flutter SVG, Cached Network Image, Shimmer, FL Chart
- **Utilities:** Intl, URL Launcher, Image Picker, Local Auth
