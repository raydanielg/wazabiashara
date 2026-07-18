# 🇹🇿 Wazabiashara — Landing Page

Landing page ya kisasa kwa **PHP + Tailwind + React + AJAX + SweetAlert2**.

## 📁 Muundo wa Faili
```
wazabiashara-landing/
├── index.php            ← Landing page nzima
├── api/
│   └── subscribe.php    ← Newsletter AJAX endpoint (JSON)
├── assets/
│   └── logo.png         ← WEKA LOGO YAKO HAPA
└── README.md
```

## 🚀 Kuanzisha
1. Weka folder kwenye server yako (XAMPP: `htdocs/`, Laragon: `www/`).
2. Weka logo yako: `assets/logo.png` (kama haipo, herufi "W" itaonekana badala yake).
3. Fungua `index.php` na badilisha namba ya WhatsApp:
   `$whatsapp = '255700000000';`
4. Fungua kwenye browser: `http://localhost/wazabiashara-landing/`

## 📬 Kuunganisha Database (Newsletter)
Kwa sasa email zinahifadhiwa `api/subscribers.json` (fallback — inafanya kazi mara moja).
Ukiwa tayari na MySQL:
1. Fungua `api/subscribe.php`.
2. Jaza `$DB_HOST, $DB_NAME, $DB_USER, $DB_PASS`.
3. Ondoa comment kwenye sehemu ya **DATABASE (PDO)**.
4. Run SQL iliyoko mwishoni mwa faili (table: `newsletter_subscribers`).

## 🧩 Vilivyomo
- Header sticky + glass on scroll, mobile menu responsive
- Awning strip (kama kibanda — signature ya brand) juu ya header
- Hero yenye duka la SVG (flat + bold outline style), floating cards + counters
- Ticker ya njia za malipo (M-Pesa, Tigo, Airtel...)
- Stats strip yenye animated counters
- Features 6 zenye icons za custom SVG (style ya flat-outline kama sample yako)
- Hatua 3 (How it works)
- **React** testimonials carousel (auto-slide, dots, arrows)
- **Newsletter AJAX** (fetch → `api/subscribe.php`) + **SweetAlert2**
- WhatsApp floating button yenye pulse + tooltip animation
- Scroll-reveal animations; `prefers-reduced-motion` inaheshimiwa
- Font: **Nunito** (Bunny.net: 400–900) • Palette: Emerald `#024938` + Gold `#f9ac00`
