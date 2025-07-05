</main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. LOGIKA UNTUK POP-UP KONFIRMASI HAPUS ---
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            // Mencegah link langsung dieksekusi
            event.preventDefault(); 
            
            const url = this.href; // Simpan URL untuk hapus

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: document.body.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.body.classList.contains('dark') ? '#e5e7eb' : '#1f2937'
            }).then((result) => {
                // Jika pengguna menekan tombol "Ya, hapus!"
                if (result.isConfirmed) {
                    // Arahkan ke URL hapus
                    window.location.href = url;
                }
            });
        });
    });

    // --- 2. LOGIKA UNTUK NOTIFIKASI SUKSES ---
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    let message = '';

    if (status === 'hapus_sukses') {
        message = 'Data telah berhasil dihapus.';
    } else if (status === 'tambah_sukses') {
        message = 'Data berhasil ditambahkan.';
    } else if (status === 'edit_sukses') {
        message = 'Data berhasil diperbarui.';
    } else if (status === 'nilai_sukses') {
        message = 'Nilai berhasil disimpan.';
    }

    if (message) {
        Swal.fire({
            title: 'Berhasil!',
            text: message,
            icon: 'success',
            timer: 2000, 
            showConfirmButton: false,
            background: document.body.classList.contains('dark') ? '#1f2937' : '#fff',
            color: document.body.classList.contains('dark') ? '#e5e7eb' : '#1f2937'
        });
        // Membersihkan parameter URL agar notifikasi tidak muncul lagi saat refresh
        const newUrl = window.location.pathname + window.location.hash;
        history.pushState({}, '', newUrl);
    }
});
</script>
</body>
</html>