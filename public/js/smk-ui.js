/* Absensi GPS — dashboard siswa */
(function () {
  var btn = document.getElementById('btn-absen');
  if (!btn) return;

  var form = document.getElementById('form-absen');
  var status = document.getElementById('absen-status');
  var latInput = document.getElementById('latitude');
  var lngInput = document.getElementById('longitude');

  function setStatus(msg) {
    if (status) status.textContent = msg;
  }

  btn.addEventListener('click', function () {
    if (!navigator.geolocation) {
      setStatus('Peramban tidak mendukung layanan lokasi.');
      return;
    }

    btn.disabled = true;
    setStatus('Mengambil lokasi... (izinkan akses lokasi jika diminta)');

    navigator.geolocation.getCurrentPosition(
      function (pos) {
        if (latInput) latInput.value = pos.coords.latitude;
        if (lngInput) lngInput.value = pos.coords.longitude;
        setStatus('Lokasi didapat. Mengirim absensi...');
        form.submit();
      },
      function (err) {
        btn.disabled = false;
        if (err.code === 1) {
          setStatus('Izin lokasi ditolak. Izinkan lokasi di pengaturan peramban, lalu coba lagi.');
        } else if (err.code === 2) {
          setStatus('Lokasi tidak tersedia. Pastikan GPS aktif.');
        } else if (err.code === 3) {
          setStatus('Waktu menunggu lokasi habis. Coba lagi.');
        } else {
          setStatus('Gagal mengambil lokasi. Coba lagi.');
        }
      },
      { enableHighAccuracy: false, timeout: 25000, maximumAge: 60000 }
    );
  });
})();
