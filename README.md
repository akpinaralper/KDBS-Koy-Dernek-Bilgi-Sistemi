# KDBS Koy Dernek Bilgi Sistemi


KDBS, bir köy derneği için geliştirilen tam kapsamlı bir web tabanlı bilgi ve yönetim sistemidir. Üyelik takibi, bağış işlemleri, etkinlik yönetimi, aidat takibi ve daha fazlası bu sistem üzerinden kolayca yönetilebilir.

🚀 Özellikler

🧑‍💼 Admin ve kullanıcı rolleri
📝 Kişisel bilgiler güncelleme
💸 Aidat takibi ve ödeme detayları
❤️ Bağış yönetimi (proje bazlı)
📅 Etkinlik takibi
📜 Duyurular ve proje paylaşımı
🔐 Giriş / çıkış sistemi
🖼️ Ekran Görüntüleri
![index php](https://github.com/user-attachments/assets/49f65716-c9cc-4803-8022-f1693d9c93ce)
![admin php](https://github.com/user-attachments/assets/295b2abe-c97c-4e65-ab44-1b288572024f)
![profile php](https://github.com/user-attachments/assets/9a712fa1-e767-4d81-af3a-5b6556eb78a1)
![login php](https://github.com/user-attachments/assets/ac7f9f17-f0d3-4161-bcdf-c6ac9921bac2)
![index0 php](https://github.com/user-attachments/assets/7634d9a4-7f05-4ae3-9375-a450e3598648)



Ana sayfa, kullanıcı profili ve bağış ekranları gibi örnek görüntüler için proje dizininde yer alan assets/logo.png dosyası veya kendi ekran görüntüleriniz eklenebilir.

🛠️ Teknolojiler

Backend: PHP 7+

Veritabanı: Oracle (OCI bağlantı kullanılır)

Frontend: HTML5, CSS3, JavaScript

Kütüphaneler: Bootstrap 5, FontAwesome

Oturum Yönetimi: PHP $_SESSION

🔧 Kurulum
Proje dosyalarını bir web sunucusuna (XAMPP/WAMP) yerleştirin.
Oracle veritabanında gerekli tabloları oluşturun. (Tablo yapısı dosyada yoksa geliştiriciye başvurunuz.)
SEFER / Sefer123 Oracle kullanıcı bilgileri ile admin.php, profile.php, login.php gibi dosyaları kontrol edin.
assets/ klasöründe logo.png dosyası bulunmalıdır.
index.php dosyasını tarayıcıdan açarak sistemi test edin.

👤 Giriş Bilgileri (Test Amaçlı)
Admin Kullanıcı:
E-posta: 0@mail.com
Şifre: 123
Kullanıcı (ID=1):
E-posta: 1@mail.com
Şifre: 123
Not: Giriş doğrulama, e-posta formatına ve UYE_ID değerine göre yapılır.

📂 Klasör ve Dosya Yapısı

/kdbs
│
├── index.php              # Ana sayfa
├── login.php              # Giriş ekranı
├── logout.php             # Oturum sonlandırma
├── admin.php              # Yönetici paneli
├── profile.php            # Üye paneli
├── styles.css             # Stil dosyası
├── script.js              # JS fonksiyonları
├── assets/logo.png        # Logo
├── README.md              # Proje açıklaması (bu dosya)



📧 İletişim
Köyümüzün geleceği için birlikte daha güclü 💪


