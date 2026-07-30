document.addEventListener('DOMContentLoaded', () => {
    // Modal para mostrar el QR de asistencia
    const modalQr = document.getElementById('modalQrSesion');
    const closeBtn = document.querySelector('.modal-close');

    window.verQrAsistencia = (token) => {
        const qrContainer = document.getElementById('qrContainer');
        qrContainer.innerHTML = ''; // Limpiar anterior

        // Aquí podrías usar una librería JS de QR o simplemente cargar una imagen generada por Laravel
        // Por simplicidad, asumiremos que mostramos el QR generado por la ruta
        const qrImg = document.createElement('img');
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${window.location.origin}/asistencia/scan/${token}`;
        qrImg.style.width = "100%";

        qrContainer.appendChild(qrImg);
        modalQr.style.display = 'flex';
    };

    if(closeBtn) {
        closeBtn.onclick = () => modalQr.style.display = 'none';
    }
});
