<?php
session_start();
require '../login/koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'guru'){
    header("Location: ../login/login.php"); exit;
}

$id_user = isset($_SESSION['IDUser']) ? $_SESSION['IDUser'] : '';
if(empty($id_user)) {
    $ses_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $cek_user = mysqli_query($koneksi, "SELECT IDUser FROM users WHERE Username = '".mysqli_real_escape_string($koneksi,$ses_username)."'");
    if($data_user = mysqli_fetch_assoc($cek_user)){ $id_user = $data_user['IDUser']; $_SESSION['IDUser'] = $id_user; }
}

$query_status_sandi = mysqli_query($koneksi, "SELECT WajibUbahPassword FROM users WHERE IDUser = '$id_user'");
$status_sandi = mysqli_fetch_assoc($query_status_sandi);
$wajib_ubah = $status_sandi['WajibUbahPassword'] ?? 0;

$cek_maint = mysqli_query($koneksi, "SELECT Nilai FROM pengaturan WHERE Kunci = 'maintenance'");
$maint = mysqli_fetch_assoc($cek_maint);
if(isset($maint['Nilai']) && $maint['Nilai'] == '1') { session_destroy(); header("Location: ../login/login.php"); exit; }

$query_guru = mysqli_query($koneksi, "SELECT * FROM guru WHERE IDUser = '$id_user'");
if ($query_guru && mysqli_num_rows($query_guru) > 0) {
    $guru      = mysqli_fetch_assoc($query_guru);
    $id_guru   = $guru['IDGuru'];
    $nama_guru = $guru['NamaGuru'];
    $nip_guru  = !empty($guru['NIP_NUPTK']) ? $guru['NIP_NUPTK'] : '-';
} else { $id_guru=''; $nama_guru='Guru Pengampu'; $nip_guru='-'; }

$query_mapel = mysqli_query($koneksi, "SELECT * FROM mapel WHERE IDGuru='$id_guru' ORDER BY Kelas ASC, NamaMapel ASC");
$total_mapel = $query_mapel ? mysqli_num_rows($query_mapel) : 0;
$q_kelas = mysqli_query($koneksi, "SELECT COUNT(DISTINCT Kelas) as n FROM mapel WHERE IDGuru='$id_guru'");
$total_kelas_diajar = mysqli_fetch_assoc($q_kelas)['n'] ?? 0;
$q_bd = mysqli_query($koneksi, "SELECT COUNT(*) as n FROM pengumpulan_tugas pt JOIN tugas t ON pt.IDTugas=t.IDTugas JOIN mapel m ON t.IDMapel=m.IDMapel WHERE m.IDGuru='$id_guru' AND pt.Status='belum_dinilai'");
$total_belum_dinilai = mysqli_fetch_assoc($q_bd)['n'] ?? 0;

