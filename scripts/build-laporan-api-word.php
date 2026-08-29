<?php

$out = __DIR__ . '/../docs/Laporan-API-SI-SDM.docx';

$esc = static fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');

$paragraph = static function (string $text, string $style = 'Normal') use ($esc): string {
    return '<w:p><w:pPr><w:pStyle w:val="' . $style . '"/></w:pPr><w:r><w:t xml:space="preserve">' . $esc($text) . '</w:t></w:r></w:p>';
};

$heading = static function (string $text, int $level = 1) use ($paragraph): string {
    return $paragraph($text, 'Heading' . $level);
};

$bullet = static function (string $text) use ($paragraph): string {
    return $paragraph('- ' . $text);
};

$table = static function (array $headers, array $rows) use ($esc): string {
    $xml = '<w:tbl><w:tblPr><w:tblBorders><w:top w:val="single" w:sz="4"/><w:left w:val="single" w:sz="4"/><w:bottom w:val="single" w:sz="4"/><w:right w:val="single" w:sz="4"/><w:insideH w:val="single" w:sz="4"/><w:insideV w:val="single" w:sz="4"/></w:tblBorders></w:tblPr>';
    $allRows = [$headers, ...$rows];
    foreach ($allRows as $index => $row) {
        $xml .= '<w:tr>';
        foreach ($row as $cell) {
            $fill = $index === 0 ? '<w:shd w:fill="1F4E79"/>' : '';
            $color = $index === 0 ? '<w:color w:val="FFFFFF"/>' : '';
            $bold = $index === 0 ? '<w:b/>' : '';
            $xml .= '<w:tc><w:tcPr>' . $fill . '</w:tcPr><w:p><w:r><w:rPr>' . $bold . $color . '</w:rPr><w:t xml:space="preserve">' . $esc((string) $cell) . '</w:t></w:r></w:p></w:tc>';
        }
        $xml .= '</w:tr>';
    }
    return $xml . '</w:tbl>';
};

$body = '';
$body .= $paragraph('LAPORAN API SISTEM INFORMASI SDM VILLA MERAH', 'Title');
$body .= $paragraph('Inventarisasi endpoint, hak akses, keamanan, dan panduan integrasi', 'Subtitle');
$body .= $paragraph('Tanggal laporan: 27 Agustus 2026', 'Subtitle');

$body .= $heading('1. Ringkasan Eksekutif');
$body .= $paragraph('Sistem Informasi SDM Villa Merah saat ini menggunakan endpoint Laravel berbasis web. Route aplikasi didefinisikan pada routes/web.php dan dilindungi oleh session authentication, CSRF, serta middleware role. Belum terdapat routes/api.php, API publik berbasis token, atau autentikasi Bearer token.');
$body .= $paragraph('Tiga endpoint JSON tersedia pada modul manajemen kontrak. Endpoint lain digunakan untuk halaman aplikasi, proses form, upload dokumen, redirect, ekspor, dan download file.');

$body .= $heading('2. Ruang Lingkup dan Arsitektur');
$body .= $bullet('Framework: Laravel 13 dengan route web dan controller.');
$body .= $bullet('Base URL: mengikuti domain atau host aplikasi yang dikonfigurasi pada environment.');
$body .= $bullet('Transport: HTTP atau HTTPS sesuai konfigurasi deployment.');
$body .= $bullet('Autentikasi: session login Laravel.');
$body .= $bullet('Otorisasi: middleware auth dan role:super_admin, role:direksi, role:karyawan, role:pengajar, atau role:karyawan_pengajar.');
$body .= $bullet('Format response: HTML, redirect dengan flash message, file download, dan JSON untuk endpoint yang ditandai.');

