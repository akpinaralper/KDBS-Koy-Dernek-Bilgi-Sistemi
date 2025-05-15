document.addEventListener('DOMContentLoaded', function() {
    // Menü aç/kapat (mobil)
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            nav.classList.toggle('active');
        });
    }

    // Sayfa kaydırma - Smooth scroll
    const navLinks = document.querySelectorAll('nav ul li a, .btn[href^="#"]');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });

                    // Mobil menüyü kapat
                    if (nav.classList.contains('active')) {
                        nav.classList.remove('active');
                    }
                }
            }
        });
    });

    // Aktif menü linki
    window.addEventListener('scroll', function () {
        const sections = document.querySelectorAll('section');
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 150) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
            if (current === '' && link.getAttribute('href') === '#') {
                link.classList.add('active');
            }
        });
    });

    // Bağış miktarı seçimi
    const donationOptions = document.querySelectorAll('.donation-option');
    const donationAmountDisplay = document.getElementById('donation-amount');
    let selectedAmount = '100 TL';

    donationOptions.forEach(option => {
        option.addEventListener('click', function () {
            donationOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            const amount = this.getAttribute('data-amount');
            if (amount === 'custom') {
                const customInput = document.getElementById('custom-amount');
                customInput.focus();
                customInput.addEventListener('input', function () {
                    selectedAmount = this.value + ' TL';
                    donationAmountDisplay.textContent = selectedAmount;
                });
            } else {
                selectedAmount = amount + ' TL';
                donationAmountDisplay.textContent = selectedAmount;
            }
        });
    });

    // Proje seçimi
    const projectSelect = document.getElementById('project');
    const donationProjectDisplay = document.getElementById('donation-project');

    if (projectSelect && donationProjectDisplay) {
        projectSelect.addEventListener('change', function () {
            donationProjectDisplay.textContent = this.options[this.selectedIndex].text;
        });
    }

    // Ödeme yöntemi gösterimi
    const paymentOptions = document.querySelectorAll('input[name="payment"]');
    const creditCardSection = document.querySelector('.credit-card-section');
    const bankTransferSection = document.querySelector('.bank-transfer-section');

    if (paymentOptions.length > 0 && creditCardSection && bankTransferSection) {
        paymentOptions.forEach(option => {
            option.addEventListener('change', function () {
                creditCardSection.style.display = this.value === 'credit-card' ? 'block' : 'none';
                bankTransferSection.style.display = this.value === 'bank-transfer' ? 'block' : 'none';
            });
        });
    }

    // Bağış formu gönderimi
    const paymentForm = document.getElementById('payment-form');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;

            if (!name || !email) {
                showToast('Lütfen tüm zorunlu alanları doldurun.', 'error');
                return;
            }

            showToast('Bağışınız için teşekkür ederiz! İşleminiz tamamlandı.', 'success');
            this.reset();
            donationOptions[0].click();
            if (projectSelect) {
                projectSelect.selectedIndex = 0;
                donationProjectDisplay.textContent = projectSelect.options[0].text;
            }
            if (paymentOptions[0]) paymentOptions[0].checked = true;
            creditCardSection.style.display = 'block';
            bankTransferSection.style.display = 'none';
        });
    }

    // İletişim formu
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('contact-name').value;
            const email = document.getElementById('contact-email').value;
            const message = document.getElementById('contact-message').value;

            if (!name || !email || !message) {
                showToast('Lütfen tüm zorunlu alanları doldurun.', 'error');
                return;
            }

            showToast('Mesajınız gönderildi! En kısa sürede size dönüş yapacağız.', 'success');
            this.reset();
        });
    }

    // Bülten formu
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            if (!emailInput.value) {
                showToast('Lütfen e-posta adresinizi girin.', 'error');
                return;
            }

            showToast('Bültenimize abone oldunuz!', 'success');
            emailInput.value = '';
        });
    }

    // Bildirim (toast) fonksiyonu
    function showToast(message, type = 'info') {
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
            Object.assign(toastContainer.style, {
                position: 'fixed',
                bottom: '20px',
                right: '20px',
                zIndex: '9999'
            });
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        Object.assign(toast.style, {
            padding: '12px 20px',
            marginBottom: '10px',
            borderRadius: '4px',
            boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
            backgroundColor: type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3',
            color: 'white',
            fontWeight: '500',
            minWidth: '250px',
            opacity: '0',
            transition: 'opacity 0.3s, transform 0.3s',
            transform: 'translateY(20px)'
        });

        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
});
