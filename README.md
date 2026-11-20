# AFLMUN — Model United Nations Conference Website & Admin CMS

*[English](#english) | [Türkçe](#türkçe)*

---

<a name="english"></a>
## English

### Overview
**AFLMUN** is a full-stack PHP & MySQL conference website and custom Content Management System (CMS) built for the AFL Model United Nations conference. In addition to public-facing pages for committees, study guides, application packages, executive board, and contact details, it includes an administration dashboard (`admin/`) allowing organizers to manage committees, application cards, countdown settings, team members, and executive visibility without touching code.

### Development Timeline & Process
- **Development Period:** December 3–8, 2025 (based on original source timestamps)
- **Process:** Designed with a clean server-rendered PHP + PDO architecture, modular includes (`includes/navbar.php`, `includes/footer.php`), and a dedicated admin panel (`admin/`).
- **Security Sanitization:** All production database credentials (`config.php`, `admin/db.php`) and hardcoded admin login passwords (`admin/login.php`) have been removed and replaced with environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_PASSWORD`). See `.env.example`.

### Setup & Running
1. Configure a PHP + MySQL environment (XAMPP, Laragon, or Apache/Nginx + PHP-FPM).
2. Set the environment variables listed in `.env.example`.
3. Create your MySQL database and tables (`settings`, committees, team, applications), then serve the root directory:
```bash
php -S localhost:8000
```

### Acknowledgments & Open-Source Credits
- **[Font Awesome](https://fontawesome.com/)** — UI icons.
- **[PHP PDO](https://www.php.net/manual/en/book.pdo.php)** & **MySQL** — Backend data layer.

---

<a name="türkçe"></a>
## Türkçe

### Genel Bakış
**AFLMUN**, Model Birleşmiş Milletler (MUN) konferansı için PHP ve MySQL ile geliştirilmiş tam kapsamlı konferans web sitesi ve özel yönetim panelidir (CMS). Komiteler, çalışma rehberleri (study guides), başvuru kartları, yönetim kurulu ve iletişim sayfalarının yanı sıra; organizatörlerin kod yazmadan içerik güncelleyebilmesi için kapsamlı bir yönetici paneli (`admin/`) içerir.

### Geliştirme Süreci ve Tarihçesi
- **Geliştirme Tarihleri:** 3–8 Aralık 2025 (orijinal kaynak dosya tarihlerine göre)
- **Süreç:** Modüler PHP şablonları ve PDO veritabanı katmanı üzerine kuruldu. Komite ekleme/düzenleme, geri sayım ayarı, başvuru paketleri ve ekip yönetimi tek bir panelde toplandı.
- **Güvenlik Temizliği:** `config.php`, `admin/db.php` içindeki veritabanı erişim bilgileri ve `admin/login.php` içindeki sabit yönetici şifresi temizlenerek ortam değişkenlerine (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_PASSWORD`) taşındı. Örnek yapılandırma için `.env.example` dosyasına bakınız.

### Açık Kaynak Teşekkürler
- **[Font Awesome](https://fontawesome.com/)** — İkon seti.
- **PHP & MySQL** — Sunucu ve veritabanı altyapısı.