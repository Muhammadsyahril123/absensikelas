/* Absensi GPS — dashboard siswa */
(function () {
  var btn = document.getElementById('btn-absen');
  if (!btn) return;
  var form = document.getElementById('form-absen');
  var status = document.getElementById('absen-status');

  btn.addEventListener('click', function () {
    if (!navigator.geolocation) {
      if (status) status.textContent = 'Peramban tidak mendukung lokasi.';
      return;
    }
    btn.disabled = true;
    if (status) status.textContent = 'Mengambil lokasi...';
    navigator.geolocation.getCurrentPosition(
      function (pos) {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        if (status) status.textContent = 'Mengirim absensi...';
        form.submit();
      },
      function (err) {
        btn.disabled = false;
        if (!status) return;
        if (err.code === 1) status.textContent = 'Izin lokasi ditolak.';
        else status.textContent = 'Gagal mengambil lokasi.';
      },
      { enableHighAccuracy: false, timeout: 30000, maximumAge: 60000 }
    );
  });
})();
