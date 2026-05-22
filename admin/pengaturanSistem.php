<?php
session_start();
require '../login/koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../login/login.php");
    exit;
}

// Simulasi data pengaturan (Nanti bisa kamu buatkan tabel 'pengaturan' di database)
$app_name = "LMS SMKN 1 Wongsorejo";
$kepsek_nama = "Drs. H. Budi Santoso, M.Pd";
$kepsek_nip = "19650817 199002 1 003";
$tahun_aktif = "2025/2026";
$semester_aktif = "Genap";
$maintenance_mode = false;
$default_password = "password123";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        
        .header-red { background: #dc3545; color: white; padding: 2rem 0 3.8rem 0; margin: 0; position: relative; }
        .header-red h2 { margin: 0; font-weight: 700; font-size: 1.7rem; letter-spacing: -0.5px; }
        .main-content { margin-top: -2.8rem; position: relative; z-index: 10; }
        
        /* Styling Tabs Pengaturan */
        .nav-pills .nav-link {
            color: #495057;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        .nav-pills .nav-link.active {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 4px 10px rgba(220,53,69,0.2);
        }
        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f1f3f5;
        }
        
        .settings-card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="admin.php"> 
                <i class="bi bi-shield-lock-fill me-2"></i> PANEL ADMIN LMS
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="topNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=Administrator&background=fff&color=dc3545" class="rounded-circle me-2 border border-2 border-white" width="30" height="30">
                            Administrator
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="../login/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-0">
                
                <div class="header-red">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2>Pengaturan Sistem</h2>
                                <p class="mb-0 text-white-50 small">Konfigurasi utama aplikasi, tahun ajaran, dan keamanan dasar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container main-content pb-5">
                    <div class="card settings-card">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="row">
                                <div class="col-md-3 border-end pe-md-4 mb-4 mb-md-0">
                                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        <button class="nav-link active text-start" data-bs-toggle="pill" data-bs-target="#tab-identitas" type="button">
                                            <i class="bi bi-building me-2"></i> Identitas Sekolah
                                        </button>
                                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-akademik" type="button">
                                            <i class="bi bi-calendar3 me-2"></i> Tahun Akademik
                                        </button>
                                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-sistem" type="button">
                                            <i class="bi bi-shield-check me-2"></i> Sistem & Keamanan
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-9 ps-md-4">
                                    <form action="#" method="POST" enctype="multipart/form-data">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            
                                            <div class="tab-pane fade show active" id="tab-identitas" role="tabpanel">
                                                <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Profil & Identitas Aplikasi</h5>
                                                
                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold small text-muted">Nama Aplikasi / Sekolah</label>
                                                        <input type="text" name="app_name" class="form-control bg-light" value="<?= $app_name ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted">Nama Kepala Sekolah</label>
                                                        <input type="text" name="kepsek_nama" class="form-control bg-light" value="<?= $kepsek_nama ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted">NIP Kepala Sekolah</label>
                                                        <input type="text" name="kepsek_nip" class="form-control bg-light" value="<?= $kepsek_nip ?>">
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <label class="form-label fw-bold small text-muted">Logo Sekolah</label>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="bg-light border rounded p-2 text-center" style="width: 80px; height: 80px;">
                                                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                            </div>
                                                            <input type="file" name="logo" class="form-control w-50" accept="image/*">
                                                        </div>
                                                        <div class="form-text mt-2">Format PNG berlatar transparan disarankan. Maksimal 1MB.</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="tab-akademik" role="tabpanel">
                                                <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Konfigurasi Tahun Akademik</h5>
                                                
                                                <div class="alert alert-warning border-0 shadow-sm mb-4">
                                                    <i class="bi bi-info-circle-fill me-2"></i> <strong>Perhatian:</strong> Mengubah tahun ajaran akan mempengaruhi data tugas dan materi yang tampil pada dashboard siswa & guru.
                                                </div>

                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted">Tahun Ajaran Aktif</label>
                                                        <select name="tahun_aktif" class="form-select bg-light">
                                                            <option value="2024/2025" <?= ($tahun_aktif == '2024/2025') ? 'selected' : '' ?>>2024/2025</option>
                                                            <option value="2025/2026" <?= ($tahun_aktif == '2025/2026') ? 'selected' : '' ?>>2025/2026</option>
                                                            <option value="2026/2027" <?= ($tahun_aktif == '2026/2027') ? 'selected' : '' ?>>2026/2027</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small text-muted">Semester Aktif</label>
                                                        <select name="semester_aktif" class="form-select bg-light">
                                                            <option value="Ganjil" <?= ($semester_aktif == 'Ganjil') ? 'selected' : '' ?>>Semester Ganjil</option>
                                                            <option value="Genap" <?= ($semester_aktif == 'Genap') ? 'selected' : '' ?>>Semester Genap</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="tab-sistem" role="tabpanel">
                                                <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Kontrol Sistem & Keamanan</h5>
                                                
                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold small text-muted">Pengumuman Global Dashboard</label>
                                                        <textarea name="pengumuman" class="form-control bg-light" rows="2" placeholder="Contoh: Ujian Akhir Semester dimulai pada tanggal 12 Juni 2026."></textarea>
                                                        <div class="form-text">Jika diisi, pesan ini akan muncul sebagai banner kuning di paling atas layar semua Siswa dan Guru. Kosongkan jika tidak ada pengumuman.</div>
                                                    </div>

                                                    <div class="col-md-6 mt-4">
                                                        <label class="form-label fw-bold small text-muted">Batas Maksimal Upload Tugas</label>
                                                        <select name="max_upload" class="form-select bg-light">
                                                            <option value="5">5 MB</option>
                                                            <option value="10" selected>10 MB</option>
                                                            <option value="20">20 MB</option>
                                                            <option value="50">50 MB</option>
                                                        </select>
                                                        <div class="form-text">Membatasi ukuran file yang bisa diunggah siswa agar server tidak cepat penuh.</div>
                                                    </div>
                                                    
                                                    <div class="col-12 mt-4 pt-3 border-top">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="fw-bold mb-1 text-danger">Mode Pemeliharaan (Maintenance)</h6>
                                                                <span class="text-muted small">Jika diaktifkan, siswa dan guru tidak dapat login ke dalam sistem.</span>
                                                            </div>
                                                            <div class="form-check form-switch fs-4">
                                                                <input class="form-check-input" type="checkbox" role="switch" name="maintenance" <?= $maintenance_mode ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mt-5 pt-3 border-top d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm" onclick="event.preventDefault(); Swal.fire('Tersimpan!', 'Pengaturan berhasil diperbarui (Simulasi)', 'success');">
                                                <i class="bi bi-save2 me-2"></i> Simpan Perubahan
                                            </button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>