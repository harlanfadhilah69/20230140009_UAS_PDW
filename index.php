<?php
// Mengambil path URL yang diminta, contoh: /login, /register, /mahasiswa/dashboard
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Menghapus nama folder proyek dari URI jika ada (opsional, tergantung cara run)
$base_path = '/SistemPengumpulanTugas'; // Sesuaikan jika nama folder berbeda
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

// Menentukan file yang akan di-include berdasarkan URL
switch ($request_uri) {
    case '/':
    case '':
        require __DIR__ . '/login.php';
        break;

    case '/login':
        require __DIR__ . '/login.php';
        break;

    case '/register':
        require __DIR__ . '/register.php';
        break;
        
    case '/logout':
        require __DIR__ . '/logout.php';
        break;

    // Menangani semua request ke folder mahasiswa
    case (preg_match('/^\/mahasiswa\/(.*)/', $request_uri, $matches) ? true : false):
        $file_path = __DIR__ . '/mahasiswa/' . $matches[1];
        if (file_exists($file_path) && is_file($file_path)) {
            require $file_path;
        } else {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p>";
        }
        break;

    // Menangani semua request ke folder asisten
    case (preg_match('/^\/asisten\/(.*)/', $request_uri, $matches) ? true : false):
        $file_path = __DIR__ . '/asisten/' . $matches[1];
        if (file_exists($file_path) && is_file($file_path)) {
            require $file_path;
        } else {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p>";
        }
        break;
    
    // Menangani request ke file di folder uploads
    case (preg_match('/^\/uploads\/(.*)/', $request_uri, $matches) ? true : false):
        $file_path = __DIR__ . '/uploads/' . $matches[1];
        if (file_exists($file_path) && is_file($file_path)) {
            // Tentukan tipe konten (MIME type) agar file bisa ditampilkan/diunduh dengan benar
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            header('Content-Type: ' . $mime_type);
            readfile($file_path); // Baca dan kirimkan isi file
        } else {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>File tidak ditemukan.</p>";
        }
        break;

    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p>";
        break;
}

?>