<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <span class="badge bg-success-subtle fw-bold px-3 py-2 rounded-pill mb-2" style="color:#0a3622;">Pusat Bantuan</span>
                <h2 class="fw-bold">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                <p class="text-muted">Temukan jawaban atas berbagai pertanyaan seputar program kami</p>
            </div>

            <?php if (!empty($faqs)): ?>
                <div class="accordion shadow-sm rounded-4 overflow-hidden" id="mainFaqAccordion">
                    <?php foreach ($faqs as $i => $faq): ?>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header" id="faqHeading<?= $i ?>">
                                <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?> fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?= $i ?>">
                                    <span class="badge bg-success-subtle me-2 text-capitalize" style="color:#0a3622;"><?= e($faq['category']) ?></span>
                                    <?= e($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="faqCollapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#mainFaqAccordion">
                                <div class="accordion-body text-secondary lh-base">
                                    <?= nl2br(e($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <p class="text-muted">Belum ada daftar FAQ saat ini.</p>
                </div>
            <?php endif; ?>

            <div class="text-center mt-5 p-4 bg-white rounded-4 shadow-sm border">
                <h5 class="fw-bold mb-2">Masih memiliki pertanyaan lain?</h5>
                <p class="text-muted small mb-3">Tim kami siap membantu Anda menjawab berbagai pertanyaan seputar program dan donasi.</p>
                <a href="https://wa.me/6281200000001" target="_blank" class="btn btn-success fw-bold rounded-pill px-4">
                    <i class="bi bi-whatsapp me-2"></i> Hubungi Kami via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