$body .= $heading('3. Daftar Endpoint API JSON');
$body .= $paragraph('Endpoint berikut merupakan API JSON internal dan memerlukan session login. Semua endpoint berada di bawah middleware auth dan group super_admin pada routes/web.php; validasi role tambahan dilakukan di controller.');
$body .= $table(['Method', 'Endpoint', 'Akses', 'Response dan fungsi'], [
    ['GET', '/admin/contracts/api/admin', 'super_admin', 'JSON data kontrak lengkap seluruh pegawai untuk administrasi dan ekspor'],
    ['GET', '/admin/contracts/api/direksi', 'super_admin dan controller role direksi', 'JSON ringkasan total pegawai, pegawai dengan kontrak, dan kontrak berakhir dalam 30 hari'],
    ['GET', '/admin/contracts/api/me', 'authenticated', 'JSON kontrak dan riwayat kontrak pengguna yang sedang login'],
]);
$body .= $paragraph('Catatan penting: endpoint apiDireksi melakukan pemeriksaan role direksi di controller, tetapi route-nya berada di dalam group route super_admin. Akibatnya, pengguna direksi kemungkinan tertolak oleh middleware route sebelum pemeriksaan controller dijalankan. Jika endpoint ini memang ditujukan untuk Direksi, route perlu dipindahkan ke group direksi atau middleware route-nya disesuaikan.');

$body .= $heading('4. Matriks Endpoint Internal');
$body .= $heading('4.1 Autentikasi dan umum', 2);
$body .= $table(['Method', 'Endpoint', 'Akses', 'Fungsi'], [
    ['GET', '/login', 'guest', 'Menampilkan form login'],
    ['POST', '/login', 'guest', 'Memproses login'],
    ['POST', '/logout', 'authenticated', 'Mengakhiri sesi'],
    ['POST', '/impersonation/stop', 'authenticated', 'Menghentikan impersonasi pengguna'],
    ['GET', '/', 'authenticated', 'Redirect ke dashboard sesuai role'],
    ['GET', '/calendar', 'authenticated', 'Menampilkan kalender'],
]);

$body .= $heading('4.2 Super Admin', 2);
$body .= $table(['Method', 'Endpoint', 'Fungsi'], [
    ['GET', '/admin/dashboard', 'Dashboard admin'],
    ['GET/PUT', '/admin/profile', 'Melihat dan memperbarui profil admin'],
    ['GET', '/admin/activity-logs', 'Melihat log aktivitas'],
    ['GET', '/admin/backup', 'Mengunduh backup ZIP berisi JSON'],
    ['GET/POST', '/admin/users dan /admin/users/create', 'Daftar, form, dan tambah pengguna'],
    ['PATCH', '/admin/users/{user}', 'Memperbarui pengguna'],
    ['PATCH', '/admin/users/{user}/role', 'Mengubah role pengguna'],
    ['PATCH', '/admin/users/{user}/password', 'Mereset password pengguna'],
    ['PATCH', '/admin/users/{user}/suspend', 'Mengaktifkan atau menangguhkan pengguna'],
    ['POST/DELETE', '/admin/users/{user}/impersonate atau /{user}', 'Impersonasi atau menghapus pengguna'],
    ['GET/POST/DELETE', '/admin/specializations[/{specialization}]', 'Mengelola spesialisasi'],
    ['GET/POST/DELETE', '/admin/announcements[/{announcement}]', 'Mengelola pengumuman'],
    ['GET/POST/DELETE', '/admin/employees dan resource terkait', 'Mengelola data karyawan'],
    ['GET/POST/DELETE', '/admin/teachers dan resource terkait', 'Mengelola data pengajar'],
    ['POST/GET', '/admin/employees/{employee}/documents dan /admin/documents/{document}/file', 'Upload dan akses dokumen'],
]);

$body .= $heading('4.3 Manajemen kontrak dan laporan', 2);
$body .= $table(['Method', 'Endpoint', 'Fungsi'], [
    ['GET', '/admin/contracts, /data, /monitoring, /expiring, /history', 'Halaman ringkasan, data, monitoring, kontrak berakhir, dan riwayat'],
    ['GET', '/admin/contracts/{nip}', 'Detail kontrak pegawai'],
    ['POST', '/admin/contracts/{nip}/extend', 'Menambah perpanjangan kontrak'],
    ['PUT/DELETE', '/admin/contracts/history/{contract}', 'Memperbarui atau membatalkan perpanjangan'],
    ['GET', '/admin/contracts/export dan /{nip}/export', 'Ekspor seluruh kontrak atau kontrak pegawai'],
    ['GET/POST', '/admin/director/report-request', 'Form dan pengajuan permintaan laporan'],
    ['GET', '/admin/director/dashboard dan /report-history', 'Dashboard dan riwayat laporan'],
    ['GET/DELETE', '/admin/director/report/{report}/download atau /{report}', 'Download atau menghapus laporan'],
]);

