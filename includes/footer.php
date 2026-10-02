<?php
/**
 * Footer مشترك
 */
?>
        </div><!-- /page-content -->

        <!-- فوتر المنظومة والموقع الموحد -->
        <footer class="site-footer">
            <div class="footer-container">
                <div class="footer-top-grid">
                    <!-- العمود 1: عن المنظومة -->
                    <div class="footer-col">
                        <div class="footer-col-title">
                            <i class="fas fa-cubes"></i>
                            <span>منظومة كود التطور</span>
                        </div>
                        <p class="footer-about-text">
                            منظومة تقنية عربية رائدة أسسها المهندس محمد أبو خشريف لتقديم حلول برمجية متقدمة، أكاديميات تعليمية، ومنصات رقمية متخصصة تخدم المطورين والشركات والمستخدم العربي.
                        </p>
                        <a href="<?= BASE_URL ?>ecosystem.php" class="btn btn-ghost btn-sm" style="font-size:0.8rem">
                            <i class="fas fa-network-wired text-accent"></i>
                            <span>استعراض دليل المنظومة بالكامل</span>
                        </a>
                    </div>

                    <!-- العمود 2: منصات المنظومة التابعة -->
                    <div class="footer-col">
                        <div class="footer-col-title">
                            <i class="fas fa-globe"></i>
                            <span>منصات ومشاريع المنظومة</span>
                        </div>
                        <ul class="footer-links-list">
                            <li><a href="https://code-elta6ur.com" target="_blank" rel="noopener noreferrer"><i class="fas fa-graduation-cap"></i> كود التطور (الموقع الرئيسي)</a></li>
                            <li><a href="https://portfolio.code-elta6ur.net" target="_blank" rel="noopener noreferrer"><i class="fas fa-id-card"></i> AI Portfolio Builder</a></li>
                            <li><a href="https://code-ora.com" target="_blank" rel="noopener noreferrer"><i class="fas fa-laptop-code"></i> Code Ora (كود اورا)</a></li>
                            <li><a href="https://baccalaureate.code-elta6ur.sy" target="_blank" rel="noopener noreferrer"><i class="fas fa-book-reader"></i> منصة البكالوريا الذكية</a></li>
                            <li><a href="https://code-elta6ur.net" target="_blank" rel="noopener noreferrer"><i class="fas fa-cubes-stacked"></i> كود التطور للبرمجيات والحلول</a></li>
                            <li><a href="https://hardwarebase.code-elta6ur.com" target="_blank" rel="noopener noreferrer"><i class="fas fa-microchip"></i> هاردوير بيس (HardwareBase)</a></li>
                        </ul>
                    </div>

                    <!-- العمود 3: أقسام المنصة -->
                    <div class="footer-col">
                        <div class="footer-col-title">
                            <i class="fas fa-th-large"></i>
                            <span>أقسام وحاسبات سريعة</span>
                        </div>
                        <ul class="footer-links-list">
                            <li><a href="<?= BASE_URL ?>#category-block-finance"><i class="fas fa-coins"></i> حاسبات المال والرواتب</a></li>
                            <li><a href="<?= BASE_URL ?>#category-block-home"><i class="fas fa-home"></i> حاسبات البناء والتشطيب</a></li>
                            <li><a href="<?= BASE_URL ?>#category-block-energy"><i class="fas fa-bolt"></i> الطاقة الشمسية والكهرباء</a></li>
                            <li><a href="<?= BASE_URL ?>#category-block-education"><i class="fas fa-graduation-cap"></i> حاسبات الدراسة والتعليم</a></li>
                            <li><a href="<?= BASE_URL ?>#category-block-text"><i class="fas fa-font"></i> أدوات النصوص والمحررات</a></li>
                            <li><a href="<?= BASE_URL ?>#category-block-dev"><i class="fas fa-code"></i> أدوات المطورين والبرمجة</a></li>
                        </ul>
                    </div>

                    <!-- العمود 4: المطور الرئيسي -->
                    <div class="footer-col">
                        <div class="footer-col-title">
                            <i class="fas fa-user-tie"></i>
                            <span>المطور الرئيسي والمؤسس</span>
                        </div>
                        <div class="footer-developer-card">
                            <div class="footer-dev-name">م. محمد أبو خشريف</div>
                            <div class="footer-dev-desc">مهندس برمجيات ومطور Full-Stack ومؤسس منظومة كود التطور ومشاريعها الرقمية.</div>
                            <div class="footer-dev-links">
                                <a href="https://mohammad.code-elta6ur.com" target="_blank" rel="noopener noreferrer" class="footer-dev-btn" title="معرض الأعمال الشخصي">
                                    <i class="fas fa-briefcase text-accent"></i>
                                    <span>Portfolio</span>
                                </a>
                                <a href="https://github.com/mokashreef" target="_blank" rel="noopener noreferrer" class="footer-dev-btn" title="حساب GitHub">
                                    <i class="fab fa-github"></i>
                                    <span>GitHub</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="footer-copyright">
                        <span>جميع الحقوق محفوظة لمنظومة <strong>كود التطور</strong> © <?= date('Y') ?></span>
                        <span style="margin:0 0.5rem">|</span>
                        <span>تطوير وإشراف: <strong>م. محمد أبو خشريف</strong></span>
                    </div>
                    <div class="footer-bottom-links">
                        <a href="<?= BASE_URL ?>ecosystem.php" style="color:var(--text-muted);text-decoration:none;margin-left:1rem">دليل المنظومة</a>
                        <a href="<?= BASE_URL ?>" style="color:var(--text-muted);text-decoration:none">الرئيسية</a>
                    </div>
                </div>
            </div>
        </footer>
    </main><!-- /main-content -->

    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
    <?php if (isset($extraJS)): ?>
    <script src="<?= BASE_URL ?>assets/js/<?= $extraJS ?>"></script>
    <?php endif; ?>
    <?php if (isset($inlineJS)): ?>
    <script><?= $inlineJS ?></script>
    <?php endif; ?>
</body>
</html>