$id_mapel_pertama = '';
if($total_mapel > 0){
    mysqli_data_seek($query_mapel,0);
    $r = mysqli_fetch_assoc($query_mapel);
    $id_mapel_pertama = $r['IDMapel'];
    mysqli_data_seek($query_mapel,0);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Guru – LMS SMKN 1 Wongsorejo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
:root{--primary:#4f46e5;--primary-dk:#3730a3;--grad:linear-gradient(135deg,#4f46e5,#3730a3);}
*{font-family:'Segoe UI',system-ui,sans-serif;}
body{background:#f0f2f8;transition:background .3s;}
/* Navbar */
.navbar-custom{background:var(--grad);box-shadow:0 4px 12px rgba(0,0,0,.12);}
/* Hero */
.hero-card{background:linear-gradient(135deg,#1e1e2f,#111119);color:#fff;border:none;border-radius:22px;box-shadow:0 12px 35px rgba(0,0,0,.18);position:relative;overflow:hidden;}
.hero-card::before{content:'';position:absolute;top:-60px;right:-30px;width:320px;height:320px;background:rgba(79,70,229,.18);filter:blur(55px);border-radius:50%;pointer-events:none;}
.hero-card::after{content:'';position:absolute;bottom:-40px;left:-20px;width:200px;height:200px;background:rgba(6,182,212,.12);filter:blur(45px);border-radius:50%;pointer-events:none;}
.stat-box{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.09);border-radius:14px;padding:14px 20px;text-align:center;}
.stat-box .val{font-size:1.9rem;font-weight:800;}
/* Quick actions */
.qa-card{background:#fff;border-radius:16px;padding:18px 12px;text-align:center;box-shadow:0 3px 10px rgba(0,0,0,.05);transition:.25s;text-decoration:none;color:#333;display:block;border:2px solid transparent;}
.qa-card:hover{border-color:var(--primary);transform:translateY(-4px);color:var(--primary);}
.qa-icon{width:50px;height:50px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 8px;}
/* Mapel card */
.mapel-card{border:none;border-radius:18px;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.05);transition:all .3s cubic-bezier(.25,.8,.25,1);overflow:hidden;}
.mapel-card:hover{transform:translateY(-7px);box-shadow:0 16px 32px rgba(79,70,229,.14);}
.mapel-img-wrap{height:140px;background:#e9ecef;position:relative;overflow:hidden;}
.mapel-img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.mapel-card:hover .mapel-img{transform:scale(1.07);}
.kelas-badge{position:absolute;top:10px;left:10px;background:rgba(0,0,0,.65);backdrop-filter:blur(6px);color:#fff;border-radius:20px;padding:4px 12px;font-size:.74rem;font-weight:600;}
.alert-badge{position:absolute;top:10px;right:10px;background:#ef4444;color:#fff;border-radius:20px;padding:4px 10px;font-size:.7rem;font-weight:700;}
.btn-kelola{background:var(--grad);color:#fff;border:none;border-radius:20px;padding:7px 18px;font-size:.82rem;font-weight:600;transition:.25s;text-decoration:none;}
.btn-kelola:hover{opacity:.85;transform:scale(1.04);color:#fff;}
/* Empty */
.empty-box{background:#fff;border-radius:20px;padding:4rem 2rem;box-shadow:0 8px 25px rgba(0,0,0,.04);}
/* Dark mode */
[data-bs-theme="dark"] body{background:#0f0f1a;color:#e0e0e0;}
[data-bs-theme="dark"] .qa-card,[data-bs-theme="dark"] .mapel-card,[data-bs-theme="dark"] .empty-box{background:#1a1a2e;}
[data-bs-theme="dark"] .qa-card{color:#e0e0e0;}
[data-bs-theme="dark"] footer{background:#12121e!important;border-top:1px solid #2d2d4e;}
[data-bs-theme="dark"] .text-dark{color:#e0e0e0!important;}
@media(max-width:576px){.stat-box .val{font-size:1.4rem;}.hero-card{border-radius:16px;}}
</style>
<script>document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||'light');</script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container py-1">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="guru.php">
            <i class="bi bi-person-workspace fs-4"></i> PANEL GURU LMS
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="topNav">
            <ul class="navbar-nav align-items-center gap-1 mt-2 mt-lg-0">
                <li class="nav-item"><a class="nav-link active fw-semibold px-3" href="guru.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li class="nav-item">
                    <button class="btn btn-link text-white p-2" id="themeToggle" onclick="switchTheme()" style="text-decoration:none;">
                        <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                    </button>
                </li>
                <li class="nav-item dropdown ms-1">
                    <a class="nav-link dropdown-toggle bg-white bg-opacity-10 rounded-pill px-3 text-white d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <img src="data:image/jpeg;base64,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<?= urlencode($nama_guru) ?>&background=fff&color=4f46e5" class="rounded-circle" width="26" height="26">
                        <?= htmlspecialchars(explode(' ',trim($nama_guru))[0]) ?>
                        <?php if($total_belum_dinilai>0): ?><span class="badge bg-danger rounded-pill" style="font-size:.62rem;"><?= $total_belum_dinilai ?></span><?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li class="px-3 py-2">
                            <div class="fw-bold" style="font-size:.88rem;"><?= htmlspecialchars($nama_guru) ?></div>
                            <div class="text-muted" style="font-size:.74rem;">NIP: <?= htmlspecialchars($nip_guru) ?></div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item text-danger" href="../login/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4 py-md-5">

    <!-- HERO -->
    <div class="card hero-card p-4 p-md-5 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <img src="image/profile_guru.avif=<?= urlencode($nama_guru) ?>&size=128&background=4f46e5&color=fff"
                     class="rounded-4 border border-4 border-white border-opacity-20 shadow" width="90" height="90">
            </div>
            <div class="col">
                <span class="badge rounded-pill fw-semibold mb-2 small px-3 py-2" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">
                    <i class="bi bi-patch-check-fill me-1"></i> Tenaga Pendidik Aktif
                </span>
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($nama_guru) ?></h3>
                <p class="text-white-50 mb-0 small">NIP / NUPTK: <strong><?= htmlspecialchars($nip_guru) ?></strong></p>
            </div>
            <div class="col-xl-5 col-lg-6 col-12">
                <div class="row g-2">
                    <div class="col-4"><div class="stat-box"><div class="text-white-50 small mb-1">Mapel</div><div class="val text-warning"><?= $total_mapel ?></div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="text-white-50 small mb-1">Kelas</div><div class="val text-info"><?= $total_kelas_diajar ?></div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="text-white-50 small mb-1">Dinilai</div><div class="val text-danger"><?= $total_belum_dinilai ?></div></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <?php if($total_mapel > 0): ?>
    <div class="mb-2 ps-1">
        <span class="fw-bold" style="font-size:1rem;"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</span>
        <span class="text-muted ms-2" style="font-size:.78rem;">Pilih mapel di bawah untuk aksi spesifik</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <a href="kelola_mapel.php?id_mapel=<?= $id_mapel_pertama ?>&tab=materi" class="qa-card">
                <div class="qa-icon" style="background:#eff6ff;"><i class="bi bi-file-earmark-plus text-primary"></i></div>
                <div style="font-size:.82rem;font-weight:600;">Upload Materi</div>
            </a>
        </div>
        <div class="col-6 col-sm-3">
            <a href="kelola_mapel.php?id_mapel=<?= $id_mapel_pertama ?>&tab=tugas" class="qa-card">
                <div class="qa-icon" style="background:#f0fdf4;"><i class="bi bi-journal-plus" style="color:#059669;"></i></div>
                <div style="font-size:.82rem;font-weight:600;">Buat Tugas</div>
            </a>
        </div>
        <div class="col-6 col-sm-3">
            <a href="kelola_mapel.php?id_mapel=<?= $id_mapel_pertama ?>&tab=quiz" class="qa-card">
                <div class="qa-icon" style="background:#fefce8;"><i class="bi bi-patch-question-fill" style="color:#d97706;"></i></div>
                <div style="font-size:.82rem;font-weight:600;">Buat Quiz</div>
            </a>
        </div>
        <div class="col-6 col-sm-3">
            <a href="kelola_mapel.php?id_mapel=<?= $id_mapel_pertama ?>&tab=penilaian" class="qa-card">
                <div class="qa-icon" style="background:#fdf4ff;"><i class="bi bi-pencil-square" style="color:#9333ea;"></i></div>
                <div style="font-size:.82rem;font-weight:600;">Penilaian</div>
                <?php if($total_belum_dinilai>0): ?><div style="font-size:.7rem;color:#ef4444;font-weight:700;"><?= $total_belum_dinilai ?> menunggu</div><?php endif; ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- HEADER MAPEL -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="fw-bold" style="font-size:1rem;"><i class="bi bi-collection-fill me-2" style="color:var(--primary);"></i>Ruang Kelas & Mapel Diampu</span><br>
            <span class="text-muted" style="font-size:.78rem;">Klik "Kelola Kelas" untuk unggah materi, buat tugas, quiz & penilaian</span>
        </div>
    </div>

    <?php if($total_mapel==0): ?>
    <div class="empty-box text-center">
        <div style="font-size:4rem;color:#6366f1;" class="mb-3"><i class="bi bi-folder-symlink-fill"></i></div>
        <h5 class="fw-bold">Belum Mengampu Mata Pelajaran</h5>
        <p class="text-muted mx-auto small" style="max-width:480px;">Akun Anda belum dipetakan ke mata pelajaran oleh Administrator. Hubungi admin untuk mendapatkan akses kelas.</p>
        <button class="btn btn-primary rounded-pill px-4" style="background:var(--primary);border:none;" onclick="location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i> Cek Ulang
        </button>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php
        mysqli_data_seek($query_mapel,0);
        while($mapel = mysqli_fetch_assoc($query_mapel)):
            $cover = !empty($mapel['Gambar']) ? '../assets/img/cover/'.$mapel['Gambar'] : 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=600';
            $q_bm = mysqli_query($koneksi,"SELECT COUNT(*) as n FROM pengumpulan_tugas pt JOIN tugas t ON pt.IDTugas=t.IDTugas WHERE t.IDMapel='{$mapel['IDMapel']}' AND pt.Status='belum_dinilai'");
            $belum_mapel = mysqli_fetch_assoc($q_bm)['n'] ?? 0;
            $q_jm = mysqli_query($koneksi,"SELECT COUNT(*) as n FROM materi WHERE IDMapel='{$mapel['IDMapel']}'");
            $jml_materi = mysqli_fetch_assoc($q_jm)['n'] ?? 0;
            $q_jt = mysqli_query($koneksi,"SELECT COUNT(*) as n FROM tugas WHERE IDMapel='{$mapel['IDMapel']}'");
            $jml_tugas = mysqli_fetch_assoc($q_jt)['n'] ?? 0;
        ?>
        <div class="col-xl-4 col-md-6">
            <div class="card mapel-card h-100">
                <div class="mapel-img-wrap">
                    <img src="<?= htmlspecialchars($cover) ?>" class="mapel-img" alt="cover">
                    <span class="kelas-badge"><i class="bi bi-building-fill me-1 text-info"></i><?= htmlspecialchars($mapel['Kelas']??'-') ?></span>
                    <?php if($belum_mapel>0): ?><span class="alert-badge"><i class="bi bi-exclamation me-1"></i><?= $belum_mapel ?> Belum Dinilai</span><?php endif; ?>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold mb-1 text-truncate" title="<?= htmlspecialchars($mapel['NamaMapel']) ?>"><?= htmlspecialchars($mapel['NamaMapel']) ?></h5>
                    <p class="text-muted small mb-3 flex-grow-1"><?= htmlspecialchars($mapel['Deskripsi']??'Kelola seluruh konten pembelajaran untuk kelas ini.') ?></p>
                    <div class="d-flex gap-3 mb-3" style="font-size:.74rem;color:#94a3b8;">
                        <span><i class="bi bi-file-earmark-text me-1"></i><?= $jml_materi ?> Materi</span>
                        <span><i class="bi bi-journal-check me-1"></i><?= $jml_tugas ?> Tugas</span>
                        <?php if(!empty($mapel['TahunAjaran'])): ?><span><i class="bi bi-calendar me-1"></i><?= htmlspecialchars($mapel['TahunAjaran']) ?></span><?php endif; ?>
                    </div>
                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <span class="small text-muted"><i class="bi bi-people me-1"></i>Ruang Guru</span>
                        <a href="kelolaMapel.php?id_mapel=<?= $mapel['IDMapel'] ?>" class="btn-kelola">
                            Kelola Kelas <i class="bi bi-gear-fill ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</div>

<footer class="bg-white border-top py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0"><p class="text-muted small mb-0">&copy; 2026 <strong>LMS SMKN 1 Wongsorejo</strong>. All Rights Reserved.</p></div>
            <div class="col-md-6 text-center text-md-end"><p class="text-muted small mb-0">Developed with ❤️ by <strong>V</strong> & Kelompok.</p></div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateIcon(t){document.getElementById('themeIcon').className=t==='dark'?'bi bi-moon-stars-fill fs-5':'bi bi-sun-fill fs-5';}
updateIcon(localStorage.getItem('theme')||'light');
function switchTheme(){const c=document.documentElement.getAttribute('data-bs-theme'),n=c==='dark'?'light':'dark';document.documentElement.setAttribute('data-bs-theme',n);localStorage.setItem('theme',n);updateIcon(n);}
</script>
<?php if(isset($wajib_ubah)&&$wajib_ubah==1): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded",function(){
    Swal.fire({title:'Keamanan Akun!',text:'Login pertama. Silakan tentukan password baru sekarang.',icon:'warning',input:'password',inputAttributes:{placeholder:'Password baru...',required:'required',autocapitalize:'off'},showCancelButton:false,confirmButtonText:'Simpan & Buka Akses',confirmButtonColor:'#4f46e5',allowOutsideClick:false,allowEscapeKey:false,
    preConfirm:(p)=>{if(!p||!p.trim()){Swal.showValidationMessage('Password tidak boleh kosong!');return false;}
    return fetch('proses_ubah_sandi_paksa.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'password_baru='+encodeURIComponent(p)}).then(async r=>{const t=await r.text();try{const d=JSON.parse(t);if(d.status==='sukses')return d;Swal.showValidationMessage('Gagal: '+d.pesan);return false;}catch(e){Swal.showValidationMessage('Error: '+t.substring(0,100));return false;}}).catch(e=>{Swal.showValidationMessage('Koneksi gagal: '+e.message);return false;});}
    }).then(r=>{if(r.isConfirmed)Swal.fire({title:'Berhasil!',icon:'success',timer:1500,showConfirmButton:false}).then(()=>location.reload());});
});
</script>
<?php endif; ?>
</body>
</html>