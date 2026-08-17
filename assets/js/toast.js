function showToast(message, type = 'success', duration = 2200) {
    const container = document.querySelector('.toast-container') || (() => {
        const el = document.createElement('div');
        el.className = 'toast-container';
        document.body.appendChild(el);
        return el;
    })();

    const toast = document.createElement('div');
    toast.className = 'toast ' + (type === 'error' ? 'error' : 'success');
    toast.textContent = message;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 220);
    }, duration);
}

// ตรวจสอบ URL parameter status และ message แล้วแสดง toast ถ้ามีค่ามา
// ใช้เวลาหน้า redirect มากจาก EXC.php พร้อมกับ query string เช่น ?status=success&message=Added
function checkAndShowToastFromUrl() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const message = params.get('message');

    if (status && message) {
        const type = status === 'error' ? 'error' : 'success';
        showToast(decodeURIComponent(message), type, 2200);
        
        // ลบ query string ออกจาก URL เพื่อไม่ให้ reload page ทำให้ toast ปรากฏซ้ำ
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// เรียก checkAndShowToastFromUrl ตอน DOMContentLoaded
document.addEventListener('DOMContentLoaded', checkAndShowToastFromUrl);