$body .= $heading('4.4 Endpoint berdasarkan role', 2);
$body .= $table(['Role', 'Method', 'Endpoint utama', 'Fungsi'], [
    ['Direksi', 'GET/PUT', '/direksi/dashboard, /profile, /contracts, /employees, /teachers', 'Monitoring dan akses baca data organisasi; pembaruan profil'],
    ['Karyawan', 'GET/PUT/POST', '/employee/dashboard, /profile, /contracts, /documents', 'Dashboard, profil, kontrak, dan dokumen sendiri'],
    ['Pengajar', 'GET/PUT/POST/DELETE', '/teacher/dashboard, /profile, /profile/academic, /contracts, /documents, /competencies, /portfolios', 'Profil administratif dan akademik, dokumen, kompetensi, serta portofolio'],
    ['Karyawan Pengajar', 'GET/PUT/POST', '/double-role/dashboard, /profile, /contracts, /competencies, /portfolios', 'Profil dua peran, kontrak, kompetensi, dan portofolio'],
]);

$body .= $heading('5. Keamanan dan Aturan Request');
$body .= $bullet('Login diperlukan untuk seluruh endpoint internal selain GET /login dan POST /login.');
$body .= $bullet('Request POST, PUT, PATCH, dan DELETE dari browser harus menyertakan CSRF token.');
$body .= $bullet('Akses data dibatasi berdasarkan role dan kepemilikan profil atau dokumen.');
$body .= $bullet('Endpoint mutasi umumnya mengembalikan redirect dengan flash message, bukan JSON.');
$body .= $bullet('Validasi gagal dikembalikan ke form sebagai error validasi Laravel.');
$body .= $bullet('Endpoint download dokumen, laporan, backup, dan ekspor harus diuji dengan akun berizin.');
$body .= $bullet('Belum tersedia API key, OAuth2, Sanctum, Passport, atau Bearer token untuk integrasi eksternal.');

$body .= $heading('6. Panduan Integrasi');
$body .= $paragraph('Untuk integrasi dari halaman web internal, gunakan session login yang sama dan sertakan CSRF token pada request mutasi. Untuk konsumsi endpoint JSON, kirim request GET dengan cookie session aktif dan terima response application/json.');
$body .= $paragraph('Untuk integrasi aplikasi mobile atau pihak ketiga, disarankan membuat route API versioned seperti /api/v1, memilih mekanisme token, mendefinisikan format error JSON yang konsisten, dan memisahkan middleware API dari middleware web sebelum endpoint dibuka ke luar sistem.');

$body .= $heading('7. Kesimpulan');
$body .= $paragraph('Sistem telah memiliki endpoint internal yang cukup untuk operasional SDM berbasis web, tetapi belum menyediakan API publik atau API token. Tiga endpoint JSON kontrak dapat digunakan untuk kebutuhan dashboard dan data kontrak internal. Dokumentasi ini menjadi inventaris awal untuk pengujian akses, pemeliharaan route, dan perencanaan API eksternal.');
$body .= $paragraph('Sumber: routes/web.php, app/Http/Controllers/Admin/ContractManagementController.php, dan docs/api.md.', 'Subtitle');

$document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>' . $body . '<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1080" w:right="1080" w:bottom="1080" w:left="1080"/></w:sectPr></w:body></w:document>';
$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:rPr><w:rFonts w:ascii="Aptos"/><w:sz w:val="20"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:rPr><w:b/><w:color w:val="17365D"/><w:sz w:val="40"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Subtitle"><w:name w:val="Subtitle"/><w:rPr><w:color w:val="666666"/><w:sz w:val="20"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="Heading 1"/><w:rPr><w:b/><w:color w:val="1F4E79"/><w:sz w:val="28"/></w:rPr></w:style><w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="Heading 2"/><w:rPr><w:b/><w:color w:val="2F75B5"/><w:sz w:val="24"/></w:rPr></w:style></w:styles>';
$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/></Types>';
$rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>';
$documentRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';

$zip = new ZipArchive();
if ($zip->open($out, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    throw new RuntimeException('Tidak dapat membuat file Word.');
}
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rootRels);
$zip->addFromString('word/_rels/document.xml.rels', $documentRels);
$zip->addFromString('word/styles.xml', $styles);
$zip->addFromString('word/document.xml', $document);
$zip->close();

echo $out . PHP_EOL;
