# 🇹🇿 WAZABIASHARA
## Mfumo wa Kisasa wa Kusimamia Biashara za Tanzania (SaaS Platform)

> **"Biashara Yako, Mkononi Mwako"** — Simamia mauzo, stoo, wafanyakazi, matawi na madeni popote ulipo — kwa Web na Simu.

---

## 📖 YALIYOMO

1. [Utangulizi wa Mfumo](#1-utangulizi-wa-mfumo)
2. [Dhana ya SaaS — Jinsi Mfumo Unavyofanya Kazi](#2-dhana-ya-saas)
3. [Majukumu ya Watumiaji (Roles & Permissions)](#3-majukumu-ya-watumiaji)
4. [Muundo wa Matawi (Multi-Branch Structure)](#4-muundo-wa-matawi)
5. [Moduli Kuu za Mfumo](#5-moduli-kuu-za-mfumo)
6. [Mtiririko wa Kazi (Workflows)](#6-mtiririko-wa-kazi)
7. [Muundo wa Kiufundi (Technical Architecture)](#7-muundo-wa-kiufundi)
8. [Muundo wa Database](#8-muundo-wa-database)
9. [Usalama wa Mfumo](#9-usalama-wa-mfumo)
10. [Malipo ya Kitanzania (Payment Integrations)](#10-malipo-ya-kitanzania)
11. [Mobile App vs Web App](#11-mobile-app-vs-web-app)
12. [Ripoti na Takwimu](#12-ripoti-na-takwimu)
13. [Awamu za Utekelezaji (Roadmap)](#13-awamu-za-utekelezaji)

---

## 1. UTANGULIZI WA MFUMO

**Wazabiashara** ni mfumo wa kidijitali wa aina ya **SaaS (Software as a Service)** uliotengenezwa mahsusi kwa ajili ya wafanyabiashara wa Tanzania — kuanzia duka dogo la rejareja, duka la dawa (pharmacy), duka la vifaa vya ujenzi (hardware), supermarket, wholesale, hadi biashara kubwa yenye matawi mengi mikoa tofauti.

### 🎯 Malengo ya Mfumo

| # | Lengo | Maelezo |
|---|-------|---------|
| 1 | **Kudhibiti Mauzo** | Kila muuzo unarekodiwa — hakuna hela inayopotea |
| 2 | **Kusimamia Stoo (Inventory)** | Kujua bidhaa zilizopo, zinazoisha, na zilizokwisha muda |
| 3 | **Kusimamia Wafanyakazi** | Kujua nani kauza nini, saa ngapi, na tawi gani |
| 4 | **Kusimamia Matawi** | Kuona biashara zote sehemu moja hata ukiwa mbali |
| 5 | **Kufuatilia Madeni** | Wateja wanaodaiwa na wasambazaji unaowadai |
| 6 | **Ripoti za Papo kwa Papo** | Faida, hasara, mauzo — kwa siku, wiki, mwezi, mwaka |
| 7 | **Kufanya kazi Bila Intaneti** | Offline mode — mauzo yanaendelea hata mtandao ukikatika |

### 🌍 Kwa Nini Tanzania?

- Mfumo umejengwa kwa **Kiswahili na Kiingereza** (mtumiaji anachagua lugha)
- Sarafu ni **Shilingi ya Tanzania (TZS)**
- Malipo kupitia **M-Pesa, Tigo Pesa, Airtel Money, Halopesa** na benki
- Risiti zinazokidhi mahitaji ya **TRA (EFD/VFD integration — hatua ya baadaye)**
- Ujumbe wa SMS kwa wateja kupitia mitandao ya ndani
- Umejengwa kuhimili **intaneti ya kasi ndogo** na simu za bei nafuu

---

## 2. DHANA YA SAAS

### Mfumo Mmoja — Biashara Nyingi (Multi-Tenancy)

Wazabiashara ni jukwaa moja kuu (platform) linalohudumia biashara nyingi kwa wakati mmoja. Kila biashara inayojisajili inapata **"akaunti yake huru"** — data zake haziwezi kuonekana na biashara nyingine yoyote.

```
                    ┌─────────────────────────────┐
                    │      WAZABIASHARA CLOUD     │
                    │    (Super Admin - Mmiliki    │
                    │        wa Jukwaa)           │
                    └──────────────┬──────────────┘
           ┌───────────────────────┼───────────────────────┐
           ▼                       ▼                       ▼
   ┌───────────────┐      ┌───────────────┐       ┌───────────────┐
   │  Biashara A    │      │  Biashara B    │       │  Biashara C    │
   │  (Duka la Dawa │      │  (Hardware -   │       │  (Supermarket  │
   │   - Mwanza)    │      │   Dar es Salaam│       │   - Arusha)    │
   ├───────────────┤      ├───────────────┤       ├───────────────┤
   │ Tawi 1, Tawi 2 │      │ Tawi 1..Tawi 5 │       │ Tawi 1         │
   │ Wafanyakazi 6  │      │ Wafanyakazi 20 │       │ Wafanyakazi 4  │
   └───────────────┘      └───────────────┘       └───────────────┘
```

### Jinsi Biashara Inavyojiunga (Onboarding Flow)

1. **Kujisajili** — Mmiliki wa biashara anajisajili kwa jina, namba ya simu na barua pepe
2. **Kuthibitisha** — Anapokea OTP kwa SMS kuthibitisha namba yake
3. **Kuweka Taarifa za Biashara** — Jina la biashara, aina (rejareja/jumla/pharmacy n.k.), mkoa, tawi la kwanza
4. **Kuchagua Kifurushi (Plan)** — Kulingana na ukubwa wa biashara yake
5. **Kuanza Kazi** — Anaongeza bidhaa, wafanyakazi na matawi — biashara inaanza mara moja

### Vifurushi vya Huduma (Subscription Tiers — Muundo)

| Kipengele | 🥉 Mwanzo | 🥈 Kati | 🥇 Biashara Kubwa |
|-----------|-----------|---------|-------------------|
| Idadi ya Matawi | 1 | Hadi 3 | Bila kikomo |
| Wafanyakazi | Hadi 3 | Hadi 10 | Bila kikomo |
| Bidhaa | Hadi 500 | Hadi 5,000 | Bila kikomo |
| Ripoti za Msingi | ✅ | ✅ | ✅ |
| Ripoti za Kina (Analytics) | ❌ | ✅ | ✅ |
| SMS kwa Wateja | ❌ | ✅ | ✅ |
| Offline Mode | ✅ | ✅ | ✅ |
| API Access | ❌ | ❌ | ✅ |
| Msaada wa Haraka (Priority Support) | ❌ | ❌ | ✅ |

---

## 3. MAJUKUMU YA WATUMIAJI

Mfumo una ngazi **nne (4)** kuu za watumiaji, kila mmoja na mipaka yake (Role-Based Access Control — RBAC).

### 🟣 3.1 Super Admin (Msimamizi Mkuu wa Jukwaa — Wazabiashara HQ)

Huyu ni mmiliki/timu ya Wazabiashara yenyewe. **Haingii kwenye data za ndani za biashara**, bali anasimamia jukwaa zima.

**Majukumu:**
- Kusimamia biashara zote zilizojisajili (approve / suspend / activate)
- Kusimamia vifurushi (plans) na malipo ya subscription
- Kuona takwimu za jukwaa: biashara ngapi, watumiaji wangapi, mapato ya jukwaa
- Kutuma matangazo na notisi kwa biashara zote
- Kusimamia tiketi za msaada (support tickets)
- Kusimamia mipangilio ya jumla ya mfumo (SMS gateway, payment gateway, n.k.)
- Kuona logi za mfumo (system logs & audit)

### 🔵 3.2 Business Admin / Mmiliki wa Biashara (Tenant Owner)

Huyu ndiye **bosi wa biashara husika**. Ana mamlaka kamili ndani ya biashara yake TU.

**Majukumu:**
- Kusajili na kufuta **matawi (branches)** ya biashara yake
- Kuajiri, kusimamisha na kuweka majukumu ya **wafanyakazi**
- Kuona **ripoti za matawi yote** — mauzo, faida, stoo, matumizi
- Kuweka bei za bidhaa, punguzo (discounts) na promosheni
- Kuhamisha bidhaa kati ya matawi (stock transfer approval)
- Kusimamia madeni ya wateja na wasambazaji
- Kulipa na kusimamia kifurushi (subscription) cha biashara
- Kupokea **taarifa za kila siku kwa SMS/App** — "Leo umeuza TZS X, faida TZS Y"
- Kuweka mipangilio ya biashara: risiti, logo, VAT, sarafu, lugha

### 🟢 3.3 Meneja wa Tawi (Branch Manager)

Anasimamia **tawi moja tu** alilopangiwa na Business Admin.

**Majukumu:**
- Kuona ripoti za tawi lake (mauzo, stoo, wafanyakazi)
- Kupokea bidhaa kutoka kwa wasambazaji au tawi jingine
- Kuomba uhamisho wa bidhaa (stock transfer request)
- Kusimamia zamu (shifts) za wafanyakazi wa tawi lake
- Kuidhinisha marejesho ya bidhaa (returns) na punguzo makubwa
- Kufunga hesabu za siku (End of Day closing)

### 🟡 3.4 Mfanyakazi / Muuzaji (Cashier / Sales Staff)

Mtumiaji wa kawaida — anafanya kazi ya mauzo ya kila siku.

**Majukumu:**
- Kuuza bidhaa kupitia **POS (Point of Sale)** — web au simu
- Kutoa risiti (kuchapisha au kutuma kwa SMS/WhatsApp)
- Kurekodi mauzo ya deni (credit sales) — kwa idhini
- Kuona bidhaa na bei (hawezi kubadilisha bei)
- Kufungua na kufunga zamu yake (shift open/close na cash count)

### 📊 Jedwali la Ruhusa (Permission Matrix)

| Kitendo | Super Admin | Business Admin | Branch Manager | Mfanyakazi |
|---------|:-----------:|:--------------:|:--------------:|:----------:|
| Kusimamia biashara zote za jukwaa | ✅ | ❌ | ❌ | ❌ |
| Kuongeza/kufuta matawi | ❌ | ✅ | ❌ | ❌ |
| Kuajiri wafanyakazi | ❌ | ✅ | ⚠️ (kwa idhini) | ❌ |
| Kubadilisha bei za bidhaa | ❌ | ✅ | ⚠️ (kwa idhini) | ❌ |
| Kuona ripoti za matawi yote | ❌ | ✅ | ❌ | ❌ |
| Kuona ripoti za tawi lake | ❌ | ✅ | ✅ | ❌ |
| Kufanya mauzo (POS) | ❌ | ✅ | ✅ | ✅ |
| Kufuta muuzo (void sale) | ❌ | ✅ | ✅ | ❌ |
| Kuhamisha stoo kati ya matawi | ❌ | ✅ | ⚠️ (request) | ❌ |
| Kuona faida/hasara | ❌ | ✅ | ⚠️ (tawi tu) | ❌ |

> ⚠️ = Inawezekana kwa idhini au kwa mipaka. Business Admin anaweza ku-customize ruhusa hizi kwa kila mfanyakazi.

---

## 4. MUUNDO WA MATAWI

Biashara moja inaweza kuwa na matawi mengi, kila tawi likiwa na stoo yake, wafanyakazi wake na hesabu zake — lakini vyote vinaonekana sehemu moja kwa mmiliki.

```
                 ┌──────────────────────────────┐
                 │   BIASHARA: "Mangi Traders"   │
                 │   (Business Admin: Mangi)     │
                 └───────────────┬──────────────┘
        ┌────────────────────────┼────────────────────────┐
        ▼                        ▼                        ▼
┌────────────────┐     ┌────────────────┐      ┌────────────────┐
│ TAWI: Kariakoo  │     │ TAWI: Mwanza    │      │ TAWI: Arusha    │
│ Meneja: Neema   │     │ Meneja: Juma    │      │ Meneja: Baraka  │
├────────────────┤     ├────────────────┤      ├────────────────┤
│ • Stoo yake     │     │ • Stoo yake     │      │ • Stoo yake     │
│ • Wauzaji 4     │     │ • Wauzaji 3     │      │ • Wauzaji 2     │
│ • Hesabu zake   │     │ • Hesabu zake   │      │ • Hesabu zake   │
└────────────────┘     └────────────────┘      └────────────────┘
```

### Sifa za Muundo wa Matawi

- **Stoo Huru kwa Kila Tawi** — bidhaa moja inaweza kuwa na idadi tofauti kila tawi, na hata bei tofauti (mfano: Dar bei tofauti na Mwanza)
- **Uhamisho wa Bidhaa (Stock Transfer)** — tawi lenye bidhaa nyingi linaweza kutuma kwa tawi lililoishiwa; mchakato una hatua: *Ombi → Idhini → Kutumwa → Kupokelewa* (kila hatua inarekodiwa)
- **Ripoti Zilizounganishwa (Consolidated Reports)** — mmiliki anaona jumla ya biashara nzima au tawi mojamoja
- **Kulinganisha Matawi** — tawi gani linauza zaidi? Tawi gani lina hasara? Grafu za kulinganisha moja kwa moja
- **Mfanyakazi Kuhamishwa** — mmiliki anaweza kumhamisha mfanyakazi kutoka tawi moja kwenda jingine kwa kubofya tu

---

## 5. MODULI KUU ZA MFUMO

### 🛒 5.1 Moduli ya Mauzo (POS — Point of Sale)

Moyo wa mfumo. Imejengwa iwe **ya haraka sana** — muuzo unakamilika ndani ya sekunde chache.

- Kutafuta bidhaa kwa jina, code, au **ku-scan barcode kwa kamera ya simu**
- Kikapu cha bidhaa (cart) na jumla inayojihesabu yenyewe (pamoja na VAT ikiwashwa)
- Njia za malipo: **Taslimu (Cash), M-Pesa, Tigo Pesa, Airtel Money, Halopesa, Benki/Kadi, Deni (Credit), Mchanganyiko (Split payment)**
- Kutoa risiti: kuchapisha (thermal printer), SMS, WhatsApp, au barua pepe
- Punguzo (discount) kwa bidhaa au jumla — kwa mipaka aliyowekewa muuzaji
- **Kuweka muuzo kando (Hold Sale)** — mteja akitaka kuongeza kitu, muuzo unasubiri
- Marejesho ya bidhaa (Sales Return) yenye sababu na idhini
- **Offline POS** — mauzo yanahifadhiwa kwenye simu/kompyuta, yana-sync intaneti ikirudi

### 📦 5.2 Moduli ya Stoo (Inventory Management)

- Kusajili bidhaa: jina, picha, code/barcode, kategoria, kipimo (units — kipande, kilo, lita, katoni, dazani)
- **Bei mbili:** bei ya kununulia (cost) na bei ya kuuzia (selling) — mfumo unahesabu faida wenyewe
- **Vipimo vingi (Multi-unit):** kununua kwa katoni, kuuza kwa kipande — mfumo unabadilisha wenyewe
- **Tahadhari za Stoo (Alerts):**
  - 🔴 Bidhaa inakaribia kuisha (low stock alert)
  - 🟠 Bidhaa imekwisha kabisa (out of stock)
  - ⏰ Bidhaa inakaribia ku-expire (kwa dawa, vyakula, vinywaji)
- **Kuhesabu Stoo (Stock Taking / Stocktake):** kulinganisha stoo ya mfumoni na ya hali halisi, tofauti (variance) inaonekana na kurekodiwa
- Historia kamili ya bidhaa: iliingiaje, ilitoka lini, nani aligusa
- Kategoria na sub-kategoria zisizo na kikomo

### 🧾 5.3 Moduli ya Manunuzi (Purchases & Suppliers)

- Kusajili wasambazaji (suppliers) na taarifa zao
- Kutengeneza **Order ya Manunuzi (Purchase Order)** na kuituma kwa msambazaji
- Kupokea bidhaa (Goods Received) — stoo inaongezeka yenyewe
- Manunuzi ya deni — kufuatilia **unachodaiwa na wasambazaji**
- Historia ya bei za manunuzi (bei ikipanda unaona)
- Marejesho kwa msambazaji (Purchase Returns)

### 👥 5.4 Moduli ya Wateja na Madeni (Customers & Credit)

Madeni ni sehemu kubwa ya biashara za Tanzania — moduli hii ni ya kipekee:

- Kusajili wateja: jina, simu, mahali
- **Kitabu cha Madeni cha Kidijitali:** kila deni lina tarehe, bidhaa, na tarehe ya makubaliano ya kulipa
- **SMS za kukumbusha deni kiotomatiki:** *"Habari Juma, unadaiwa TZS 45,000 na Mangi Traders. Tafadhali lipa kabla ya 25/07."*
- Kupokea malipo ya deni kidogokidogo (partial payments) na salio kuonekana
- Ukomo wa deni kwa mteja (credit limit) — mteja akifikia kikomo mfumo unakataa deni jipya
- Historia ya manunuzi ya kila mteja — wateja bora wanaonekana
- Pointi za uaminifu (Loyalty points) — hatua ya baadaye

### 👨‍💼 5.5 Moduli ya Wafanyakazi (Staff / HR Lite)

- Kusajili mfanyakazi na kumpa role + tawi
- **Login kwa PIN au namba ya simu** — rahisi kwa wauzaji
- Zamu (Shifts): kufungua zamu na hela ya kuanzia (opening float), kufunga na kuhesabu hela (cash reconciliation)
- Kufuatilia utendaji: nani ameuza kiasi gani leo/wiki/mwezi
- Kumbukumbu za mishahara na advance (rekodi tu — si payroll kamili awamu ya kwanza)
- **Audit Trail:** kila kitendo cha mfanyakazi kinarekodiwa — akifuta muuzo, akipunguza bei, mmiliki anaona

### 💰 5.6 Moduli ya Matumizi (Expenses)

- Kurekodi matumizi ya kila siku: kodi ya fremu, umeme, maji, usafiri, chakula, mishahara
- Kategoria za matumizi zinazoweza kubadilishwa
- Kupachika picha ya risiti/ankara
- Matumizi kwa tawi — kila tawi na matumizi yake
- Matumizi yanaingia moja kwa moja kwenye hesabu ya **Faida na Hasara**

### 📊 5.7 Moduli ya Ripoti (Reports & Analytics)

Angalia [Sehemu ya 12](#12-ripoti-na-takwimu) kwa maelezo kamili.

### 🔔 5.8 Moduli ya Taarifa (Notifications)

- **Kwa Mmiliki:** muhtasari wa siku (daily summary), tahadhari za stoo, mauzo makubwa, mfanyakazi kufuta muuzo
- **Kwa Wateja:** risiti kwa SMS, kumbukumbu za madeni, promosheni
- **Njia:** Push notification (app), SMS, WhatsApp, barua pepe

---

## 6. MTIRIRIKO WA KAZI

### 6.1 Mtiririko wa Muuzo wa Kawaida (Cash Sale)

```
Mteja anafika ➜ Muuzaji anatafuta/anascan bidhaa ➜ Bidhaa zinaingia kikapuni
     ➜ Mfumo unahesabu jumla ➜ Mteja analipa (Cash/M-Pesa/...)
     ➜ Risiti inatoka (print/SMS) ➜ Stoo inapungua yenyewe
     ➜ Mauzo yanaonekana kwenye dashibodi ya mmiliki PAPO HAPO
```

### 6.2 Mtiririko wa Muuzo wa Deni (Credit Sale)

```
Mteja anachukua bidhaa ➜ Muuzaji anachagua "Deni" ➜ Anachagua mteja aliyesajiliwa
     ➜ Mfumo unakagua credit limit ➜ Deni linarekodiwa na tarehe ya kulipa
     ➜ Mteja anapokea SMS ya uthibitisho wa deni
     ➜ Siku ya malipo ikikaribia ➜ SMS ya kukumbusha inatumwa yenyewe
     ➜ Mteja akilipa ➜ Salio linapungua ➜ Mmiliki anaona
```

### 6.3 Mtiririko wa Uhamisho wa Stoo (Stock Transfer)

```
Tawi la Mwanza limeishiwa sukari
     ➜ Meneja (Mwanza) anaomba: "Tunaomba katoni 20 za sukari"
     ➜ Business Admin anapokea ombi ➜ Anaidhinisha
     ➜ Tawi la Kariakoo linatuma (stoo yao inapungua - "in transit")
     ➜ Mwanza wanapokea na kuthibitisha idadi ➜ Stoo yao inaongezeka
     ➜ Tofauti yoyote (upungufu njiani) inarekodiwa na kuripotiwa
```

### 6.4 Mtiririko wa Kufunga Siku (End of Day)

```
Muuzaji anafunga zamu ➜ Anahesabu hela halisi mkononi
     ➜ Mfumo unalinganisha: hela ya mfumoni vs hela halisi
     ➜ Tofauti (over/short) inarekodiwa ➜ Meneja anaidhinisha
     ➜ Ripoti ya siku inatengenezwa yenyewe
     ➜ Mmiliki anapokea SMS/notification:
        "Mangi Traders - 18/07: Mauzo TZS 1,240,000 | Faida TZS 310,000
         | Matumizi TZS 45,000 | Madeni mapya TZS 80,000"
```

---

## 7. MUUNDO WA KIUFUNDI

### 7.1 Architecture ya Jumla

```
┌─────────────────────────────────────────────────────────────┐
│                      WATUMIAJI (CLIENTS)                     │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────────────┐  │
│  │  Web App      │  │  Mobile App   │  │  POS Terminal /   │  │
│  │  (Browser)    │  │ (Android/iOS) │  │  Thermal Printer  │  │
│  └──────┬───────┘  └──────┬───────┘  └─────────┬─────────┘  │
└─────────┼─────────────────┼────────────────────┼────────────┘
          │                 │                    │
          ▼                 ▼                    ▼
┌─────────────────────────────────────────────────────────────┐
│                   REST API + WebSockets (HTTPS)              │
│              (Authentication • Rate Limiting)                │
└──────────────────────────┬──────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    BACKEND SERVICES                          │
│  ┌─────────┐ ┌─────────┐ ┌──────────┐ ┌─────────────────┐   │
│  │ Auth &   │ │ Sales & │ │Inventory │ │ Reports &        │   │
│  │ Tenants  │ │ POS     │ │& Transfer│ │ Analytics Engine │   │
│  └─────────┘ └─────────┘ └──────────┘ └─────────────────┘   │
│  ┌─────────┐ ┌─────────┐ ┌──────────┐ ┌─────────────────┐   │
│  │Payments  │ │SMS/Notif│ │ Billing  │ │ Audit & Logs     │   │
│  │Gateway   │ │Service  │ │(Subscr.) │ │                  │   │
│  └─────────┘ └─────────┘ └──────────┘ └─────────────────┘   │
└──────────────────────────┬──────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                        DATA LAYER                            │
│  ┌────────────┐  ┌────────────┐  ┌────────────────────────┐  │
│  │ PostgreSQL  │  │ Redis      │  │ Object Storage         │  │
│  │ (Main DB -  │  │ (Cache &   │  │ (Picha za bidhaa,      │  │
│  │ Multi-tenant│  │  Queues)   │  │  risiti, backups)      │  │
│  └────────────┘  └────────────┘  └────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────┐
│              HUDUMA ZA NJE (3rd Party Integrations)          │
│   M-Pesa API • Tigo Pesa • Airtel Money • SMS Gateway        │
│   WhatsApp Business API • TRA VFD (baadaye)                  │
└─────────────────────────────────────────────────────────────┘
```

### 7.2 Teknolojia Zinazopendekezwa (Tech Stack)

| Sehemu | Teknolojia | Sababu |
|--------|-----------|--------|
| **Web Frontend** | React.js / Next.js + Tailwind CSS | Kasi, PWA support, SEO kwa landing page |
| **Mobile App** | Flutter | App moja kwa Android na iOS; nzuri kwa offline |
| **Backend API** | Laravel (PHP) au Node.js (NestJS) | Imara, jamii kubwa ya developers Tanzania |
| **Database** | PostgreSQL | Multi-tenancy imara, data integrity |
| **Cache & Queues** | Redis | Kasi ya dashibodi na foleni za SMS/notifications |
| **Real-time** | WebSockets (Socket.io / Pusher) | Dashibodi kuona mauzo papo hapo |
| **Offline Sync** | SQLite (device) + Sync Engine | Mauzo bila intaneti |
| **Hosting** | Cloud (na server Afrika Mashariki ikiwezekana) | Kasi kwa watumiaji wa Tanzania |
| **Payments** | M-Pesa (Vodacom), Tigo, Airtel APIs | Malipo ya subscription na mauzo |

### 7.3 Offline-First — Kipengele cha Dhahabu 🏆

Intaneti Tanzania si ya uhakika kila mahali. Wazabiashara imejengwa **Offline-First**:

1. App inahifadhi data muhimu (bidhaa, bei, wateja) kwenye **SQLite ya simu/kompyuta**
2. Intaneti ikikatika — **mauzo yanaendelea kawaida**, yanahifadhiwa locally
3. Intaneti ikirudi — data ina-**sync kiotomatiki** na cloud (bila muuzaji kufanya chochote)
4. Migongano (conflicts) inatatuliwa kwa kanuni: *server ndiye mwamuzi wa mwisho, lakini hakuna muuzo unaopotea*
5. Alama ya rangi inaonyesha hali: 🟢 Online | 🟡 Inasync | 🔴 Offline

---

## 8. MUUNDO WA DATABASE

### 8.1 Mkakati wa Multi-Tenancy

**Shared Database, Shared Schema** na `business_id` (tenant ID) kwenye kila jedwali — pamoja na **Row-Level Security** kuhakikisha biashara moja haiwezi kamwe kuona data ya nyingine.

### 8.2 Majedwali Makuu (Core Tables)

```
┌─ TENANCY & USERS ─────────────────────────────────────────┐
│ businesses      (id, jina, aina, mkoa, plan_id, status)    │
│ branches        (id, business_id, jina, mahali, simu)      │
│ users           (id, business_id, branch_id, jina, simu,   │
│                  role, pin_hash, status)                    │
│ roles_permissions (role, permission, allowed)               │
│ subscriptions   (business_id, plan, tarehe_mwisho, status)  │
└────────────────────────────────────────────────────────────┘

┌─ PRODUCTS & INVENTORY ────────────────────────────────────┐
│ categories      (id, business_id, jina, parent_id)         │
│ products        (id, business_id, jina, barcode, category, │
│                  unit, cost_price, selling_price, image)    │
│ branch_stock    (branch_id, product_id, qty, reorder_level)│
│ stock_movements (id, product_id, branch_id, aina[in/out/   │
│                  transfer/adjust], qty, ref, user_id, date) │
│ stock_transfers (id, from_branch, to_branch, status, items)│
│ stocktakes      (id, branch_id, tarehe, variance_report)   │
└────────────────────────────────────────────────────────────┘

┌─ SALES ───────────────────────────────────────────────────┐
│ sales           (id, business_id, branch_id, user_id,      │
│                  customer_id, jumla, discount, vat,         │
│                  payment_method, status, tarehe)            │
│ sale_items      (sale_id, product_id, qty, bei, cost, faida)│
│ sale_payments   (sale_id, method, amount, ref_no)  ← split  │
│ sale_returns    (id, sale_id, sababu, qty, approved_by)     │
│ shifts          (id, user_id, branch_id, opening_float,     │
│                  closing_cash, variance, status)             │
└────────────────────────────────────────────────────────────┘

┌─ CUSTOMERS & CREDIT ──────────────────────────────────────┐
│ customers       (id, business_id, jina, simu, credit_limit)│
│ customer_debts  (id, customer_id, sale_id, deni, due_date, │
│                  salio, status)                             │
│ debt_payments   (debt_id, amount, method, tarehe, user_id)  │
└────────────────────────────────────────────────────────────┘

┌─ PURCHASES & SUPPLIERS ───────────────────────────────────┐
│ suppliers       (id, business_id, jina, simu, salio_deni)  │
│ purchases       (id, branch_id, supplier_id, jumla, status)│
│ purchase_items  (purchase_id, product_id, qty, cost_price)  │
│ supplier_payments (supplier_id, amount, tarehe)             │
└────────────────────────────────────────────────────────────┘

┌─ FINANCE & SYSTEM ────────────────────────────────────────┐
│ expenses        (id, branch_id, category, amount, picha,   │
│                  user_id, tarehe)                           │
│ notifications   (id, user_id, aina, ujumbe, status)         │
│ sms_logs        (id, business_id, kwa_nani, ujumbe, status) │
│ audit_logs      (id, user_id, kitendo, jedwali, before,     │
│                  after, ip, tarehe)                          │
│ sync_queue      (device_id, payload, status)  ← offline     │
└────────────────────────────────────────────────────────────┘
```

---

## 9. USALAMA WA MFUMO

| Eneo | Hatua za Usalama |
|------|------------------|
| **Utambulisho (Auth)** | OTP kwa SMS, PIN kwa wauzaji, 2FA kwa wamiliki, JWT tokens zenye muda |
| **Utengano wa Data** | Row-Level Security — biashara A haiwezi kamwe kugusa data ya biashara B |
| **Usimbaji (Encryption)** | HTTPS/TLS kila mawasiliano; PIN na passwords zime-hash (bcrypt/argon2) |
| **Audit Trail** | Kila kitendo nyeti (kufuta muuzo, kubadili bei, kutoa discount kubwa) kinarekodiwa na hakifutiki |
| **Backups** | Backup ya kiotomatiki kila siku + point-in-time recovery |
| **Kudhibiti Vifaa** | Mmiliki anaona vifaa (devices) vilivyo-login na anaweza kuviondoa (remote logout) |
| **Rate Limiting** | Kuzuia mashambulizi ya brute-force kwenye login |
| **Ruhusa (RBAC)** | Kila API endpoint inakagua role na business_id kabla ya kujibu |

---

## 10. MALIPO YA KITANZANIA

### 10.1 Malipo ya Mauzo (Ndani ya POS)

Muuzaji anachagua njia mteja aliyolipia:

- 💵 **Taslimu (Cash)**
- 📱 **M-Pesa / Tigo Pesa / Airtel Money / Halopesa** — kwa kuanzia ni kurekodi ref number; awamu ya pili ni **integration ya moja kwa moja (Push USSD/STK)** ambapo mteja anapokea ombi la malipo kwenye simu yake na mfumo unathibitisha malipo wenyewe
- 🏦 **Benki / Kadi**
- 📒 **Deni (Credit)**
- ➗ **Split Payment** — nusu cash, nusu M-Pesa

### 10.2 Malipo ya Subscription (Biashara kulipia Wazabiashara)

- Mobile Money (M-Pesa, Tigo, Airtel) — **Lipa Namba / Push payment**
- Uthibitisho wa moja kwa moja — akaunti inawashwa sekunde chache baada ya malipo
- Kumbusho la SMS siku 7, 3, na 1 kabla kifurushi hakijaisha
- Grace period ya siku chache kabla ya akaunti kusimamishwa (data haifutwi)

---

## 11. MOBILE APP VS WEB APP

| Kipengele | 🌐 Web App | 📱 Mobile App (Android/iOS) |
|-----------|-----------|------------------------------|
| Walengwa wakuu | Wamiliki, mameneja, POS ya kaunta (kompyuta) | Wauzaji, wamiliki wakiwa safarini |
| POS / Mauzo | ✅ Kamili (keyboard shortcuts) | ✅ Kamili + barcode scan kwa kamera |
| Dashibodi & Ripoti | ✅ Kina zaidi (grafu kubwa, export) | ✅ Muhtasari wa haraka |
| Offline Mode | ✅ (PWA) | ✅ Imara zaidi (SQLite) |
| Push Notifications | ⚠️ Browser | ✅ Kamili |
| Kuchapisha Risiti | ✅ Thermal/A4 printers | ✅ Bluetooth thermal printers |
| Kusimamia mfumo (settings, matawi, wafanyakazi) | ✅ Kamili | ✅ Muhimu tu |

> **Kanuni:** Kazi zote za msingi zinapatikana kote — lakini Web ni "ofisi", Mobile ni "mkononi mwako."

---

## 12. RIPOTI NA TAKWIMU

### 12.1 Dashibodi ya Mmiliki (Real-Time)

Anapofungua mfumo, mmiliki anaona papo hapo:

- 💰 Mauzo ya leo (na kulinganisha na jana / wiki iliyopita)
- 📈 Faida ya leo (ghafi — gross profit)
- 🏪 Mauzo kwa kila tawi (live)
- 🔴 Tahadhari: bidhaa zinazoisha, madeni yaliyochelewa, bidhaa zinazo-expire
- 🏆 Bidhaa zinazouzwa zaidi (top sellers)
- 👤 Wafanyakazi wanaouza zaidi

### 12.2 Ripoti Kamili

| Kundi | Ripoti |
|-------|--------|
| **Mauzo** | Kwa siku/wiki/mwezi/mwaka • kwa tawi • kwa mfanyakazi • kwa bidhaa • kwa kategoria • kwa njia ya malipo • kwa saa (rush hours) |
| **Faida** | Faida na Hasara (P&L) • faida kwa bidhaa • faida kwa tawi • margin analysis |
| **Stoo** | Thamani ya stoo (stock valuation) • bidhaa zisizotembea (dead stock) • bidhaa zinazoisha • zinazokwisha muda • movement history |
| **Madeni** | Wateja wanaodaiwa (aging report: siku 0-30, 31-60, 61+) • wasambazaji wanaodai • historia ya malipo |
| **Matumizi** | Kwa kategoria • kwa tawi • mwenendo (trend) |
| **Wafanyakazi** | Utendaji wa mauzo • cash variance za zamu • vitendo vilivyofutwa (voids) |
| **Matawi** | Kulinganisha matawi • ukuaji kwa tawi |

### 12.3 Export & Kushirikisha

- Kupakua ripoti kwa **PDF na Excel**
- Kutuma ripoti kwa barua pepe kiotomatiki (mfano: kila Jumatatu asubuhi)
- Muhtasari wa siku kwa **SMS/WhatsApp** kwa mmiliki

---

## 13. AWAMU ZA UTEKELEZAJI

### 🚀 Awamu ya 1 — MVP (Msingi wa Mfumo)
> Lengo: Biashara iweze kuuza, kusimamia stoo na kuona ripoti

- Usajili wa biashara (onboarding) + subscription ya msingi
- Roles: Business Admin, Meneja, Muuzaji
- POS kamili (cash + kurekodi mobile money) + risiti
- Inventory: bidhaa, kategoria, stock alerts
- Wateja na kitabu cha madeni + SMS za kukumbusha
- Matumizi (expenses)
- Ripoti za msingi + dashibodi
- Matawi + stock transfer
- Web App (PWA) + Mobile App (Android)
- Offline mode ya msingi

### ⚡ Awamu ya 2 — Ukuaji
- Integration za moja kwa moja za M-Pesa/Tigo/Airtel (Push payment)
- WhatsApp risiti na notifications
- Manunuzi kamili (Purchase Orders) na wasambazaji
- Ripoti za kina (analytics engine, kulinganisha matawi)
- iOS App
- Loyalty points za wateja
- Super Admin panel kamili (billing automation, support tickets)

### 🏆 Awamu ya 3 — Ubora wa Juu
- TRA VFD/EFD integration (risiti za kodi)
- Payroll kamili ya wafanyakazi
- AI Insights: *"Bidhaa X huuzwa zaidi Ijumaa — ongeza stoo Alhamisi"*, utabiri wa mahitaji (demand forecasting)
- API kwa wateja wakubwa (ERP integration)
- Marketplace ya wasambazaji (kuagiza bidhaa ndani ya mfumo)
- Mikopo ya biashara kwa kushirikiana na taasisi za fedha (kwa kutumia historia ya mauzo)

---

## 🏁 HITIMISHO

**Wazabiashara** si programu tu — ni **mshirika wa biashara wa kidijitali** kwa mfanyabiashara wa Kitanzania. Kuanzia muuzaji anayescan bidhaa kwa simu yake Kariakoo, hadi mmiliki anayeangalia matawi yake matatu akiwa safarini Dodoma — mfumo unahakikisha:

> ✅ **Hakuna hela inayopotea** • ✅ **Hakuna bidhaa isiyojulikana ilipo** • ✅ **Hakuna deni linalosahaulika** • ✅ **Maamuzi kwa takwimu, si kubahatisha**

---

*Wazabiashara — Biashara Yako, Mkononi Mwako.* 🇹🇿
