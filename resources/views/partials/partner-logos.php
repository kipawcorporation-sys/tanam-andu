<?php
/**
 * Partial: Logo Sponsor & Media Partner (Plant For Fun: Kerenceng)
 * Path: resources/views/partials/partner-logos.php
 * 
 * Menampilkan logo resmi dari poster kegiatan:
 * PRESENTED BY: andu, Karsa Kerentjeng, Ethera
 * MEDIA PARTNER: CLK.PNY (Tjitjalengka Poenja), Cicalengka GO
 */
$theme = $logoTheme ?? 'dark'; // 'dark' (untuk background terang), 'light' (untuk background gelap)
$isLight = ($theme === 'light');

$color = $isLight ? '#ffffff' : '#12382c';
$mutedColor = $isLight ? 'rgba(255,255,255,0.7)' : 'rgba(18,56,44,0.7)';
$badgeBg = $isLight ? 'rgba(255,255,255,0.06)' : 'rgba(18,56,44,0.03)';
$badgeHoverBg = $isLight ? 'rgba(255,255,255,0.14)' : 'rgba(18,56,44,0.08)';
$borderCol = $isLight ? 'rgba(255,255,255,0.18)' : 'rgba(18,56,44,0.15)';
$suffix = $isLight ? '-white.png' : '.png';
?>

<div class="partner-logos-container py-2">
    <div class="row g-4 align-items-center justify-content-between">
        
        <!-- PRESENTED BY -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="small fw-bold text-uppercase font-poster" style="color: <?= $color ?>; font-size: 12px; letter-spacing: 1.5px;">
                    Presented By
                </span>
                <span style="height: 1px; flex-grow: 1; background: <?= $borderCol ?>; max-width: 60px;"></span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                
                <!-- Logo 1: andu -->
                <div class="partner-logo-box d-flex align-items-center justify-content-center p-2 px-3 rounded-3 transition" 
                     style="background: <?= $badgeBg ?>; border: 1px solid <?= $borderCol ?>; min-height: 54px;" 
                     title="andu">
                    <img src="<?= asset('assets/images/logo-andu' . $suffix) ?>" 
                         alt="andu" 
                         class="img-fluid" 
                         style="height: 38px; width: auto; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
                </div>

                <!-- Logo 2: Karsa Kerentjeng -->
                <div class="partner-logo-box d-flex align-items-center justify-content-center p-2 px-3 rounded-3 transition" 
                     style="background: <?= $badgeBg ?>; border: 1px solid <?= $borderCol ?>; min-height: 54px;" 
                     title="Karsa Kerentjeng">
                    <img src="<?= asset('assets/images/logo-karsa-kerentjeng' . $suffix) ?>" 
                         alt="Karsa Kerentjeng" 
                         class="img-fluid" 
                         style="height: 38px; width: auto; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
                </div>

                <!-- Logo 3: Ethera -->
                <div class="partner-logo-box d-flex align-items-center justify-content-center p-2 px-3 rounded-3 transition" 
                     style="background: <?= $badgeBg ?>; border: 1px solid <?= $borderCol ?>; min-height: 54px;" 
                     title="Ethera">
                    <img src="<?= asset('assets/images/logo-ethera' . $suffix) ?>" 
                         alt="Ethera" 
                         class="img-fluid" 
                         style="height: 28px; width: auto; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
                </div>

            </div>
        </div>

        <!-- MEDIA PARTNER -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="small fw-bold text-uppercase font-poster" style="color: <?= $color ?>; font-size: 12px; letter-spacing: 1.5px;">
                    Media Partner
                </span>
                <span style="height: 1px; flex-grow: 1; background: <?= $borderCol ?>; max-width: 60px;"></span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">

                <!-- Logo 4: CLK.PNY (Tjitjalengka Poenja) -->
                <div class="partner-logo-box d-flex align-items-center justify-content-center p-2 px-3 rounded-3 transition" 
                     style="background: <?= $badgeBg ?>; border: 1px solid <?= $borderCol ?>; min-height: 54px;" 
                     title="CLK.PNY (Tjitjalengka Poenja)">
                    <img src="<?= asset('assets/images/logo-clkpny' . $suffix) ?>" 
                         alt="CLK.PNY — Tjitjalengka Poenja" 
                         class="img-fluid" 
                         style="height: 38px; width: auto; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
                </div>

                <!-- Logo 5: Cicalengka GO -->
                <div class="partner-logo-box d-flex align-items-center justify-content-center p-2 px-3 rounded-3 transition" 
                     style="background: <?= $badgeBg ?>; border: 1px solid <?= $borderCol ?>; min-height: 54px;" 
                     title="Cicalengka GO">
                    <img src="<?= asset('assets/images/logo-cicalengka-go' . $suffix) ?>" 
                         alt="Cicalengka GO" 
                         class="img-fluid" 
                         style="height: 38px; width: auto; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));">
                </div>

            </div>
        </div>

    </div>
</div>
