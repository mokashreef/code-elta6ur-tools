<?php
/**
 * سجل الأدوات المركزي الموحد (Tool Registry)
 * يحتوي على جميع الأدوات مع التصنيفات، الكلمات المفتاحية، ومعلومات الـ SEO
 * Code Elta6ur Tools
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}

/**
 * التصنيفات المعتمدة للمنصة
 */
function getAppCategories() {
    return [
        'finance' => [
            'id' => 'finance',
            'name' => 'المال والأعمال والتجارة',
            'short_name' => 'المال والأعمال',
            'icon' => 'fa-coins',
            'desc' => 'حاسبات الرواتب، الأرباح، التكاليف، والضرائب والعمولات'
        ],
        'home' => [
            'id' => 'home',
            'name' => 'المنزل والبناء',
            'short_name' => 'المنزل والبناء',
            'icon' => 'fa-home',
            'desc' => 'حاسبات الدهان، البلاط، الإسمنت، وتشطيب وبناء المنازل'
        ],
        'energy' => [
            'id' => 'energy',
            'name' => 'الكهرباء والطاقة',
            'short_name' => 'الكهرباء والطاقة',
            'icon' => 'fa-bolt',
            'desc' => 'حاسبات الطاقة الشمسية، البطاريات، المولدات، واستهلاك الأجهزة'
        ],
        'auto' => [
            'id' => 'auto',
            'name' => 'السيارات والسفر',
            'short_name' => 'السيارات والسفر',
            'icon' => 'fa-car',
            'desc' => 'حاسبات استهلاك البنزين، تكلفة السفر، وتقسيم نفقات الرحلات'
        ],
        'education' => [
            'id' => 'education',
            'name' => 'الدراسة والتعليم',
            'short_name' => 'التعليم والدراسة',
            'icon' => 'fa-graduation-cap',
            'desc' => 'حاسبات المعدل التراكمي، النسب المئوية، وخطط المذاكرة'
        ],
        'life' => [
            'id' => 'life',
            'name' => 'الحياة اليومية والمال الشخصي',
            'short_name' => 'الحياة اليومية',
            'icon' => 'fa-wallet',
            'desc' => 'حاسبات تقسيم الراتب، ميزانية الأسرة، وأهداف الادخار'
        ],
        'text' => [
            'id' => 'text',
            'name' => 'النصوص والمحتوى',
            'short_name' => 'النصوص والمحتوى',
            'icon' => 'fa-paragraph',
            'desc' => 'محررات Markdown، عدادات الكلمات، وتنظيف النصوص وتنسيقها'
        ],
        'dev' => [
            'id' => 'dev',
            'name' => 'أدوات المطورين',
            'short_name' => 'المطورين',
            'icon' => 'fa-code',
            'desc' => 'منسقات الأكواد، محولات JSON/Base64، ومولدات التشفير'
        ],
        'career' => [
            'id' => 'career',
            'name' => 'العمل الحر والمهني',
            'short_name' => 'العمل الحر',
            'icon' => 'fa-rocket',
            'desc' => 'مولدات السيرة الذاتية، عروض العمل، ونماذج التقديم والـ README'
        ],
        'generators' => [
            'id' => 'generators',
            'name' => 'المولدات السريعة',
            'short_name' => 'المولدات',
            'icon' => 'fa-magic',
            'desc' => 'توليد كلمات المرور، الألوان، وأسماء المشاريع والدومينات'
        ],
        'productivity' => [
            'id' => 'productivity',
            'name' => 'الإنتاجية وإدارة المهام',
            'short_name' => 'الإنتاجية',
            'icon' => 'fa-check-circle',
            'desc' => 'قوائم المهام، تتبع الوقت، الفواتير، وتدوين الملاحظات'
        ]
    ];
}

/**
 * قائمة العملات المدعومة في الأدوات المالية
 */
function getSupportedCurrencies() {
    return [
        'SAR' => ['code' => 'SAR', 'name' => 'ريال سعودي', 'symbol' => 'ر.س'],
        'AED' => ['code' => 'AED', 'name' => 'درهم إماراتي', 'symbol' => 'د.إ'],
        'QAR' => ['code' => 'QAR', 'name' => 'ريال قطري', 'symbol' => 'ر.ق'],
        'KWD' => ['code' => 'KWD', 'name' => 'دينار كويتي', 'symbol' => 'د.ك'],
        'BHD' => ['code' => 'BHD', 'name' => 'دينار بحريني', 'symbol' => 'د.ب'],
        'OMR' => ['code' => 'OMR', 'name' => 'ريال عماني', 'symbol' => 'ر.ع'],
        'JOD' => ['code' => 'JOD', 'name' => 'دينار أردني', 'symbol' => 'د.أ'],
        'EGP' => ['code' => 'EGP', 'name' => 'جنيه مصري', 'symbol' => 'ج.م'],
        'SYP' => ['code' => 'SYP', 'name' => 'ليرة سورية', 'symbol' => 'ل.س'],
        'IQD' => ['code' => 'IQD', 'name' => 'دينار عراقي', 'symbol' => 'د.ع'],
        'LBP' => ['code' => 'LBP', 'name' => 'ليرة لبنانية', 'symbol' => 'ل.ل'],
        'YER' => ['code' => 'YER', 'name' => 'ريال يمني', 'symbol' => 'ر.ي'],
        'DZD' => ['code' => 'DZD', 'name' => 'دينار جزائري', 'symbol' => 'د.ج'],
        'MAD' => ['code' => 'MAD', 'name' => 'درهم مغربي', 'symbol' => 'د.م'],
        'TND' => ['code' => 'TND', 'name' => 'دينار تونسي', 'symbol' => 'د.ت'],
        'LYD' => ['code' => 'LYD', 'name' => 'دينار ليبي', 'symbol' => 'د.ل'],
        'SDG' => ['code' => 'SDG', 'name' => 'جنيه سوداني', 'symbol' => 'ج.س'],
        'TRY' => ['code' => 'TRY', 'name' => 'ليرة تركية', 'symbol' => '₺'],
        'USD' => ['code' => 'USD', 'name' => 'دولار أمريكي', 'symbol' => '$'],
        'EUR' => ['code' => 'EUR', 'name' => 'يورو', 'symbol' => '€'],
        'GBP' => ['code' => 'GBP', 'name' => 'جنيه إسترليني', 'symbol' => '£']
    ];
}

/**
 * السجل الكامل لجميع الأدوات
 */
function getMasterToolsRegistry() {
    static $registry = null;
    if ($registry !== null) {
        return $registry;
    }

    $raw = [
        // ============================================
        // A) المال والعمل والتجارة (30 أداة)
        // ============================================
        'net-salary-calculator' => [
            'name' => 'حاسبة صافي الراتب بعد الخصومات',
            'description' => 'احسب صافي راتبك الشهري بدقة بعد خصم التأمينات الاجتماعية والضرائب والخصومات الإضافية',
            'category' => 'finance',
            'icon' => 'fa-money-bill-wave',
            'keywords' => ['راتب', 'مرتب', 'صافي', 'اجمالي', 'خصومات', 'تأمينات', 'ضريبة', 'معاش', 'أجر', 'salary', 'net salary'],
            'featured' => true, 'popular' => true,
            'related' => ['hourly-wage-calculator', 'daily-wage-calculator', 'overtime-calculator', 'salary-division-calculator']
        ],
        'hourly-wage-calculator' => [
            'name' => 'حاسبة الراتب بالساعة',
            'description' => 'حساب قيمة أجر الساعة الواحدة بناءً على راتبك الشهري وساعات وأيام العمل',
            'category' => 'finance',
            'icon' => 'fa-clock',
            'keywords' => ['ساعة', 'أجر الساعة', 'راتب ساعي', 'ساعات العمل', 'hourly rate', 'wage'],
            'related' => ['net-salary-calculator', 'daily-wage-calculator', 'overtime-calculator', 'freelancer-hourly-rate-calculator']
        ],
        'daily-wage-calculator' => [
            'name' => 'حاسبة الراتب اليومي',
            'description' => 'حساب أجرك اليومي ومعدل الدخل في اليوم الواحد بدقة',
            'category' => 'finance',
            'icon' => 'fa-calendar-day',
            'keywords' => ['يومية', 'أجر يومي', 'راتب يومي', 'حساب اليومية', 'daily wage'],
            'related' => ['hourly-wage-calculator', 'net-salary-calculator', 'salary-to-hourly-daily-converter']
        ],
        'employee-cost-calculator' => [
            'name' => 'حاسبة تكلفة الموظف الحقيقية على الشركة',
            'description' => 'حساب إجمالي التكلفة الحقيقية لتوظيف موظف متضمنة التأمينات، البدلات، الإجازات ومصاريف العمل',
            'category' => 'finance',
            'icon' => 'fa-user-tie',
            'keywords' => ['تكلفة الموظف', 'توظيف', 'تأمينات', 'بدلات', 'شركات', 'موارد بشرية', 'employee cost'],
            'related' => ['employee-hourly-cost-calculator', 'net-salary-calculator', 'freelancer-project-price-calculator']
        ],
        'employee-hourly-cost-calculator' => [
            'name' => 'حاسبة تكلفة الساعة للموظف',
            'description' => 'حساب التكلفة الفعلية للساعة الإنتاجية للموظف على صاحب العمل بعد استبعاد الإجازات والغياب',
            'category' => 'finance',
            'icon' => 'fa-business-time',
            'keywords' => ['تكلفة ساعة الموظف', 'ساعات الإنتاج', 'تكلفة فعلية', 'تكلفة العمل'],
            'related' => ['employee-cost-calculator', 'hourly-wage-calculator', 'project-hours-calculator']
        ],
        'overtime-calculator' => [
            'name' => 'حاسبة العمل الإضافي',
            'description' => 'حساب قيمة ساعات العمل الإضافي والأوفر تايم في الأيام العادية والعطلات الرسمية',
            'category' => 'finance',
            'icon' => 'fa-stopwatch-20',
            'keywords' => ['اوفر تايم', 'عمل اضافي', 'ساعات اضافية', 'بدل اضافي', 'overtime'],
            'related' => ['hourly-wage-calculator', 'net-salary-calculator', 'daily-wage-calculator']
        ],
        'commission-calculator' => [
            'name' => 'حاسبة العمولة',
            'description' => 'حساب مبالغ العمولات الثابتة والنسبية على المبيعات والصفقات التجارية',
            'category' => 'finance',
            'icon' => 'fa-percent',
            'keywords' => ['عمولة', 'نسبة', 'مبيعات', 'حساب العمولة', 'ارباح', 'commission'],
            'related' => ['sales-commission-calculator', 'profit-margin-calculator', 'discount-tax-calculator']
        ],
        'sales-commission-calculator' => [
            'name' => 'حاسبة عمولة مندوب المبيعات',
            'description' => 'حساب عمولة ممثل ومندوب المبيعات بنظام الشرائح والتارغت المستهدف',
            'category' => 'finance',
            'icon' => 'fa-handshake',
            'keywords' => ['مندوب مبيعات', 'تارغت', 'شرائح العمولة', 'بونص', 'مكافأة مبيعات'],
            'related' => ['commission-calculator', 'profit-margin-calculator', 'break-even-calculator']
        ],
        'profit-margin-calculator' => [
            'name' => 'حاسبة هامش الربح',
            'description' => 'حساب هامش الربح الإجمالي ونسبة الربح إلى التكلفة أو سعر البيع (Markup vs Margin)',
            'category' => 'finance',
            'icon' => 'fa-chart-pie',
            'keywords' => ['هامش الربح', 'نسبة الربح', 'مارجن', 'مارك اب', 'profit margin', 'markup'],
            'featured' => true,
            'related' => ['net-profit-margin-calculator', 'product-selling-price-calculator', 'break-even-calculator']
        ],
        'net-profit-margin-calculator' => [
            'name' => 'حاسبة هامش الربح الحقيقي بعد المصاريف',
            'description' => 'حساب صافي الأرباح بعد خصم مصاريف التشغيل، التسويق، الإيجار والضرائب',
            'category' => 'finance',
            'icon' => 'fa-chart-line',
            'keywords' => ['صافي الربح', 'ربح حقيقي', 'مصاريف تشغيلية', 'نفقات', 'net profit'],
            'related' => ['profit-margin-calculator', 'real-product-cost-calculator', 'break-even-calculator']
        ],
        'break-even-calculator' => [
            'name' => 'حاسبة نقطة التعادل',
            'description' => 'احسب عدد الوحدات أو حجم الإيرادات المطلوب لتغطية التكاليف الثابتة والمتغيرة وبدء الربح',
            'category' => 'finance',
            'icon' => 'fa-balance-scale',
            'keywords' => ['نقطة التعادل', 'تكاليف ثابتة', 'تكاليف متغيرة', 'break even', 'تعادل'],
            'related' => ['profit-margin-calculator', 'product-selling-price-calculator', 'real-product-cost-calculator']
        ],
        'product-selling-price-calculator' => [
            'name' => 'حاسبة سعر بيع المنتج',
            'description' => 'تحديد سعر البيع المثالي لمنتجك لتحقيق هامش الربح المستهدف بدقة',
            'category' => 'finance',
            'icon' => 'fa-tag',
            'keywords' => ['تسعير', 'سعر البيع', 'تسعير المنتجات', 'حساب السعر', 'pricing'],
            'related' => ['real-product-cost-calculator', 'profit-margin-calculator', 'shipping-cost-share-calculator']
        ],
        'real-product-cost-calculator' => [
            'name' => 'حاسبة تكلفة المنتج الحقيقية',
            'description' => 'حساب التكلفة الشاملة للوحدة: المواد الأولية، التصنيع، التغليف، الهالك والتخزين',
            'category' => 'finance',
            'icon' => 'fa-boxes',
            'keywords' => ['تكلفة المنتج', 'تكلفة الوحدة', 'مواد اولية', 'تصنيع', 'تغليف', 'cost of goods'],
            'related' => ['product-selling-price-calculator', 'shipping-cost-share-calculator', 'ecommerce-profit-calculator']
        ],
        'shipping-cost-share-calculator' => [
            'name' => 'حاسبة تكلفة الشحن ضمن سعر المنتج',
            'description' => 'توزيع تكاليف الشحن الدولي أو المحلي على كل قطعة بحسب وزنها أو قيمتها',
            'category' => 'finance',
            'icon' => 'fa-shipping-fast',
            'keywords' => ['شحن', 'تكلفة الشحن', 'توزيع الشحن', 'شحن دولي', 'جمارك', 'shipping cost'],
            'related' => ['real-product-cost-calculator', 'product-selling-price-calculator', 'ecommerce-profit-calculator']
        ],
        'ecommerce-profit-calculator' => [
            'name' => 'حاسبة الربح من بيع منتج أونلاين',
            'description' => 'حساب أرباح التجارة الإلكترونية بعد عمولات الدفع الإلكتروني، الشحن، الإعلانات والتغليف',
            'category' => 'finance',
            'icon' => 'fa-shopping-cart',
            'keywords' => ['متجر الكتروني', 'دروبشيبينغ', 'شحن', 'بوابة دفع', 'تجارة الكترونية', 'ecommerce'],
            'featured' => true,
            'related' => ['gateway-shipping-profit-calculator', 'online-store-profit-calculator', 'roas-calculator']
        ],
        'discount-tax-calculator' => [
            'name' => 'حاسبة الخصم والضريبة',
            'description' => 'حساب السعر النهائي بعد تطبيق نسبة الخصم والضريبة مع بيان التوفير وقيمة الضريبة',
            'category' => 'finance',
            'icon' => 'fa-tags',
            'keywords' => ['تخفيضات', 'خصم', 'ضريبة', 'عروض', 'اوفر', 'discount', 'tax'],
            'related' => ['vat-calculator', 'commission-calculator', 'product-selling-price-calculator']
        ],
        'vat-calculator' => [
            'name' => 'حاسبة ضريبة القيمة المضافة',
            'description' => 'حساب ضريبة القيمة المضافة (VAT) إضافةً إلى السعر أو استخراجها من السعر الشامل',
            'category' => 'finance',
            'icon' => 'fa-receipt',
            'keywords' => ['ضريبة القيمة المضافة', 'فاتورة', 'ضريبة', 'vat', '15%', '5%'],
            'popular' => true,
            'related' => ['discount-tax-calculator', 'invoice-generator', 'net-profit-margin-calculator']
        ],
        'roas-calculator' => [
            'name' => 'حاسبة العائد على الإنفاق الإعلاني (ROAS)',
            'description' => 'قياس كفاءة حملاتك الإعلانية على فيسبوك، جوجل وتيك توك والعائد المالي لكل دولار مصروف',
            'category' => 'finance',
            'icon' => 'fa-bullhorn',
            'keywords' => ['اعلان', 'حملات اعلانية', 'روك', 'roas', 'تسويق', 'اعلان ممول', 'google ads'],
            'related' => ['cac-calculator', 'roi-calculator', 'ecommerce-profit-calculator']
        ],
        'cac-calculator' => [
            'name' => 'حاسبة تكلفة اكتساب العميل (CAC)',
            'description' => 'احسب تكلفة استقطاب العميل الواحد بناءً على ميزانيات التسويق والمبيعات',
            'category' => 'finance',
            'icon' => 'fa-user-plus',
            'keywords' => ['اكتساب العميل', 'تكلفة العميل', 'cac', 'عملاء جدد', 'تسويق'],
            'related' => ['ltv-calculator', 'roas-calculator', 'roi-calculator']
        ],
        'ltv-calculator' => [
            'name' => 'حاسبة القيمة الدائمة للعميل (LTV)',
            'description' => 'تقدير إجمالي الإيراد أو الربح المتوقع من العميل الواحد طوال فترة تعامله معك',
            'category' => 'finance',
            'icon' => 'fa-infinity',
            'keywords' => ['قيمة العميل', 'عمر العميل', 'ltv', 'customer lifetime value', 'ولاء العملاء'],
            'related' => ['cac-calculator', 'roas-calculator', 'roi-calculator']
        ],
        'roi-calculator' => [
            'name' => 'حاسبة العائد على الاستثمار (ROI)',
            'description' => 'حساب نسبة ومقدار العائد الاستثماري لأي مشروع أو فرصة مالية',
            'category' => 'finance',
            'icon' => 'fa-funnel-dollar',
            'keywords' => ['استثمار', 'عائد', 'مشاريع', 'جدوى', 'ارباح استثمار', 'roi'],
            'featured' => true,
            'related' => ['payback-period-calculator', 'roas-calculator', 'break-even-calculator']
        ],
        'payback-period-calculator' => [
            'name' => 'حاسبة فترة استرداد الاستثمار',
            'description' => 'حساب الوقت المطلوب بالشهور والسنوات لاسترداد رأس المال المستثمر في المشروع',
            'category' => 'finance',
            'icon' => 'fa-hourglass-half',
            'keywords' => ['فترة الاسترداد', 'استرداد راس المال', 'payback period', 'دراسة جدوى'],
            'related' => ['roi-calculator', 'break-even-calculator', 'solar-payback-calculator']
        ],
        'freelancer-hourly-rate-calculator' => [
            'name' => 'حاسبة سعر الساعة للفريلانسر',
            'description' => 'تحديد سعرك بالساعة كمستقل بناءً على دخلك الشهري المستهدف، المصاريف، وساعات العمل الفعلية',
            'category' => 'career',
            'icon' => 'fa-user-astronaut',
            'keywords' => ['فريلانسر', 'عمل حر', 'سعر الساعة', 'مستقل', 'freelance hourly rate'],
            'popular' => true,
            'related' => ['freelancer-project-price-calculator', 'hourly-wage-calculator', 'dev-pricing-calculator']
        ],
        'freelancer-project-price-calculator' => [
            'name' => 'حاسبة سعر المشروع للفريلانسر',
            'description' => 'تسعير المشاريع البرمجية أو التصميمية بنظام السعر الثابت (Fixed Price) مع هامش الطوارئ',
            'category' => 'career',
            'icon' => 'fa-briefcase',
            'keywords' => ['سعر المشروع', 'تسعير المشاريع', 'عرض سعر', 'مستقل', 'project pricing'],
            'related' => ['freelancer-hourly-rate-calculator', 'project-hours-calculator', 'proposal-generator']
        ],
        'project-hours-calculator' => [
            'name' => 'حاسبة عدد ساعات المشروع',
            'description' => 'تقدير ساعات عمل المشروع بناءً على المهام والتعقيد وإضافة نسبة الأمان والاجتماعات',
            'category' => 'career',
            'icon' => 'fa-history',
            'keywords' => ['تقدير الساعات', 'ساعات المشروع', 'وقت المشروع', 'project hours'],
            'related' => ['freelancer-project-price-calculator', 'time-tracker', 'freelancer-hourly-rate-calculator']
        ],
        'design-pricing-calculator' => [
            'name' => 'حاسبة تسعير خدمات التصميم',
            'description' => 'تسعير الهويات البصرية، تصاميم السوشيال ميديا، واجهات المستخدم، ومطبوعات الجرافيك',
            'category' => 'career',
            'icon' => 'fa-paint-brush',
            'keywords' => ['تسعير تصميم', 'شعار', 'لوجو', 'هوية بصرية', 'سوشيال ميديا', 'ui ux'],
            'related' => ['freelancer-project-price-calculator', 'dev-pricing-calculator', 'social-media-pricing-calculator']
        ],
        'dev-pricing-calculator' => [
            'name' => 'حاسبة تسعير خدمات البرمجة',
            'description' => 'تسعير برمجة المواقع والمتاجر والتطبيقات وحساب تكلفة الباك إند والفرونت إند وAPIs',
            'category' => 'career',
            'icon' => 'fa-laptop-code',
            'keywords' => ['تسعير برمجة', 'تطبيق', 'موقع', 'متجر', 'برمجة خاصة', 'web dev pricing'],
            'related' => ['freelancer-project-price-calculator', 'design-pricing-calculator', 'project-hours-calculator']
        ],
        'social-media-pricing-calculator' => [
            'name' => 'حاسبة تسعير إدارة حسابات السوشيال ميديا',
            'description' => 'حساب باقات إدارة المنصات، كتابة المحتوى، النشر والرد على الرسائل شهريًا',
            'category' => 'career',
            'icon' => 'fa-share-alt-square',
            'keywords' => ['سوشيال ميديا', 'ادارة حسابات', 'انستغرام', 'تسويق الكتروني', 'social media management'],
            'related' => ['design-pricing-calculator', 'freelancer-project-price-calculator', 'roas-calculator']
        ],
        'online-store-profit-calculator' => [
            'name' => 'حاسبة أرباح المتجر الإلكتروني',
            'description' => 'حساب صافي أرباح مبيعات متجرك الشهري بعد خصم تكلفة البضاعة، المنصة، الشحن والإعلانات',
            'category' => 'finance',
            'icon' => 'fa-store',
            'keywords' => ['ارباح متجر', 'سلة', 'زد', 'شوبيفاي', 'تجارة الكترونية', 'ecommerce profit'],
            'related' => ['ecommerce-profit-calculator', 'gateway-shipping-profit-calculator', 'break-even-calculator']
        ],
        'gateway-shipping-profit-calculator' => [
            'name' => 'حاسبة الأرباح بعد بوابة الدفع والشحن والإعلانات',
            'description' => 'حساب الربح الصافي الدقيق لكل طلبية بعد عمولة بوابة الدفع (Stripe/Paypal/Mada) والشحن',
            'category' => 'finance',
            'icon' => 'fa-credit-card',
            'keywords' => ['بوابة دفع', 'مدى', 'فيزا', 'شحن وتوصيل', 'عمولة البنك', 'payment gateway'],
            'related' => ['ecommerce-profit-calculator', 'online-store-profit-calculator', 'product-selling-price-calculator']
        ],

        // ============================================
        // B) المنزل والبناء (30 أداة)
        // ============================================
        'marriage-cost-calculator' => [
            'name' => 'حاسبة تكلفة الزواج',
            'description' => 'حساب تكاليف الزواج الشاملة: المهر، الصالة، الذهب، الضيافة، وتجهيزات الحفل',
            'category' => 'home',
            'icon' => 'fa-heart',
            'keywords' => ['زواج', 'عرس', 'فرح', 'تكاليف الزواج', 'مهر', 'تجهيز زواج', 'wedding cost'],
            'featured' => true,
            'related' => ['apartment-furnishing-cost-calculator', 'family-monthly-budget-calculator', 'savings-goal-calculator']
        ],
        'apartment-furnishing-cost-calculator' => [
            'name' => 'حاسبة تكلفة تجهيز شقة للزواج',
            'description' => 'تقدير تكلفة أثاث وأجهزة شقة الزوجية: الصالون، غرفة النوم، المطبخ، والأجهزة الكهربائية',
            'category' => 'home',
            'icon' => 'fa-couch',
            'keywords' => ['تجهيز شقة', 'اثاث شقة', 'غرفة نوم', 'اجهزة منزلية', 'صالون', 'فرش شقة'],
            'related' => ['marriage-cost-calculator', 'house-finishing-cost-calculator', 'furniture-quantity-calculator']
        ],
        'house-building-cost-calculator' => [
            'name' => 'حاسبة تكلفة بناء منزل',
            'description' => 'تقدير تكلفة بناء عظم ومفتاح للمنزل بحسب المساحة وعدد الطوابق ونوع التشطيب',
            'category' => 'home',
            'icon' => 'fa-building',
            'keywords' => ['بناء بيت', 'تكلفة البناء', 'عظم', 'تسليم مفتاح', 'مقاولات', 'building cost'],
            'popular' => true,
            'related' => ['house-finishing-cost-calculator', 'cement-calculator', 'block-calculator']
        ],
        'house-finishing-cost-calculator' => [
            'name' => 'حاسبة تكلفة تشطيب منزل',
            'description' => 'حساب تكلفة تشطيب الشقة أو الفيلا: سباكة، كهرباء، لياسة، دهان، وأرضيات',
            'category' => 'home',
            'icon' => 'fa-tools',
            'keywords' => ['تشطيب', 'ديكور', 'سباكة', 'كهرباء منازل', 'لياسة', 'finishing cost'],
            'related' => ['house-building-cost-calculator', 'paint-calculator', 'floor-tiles-calculator']
        ],
        'furniture-quantity-calculator' => [
            'name' => 'حاسبة كمية الأثاث المطلوبة',
            'description' => 'تحديد القطع المناسبة للغرف والمساحات لتجنب الازدحام وضمان سهولة الحركة',
            'category' => 'home',
            'icon' => 'fa-chair',
            'keywords' => ['قطع الاثاث', 'فرش الغرف', 'تأثيث', 'توزيع الاثاث'],
            'related' => ['room-furniture-area-calculator', 'carpet-area-calculator', 'apartment-furnishing-cost-calculator']
        ],
        'room-furniture-area-calculator' => [
            'name' => 'حاسبة مساحة الغرفة المطلوبة للأثاث',
            'description' => 'حساب المساحة الصافية المتبقية وممرات الحركة بعد وضع الأثاث في الغرفة',
            'category' => 'home',
            'icon' => 'fa-vector-square',
            'keywords' => ['مساحة الغرفة', 'ممرات الحركة', 'مساحة الاثاث', 'مخطط الغرفة'],
            'related' => ['furniture-quantity-calculator', 'carpet-area-calculator', 'wall-area-calculator']
        ],
        'paint-calculator' => [
            'name' => 'حاسبة كمية الدهان',
            'description' => 'حساب عدد سطلاّت ولترات الدهان المطلوبة للجدران والأسقف مع عدد الأوجه ونسبة الهدر',
            'category' => 'home',
            'icon' => 'fa-fill-drip',
            'keywords' => ['دهان', 'بوية', 'طلاء', 'سطل بوية', 'لتر دهان', 'paint calculator'],
            'popular' => true,
            'related' => ['putty-calculator', 'wall-area-calculator', 'wallpaper-calculator']
        ],
        'putty-calculator' => [
            'name' => 'حاسبة كمية المعجون',
            'description' => 'حساب كمية معجون الجدران وسكاكين الفيلر لتجهيز الحوائط قبل الطلاء',
            'category' => 'home',
            'icon' => 'fa-trowel',
            'keywords' => ['معجون', 'معجون جدران', 'فيلر', 'تأسيس دهان', 'putty'],
            'related' => ['paint-calculator', 'wall-area-calculator', 'gypsum-board-calculator']
        ],
        'gypsum-board-calculator' => [
            'name' => 'حاسبة كمية الجبس بورد',
            'description' => 'حساب عدد ألواح الجبسوم بورد والهياكل المعدنية والبراغي للأسقف والجدران المعلقة',
            'category' => 'home',
            'icon' => 'fa-layer-group',
            'keywords' => ['جبس بورد', 'اسقف معلقة', 'الواح جبس', 'ديكور جبس', 'gypsum board'],
            'related' => ['ceiling-area-calculator', 'wall-area-calculator', 'insulation-calculator']
        ],
        'cement-calculator' => [
            'name' => 'حاسبة كمية الإسمنت',
            'description' => 'حساب عدد أكياس الإسمنت والطن للخرسانة المسلحة، المونة، واللياسة',
            'category' => 'home',
            'icon' => 'fa-cubes',
            'keywords' => ['اسمنت', 'اكياس اسمنت', 'طن اسمنت', 'خرسانة', 'صبة', 'cement'],
            'featured' => true,
            'related' => ['sand-calculator', 'gravel-calculator', 'house-building-cost-calculator']
        ],
        'sand-calculator' => [
            'name' => 'حاسبة كمية الرمل',
            'description' => 'حساب حجم الرمل بالمتر المكعب وعدد الشاحنات للخرسانة والبناء والمحارة',
            'category' => 'home',
            'icon' => 'fa-mountain',
            'keywords' => ['رمل', 'متر رمل', 'رمل بناء', 'قلاب رمل', 'sand'],
            'related' => ['cement-calculator', 'gravel-calculator', 'block-calculator']
        ],
        'gravel-calculator' => [
            'name' => 'حاسبة كمية الحصى (السن)',
            'description' => 'حساب كمية الزلط والحصى بالمتر المكعب للخلطة الخرسانية والأساسات',
            'category' => 'home',
            'icon' => 'fa-gem',
            'keywords' => ['حصى', 'زلط', 'سن', 'خرسانة مسلحة', 'gravel'],
            'related' => ['cement-calculator', 'sand-calculator', 'house-building-cost-calculator']
        ],
        'block-calculator' => [
            'name' => 'حاسبة كمية البلوك',
            'description' => 'حساب عدد حبات البلوك والخرسانة المفرغة لبناء الجدران مع خصم الأبواب والنوافذ',
            'category' => 'home',
            'icon' => 'fa-th-large',
            'keywords' => ['بلوك', 'بلك', 'طابوق', 'جدران', 'بناء حائط', 'block'],
            'related' => ['brick-calculator', 'cement-calculator', 'wall-area-calculator']
        ],
        'brick-calculator' => [
            'name' => 'حاسبة كمية الطوب',
            'description' => 'حساب عدد قوالب الطوب الأحمر أو الأسمنتي بالمتر المربع أو التكعيب مع نسبة الهدر',
            'category' => 'home',
            'icon' => 'fa-square',
            'keywords' => ['طوب', 'طوب احمر', 'بناء طوب', 'الف طوبة', 'brick'],
            'related' => ['block-calculator', 'cement-calculator', 'wall-area-calculator']
        ],
        'floor-tiles-calculator' => [
            'name' => 'حاسبة كمية البلاط',
            'description' => 'حساب عدد كراتين وقطع البلاط للأرضيات بدقة بحسب أبعاد البلاطة والمساحة',
            'category' => 'home',
            'icon' => 'fa-border-all',
            'keywords' => ['بلاط', 'كرتونة بلاط', 'تبليط', 'ارضيات', 'tiles'],
            'popular' => true,
            'related' => ['ceramic-calculator', 'tile-adhesive-calculator', 'grout-calculator']
        ],
        'ceramic-calculator' => [
            'name' => 'حاسبة كمية السيراميك والبورسلان',
            'description' => 'حساب مساحة وكراتين السيراميك والبورسلان للحمامات والمطابخ والأرضيات',
            'category' => 'home',
            'icon' => 'fa-grip-horizontal',
            'keywords' => ['سيراميك', 'بورسلان', 'حمامات', 'مطابخ', 'ceramic'],
            'related' => ['floor-tiles-calculator', 'tile-adhesive-calculator', 'grout-calculator']
        ],
        'tile-adhesive-calculator' => [
            'name' => 'حاسبة كمية الغراء للبلاط',
            'description' => 'حساب عدد أكياس غراء السيراميك والبورسلان اللازمة للمساحة',
            'category' => 'home',
            'icon' => 'fa-box-open',
            'keywords' => ['غراء بلاط', 'غراء سيراميك', 'لاصق بلاط', 'tile adhesive'],
            'related' => ['floor-tiles-calculator', 'ceramic-calculator', 'grout-calculator']
        ],
        'grout-calculator' => [
            'name' => 'حاسبة كمية الجراوت (الترويبة)',
            'description' => 'حساب كمية روبة الفواصل بين البلاط والسيراميك بالكيلوغرام بناءً على عرض الفاصل',
            'category' => 'home',
            'icon' => 'fa-brush',
            'keywords' => ['ترويبة', 'جراوت', 'فواصل بلاط', 'روبة', 'grout'],
            'related' => ['floor-tiles-calculator', 'ceramic-calculator', 'tile-adhesive-calculator']
        ],
        'wallpaper-calculator' => [
            'name' => 'حاسبة كمية ورق الجدران',
            'description' => 'حساب عدد رولات ورق الحائط المطلوبة مع مراعاة تكرار النقشة (Pattern Repeat)',
            'category' => 'home',
            'icon' => 'fa-scroll',
            'keywords' => ['ورق جدران', 'ورق حائط', 'رول ورق', 'wallpaper'],
            'related' => ['wall-area-calculator', 'paint-calculator', 'skirting-board-calculator']
        ],
        'wall-area-calculator' => [
            'name' => 'حاسبة مساحة الجدران',
            'description' => 'حساب المساحة الإجمالية والصافية للجدران مع خصم فتحات النوافذ والأبواب',
            'category' => 'home',
            'icon' => 'fa-ruler-combined',
            'keywords' => ['مساحة الجدران', 'مساحة الحائط', 'خصم ابواب ونوافذ', 'wall area'],
            'related' => ['paint-calculator', 'wallpaper-calculator', 'ceiling-area-calculator']
        ],
        'ceiling-area-calculator' => [
            'name' => 'حاسبة مساحة السقف',
            'description' => 'حساب مساحة الأسقف العادية والديكورية للدهان والجبس بورد والإضاءة',
            'category' => 'home',
            'icon' => 'fa-shapes',
            'keywords' => ['مساحة السقف', 'سقف', 'دهان سقف', 'جبس سقف', 'ceiling area'],
            'related' => ['wall-area-calculator', 'gypsum-board-calculator', 'paint-calculator']
        ],
        'skirting-board-calculator' => [
            'name' => 'حاسبة طول الوزرة (النعلة)',
            'description' => 'حساب أمتار الوزرات الأرضية المطلوبة للمحيط بعد خصم فتحات الأبواب',
            'category' => 'home',
            'icon' => 'fa-ruler-horizontal',
            'keywords' => ['وزرة', 'نعلة', 'برور', 'اطار ارضيات', 'skirting board'],
            'related' => ['floor-tiles-calculator', 'wall-area-calculator', 'carpet-area-calculator']
        ],
        'insulation-calculator' => [
            'name' => 'حاسبة كمية العزل',
            'description' => 'حساب المساحة ولفات العزل للمباني والأسطح والخزانات',
            'category' => 'home',
            'icon' => 'fa-shield-alt',
            'keywords' => ['عزل', 'عازل', 'لفات عزل', 'insulation'],
            'related' => ['thermal-insulation-calculator', 'waterproofing-calculator', 'roof-slope-calculator']
        ],
        'thermal-insulation-calculator' => [
            'name' => 'حاسبة كمية العزل الحراري',
            'description' => 'حساب ألواح الصوف الصخري أو البوليسترين للجدران والأسقف للحد من استهلاك التكييف',
            'category' => 'home',
            'icon' => 'fa-temperature-low',
            'keywords' => ['عزل حراري', 'صوف صخري', 'فوم', 'بوليسترين', 'حرارة البيت'],
            'related' => ['insulation-calculator', 'waterproofing-calculator', 'ac-consumption-calculator']
        ],
        'waterproofing-calculator' => [
            'name' => 'حاسبة كمية العزل المائي',
            'description' => 'حساب رولات الممبرين، البيتومين، والعوازل الإسمنتية للأسطح والحمامات والخزانات',
            'category' => 'home',
            'icon' => 'fa-tint-slash',
            'keywords' => ['عزل مائي', 'رولات زفتة', 'بيتومين', 'عزل اسطح', 'تسريب مياه'],
            'related' => ['insulation-calculator', 'roof-slope-calculator', 'thermal-insulation-calculator']
        ],
        'roof-slope-calculator' => [
            'name' => 'حاسبة ميلان السطح وتصريف المياه',
            'description' => 'حساب نسبة ومقدار ميل السطح لتصريف مياه الأمطار بكفاءة نحو المزاريب',
            'category' => 'home',
            'icon' => 'fa-chart-area',
            'keywords' => ['ميلان السطح', 'تصريف الامطار', 'ميول السطح', 'roof slope'],
            'related' => ['waterproofing-calculator', 'insulation-calculator', 'house-building-cost-calculator']
        ],
        'stair-calculator' => [
            'name' => 'حاسبة الدرج المعمارية',
            'description' => 'حساب أبعاد الدرج الهندسية: القائمة، النائمة، الزاوية، والراحة المعمارية (قاعدة بلونديل)',
            'category' => 'home',
            'icon' => 'fa-stairs',
            'keywords' => ['درج', 'سلم', 'قائمة ونائمة', 'زاوية الدرج', 'stair calculator'],
            'related' => ['stair-steps-calculator', 'house-building-cost-calculator']
        ],
        'stair-steps-calculator' => [
            'name' => 'حاسبة عدد درجات السلم',
            'description' => 'حساب عدد الدرجات وارتفاع كل درجة بناءً على الارتفاع الصافي بين الطوابق',
            'category' => 'home',
            'icon' => 'fa-sort-amount-up-alt',
            'keywords' => ['عدد الدرجات', 'ارتفاع الدرجة', 'سلم دورين', 'stair steps'],
            'related' => ['stair-calculator', 'house-building-cost-calculator']
        ],
        'carpet-area-calculator' => [
            'name' => 'حاسبة مساحة السجاد والموكيت',
            'description' => 'حساب أمتار السجاد المناسبة للغرفة مع أبعاد الحواف الجمالية أو التغطية الكاملة',
            'category' => 'home',
            'icon' => 'fa-rug',
            'keywords' => ['سجاد', 'موكيت', 'فرش ارضيات', 'مساحة السجاد', 'carpet'],
            'related' => ['room-furniture-area-calculator', 'curtain-calculator', 'floor-tiles-calculator']
        ],
        'curtain-calculator' => [
            'name' => 'حاسبة كمية الستائر والقماش',
            'description' => 'حساب أمتار قماش الستائر اللازمة بناءً على عرض النافذة ونسبة الكشكشة (Fullness)',
            'category' => 'home',
            'icon' => 'fa-blinds',
            'keywords' => ['ستائر', 'قماش ستائر', 'كشكشة', 'تفصيل ستائر', 'curtains'],
            'related' => ['carpet-area-calculator', 'room-furniture-area-calculator', 'wall-area-calculator']
        ],

        // ============================================
        // C) الكهرباء والطاقة (25 أداة)
        // ============================================
        'electricity-consumption-calculator' => [
            'name' => 'حاسبة استهلاك الكهرباء الشاملة',
            'description' => 'احسب إجمالي استهلاكك الشهري بالكيلوواط ساعة وقيمة الفاتورة حسب شرائح الكهرباء',
            'category' => 'energy',
            'icon' => 'fa-bolt',
            'keywords' => ['كهرباء', 'فاتورة الكهرباء', 'كيلوواط', 'كيلو واط ساعي', 'استهلاك المنزل', 'electricity'],
            'featured' => true, 'popular' => true,
            'related' => ['device-monthly-cost-calculator', 'ac-consumption-calculator', 'solar-panels-calculator']
        ],
        'fridge-consumption-calculator' => [
            'name' => 'حاسبة استهلاك الثلاجة للكهرباء',
            'description' => 'حساب استهلاك الثلاجة والفريزر بالكيلوواط وتكلفة تشغيلها شهريًا وسنويًا',
            'category' => 'energy',
            'icon' => 'fa-snowflake',
            'keywords' => ['ثلاجة', 'براد', 'فريزر', 'استهلاك الثلاجة', 'كهرباء البراد'],
            'related' => ['electricity-consumption-calculator', 'device-monthly-cost-calculator', 'inverter-size-calculator']
        ],
        'ac-consumption-calculator' => [
            'name' => 'حاسبة استهلاك المكيف',
            'description' => 'حساب استهلاك مكيف الهواء (طن / BTU / إنفرتر) وتكلفته في الصيف والشتاء',
            'category' => 'energy',
            'icon' => 'fa-wind',
            'keywords' => ['مكيف', 'سبليت', 'طن تبريد', 'btu', 'انفرتر', 'استهلاك المكيف', 'ac power'],
            'popular' => true,
            'related' => ['electricity-consumption-calculator', 'thermal-insulation-calculator', 'solar-system-cost-calculator']
        ],
        'washing-machine-consumption-calculator' => [
            'name' => 'حاسبة استهلاك الغسالة',
            'description' => 'حساب استهلاك الغسالة العادية والأوتوماتيك والمجفف حسب عدد الغسلات ودرجة حرارة الماء',
            'category' => 'energy',
            'icon' => 'fa-soap',
            'keywords' => ['غسالة', 'نشافة', 'غسيل', 'استهلاك الغسالة', 'washing machine'],
            'related' => ['electricity-consumption-calculator', 'water-heater-consumption-calculator']
        ],
        'water-heater-consumption-calculator' => [
            'name' => 'حاسبة استهلاك السخان الكهربائي',
            'description' => 'حساب كهرباء سخان الماء (سخان فوري أو خزان) وتكلفة الاستحمام والاستخدام اليومي',
            'category' => 'energy',
            'icon' => 'fa-shower',
            'keywords' => ['سخان', 'كيزر', 'سخان فوري', 'سخان ماء', 'water heater'],
            'related' => ['electricity-consumption-calculator', 'washing-machine-consumption-calculator']
        ],
        'pc-power-consumption-calculator' => [
            'name' => 'حاسبة استهلاك الكمبيوتر للكهرباء',
            'description' => 'حساب استهلاك أجهزة الكمبيوتر المكتبية والمحمولة، الشاشات، وأجهزة التعدين والألعاب',
            'category' => 'energy',
            'icon' => 'fa-desktop',
            'keywords' => ['كمبيوتر', 'لابتوب', 'تجميعة بي سي', 'استهلاك البي سي', 'باور سبلاي', 'pc power'],
            'related' => ['tv-power-consumption-calculator', 'ups-size-calculator', 'device-monthly-cost-calculator']
        ],
        'tv-power-consumption-calculator' => [
            'name' => 'حاسبة استهلاك التلفزيون',
            'description' => 'حساب استهلاك شاشات LED، OLED، ورسيفر القنوات شهريًا حسب ساعات المشاهدة',
            'category' => 'energy',
            'icon' => 'fa-tv',
            'keywords' => ['تلفزيون', 'شاشة', 'رسيفر', 'oled', 'led', 'استهلاك الشاشة'],
            'related' => ['pc-power-consumption-calculator', 'electricity-consumption-calculator']
        ],
        'device-monthly-cost-calculator' => [
            'name' => 'حاسبة تكلفة تشغيل جهاز شهريًا',
            'description' => 'حساب تكلفة أي جهاز من خلال قدرته بالواط وعدد ساعات تشغيله اليومية وسعر الكيلوواط',
            'category' => 'energy',
            'icon' => 'fa-plug',
            'keywords' => ['واط', 'كيلوواط', 'تكلفة جهاز', 'حساب الواط', 'watt calculator'],
            'popular' => true,
            'related' => ['electricity-consumption-calculator', 'inverter-size-calculator', 'generator-capacity-calculator']
        ],
        'ups-size-calculator' => [
            'name' => 'حاسبة حجم UPS المناسب',
            'description' => 'حساب قدرة الـ UPS بالفولت أمبير (VA) لتشغيل أجهزتك الحساسة وشاشات الكمبيوتر',
            'category' => 'energy',
            'icon' => 'fa-car-battery',
            'keywords' => ['ups', 'يو بي اس', 'فولت امبير', 'انقطاع الكهرباء', 'ups size'],
            'related' => ['ups-runtime-calculator', 'battery-count-calculator', 'inverter-size-calculator']
        ],
        'ups-runtime-calculator' => [
            'name' => 'حاسبة مدة تشغيل UPS',
            'description' => 'حساب المدة الزمنية بالساعات والدقائق التي سيستمر فيها الـ UPS في تشغيل الأحمال',
            'category' => 'energy',
            'icon' => 'fa-hourglass-start',
            'keywords' => ['مدة ups', 'كم ساعة يعمل ups', 'بطارية ups', 'ups runtime'],
            'related' => ['ups-size-calculator', 'battery-charging-time-calculator', 'battery-count-calculator']
        ],
        'inverter-size-calculator' => [
            'name' => 'حاسبة حجم الانفرتر (المحول)',
            'description' => 'حساب حجم الإنفرتر المطلوب بالواط والفولت أمبير لتشغيل المنزل أو المحل مع حمل البدء (Surge)',
            'category' => 'energy',
            'icon' => 'fa-random',
            'keywords' => ['انفرتر', 'محول', 'عاكس', 'inverter', 'حجم الانفرتر', 'طاقة بديلة'],
            'featured' => true,
            'related' => ['battery-count-calculator', 'battery-wiring-calculator', 'solar-panels-calculator']
        ],
        'battery-count-calculator' => [
            'name' => 'حاسبة عدد البطاريات المطلوبة',
            'description' => 'حساب عدد وسعة البطاريات بالأمبير ساعة (Ah) لتغطية ساعات انقطاع الكهرباء بدقة',
            'category' => 'energy',
            'icon' => 'fa-battery-full',
            'keywords' => ['بطاريات', 'امبير', 'امبير ساعي', 'عدد البطاريات', 'بطارية ليثيوم', 'بطارية جل'],
            'popular' => true,
            'related' => ['battery-wiring-calculator', 'battery-charging-time-calculator', 'solar-batteries-calculator']
        ],
        'battery-wiring-calculator' => [
            'name' => 'حاسبة توصيل البطاريات تسلسلي / توازي',
            'description' => 'معرفة طريقة التوصيل (توالي أو توازي أو مختلط) للوصول إلى الجهد المطلوب (12V, 24V, 48V)',
            'category' => 'energy',
            'icon' => 'fa-project-diagram',
            'keywords' => ['توالي', 'توازي', 'توصيل بطاريات', '12 فولت', '24 فولت', '48 فولت', 'battery series parallel'],
            'related' => ['battery-count-calculator', 'battery-charging-time-calculator', 'inverter-size-calculator']
        ],
        'battery-charging-time-calculator' => [
            'name' => 'حاسبة مدة شحن البطارية',
            'description' => 'حساب وقت شحن البطارية بالساعات بناءً على سعتها بالأمبير وقوة تيار الشاحن (الشاحن الكهربائي/الشمسي)',
            'category' => 'energy',
            'icon' => 'fa-charging-station',
            'keywords' => ['شحن البطارية', 'وقت الشحن', 'شاحن', 'امبير الشاحن', 'charging time'],
            'related' => ['battery-count-calculator', 'battery-wiring-calculator', 'ups-runtime-calculator']
        ],
        'generator-fuel-calculator' => [
            'name' => 'حاسبة استهلاك المولد للوقود',
            'description' => 'حساب استهلاك مولد الكهرباء للبنزين أو المازوت (ديزل) باللتر في الساعة واليوم',
            'category' => 'energy',
            'icon' => 'fa-gas-pump',
            'keywords' => ['مولد', 'استهلاك المولد', 'ديزل', 'مازوت', 'بنزين مولد', 'generator fuel'],
            'related' => ['generator-cost-calculator', 'generator-capacity-calculator', 'power-source-comparison-calculator']
        ],
        'generator-cost-calculator' => [
            'name' => 'حاسبة تكلفة تشغيل المولد',
            'description' => 'حساب تكلفة تشغيل المولد الشهرية متضمنة سعر الوقود، الزيت، والصيانة الدورية',
            'category' => 'energy',
            'icon' => 'fa-file-invoice-dollar',
            'keywords' => ['تكلفة المولد', 'مصاريف المولد', 'سعر المازوت', 'صيانة المولد'],
            'related' => ['generator-fuel-calculator', 'power-source-comparison-calculator', 'grid-vs-solar-calculator']
        ],
        'generator-capacity-calculator' => [
            'name' => 'حاسبة القدرة المطلوبة للمولد (KVA)',
            'description' => 'تحديد حجم وقدرة المولد المطلوب بالكيلوواط والـ KVA بناءً على أجهزة البدء والتشغيل',
            'category' => 'energy',
            'icon' => 'fa-tachometer-alt',
            'keywords' => ['قدرة المولد', 'kva', 'كيلو واط مولد', 'حجم المولد المناسب'],
            'related' => ['generator-fuel-calculator', 'generator-cost-calculator', 'inverter-size-calculator']
        ],
        'solar-panels-calculator' => [
            'name' => 'حاسبة الألواح الشمسية',
            'description' => 'حساب عدد وقدرة ألواح الطاقة الشمسية المطلوبة لتغطية احتياج منزلك من الواط يوميًا',
            'category' => 'energy',
            'icon' => 'fa-solar-panel',
            'keywords' => ['الواح شمسية', 'طاقة شمسية', 'عدد الالواح', 'واط لوح', 'solar panels'],
            'featured' => true, 'popular' => true,
            'related' => ['solar-batteries-calculator', 'solar-area-calculator', 'solar-yield-calculator']
        ],
        'solar-batteries-calculator' => [
            'name' => 'حاسبة البطاريات للطاقة الشمسية',
            'description' => 'حساب سعة بنك البطاريات اللازمة لتخزين الطاقة واستخدامها ليلًا أو في الأيام الغائمة',
            'category' => 'energy',
            'icon' => 'fa-car-battery',
            'keywords' => ['بطاريات طاقة شمسية', 'بنك بطاريات', 'تخزين الطاقة', 'solar batteries'],
            'related' => ['solar-panels-calculator', 'battery-count-calculator', 'battery-wiring-calculator']
        ],
        'solar-area-calculator' => [
            'name' => 'حاسبة مساحة الألواح الشمسية',
            'description' => 'حساب المساحة بالمتر المربع المطلوبة على السطح لتركيب منظومة الطاقة الشمسية بدون تظليل',
            'category' => 'energy',
            'icon' => 'fa-border-none',
            'keywords' => ['مساحة الالواح', 'مساحة السطح للطاقة', 'تركيب الواح', 'solar area'],
            'related' => ['solar-panels-calculator', 'roof-slope-calculator', 'solar-yield-calculator']
        ],
        'solar-yield-calculator' => [
            'name' => 'حاسبة إنتاج الألواح الشمسية',
            'description' => 'تقدير الإنتاج اليومي والشهري لمنظومتك الشمسية بالكيلوواط ساعة حسب ساعات ذروة الشمس',
            'category' => 'energy',
            'icon' => 'fa-sun',
            'keywords' => ['انتاج الطاقة الشمسية', 'ساعات الشمس', 'توليد الكهرباء', 'solar yield'],
            'related' => ['solar-panels-calculator', 'solar-system-cost-calculator', 'grid-vs-solar-calculator']
        ],
        'solar-system-cost-calculator' => [
            'name' => 'حاسبة تكلفة المنظومة الشمسية',
            'description' => 'تقدير تكلفة المنظومة الشمسية الكاملة: ألواح، إنفرتر، بطاريات، شاسيهات، كابلات، وتركيب',
            'category' => 'energy',
            'icon' => 'fa-calculator',
            'keywords' => ['تكلفة الطاقة الشمسية', 'اسعار منظومات الطاقة', 'سعر المنظومة', 'solar system cost'],
            'related' => ['solar-payback-calculator', 'solar-panels-calculator', 'power-source-comparison-calculator']
        ],
        'solar-payback-calculator' => [
            'name' => 'حاسبة فترة استرداد الطاقة الشمسية',
            'description' => 'حساب كم سنة تحتاج منظومتك الشمسية لتعويض تكلفة تركيبها مقارنة بفواتير الكهرباء أو المولد',
            'category' => 'energy',
            'icon' => 'fa-hand-holding-usd',
            'keywords' => ['استرداد الطاقة الشمسية', 'توفير الكهرباء', 'الجدوى من الطاقة الشمسية', 'solar payback'],
            'related' => ['solar-system-cost-calculator', 'grid-vs-solar-calculator', 'payback-period-calculator']
        ],
        'grid-vs-solar-calculator' => [
            'name' => 'حاسبة الكهرباء الحكومية مقابل الطاقة الشمسية',
            'description' => 'مقارنة مالية دقيقة بين الاستمرار على شبكة الكهرباء أو التحول الكلي/الجزئي للطاقة الشمسية',
            'category' => 'energy',
            'icon' => 'fa-balance-scale-right',
            'keywords' => ['مقارنة كهرباء', 'طاقة شمسية مقابل شبكة', 'توفير فواتير', 'grid vs solar'],
            'related' => ['solar-payback-calculator', 'solar-system-cost-calculator', 'power-source-comparison-calculator']
        ],
        'power-source-comparison-calculator' => [
            'name' => 'حاسبة المولد مقابل البطاريات مقابل الطاقة الشمسية',
            'description' => 'مقارنة شاملة للتكاليف التشغيلية والتأسيسية لثلاثة حلول للطاقة البديلة لاختيار الأنسب لك',
            'category' => 'energy',
            'icon' => 'fa-sliders-h',
            'keywords' => ['مولد ام طاقة شمسية', 'مقارنة حلول الكهرباء', 'طاقة بديلة', 'بطاريات ام مولد'],
            'featured' => true,
            'related' => ['solar-system-cost-calculator', 'generator-cost-calculator', 'grid-vs-solar-calculator']
        ],

        // ============================================
        // D) السيارات والسفر (11 أداة)
        // ============================================
        'monthly-gas-cost-calculator' => [
            'name' => 'حاسبة تكلفة البنزين الشهرية',
            'description' => 'احسب ميزانية وقود سيارتك شهريًا وسنويًا حسب المسافة المقطوعة وسعر لتر الوقود',
            'category' => 'auto',
            'icon' => 'fa-gas-pump',
            'keywords' => ['بنزين', 'وقود', 'تكلفة البنزين', 'مصروف البنزين', 'monthly gas cost'],
            'featured' => true, 'popular' => true,
            'related' => ['monthly-car-cost-calculator', 'fuel-by-distance-calculator', 'car-consumption-calculator']
        ],
        'monthly-car-cost-calculator' => [
            'name' => 'حاسبة تكلفة السيارة الشهرية',
            'description' => 'حساب تكلفة امتلاك السيارة الحقيقية متضمنة الوقود، التأمين، الصيانة، الزيت، والترخيص',
            'category' => 'auto',
            'icon' => 'fa-car-side',
            'keywords' => ['تكلفة السيارة', 'مصاريف السيارة', 'صيانة سيارة', 'تأمين سيارة', 'monthly car cost'],
            'related' => ['monthly-gas-cost-calculator', 'real-car-cost-calculator', 'buy-car-vs-transport-calculator']
        ],
        'road-trip-cost-calculator' => [
            'name' => 'حاسبة تكلفة الرحلة بالسيارة',
            'description' => 'حساب تكلفة السفر البري بالسيارة متضمنة البنزين، رسوم الطرق، الوجبات، وتكلفة الإقامة',
            'category' => 'auto',
            'icon' => 'fa-route',
            'keywords' => ['رحلة برية', 'سفر بالسيارة', 'تكلفة المشوار', 'بنزين السفر', 'road trip cost'],
            'related' => ['travel-cost-calculator', 'travel-expenses-split-calculator', 'distance-fuel-calculator']
        ],
        'travel-cost-calculator' => [
            'name' => 'حاسبة تكلفة السفر الشاملة',
            'description' => 'تخطيط ميزانية السفر والسياحة: تذاكر الطيران، الفنادق، المصروف اليومي، الفيزا، والتسوق',
            'category' => 'auto',
            'icon' => 'fa-plane-departure',
            'keywords' => ['سفر', 'سياحة', 'تذكرة طيران', 'تكلفة السفر', 'فندق', 'travel cost'],
            'popular' => true,
            'related' => ['travel-expenses-split-calculator', 'road-trip-cost-calculator', 'cost-of-living-calculator']
        ],
        'travel-expenses-split-calculator' => [
            'name' => 'حاسبة تقسيم مصاريف السفر',
            'description' => 'تقسيم نفقات السفر والرحلات الجماعية بين الأصدقاء أو العائلة ومعرفة المستحقات بدقة',
            'category' => 'auto',
            'icon' => 'fa-users',
            'keywords' => ['تقسيم مصاريف', 'قطة سفر', 'حساب الرحلة', 'مصاريف جماعية', 'split travel expenses'],
            'related' => ['travel-cost-calculator', 'restaurant-bill-split-calculator', 'road-trip-cost-calculator']
        ],
        'fuel-by-distance-calculator' => [
            'name' => 'حاسبة الوقود حسب المسافة',
            'description' => 'حساب كمية البنزين اللازمة وتكلفتها لأي مسافة بالكيلومتر بناءً على معدل استهلاك سيارتك',
            'category' => 'auto',
            'icon' => 'fa-tachometer-alt',
            'keywords' => ['بنزين للمسافة', 'كم لتر بنزين', 'حساب الكيلومترات', 'fuel by distance'],
            'related' => ['distance-fuel-calculator', 'car-consumption-calculator', 'monthly-gas-cost-calculator']
        ],
        'distance-fuel-calculator' => [
            'name' => 'حاسبة المسافة والوقود',
            'description' => 'معرفة أقصى مسافة يمكن لسيارتك قطعها بكمية وقود معينة أو بمبلغ محدد',
            'category' => 'auto',
            'icon' => 'fa-map-marked-alt',
            'keywords' => ['المسافة المقطوعة', 'كم يمشيني البنزين', 'تفويلة', 'distance on fuel'],
            'related' => ['fuel-by-distance-calculator', 'car-consumption-calculator', 'monthly-gas-cost-calculator']
        ],
        'car-consumption-calculator' => [
            'name' => 'حاسبة استهلاك السيارة (لتر/100 كم)',
            'description' => 'حساب المعدل الفعلي لاستهلاك وقود سيارتك بدقة (لتر لكل 100 كم أو كم لكل لتر)',
            'category' => 'auto',
            'icon' => 'fa-oil-can',
            'keywords' => ['معدل الاستهلاك', 'لتر لكل 100 كم', 'صرفية البنزين', 'كم لكل لتر', 'car consumption'],
            'related' => ['fuel-by-distance-calculator', 'monthly-gas-cost-calculator', 'gas-vs-ev-calculator']
        ],
        'real-car-cost-calculator' => [
            'name' => 'حاسبة تكلفة السيارة الحقيقية (التكلفة الإجمالية TCO)',
            'description' => 'حساب التكلفة الحقيقية لامتلاك السيارة لسنوات متضمنة إهلاك القيمة (Depreciation) والفرصة البديلة',
            'category' => 'auto',
            'icon' => 'fa-dollar-sign',
            'keywords' => ['اهلاك السيارة', 'تكلفة التملك', 'tco', 'خسارة قيمة السيارة', 'total cost of ownership'],
            'related' => ['monthly-car-cost-calculator', 'buy-car-vs-transport-calculator', 'when-can-i-buy-car-calculator']
        ],
        'buy-car-vs-transport-calculator' => [
            'name' => 'حاسبة شراء سيارة أم المواصلات وتطبيقات النقل',
            'description' => 'مقارنة مالية: هل الأوفر شراء سيارة خاصة أم الاعتماد على أوبر والمواصلات العامة؟',
            'category' => 'auto',
            'icon' => 'fa-taxi',
            'keywords' => ['شراء سيارة ام اوبر', 'مقارنة مواصلات', 'تكلفة اوبر', 'اوفر سيارة ام تاكسي'],
            'related' => ['real-car-cost-calculator', 'monthly-car-cost-calculator', 'gas-vs-ev-calculator']
        ],
        'gas-vs-ev-calculator' => [
            'name' => 'حاسبة سيارة بنزين مقابل سيارة كهربائية',
            'description' => 'مقارنة تكلفة استهلاك الوقود والصيانة بين سيارات البنزين والسيارات الكهربائية (EV)',
            'category' => 'auto',
            'icon' => 'fa-charging-station',
            'keywords' => ['سيارة كهربائية', 'بنزين ام كهرباء', 'شحن سيارة كهرباء', 'توفير السيارة الكهربائية', 'ev vs gas'],
            'related' => ['car-consumption-calculator', 'monthly-gas-cost-calculator', 'real-car-cost-calculator']
        ],

        // ============================================
        // E) الدراسة والتعليم (19 أداة)
        // ============================================
        'grade-percentage-calculator' => [
            'name' => 'حاسبة النسبة من العلامات',
            'description' => 'تحويل مجموع درجاتك وعلاماتك إلى نسبة مئوية مئوية مع التقدير اللفظي (ممتاز، جيد جداً)',
            'category' => 'education',
            'icon' => 'fa-percent',
            'keywords' => ['نسبة مئوية', 'حساب النسبة', 'نسبة الدرجات', 'مجموع الدرجات', 'grade percentage'],
            'featured' => true, 'popular' => true,
            'related' => ['grades-to-percentage-converter', 'final-grade-calculator', 'cumulative-gpa-calculator']
        ],
        'passing-grade-calculator' => [
            'name' => 'حاسبة العلامة المطلوبة للنجاح',
            'description' => 'احسب الدرجة التي تحتاج للحصول عليها في الاختبار النهائي لتضمن النجاح في المادة',
            'category' => 'education',
            'icon' => 'fa-check-double',
            'keywords' => ['درجة النجاح', 'كم يلزمني للنجاح', 'درجة الفاينل', 'اعمال السنة', 'passing grade'],
            'related' => ['target-gpa-calculator', 'final-grade-calculator', 'grade-percentage-calculator']
        ],
        'target-gpa-calculator' => [
            'name' => 'حاسبة العلامة المطلوبة للوصول لمعدل معين',
            'description' => 'حساب الدرجات المطلوبة في المواد المتبقية للوصول إلى التقدير أو المعدل الذي تطمح إليه',
            'category' => 'education',
            'icon' => 'fa-bullseye',
            'keywords' => ['معدل مستهدف', 'تحسين المعدل', 'درجة الامتياز', 'target gpa'],
            'related' => ['passing-grade-calculator', 'cumulative-gpa-calculator', 'next-semester-gpa-calculator']
        ],
        'final-grade-calculator' => [
            'name' => 'حاسبة المعدل النهائي للمادة',
            'description' => 'حساب درجتك النهائية الموزونة بدمج أعمال الفصل، الكويزات، والامتحان النهائي',
            'category' => 'education',
            'icon' => 'fa-calculator',
            'keywords' => ['المعدل النهائي', 'درجة المادة', 'اعمال الفصل', 'final grade'],
            'related' => ['grade-percentage-calculator', 'passing-grade-calculator', 'cumulative-gpa-calculator']
        ],
        'cumulative-gpa-calculator' => [
            'name' => 'حاسبة المعدل التراكمي (GPA)',
            'description' => 'حساب المعدل التراكمي الجامعي من 4 أو 5 أو بالنسبة المئوية مع الساعات المعتمدة',
            'category' => 'education',
            'icon' => 'fa-graduation-cap',
            'keywords' => ['معدل تراكمي', 'gpa', 'معدل جامعي', 'من 4', 'من 5', 'ساعات معتمدة', 'gpa calculator'],
            'featured' => true, 'popular' => true,
            'related' => ['next-semester-gpa-calculator', 'graduation-hours-calculator', 'target-gpa-calculator']
        ],
        'daily-study-hours-calculator' => [
            'name' => 'حاسبة ساعات الدراسة اليومية',
            'description' => 'توزيع ساعات المذاكرة اليومية المطلوبة بحسب عدد المواد وصعوبتها وموعد الاختبارات',
            'category' => 'education',
            'icon' => 'fa-clock',
            'keywords' => ['ساعات المذاكرة', 'دراسة يومية', 'جدول مذاكرة', 'تنظيم الوقت', 'study hours'],
            'related' => ['syllabus-finish-time-calculator', 'exam-countdown-calculator', 'daily-pages-calculator']
        ],
        'syllabus-finish-time-calculator' => [
            'name' => 'حاسبة وقت إنهاء المنهج',
            'description' => 'احسب التاريخ الدقيق الذي ستنهي فيه مراجعة الكتاب أو المنهج بناءً على وتيرتك اليومية',
            'category' => 'education',
            'icon' => 'fa-calendar-check',
            'keywords' => ['انهاء المنهج', 'ختم المادة', 'خطة الدراسة', 'syllabus finish'],
            'related' => ['daily-study-hours-calculator', 'exam-countdown-calculator', 'revision-plan-calculator']
        ],
        'exam-countdown-calculator' => [
            'name' => 'حاسبة الأيام المتبقية للامتحان',
            'description' => 'عداد تنازلي ذكي للأيام والساعات المتبقية على الامتحانات وتوزيع المهام عليها',
            'category' => 'education',
            'icon' => 'fa-hourglass-half',
            'keywords' => ['موعد الامتحان', 'كم باقي للامتحان', 'عد تنازلي للاختبارات', 'exam countdown'],
            'related' => ['daily-study-hours-calculator', 'revision-plan-calculator', 'curriculum-progress-calculator']
        ],
        'revision-plan-calculator' => [
            'name' => 'حاسبة خطة المراجعة الذكية',
            'description' => 'توليد جدول مراجعة مقسم إلى جولات متعددة (مراجعة أولى، ثانية، وحل نماذج سابقة)',
            'category' => 'education',
            'icon' => 'fa-tasks',
            'keywords' => ['خطة مراجعة', 'جدول مراجعة', 'مراجعة نهائية', 'تكرار متباعد', 'revision plan'],
            'related' => ['syllabus-finish-time-calculator', 'daily-pages-calculator', 'memorization-time-calculator']
        ],
        'daily-pages-calculator' => [
            'name' => 'حاسبة عدد الصفحات اليومية',
            'description' => 'حساب كم صفحة يجب قراءتها أو حفظها يوميًا لإتمام كتاب أو مقرر قبل موعد محدد',
            'category' => 'education',
            'icon' => 'fa-book-open',
            'keywords' => ['صفحات يومية', 'قراءة كتاب', 'حفظ صفحات', 'pages per day'],
            'related' => ['daily-lectures-calculator', 'memorization-time-calculator', 'syllabus-finish-time-calculator']
        ],
        'daily-lectures-calculator' => [
            'name' => 'حاسبة عدد المحاضرات اليومية',
            'description' => 'توزيع الفيديوهات والمحاضرات المسجلة على الأيام المتاحة قبل موعد الاختبار',
            'category' => 'education',
            'icon' => 'fa-video',
            'keywords' => ['محاضرات', 'فيديوهات كورسات', 'محاضرات يومية', 'lectures per day'],
            'related' => ['daily-pages-calculator', 'curriculum-progress-calculator', 'daily-study-hours-calculator']
        ],
        'curriculum-progress-calculator' => [
            'name' => 'حاسبة نسبة إنجاز المنهج',
            'description' => 'احسب النسبة المئوية الدقيقة لما درسته حتى الآن وما تبقى عليك مع رسم بياني مرئي',
            'category' => 'education',
            'icon' => 'fa-chart-pie',
            'keywords' => ['نسبة الانجاز', 'كم خلصت من المنهج', 'متابعة الدراسة', 'progress tracker'],
            'related' => ['remaining-study-time-calculator', 'syllabus-finish-time-calculator', 'daily-study-hours-calculator']
        ],
        'remaining-study-time-calculator' => [
            'name' => 'حاسبة وقت الدراسة المتبقي',
            'description' => 'حساب إجمالي الساعات المطلوبة لإنهاء الفصول المتبقية ومقارنتها بالوقت المتاح',
            'category' => 'education',
            'icon' => 'fa-user-clock',
            'keywords' => ['الوقت المتبقي', 'ساعات متبقية', 'وقت المذاكرة', 'remaining study time'],
            'related' => ['curriculum-progress-calculator', 'daily-study-hours-calculator', 'exam-countdown-calculator']
        ],
        'memorization-time-calculator' => [
            'name' => 'حاسبة وقت الحفظ المتوقع',
            'description' => 'تقدير الوقت المطلوب لحفظ نصوص أو مصطلحات أو أجزاء من القرآن الكريم وتحديد خطة المراجعة',
            'category' => 'education',
            'icon' => 'fa-brain',
            'keywords' => ['وقت الحفظ', 'حفظ القرآن', 'تسميع', 'حفظ مصطلحات', 'memorization time'],
            'related' => ['daily-pages-calculator', 'revision-plan-calculator', 'reading-time-calculator']
        ],
        'grades-to-percentage-converter' => [
            'name' => 'حاسبة تحويل الدرجات إلى نسبة',
            'description' => 'تحويل أي درجة من مقياس معين (من 20، من 60، من 100) إلى نسبة مئوية ومعدل مكافئ',
            'category' => 'education',
            'icon' => 'fa-exchange-alt',
            'keywords' => ['تحويل الدرجات', 'درجة الى نسبة', 'تحويل علامات', 'grades to percentage'],
            'related' => ['grade-percentage-calculator', 'grade-difference-calculator', 'final-grade-calculator']
        ],
        'grade-difference-calculator' => [
            'name' => 'حاسبة الفرق بين علامتين',
            'description' => 'حساب فارق الدرجات والنسبة المئوية للتحسن أو التراجع بين اختبارين أو فصلين',
            'category' => 'education',
            'icon' => 'fa-arrows-alt-v',
            'keywords' => ['فرق الدرجات', 'نسبة التحسن', 'مقارنة علامتين', 'grade difference'],
            'related' => ['grade-percentage-calculator', 'target-gpa-calculator', 'grades-to-percentage-converter']
        ],
        'grade-distribution-calculator' => [
            'name' => 'حاسبة توزيع العلامات للوصول لمعدل معين',
            'description' => 'توزيع الدرجات المستهدفة على جميع المواد بما يراعي قدراتك وصعوبة كل مقرر',
            'category' => 'education',
            'icon' => 'fa-sitemap',
            'keywords' => ['توزيع العلامات', 'تخطيط الدرجات', 'توزيع الدرجات', 'grade distribution'],
            'related' => ['target-gpa-calculator', 'next-semester-gpa-calculator', 'cumulative-gpa-calculator']
        ],
        'next-semester-gpa-calculator' => [
            'name' => 'حاسبة المعدل المطلوب في الفصل القادم',
            'description' => 'احسب المعدل الفصلي الذي تحتاجه في الفصل القادم لرفع معدلك التراكمي إلى حد معين',
            'category' => 'education',
            'icon' => 'fa-arrow-up',
            'keywords' => ['معدل الفصل القادم', 'رفع المعدل', 'تحسين التراكمي', 'next semester gpa'],
            'related' => ['cumulative-gpa-calculator', 'target-gpa-calculator', 'graduation-hours-calculator']
        ],
        'graduation-hours-calculator' => [
            'name' => 'حاسبة الساعات المطلوبة للتخرج',
            'description' => 'حساب الساعات المتبقية للتخرج وعدد الفصول المتوقعة وفق الخطة الدراسية والحد الأقصى للساعات',
            'category' => 'education',
            'icon' => 'fa-user-graduate',
            'keywords' => ['ساعات التخرج', 'موعد التخرج', 'كم باقي للتخرج', 'خطة التخرج', 'graduation hours'],
            'related' => ['cumulative-gpa-calculator', 'next-semester-gpa-calculator', 'curriculum-progress-calculator']
        ],

        // ============================================
        // F) أدوات الحياة اليومية والمال الشخصي (17 أداة)
        // ============================================
        'is-salary-enough-calculator' => [
            'name' => 'حاسبة هل راتبي يكفيني؟',
            'description' => 'تحليل مالي شفاف يقارن بين دخلك الشهري ومتطلبات معيشتك ويحدد الفائض أو العجز المالي',
            'category' => 'life',
            'icon' => 'fa-question-circle',
            'keywords' => ['هل الراتب يكفي', 'كفاية الراتب', 'تحليل الدخل', 'عجز الراتب', 'salary enough'],
            'featured' => true,
            'related' => ['family-monthly-budget-calculator', 'salary-division-calculator', 'cost-of-living-calculator']
        ],
        'independence-cost-calculator' => [
            'name' => 'حاسبة تكلفة الاستقلال عن الأهل',
            'description' => 'حساب الميزانية التأسيسية والتشغيلية الشهرية المطلوبة للعيش في سكن مستقل بمفردك',
            'category' => 'life',
            'icon' => 'fa-key',
            'keywords' => ['استقلال مالي', 'سكن لوحدي', 'تكلفة الاستقلال', 'عيش مستقل', 'moving out cost'],
            'related' => ['rent-split-calculator', 'cost-of-living-calculator', 'family-monthly-budget-calculator']
        ],
        'baby-first-year-cost-calculator' => [
            'name' => 'حاسبة تكلفة الطفل في السنة الأولى',
            'description' => 'تقدير تكاليف ولادة ورعاية المولود الأول: مستلزمات، حفاضات، حليب، ملابس، ورعاية طبية',
            'category' => 'life',
            'icon' => 'fa-baby',
            'keywords' => ['تكلفة المولود', 'مصاريف البيبي', 'السنة الاولى للطفل', 'baby cost'],
            'related' => ['family-monthly-budget-calculator', 'savings-goal-calculator', 'salary-division-calculator']
        ],
        'family-monthly-budget-calculator' => [
            'name' => 'حاسبة المصاريف الشهرية للأسرة',
            'description' => 'تخطيط ميزانية العائلة الشاملة: طعام، فواتير، مدارس، إيجار، مواصلات، وترفيه',
            'category' => 'life',
            'icon' => 'fa-home',
            'keywords' => ['ميزانية الاسرة', 'مصاريف العائلة', 'ميزانية البيت', 'family budget'],
            'popular' => true,
            'related' => ['salary-division-calculator', 'is-salary-enough-calculator', 'savings-goal-calculator']
        ],
        'salary-division-calculator' => [
            'name' => 'حاسبة تقسيم الراتب (قاعدة 50/30/20)',
            'description' => 'توزيع راتبك بطريقة ذكية: 50% للاحتياجات الأساسية، 30% للرغبات، و20% للادخار والاستثمار',
            'category' => 'life',
            'icon' => 'fa-pie-chart',
            'keywords' => ['تقسيم الراتب', 'قاعدة 50 30 20', 'ادخار الراتب', 'توزيع الدخل', '50/30/20 rule'],
            'featured' => true, 'popular' => true,
            'related' => ['savings-goal-calculator', 'family-monthly-budget-calculator', 'net-salary-calculator']
        ],
        'savings-goal-calculator' => [
            'name' => 'حاسبة الادخار للوصول إلى هدف',
            'description' => 'احسب كم تحتاج للادخار شهريًا لتحقيق هدفك المالي وشراء ما تريد في الوقت المحدد',
            'category' => 'life',
            'icon' => 'fa-piggy-bank',
            'keywords' => ['ادخار', 'تحويش', 'هدف مالي', 'شراء سيارة', 'شراء بيت', 'savings goal'],
            'popular' => true,
            'related' => ['salary-division-calculator', 'when-can-i-buy-car-calculator', 'when-can-i-buy-house-calculator']
        ],
        'when-can-i-buy-car-calculator' => [
            'name' => 'حاسبة متى أستطيع شراء سيارة؟',
            'description' => 'تحديد الفترة الزمنية والتاريخ المتوقع لقدرتك على شراء سيارة كاش أو أقساط وفق مدخراتك',
            'category' => 'life',
            'icon' => 'fa-car',
            'keywords' => ['متى اشتري سيارة', 'شراء سيارة', 'قسط سيارة', 'ادخار سيارة', 'buy car calculator'],
            'related' => ['when-can-i-buy-house-calculator', 'savings-goal-calculator', 'monthly-car-cost-calculator']
        ],
        'when-can-i-buy-house-calculator' => [
            'name' => 'حاسبة متى أستطيع شراء منزل؟',
            'description' => 'تقدير الوقت المطلوب لتجميع الدفعة الأولى أو شراء منزل وشقة بناءً على دخلك وادخارك',
            'category' => 'life',
            'icon' => 'fa-home',
            'keywords' => ['متى اشتري بيت', 'شراء شقة', 'دفعة اولى', 'قرض عقاري', 'buy house calculator'],
            'related' => ['when-can-i-buy-car-calculator', 'savings-goal-calculator', 'cost-of-living-calculator']
        ],
        'immigration-cost-calculator' => [
            'name' => 'حاسبة تكلفة الهجرة',
            'description' => 'حساب الميزانية التقديرية للهجرة القانونية: تأشيرات، معادلة شهادات، اختبارات لغة، وحساب مغلق',
            'category' => 'life',
            'icon' => 'fa-passport',
            'keywords' => ['هجرة', 'تكلفة الهجرة', 'فيزا كندا', 'فيزا المانيا', 'سفر للهجرة', 'immigration cost'],
            'related' => ['relocation-cost-calculator', 'travel-cost-calculator', 'cost-of-living-calculator']
        ],
        'relocation-cost-calculator' => [
            'name' => 'حاسبة تكلفة الانتقال لدولة أخرى',
            'description' => 'حساب تكاليف النقل والسفر والودائع وسكن الأشهر الأولى عند الانتقال للعمل أو الاستقرار في بلد جديد',
            'category' => 'life',
            'icon' => 'fa-globe-americas',
            'keywords' => ['انتقال لدولة ثانية', 'سفر للعمل', 'استقرار بالخارج', 'relocation cost'],
            'related' => ['immigration-cost-calculator', 'cost-of-living-calculator', 'city-income-requirement-calculator']
        ],
        'cost-of-living-calculator' => [
            'name' => 'حاسبة تكلفة المعيشة',
            'description' => 'تقدير ومقارنة تكلفة المعيشة بين المدن والمناطق بناءً على السكن والغذاء والمواصلات',
            'category' => 'life',
            'icon' => 'fa-city',
            'keywords' => ['تكلفة المعيشة', 'غلاء المعيشة', 'سكن ومعيشة', 'مقارنة معيشة', 'cost of living'],
            'related' => ['city-income-requirement-calculator', 'is-salary-enough-calculator', 'family-monthly-budget-calculator']
        ],
        'rent-split-calculator' => [
            'name' => 'حاسبة تقسيم الإيجار',
            'description' => 'تقسيم إيجار الشقة بين الزملاء بناءً على مساحة الغرف، الحمام الخاص، والمميزات',
            'category' => 'life',
            'icon' => 'fa-door-open',
            'keywords' => ['تقسيم الايجار', 'سكن مشترك', 'ايجار الغرف', 'شيرنج', 'rent split'],
            'related' => ['electricity-bill-split-calculator', 'internet-bill-split-calculator', 'restaurant-bill-split-calculator']
        ],
        'restaurant-bill-split-calculator' => [
            'name' => 'حاسبة تقسيم فاتورة المطعم',
            'description' => 'تقسيم فاتورة المطعم بين الأصدقاء بالتساوي أو حسب طلب كل شخص مع الضريبة والإكرامية',
            'category' => 'life',
            'icon' => 'fa-utensils',
            'keywords' => ['تقسيم الفاتورة', 'حساب المطعم', 'قطة مطعم', 'تبس', 'bill split'],
            'related' => ['rent-split-calculator', 'travel-expenses-split-calculator', 'electricity-bill-split-calculator']
        ],
        'electricity-bill-split-calculator' => [
            'name' => 'حاسبة تقسيم فاتورة الكهرباء',
            'description' => 'تقسيم فاتورة الكهرباء في السكن المشترك أو العمارة بناءً على عدد الأيام أو الأجهزة',
            'category' => 'life',
            'icon' => 'fa-file-invoice',
            'keywords' => ['تقسيم الكهرباء', 'فاتورة كهرباء مشتركة', 'تقسيم الفواتير'],
            'related' => ['electricity-consumption-calculator', 'rent-split-calculator', 'internet-bill-split-calculator']
        ],
        'internet-bill-split-calculator' => [
            'name' => 'حاسبة تقسيم اشتراك الإنترنت',
            'description' => 'توزيع تكلفة باقة الإنترنت والراوتر شهريًا بين السكان المشتركين بدقة وعدالة',
            'category' => 'life',
            'icon' => 'fa-wifi',
            'keywords' => ['تقسيم النت', 'اشتراك راوتر', 'باقة النت', 'internet bill split'],
            'related' => ['rent-split-calculator', 'electricity-bill-split-calculator', 'restaurant-bill-split-calculator']
        ],
        'salary-to-hourly-daily-converter' => [
            'name' => 'حاسبة تحويل الراتب الشهري إلى يومي وساعي',
            'description' => 'تحويل الراتب الشهري فوراً إلى معدل يومي وساعي ودقيقة وثانية بدقة فائقة',
            'category' => 'life',
            'icon' => 'fa-sync-alt',
            'keywords' => ['تحويل الراتب', 'راتب يومي وساعي', 'دخل في الساعة', 'salary converter'],
            'related' => ['hourly-wage-calculator', 'daily-wage-calculator', 'net-salary-calculator']
        ],
        'city-income-requirement-calculator' => [
            'name' => 'حاسبة الدخل المطلوب للعيش في مدينة',
            'description' => 'احسب الدخل الصافي الأدنى الذي تحتاجه شهريًا للعيش بكرامة وأمان مالي في مدينتك',
            'category' => 'life',
            'icon' => 'fa-map-pin',
            'keywords' => ['الدخل المطلوب', 'راتب العيش في الرياض', 'راتب العيش في دبي', 'معيشة المدينة', 'city income'],
            'related' => ['cost-of-living-calculator', 'is-salary-enough-calculator', 'relocation-cost-calculator']
        ],

        // ============================================
        // G) أدوات المحتوى والنصوص (33 أداة)
        // ============================================
        'markdown-editor' => [
            'name' => 'محرر Markdown مع معاينة مباشرة',
            'description' => 'كتابة وتحرير مستندات Markdown مع معاينة فورية وتصدير HTML وتحميل الملفات',
            'category' => 'text',
            'icon' => 'fa-edit',
            'keywords' => ['ماركداون', 'محرر نصوص', 'معاينة مباشرة', 'markdown editor', 'preview'],
            'featured' => true,
            'related' => ['markdown-arabic-editor', 'markdown-to-html', 'html-to-markdown']
        ],
        'markdown-arabic-editor' => [
            'name' => 'محرر Markdown عربي احترافي',
            'description' => 'محرر متقدم مخصص للمحتوى العربي يدعم RTL، علامات الاقتباس العربية، والجداول',
            'category' => 'text',
            'icon' => 'fa-pen-fancy',
            'keywords' => ['ماركداون عربي', 'محرر عربي', 'كتابة مقالات', 'arabic markdown'],
            'related' => ['markdown-editor', 'markdown-to-html', 'word-counter']
        ],
        'markdown-to-html' => [
            'name' => 'تحويل Markdown إلى HTML',
            'description' => 'تحويل نصوص وأكواد Markdown فورياً إلى كود HTML نظيف ومتوافق مع المعايير',
            'category' => 'text',
            'icon' => 'fa-code',
            'keywords' => ['تحويل ماركداون', 'markdown to html', 'md to html', 'توليد html'],
            'related' => ['html-to-markdown', 'markdown-editor', 'html-formatter']
        ],
        'html-to-markdown' => [
            'name' => 'تحويل HTML إلى Markdown',
            'description' => 'استخراج المحتوى من أكواد وصفحات HTML وتحويلها إلى تنسيق Markdown بسيط',
            'category' => 'text',
            'icon' => 'fa-file-code',
            'keywords' => ['html to markdown', 'تحويل كود الى ماركداون', 'html to md'],
            'related' => ['markdown-to-html', 'markdown-editor', 'clean-html-text']
        ],
        'word-counter' => [
            'name' => 'عداد الكلمات والأحرف المتقدم',
            'description' => 'حساب دقيق لعدد الكلمات، الأحرف (مع وبدون مسافات)، الأسطر، والفقرات مع وقت القراءة',
            'category' => 'text',
            'icon' => 'fa-sort-numeric-up',
            'keywords' => ['عداد كلمات', 'عدد الحروف', 'احصاء النص', 'طول النص', 'word counter'],
            'featured' => true, 'popular' => true,
            'related' => ['arabic-word-counter', 'arabic-char-counter', 'reading-time-calculator']
        ],
        'arabic-word-counter' => [
            'name' => 'عداد الكلمات العربي المتخصص',
            'description' => 'عداد مخصص للنصوص العربية يتعامل بدقة مع حروف العطف والضمائر المتصلة وأدوات التعريف',
            'category' => 'text',
            'icon' => 'fa-spell-check',
            'keywords' => ['كلمات عربية', 'حساب الكلمات بالعربي', 'عدد الكلمات العربية'],
            'related' => ['word-counter', 'arabic-char-counter', 'keyword-density-calculator']
        ],
        'arabic-char-counter' => [
            'name' => 'عداد الأحرف والمسافات العربي',
            'description' => 'عد الحروف مع فصل الحروف الأبجدية، الأرقام، علامات الترقيم، والرموز التعبيرية (Emoji)',
            'category' => 'text',
            'icon' => 'fa-font',
            'keywords' => ['عدد الاحرف', 'حروف بدون مسافات', 'طول التغريدة', 'char counter'],
            'related' => ['word-counter', 'arabic-word-counter', 'clean-arabic-text']
        ],
        'sentence-counter' => [
            'name' => 'عداد الجمل في النص',
            'description' => 'حساب عدد الجمل في المقال بناءً على علامات الوقف العربية والإنجليزية',
            'category' => 'text',
            'icon' => 'fa-list-ol',
            'keywords' => ['عدد الجمل', 'جمل النص', 'sentence counter'],
            'related' => ['paragraph-counter', 'avg-sentence-length', 'word-counter']
        ],
        'paragraph-counter' => [
            'name' => 'عداد الفقرات والأسطر',
            'description' => 'إحصاء عدد الفقرات الفعلية، الأسطر العادية والأسطر الفارغة في النص',
            'category' => 'text',
            'icon' => 'fa-align-justify',
            'keywords' => ['فقرات', 'عدد الفقرات', 'اسطر النص', 'paragraph counter'],
            'related' => ['sentence-counter', 'word-counter', 'split-text-paragraphs']
        ],
        'avg-sentence-length' => [
            'name' => 'حساب متوسط طول الجملة',
            'description' => 'قياس سهولة قراءة النص من خلال حساب متوسط عدد الكلمات في كل جملة ومؤشر المقروئية',
            'category' => 'text',
            'icon' => 'fa-ruler',
            'keywords' => ['طول الجملة', 'مقروئية النص', 'سهولة القراءة', 'readability'],
            'related' => ['sentence-counter', 'reading-time-calculator', 'speech-time-calculator']
        ],
        'reading-time-calculator' => [
            'name' => 'حساب وقت قراءة النص',
            'description' => 'تقدير وقت القراءة الصامتة بالدقائق والثواني للمقالات والتدوينات والكتب',
            'category' => 'text',
            'icon' => 'fa-book-reader',
            'keywords' => ['وقت القراءة', 'كم دقيقة قراءة', 'مدة قراءة المقال', 'reading time'],
            'popular' => true,
            'related' => ['speech-time-calculator', 'word-counter', 'avg-sentence-length']
        ],
        'speech-time-calculator' => [
            'name' => 'حساب وقت إلقاء النص والخطاب',
            'description' => 'تقدير المدة الزمنية لإلقاء كلمة أو خطبة أو تسجيل فيديو (Voiceover) بسرعات مختلفة',
            'category' => 'text',
            'icon' => 'fa-microphone',
            'keywords' => ['وقت الالقاء', 'مدة الخطاب', 'فويس اوفر', 'تسجيل صوتي', 'speech time'],
            'related' => ['reading-time-calculator', 'word-counter', 'avg-sentence-length']
        ],
        'clean-arabic-text' => [
            'name' => 'تنظيف النص العربي',
            'description' => 'إزالة التشكيل، الكشيدة والتطويل (ـ)، وتوحيد الألفات والهمزات والياء والألف المقصورة',
            'category' => 'text',
            'icon' => 'fa-broom',
            'keywords' => ['تنظيف النص العربي', 'ازالة التشكيل', 'ازالة التطويل', 'توحيد الهمزات', 'clean arabic'],
            'featured' => true, 'popular' => true,
            'related' => ['remove-extra-spaces', 'clean-word-text', 'text-formatter']
        ],
        'remove-extra-spaces' => [
            'name' => 'إزالة المسافات الزائدة',
            'description' => 'تنظيف النص من المسافات المتكررة والفراغات في بداية ونهاية الأسطر',
            'category' => 'text',
            'icon' => 'fa-compress-arrows-alt',
            'keywords' => ['مسافات زائدة', 'مسح الفراغات', 'ضبط المسافات', 'remove extra spaces'],
            'related' => ['clean-arabic-text', 'remove-empty-lines', 'merge-lines-tool']
        ],
        'remove-empty-lines' => [
            'name' => 'إزالة الأسطر الفارغة',
            'description' => 'حذف جميع الأسطر الفارغة والمسافات البيضاء بين الفقرات بضغطة زر واحدة',
            'category' => 'text',
            'icon' => 'fa-bars',
            'keywords' => ['اسطر فارغة', 'حذف الاسطر الفارغة', 'تنظيف الاسطر', 'remove blank lines'],
            'related' => ['remove-extra-spaces', 'remove-duplicate-lines', 'merge-lines-tool']
        ],
        'remove-duplicate-lines' => [
            'name' => 'إزالة التكرار من النص والأسطر',
            'description' => 'حذف الكلمات والأسطر المكررة والإبقاء على العناصر الفريدة فقط مع فرزها',
            'category' => 'text',
            'icon' => 'fa-clone',
            'keywords' => ['حذف التكرار', 'ازالة الاسطر المكررة', 'عناصر فريدة', 'remove duplicates'],
            'related' => ['sort-text-lines', 'remove-empty-lines', 'text-to-list-converter']
        ],
        'sort-text-lines' => [
            'name' => 'ترتيب النص أبجدياً',
            'description' => 'ترتيب أسطر وقوائم النص أبجدياً (أ-ي أو A-Z) أو عكسياً أو حسب الطول',
            'category' => 'text',
            'icon' => 'fa-sort-alpha-down',
            'keywords' => ['ترتيب ابجدي', 'فرز الاسطر', 'ترتيب الكلمات', 'sort lines'],
            'related' => ['remove-duplicate-lines', 'text-to-list-converter', 'clean-arabic-text']
        ],
        'text-to-list-converter' => [
            'name' => 'تحويل النص العربي إلى قائمة',
            'description' => 'تحويل أي نص أو أسطر عادية إلى قائمة نقطية، مرقمة، أو وسوم HTML وMarkdown',
            'category' => 'text',
            'icon' => 'fa-list-ul',
            'keywords' => ['تحويل لقائمة', 'قائمة نقطية', 'قائمة مرقمة', 'text to list'],
            'related' => ['merge-lines-tool', 'sort-text-lines', 'split-text-paragraphs']
        ],
        'merge-lines-tool' => [
            'name' => 'دمج الأسطر في سطر واحد',
            'description' => 'دمج أسطر متعددة في سطر نصي واحد مع فاصل مخصص (مسافة، فاصلة، شرطة)',
            'category' => 'text',
            'icon' => 'fa-stream',
            'keywords' => ['دمج الاسطر', 'سطر واحد', 'merge lines', 'join lines'],
            'related' => ['split-text-paragraphs', 'remove-empty-lines', 'text-to-list-converter']
        ],
        'split-text-paragraphs' => [
            'name' => 'تقسيم النص إلى فقرات',
            'description' => 'تقسيم النصوص الطويلة تلقائياً إلى فقرات متناسقة حسب عدد الكلمات أو علامات الترقيم',
            'category' => 'text',
            'icon' => 'fa-columns',
            'keywords' => ['تقسيم النص', 'توزيع الفقرات', 'تقطيع النص', 'split text'],
            'related' => ['merge-lines-tool', 'paragraph-counter', 'word-counter']
        ],
        'extract-urls-from-text' => [
            'name' => 'استخراج الروابط من النص',
            'description' => 'استخراج وتجميع جميع روابط المواقع (URLs) والمصادر من داخل أي نص مع إمكانية نسخها وقائمتها',
            'category' => 'text',
            'icon' => 'fa-link',
            'keywords' => ['استخراج الروابط', 'روابط من النص', 'extract urls', 'url extractor'],
            'related' => ['extract-emails-from-text', 'extract-hashtags-from-text', 'extract-keywords-from-text']
        ],
        'extract-emails-from-text' => [
            'name' => 'استخراج البريد الإلكتروني من النص',
            'description' => 'البحث واستخراج جميع عناوين الإيميلات من النصوص الطويلة وتصفيتها من التكرار',
            'category' => 'text',
            'icon' => 'fa-at',
            'keywords' => ['استخراج الايميلات', 'بريد الكتروني', 'extract emails', 'email extractor'],
            'related' => ['extract-urls-from-text', 'extract-hashtags-from-text', 'clean-arabic-text']
        ],
        'extract-hashtags-from-text' => [
            'name' => 'استخراج الهاشتاغات من النص',
            'description' => 'استخراج وتجميع جميع وسوم وهاشتاغات السوشيال ميديا (#) من المنشورات والنصوص',
            'category' => 'text',
            'icon' => 'fa-hashtag',
            'keywords' => ['هاشتاغ', 'استخراج الهاشتاغات', 'extract hashtags', 'وسوم'],
            'related' => ['extract-keywords-from-text', 'extract-urls-from-text', 'social-media-pricing-calculator']
        ],
        'extract-keywords-from-text' => [
            'name' => 'استخراج الكلمات المفتاحية',
            'description' => 'تحليل النص واستخراج أهم الكلمات والمصطلحات المفتاحية الأكثر تكراراً بعد استبعاد حروف الجر',
            'category' => 'text',
            'icon' => 'fa-key',
            'keywords' => ['كلمات مفتاحية', 'سيو', 'استخراج الكلمات', 'extract keywords', 'seo keywords'],
            'related' => ['keyword-density-calculator', 'word-counter', 'text-to-slug']
        ],
        'keyword-density-calculator' => [
            'name' => 'حساب كثافة الكلمات المفتاحية (Keyword Density)',
            'description' => 'فحص نسبة تكرار الكلمات المفتاحية في المقال لتحسين السيو وتجنب حشو الكلمات (Keyword Stuffing)',
            'category' => 'text',
            'icon' => 'fa-percentage',
            'keywords' => ['كثافة الكلمات', 'سيو المقال', 'keyword density', 'تكرار الكلمة'],
            'related' => ['extract-keywords-from-text', 'word-counter', 'text-to-slug']
        ],
        'slug-converter' => [
            'name' => 'تحويل النص إلى Slug (عربي وإنجليزي)',
            'description' => 'توليد روابط URL ودية لمحركات البحث (SEO Friendly Slugs) باللغتين العربية والإنجليزية',
            'category' => 'text',
            'icon' => 'fa-link',
            'keywords' => ['سلاج', 'slug', 'تحويل الرابط', 'روابط سيو', 'arabic slug'],
            'featured' => true,
            'related' => ['arabic-english-slug-generator', 'text-formatter', 'url-encode']
        ],
        'arabic-english-slug-generator' => [
            'name' => 'توليد Slug مخصص (عربي أو صوتي)',
            'description' => 'توليد سلاج مع إمكانية الاختيار بين الحفاظ على الحروف العربية أو تحويلها صوتياً للاتينية (Transliteration)',
            'category' => 'text',
            'icon' => 'fa-globe',
            'keywords' => ['سلاج مخصص', 'عربي ولاتيني', 'transliteration', 'slug generator'],
            'related' => ['slug-converter', 'text-to-slug', 'url-encode']
        ],
        'quote-converter' => [
            'name' => 'تحويل علامات الاقتباس والأقواس',
            'description' => 'تحويل علامات التنصيص العادية إلى أقواس عربية مزهرة « » أو اقتباسات ذكية منسقة',
            'category' => 'text',
            'icon' => 'fa-quote-right',
            'keywords' => ['علامات اقتباس', 'اقواس عربية', 'تنصيص', 'quote converter'],
            'related' => ['clean-arabic-text', 'text-formatter', 'clean-word-text']
        ],
        'arabic-english-numbers-converter' => [
            'name' => 'تحويل الأرقام العربية والإنجليزية',
            'description' => 'التحويل المتبادل بين الأرقام العربية المشرقية (٠-٩) والأرقام العربية الغربية (0-9) في النصوص',
            'category' => 'text',
            'icon' => 'fa-exchange-alt',
            'keywords' => ['ارقام هندية', 'ارقام عربية', 'تحويل الارقام', 'numbers converter'],
            'popular' => true,
            'related' => ['clean-arabic-text', 'text-formatter', 'word-counter']
        ],
        'rtl-ltr-text-converter' => [
            'name' => 'تحويل اتجاه النص بين RTL و LTR',
            'description' => 'إصلاح اتجاه النصوص المعكوسة وتصحيح خلط اللغات ومحاذاة الفقرات',
            'category' => 'text',
            'icon' => 'fa-text-width',
            'keywords' => ['اتجاه النص', 'عربي انجليزي', 'rtl ltr', 'تصحيح النص المعكوس'],
            'related' => ['clean-arabic-text', 'text-formatter', 'clean-word-text']
        ],
        'clean-word-text' => [
            'name' => 'تنظيف النص المنسوخ من Word',
            'description' => 'إزالة التنسيقات الخفية والأنماط المعقدة ورموز التحكم المزعجة المنسوخة من مايكروسوفت وورد',
            'category' => 'text',
            'icon' => 'fa-file-word',
            'keywords' => ['تنظيف وورد', 'نص من وورد', 'clean word text', 'ازالة التنسيق'],
            'related' => ['clean-html-text', 'clean-arabic-text', 'remove-extra-spaces']
        ],
        'clean-html-text' => [
            'name' => 'تنظيف النص المنسوخ من HTML',
            'description' => 'تجريد أكواد HTML وعناصر الوسوم واستخراج النص المقروء الصافي فقط',
            'category' => 'text',
            'icon' => 'fa-file-code',
            'keywords' => ['تنظيف html', 'استخراج النص الصافي', 'strip tags', 'clean html'],
            'related' => ['clean-word-text', 'html-to-markdown', 'html-formatter']
        ],
        'text-to-json-converter' => [
            'name' => 'تحويل النص إلى JSON',
            'description' => 'تحويل الأسطر، القوائم، وبيانات الجداول إلى مصفوفة وكائنات JSON صالحة برمجياً',
            'category' => 'text',
            'icon' => 'fa-brackets-curly',
            'keywords' => ['نص الى json', 'text to json', 'تحويل لمصفوفة', 'json converter'],
            'related' => ['json-to-formatted-text', 'json-formatter', 'text-to-list-converter']
        ],
        'json-to-formatted-text' => [
            'name' => 'تحويل JSON إلى نص منسق',
            'description' => 'تحويل كود وبيانات JSON إلى نصوص وقوائم مفهومة ومقروءة للمستخدم البسيط',
            'category' => 'text',
            'icon' => 'fa-file-alt',
            'keywords' => ['json الى نص', 'json to text', 'قراءة json', 'تفريغ json'],
            'related' => ['text-to-json-converter', 'json-formatter', 'json-validator']
        ],
        'text-formatter' => [
            'name' => 'تنسيق النص وحالات الأحرف',
            'description' => 'تحويل النصوص بين أنماط مختلفة، تكبير وتصغير الحروف اللاتينية وتنسيق العناوين',
            'category' => 'text',
            'icon' => 'fa-font',
            'keywords' => ['تنسيق النص', 'حالة الاحرف', 'uppercase', 'lowercase', 'title case'],
            'related' => ['case-converter', 'clean-arabic-text', 'word-counter']
        ],

        // ============================================
        // H) أدوات المطورين والبرمجيات (Developer Tools)
        // ============================================
        'json-formatter' => [
            'name' => 'منسق ومحقق JSON',
            'description' => 'تنسيق وتجميل وضغط والتحقق من صحة أكواد JSON مع كشف الأخطاء البرمجية',
            'category' => 'dev',
            'icon' => 'fa-code',
            'keywords' => ['json', 'تنسيق json', 'json formatter', 'json beautifier', 'json validator'],
            'featured' => true, 'popular' => true,
            'related' => ['json-validator', 'json-minifier', 'text-to-json-converter']
        ],
        'json-validator' => [
            'name' => 'التحقق من صحة JSON',
            'description' => 'فحص كود JSON وتحديد مكان الخطأ بدقة وسطر المشكلة مع رسائل توضيحية',
            'category' => 'dev',
            'icon' => 'fa-check-circle',
            'keywords' => ['فحص json', 'json validator', 'تصحيح json', 'صلاحية json'],
            'related' => ['json-formatter', 'json-minifier', 'xml-validator']
        ],
        'json-minifier' => [
            'name' => 'ضغط وتقليص حجم JSON',
            'description' => 'إزالة المسافات والأسطر الفارغة لتقليل حجم ملفات واستجابات JSON لتسريع التطبيقات',
            'category' => 'dev',
            'icon' => 'fa-compress',
            'keywords' => ['ضغط json', 'json minifier', 'تقليص json', 'minify json'],
            'related' => ['json-formatter', 'sql-minifier', 'text-to-json-converter']
        ],
        'xml-formatter' => [
            'name' => 'تنسيق وتجميل كود XML',
            'description' => 'تنسيق ملفات وأكواد XML وترتيب الوسوم والمسافات البادئة بشكل مقروء',
            'category' => 'dev',
            'icon' => 'fa-file-code',
            'keywords' => ['xml', 'تنسيق xml', 'xml formatter', 'xml beautifier'],
            'related' => ['xml-validator', 'html-formatter', 'json-formatter']
        ],
        'xml-validator' => [
            'name' => 'التحقق من صحة كود XML',
            'description' => 'فحص بنية XML والتأكد من إغلاق الوسوم وصحة الهيكل البرمجي',
            'category' => 'dev',
            'icon' => 'fa-check',
            'keywords' => ['فحص xml', 'xml validator', 'صحة xml'],
            'related' => ['xml-formatter', 'json-validator', 'html-formatter']
        ],
        'html-formatter' => [
            'name' => 'تنسيق وتجميل كود HTML',
            'description' => 'إعادة ترتيب وسوم وأسطر HTML وتوحيد المسافات البادئة وتنظيم الشفرة المصدرية',
            'category' => 'dev',
            'icon' => 'fa-html5',
            'keywords' => ['تنسيق html', 'html formatter', 'html beautifier', 'ترتيب كود html'],
            'related' => ['css-formatter', 'js-formatter', 'xml-formatter']
        ],
        'css-formatter' => [
            'name' => 'تنسيق وتجميل كود CSS',
            'description' => 'تنسيق وترتيب قواعد وملفات CSS وإظهار الخصائص بشكل مرتب ومقروء',
            'category' => 'dev',
            'icon' => 'fa-css3-alt',
            'keywords' => ['تنسيق css', 'css formatter', 'css beautifier', 'ترتيب css'],
            'related' => ['html-formatter', 'js-formatter', 'css-gradient-generator']
        ],
        'js-formatter' => [
            'name' => 'تنسيق وتجميل كود JavaScript',
            'description' => 'تنسيق وتجميل أكواد الجافاسكريبت وتحسين قراءتها وترتيب الأقواس والمسافات',
            'category' => 'dev',
            'icon' => 'fa-js',
            'keywords' => ['تنسيق js', 'javascript formatter', 'js beautifier', 'تجميل جافاسكريبت'],
            'related' => ['json-formatter', 'html-formatter', 'css-formatter']
        ],
        'sql-formatter' => [
            'name' => 'تنسيق وتجميل استعلامات SQL',
            'description' => 'تنسيق جمل واستعلامات قواعد البيانات SQL (SELECT, INSERT, JOIN) مع تمييز الكلمات المحجوزة',
            'category' => 'dev',
            'icon' => 'fa-database',
            'keywords' => ['تنسيق sql', 'sql formatter', 'sql beautifier', 'استعلامات sql'],
            'related' => ['sql-minifier', 'json-formatter', 'code-templates']
        ],
        'sql-minifier' => [
            'name' => 'ضغط استعلامات SQL',
            'description' => 'ضغط استعلامات SQL وإزالة التعليقات والمسافات لتضمينها بسطر واحد في الأكواد',
            'category' => 'dev',
            'icon' => 'fa-compress-alt',
            'keywords' => ['ضغط sql', 'sql minifier', 'تقليص sql'],
            'related' => ['sql-formatter', 'json-minifier', 'code-templates']
        ],
        'base64-converter' => [
            'name' => 'تشفير وفك تشفير Base64',
            'description' => 'تحويل وتشفير وفك تشفير النصوص والبيانات بصيغة Base64 مع دعم كامل للنصوص العربية',
            'category' => 'dev',
            'icon' => 'fa-exchange-alt',
            'keywords' => ['base64', 'تشفير base64', 'فك تشفير base64', 'base64 encode', 'base64 decode'],
            'featured' => true,
            'related' => ['url-encode', 'hash-generator', 'jwt-decoder']
        ],
        'url-encode' => [
            'name' => 'ترميز وفك ترميز الروابط (URL Encode/Decode)',
            'description' => 'تشفير الأحرف الخاصة والنصوص العربية في الروابط URL وفك تشفير الروابط المشفرة',
            'category' => 'dev',
            'icon' => 'fa-link',
            'keywords' => ['ترميز الروابط', 'url encode', 'url decode', 'percent encoding'],
            'related' => ['base64-converter', 'url-parser', 'slug-converter']
        ],
        'jwt-decoder' => [
            'name' => 'فك تشفير وقراءة JWT Token',
            'description' => 'فك تشفير رموز JSON Web Tokens وعرض الـ Header و Payload ووقت الصلاحية بدقة وبأمان في المتصفح',
            'category' => 'dev',
            'icon' => 'fa-id-card-alt',
            'keywords' => ['jwt', 'jwt decoder', 'فك تشفير jwt', 'token', 'bearer token'],
            'featured' => true,
            'related' => ['base64-converter', 'json-formatter', 'hash-generator']
        ],
        'uuid-generator' => [
            'name' => 'توليد معرفات فريدة (UUID / GUID)',
            'description' => 'توليد معرفات فريدة عالمياً من الإصدار الرابع (UUID v4) فرادى أو دفعات دفعة واحدة',
            'category' => 'dev',
            'icon' => 'fa-fingerprint',
            'keywords' => ['uuid', 'guid', 'توليد uuid', 'معرف فريد', 'uuid v4'],
            'popular' => true,
            'related' => ['random-string-generator', 'password-generator', 'hash-generator']
        ],
        'password-generator' => [
            'name' => 'مولد كلمات مرور قوية',
            'description' => 'توليد كلمات سر آمنة ومعقدة غير قابلة للتخمين مع قياس قوة كلمة المرور',
            'category' => 'generators',
            'icon' => 'fa-key',
            'keywords' => ['كلمة سر', 'باسورد', 'توليد كلمة مرور', 'password generator', 'كلمة سر قوية'],
            'featured' => true, 'popular' => true,
            'related' => ['random-string-generator', 'uuid-generator', 'hash-generator']
        ],
        'hash-generator' => [
            'name' => 'توليد Hash وتشفير النصوص',
            'description' => 'توليد قيم الهاش المتنوعة (SHA-256, SHA-512, MD5, SHA-1) لأي نص أو كود فوريًا',
            'category' => 'dev',
            'icon' => 'fa-hashtag',
            'keywords' => ['هاش', 'hash', 'تشفير', 'sha256', 'md5', 'sha512', 'hash generator'],
            'popular' => true,
            'related' => ['jwt-decoder', 'base64-converter', 'uuid-generator']
        ],
        'regex-tester' => [
            'name' => 'مختبر التعابير النمطية (Regex Tester)',
            'description' => 'اختبار وتجربة Regular Expressions ومطابقة النصوص واستخراج النتائج مع شرح الأنماط',
            'category' => 'dev',
            'icon' => 'fa-asterisk',
            'keywords' => ['regex', 'تعبير نمطي', 'مختبر regex', 'فحص regex', 'regular expression'],
            'featured' => true,
            'related' => ['text-formatter', 'json-formatter', 'url-parser']
        ],
        'timestamp-converter' => [
            'name' => 'محول التاريخ و Unix Timestamp',
            'description' => 'التحويل المتبادل بين التاريخ والوقت العادي و Unix Timestamp بالثواني والميلي ثانية',
            'category' => 'dev',
            'icon' => 'fa-clock',
            'keywords' => ['timestamp', 'unix time', 'محول التاريخ', 'وقت يونكس', 'epoch converter'],
            'popular' => true,
            'related' => ['cron-expression-explainer', 'random-number-generator', 'time-tracker']
        ],
        'color-converter' => [
            'name' => 'محول صيغ الألوان (HEX / RGB / HSL)',
            'description' => 'التحويل بين صيغ الألوان البرمجية: HEX, RGB, HSL, RGBA مع معاينة اللون المباشرة',
            'category' => 'dev',
            'icon' => 'fa-eye-dropper',
            'keywords' => ['تحويل الالوان', 'hex to rgb', 'rgb to hex', 'hsl', 'color converter'],
            'related' => ['color-palette', 'css-gradient-generator', 'css-box-shadow-generator']
        ],
        'css-gradient-generator' => [
            'name' => 'مولد تدرجات الألوان (CSS Gradient)',
            'description' => 'تصميم وإنشاء تدرجات لونية خطية وشعاعية (Linear & Radial Gradients) ونسخ كود CSS',
            'category' => 'dev',
            'icon' => 'fa-swatchbook',
            'keywords' => ['تدرج الوان', 'css gradient', 'تدرج خطي', 'تدرج لوني', 'gradient generator'],
            'popular' => true,
            'related' => ['color-converter', 'color-palette', 'css-box-shadow-generator']
        ],
        'css-box-shadow-generator' => [
            'name' => 'مولد ظلال الصناديق (CSS Box Shadow)',
            'description' => 'توليد ظلال CSS احترافية وناعمة وظلال داخلية (Inset) مع تحكم دقيق في الانتشار والضبابية',
            'category' => 'dev',
            'icon' => 'fa-clone',
            'keywords' => ['ظل css', 'box shadow', 'مولد ظلال', 'ظل ناعم', 'shadow generator'],
            'related' => ['css-border-radius-generator', 'css-gradient-generator', 'color-converter']
        ],
        'css-border-radius-generator' => [
            'name' => 'مولد حواف مخصصة (CSS Border Radius)',
            'description' => 'تصميم أشكال وحواف عضوية ومنحنية مخصصة (8-value border-radius) ونسخ الكود',
            'category' => 'dev',
            'icon' => 'fa-shapes',
            'keywords' => ['حواف css', 'border radius', 'زوايا منحنية', 'border radius generator'],
            'related' => ['css-box-shadow-generator', 'css-gradient-generator', 'color-converter']
        ],
        'qr-generator' => [
            'name' => 'مولد رموز الاستجابة السريعة (QR Code)',
            'description' => 'إنشاء باركود QR للروابط، شبكات الواي فاي، النصوص، وأرقام الهواتف وتنزيلها كصورة بدقة عالية',
            'category' => 'dev',
            'icon' => 'fa-qrcode',
            'keywords' => ['qr code', 'باركود', 'مولد qr', 'كود qr', 'توليد باركود'],
            'featured' => true, 'popular' => true,
            'related' => ['url-encode', 'url-parser', 'slug-converter']
        ],
        'lorem-ipsum-generator' => [
            'name' => 'مولد نصوص لوريم إيبسوم عربي ولاتيني',
            'description' => 'توليد نصوص نصية تجريبية وهمية باللغة العربية الفصحى أو اللاتينية بعدد الكلمات والفقرات',
            'category' => 'dev',
            'icon' => 'fa-align-left',
            'keywords' => ['لوريم ايبسوم', 'نص تجريبي', 'نص وهمي', 'lorem ipsum', 'كلام تجريبي'],
            'popular' => true,
            'related' => ['word-counter', 'text-formatter', 'html-page-generator']
        ],
        'random-string-generator' => [
            'name' => 'توليد سلاسل نصية عشوائية',
            'description' => 'توليد سلاسل نصية عشوائية للأكواد ومفاتيح التفعيل ورموز التحقق (Tokens & Keys)',
            'category' => 'dev',
            'icon' => 'fa-random',
            'keywords' => ['نص عشوائي', 'توليد كود', 'random string', 'token generator'],
            'related' => ['password-generator', 'uuid-generator', 'random-number-generator']
        ],
        'random-number-generator' => [
            'name' => 'توليد أرقام عشوائية ضمن مجال',
            'description' => 'توليد رقم عشوائي أو قائمة أرقام فريدة بين قيمتين محددتين للسحوبات والقرعة والبرمجة',
            'category' => 'dev',
            'icon' => 'fa-dice',
            'keywords' => ['رقم عشوائي', 'قرعة', 'سحب عشوائي', 'random number', 'dice'],
            'related' => ['random-string-generator', 'password-generator', 'timestamp-converter']
        ],
        'case-converter' => [
            'name' => 'محول تسمية المتغيرات البرمجية (Case Converter)',
            'description' => 'التحويل بين صيغ التسمية: camelCase, snake_case, PascalCase, kebab-case, CONSTANT_CASE',
            'category' => 'dev',
            'icon' => 'fa-font',
            'keywords' => ['camelCase', 'snake_case', 'kebab-case', 'case converter', 'تسمية المتغيرات'],
            'related' => ['text-formatter', 'slug-converter', 'clean-arabic-text']
        ],
        'html-entity-converter' => [
            'name' => 'تشفير وفك تشفير رموز HTML Entities',
            'description' => 'تحويل الرموز الخاصة والأقواس إلى كيانات HTML (&lt; &gt; &amp;) والعكس لحماية الأكواد',
            'category' => 'dev',
            'icon' => 'fa-code-branch',
            'keywords' => ['html entities', 'ترميز html', 'html entity encode', 'رموز خاصة'],
            'related' => ['html-formatter', 'clean-html-text', 'url-encode']
        ],
        'url-parser' => [
            'name' => 'تحليل الروابط وفصل المعاملات (URL Parser)',
            'description' => 'تفكيك أي رابط URL إلى عناصره الأساسية: البروتوكول، النطاق، المسار، وباراميترات الاستعلام Query',
            'category' => 'dev',
            'icon' => 'fa-sitemap',
            'keywords' => ['تحليل رابط', 'url parser', 'query params', 'باراميترات الرابط'],
            'related' => ['url-encode', 'domain-suggester', 'qr-generator']
        ],
        'user-agent-parser' => [
            'name' => 'تحليل User Agent ومعلومات المتصفح',
            'description' => 'فحص كود الـ User Agent واكتشاف نوع المتصفح، نظام التشغيل، نوع الجهاز، والمحرك بدقة',
            'category' => 'dev',
            'icon' => 'fa-info-circle',
            'keywords' => ['user agent', 'معلومات المتصفح', 'نوع الجهاز', 'نظام التشغيل', 'ua parser'],
            'related' => ['url-parser', 'timestamp-converter', 'json-formatter']
        ],
        'cron-expression-generator' => [
            'name' => 'مولد تعابير الجدولة (Cron Expression Generator)',
            'description' => 'إنشاء تعابير Cron لجدولة مهام الخادم والنسخ الاحتياطي عبر واجهة بصرية سهلة دون حفظ الصيغة',
            'category' => 'dev',
            'icon' => 'fa-clock',
            'keywords' => ['كرون جوب', 'cron job', 'cron generator', 'جدولة المهام', 'صيغة كرون'],
            'featured' => true,
            'related' => ['cron-expression-explainer', 'timestamp-converter', 'time-tracker']
        ],
        'cron-expression-explainer' => [
            'name' => 'شرح وتفسير تعبير Cron باللغة العربية',
            'description' => 'ترجمة أي تعبير Cron معقد إلى جملة عربية واضحة ومفهومة تبين أوقات التنفيذ القادمة',
            'category' => 'dev',
            'icon' => 'fa-comment-alt',
            'keywords' => ['شرح كرون', 'cron explainer', 'تفسير cron', 'متى يعمل كرون'],
            'related' => ['cron-expression-generator', 'timestamp-converter', 'time-tracker']
        ],
        'text-diff-checker' => [
            'name' => 'مقارنة النصوص واكتشاف الفروقات (Diff Checker)',
            'description' => 'مقارنة نصين أو كودين جنبًا إلى جنب وإبراز الإضافات والحذوفات والتعديلات بدقة بصرية',
            'category' => 'dev',
            'icon' => 'fa-columns',
            'keywords' => ['مقارنة نصوص', 'مقارنة كود', 'diff checker', 'فروقات النص', 'كشف التعديلات'],
            'featured' => true, 'popular' => true,
            'related' => ['word-counter', 'json-formatter', 'text-formatter']
        ],

        // ============================================
        // الأدوات الأصلية الأساسية الـ 25 (تم دمجها وتحسينها)
        // ============================================
        'cv-generator' => [
            'name' => 'مولد سيرة ذاتية احترافية',
            'description' => 'إنشاء سيرة ذاتية عربية احترافية ومنظمة جاهزة للطباعة والتصدير والتقديم للوظائف',
            'category' => 'career',
            'icon' => 'fa-file-alt',
            'keywords' => ['سيرة ذاتية', 'سي في', 'cv', 'resume', 'انشاء سيرة ذاتية'],
            'featured' => true, 'popular' => true,
            'related' => ['cover-letter', 'proposal-generator', 'portfolio-generator']
        ],
        'portfolio-generator' => [
            'name' => 'مولد بورتفوليو نصي',
            'description' => 'إنشاء صفحة بورتفوليو نصية واحترافية تعرض مشاريعك وخبراتك البرمجية والمهنية',
            'category' => 'career',
            'icon' => 'fa-briefcase',
            'keywords' => ['بورتفوليو', 'معرض اعمال', 'portfolio', 'مشاريعي'],
            'related' => ['cv-generator', 'readme-generator', 'bio-generator']
        ],
        'proposal-generator' => [
            'name' => 'مولد رسالة تقديم للعمل الحر',
            'description' => 'صياغة عروض تقديم مقنعة ومخصصة لمنصات العمل الحر (مستقل، خمسات، Upwork)',
            'category' => 'career',
            'icon' => 'fa-envelope-open-text',
            'keywords' => ['بروبوزال', 'عرض سعر', 'رسالة تقديم', 'proposal', 'مستقل'],
            'popular' => true,
            'related' => ['cover-letter', 'cv-generator', 'freelancer-project-price-calculator']
        ],
        'readme-generator' => [
            'name' => 'مولد README احترافي لمشاريع GitHub',
            'description' => 'إنشاء ملف README.md متكامل واحترافي يرفع من شأن مستودعاتك البرمجية على غيت هاب',
            'category' => 'career',
            'icon' => 'fa-book',
            'keywords' => ['readme', 'ريدمي', 'github readme', 'توثيق المشروع'],
            'related' => ['code-templates', 'markdown-editor', 'portfolio-generator']
        ],
        'cover-letter' => [
            'name' => 'مولد Cover Letter للتقدم للوظائف',
            'description' => 'كتابة خطاب تغطية رسمي ومؤثر للتقدم للشركات والمؤسسات باللغة العربية',
            'category' => 'career',
            'icon' => 'fa-file-signature',
            'keywords' => ['خطاب تغطية', 'كفر لتر', 'cover letter', 'تقديم وظيفة'],
            'related' => ['cv-generator', 'proposal-generator', 'bio-generator']
        ],
        'bio-generator' => [
            'name' => 'مولد نبذة تعريفية احترافية (Bio)',
            'description' => 'توليد نبذة شخصية وجذابة لحسابات لينكد إن، تويتر، وبايو منصات التواصل والعمل',
            'category' => 'career',
            'icon' => 'fa-id-card',
            'keywords' => ['بايو', 'نبذة شخصية', 'bio generator', 'لينكد ان'],
            'related' => ['cv-generator', 'cover-letter', 'username-generator']
        ],
        'project-ideas' => [
            'name' => 'مولد ومقترح أفكار المشاريع',
            'description' => 'اقتراح أفكار مشاريع برمجية مبتكرة حسب تخصصك ومستواك المهني لبناء البورتفوليو',
            'category' => 'career',
            'icon' => 'fa-lightbulb',
            'keywords' => ['افكار مشاريع', 'فكرة مشروع', 'project ideas', 'بورتفوليو'],
            'popular' => true,
            'related' => ['project-name-generator', 'domain-suggester', 'code-templates']
        ],
        'username-generator' => [
            'name' => 'مولد أسماء مستخدمين إبداعية',
            'description' => 'توليد أسماء مستخدمين فريدة وغير مكررة للألعاب، المنصات الاجتماعية، والبريد',
            'category' => 'generators',
            'icon' => 'fa-user-tag',
            'keywords' => ['اسم مستخدم', 'يوزر نيم', 'username generator', 'اسماء فريدة'],
            'related' => ['project-name-generator', 'domain-suggester', 'bio-generator']
        ],
        'project-name-generator' => [
            'name' => 'مولد أسماء المشاريع والتطبيقات',
            'description' => 'توليد أسماء جذابة وعصرية للشركات الناشئة والمواقع والمنتجات الرقمية',
            'category' => 'generators',
            'icon' => 'fa-project-diagram',
            'keywords' => ['اسم مشروع', 'اسم تطبيق', 'project name', 'ستارت اب'],
            'related' => ['domain-suggester', 'username-generator', 'project-ideas']
        ],
        'domain-suggester' => [
            'name' => 'اقتراح وبحث أسماء الدومينات',
            'description' => 'توليد اقتراحات نطاقات مميزة لمشروعك مع امتدادات com, net, org, io',
            'category' => 'generators',
            'icon' => 'fa-globe',
            'keywords' => ['دومين', 'اسم نطاق', 'domain suggester', 'شراء دومين'],
            'related' => ['project-name-generator', 'username-generator', 'slug-converter']
        ],
        'color-palette' => [
            'name' => 'مولد لوحات الألوان المتناسقة',
            'description' => 'إنشاء لوحات ألوان متكاملة (متجاورة، متتامة، متناسقة) مع كود CSS للتصميم',
            'category' => 'generators',
            'icon' => 'fa-palette',
            'keywords' => ['الوان', 'لوحة الوان', 'تنسيق الوان', 'color palette', 'باليت الوان'],
            'featured' => true,
            'related' => ['color-converter', 'css-gradient-generator', 'css-box-shadow-generator']
        ],
        'code-templates' => [
            'name' => 'مكتبة قوالب الأكواد الجاهزة',
            'description' => 'قوالب برمجية سريعة بلغات HTML, CSS, JavaScript, PHP جاهزة للنسخ والاستخدام',
            'category' => 'dev',
            'icon' => 'fa-file-code',
            'keywords' => ['قوالب كود', 'كود جاهز', 'code templates', 'boilerplate'],
            'related' => ['html-page-generator', 'readme-generator', 'js-formatter']
        ],
        'html-page-generator' => [
            'name' => 'مولد صفحات HTML الجاهزة',
            'description' => 'توليد كود صفحة HTML5 كاملة متجاوبة ومهيأة للغة العربية (RTL) جاهزة للنشر',
            'category' => 'dev',
            'icon' => 'fa-html5',
            'keywords' => ['صفحة html', 'توليد صفحة', 'html boilerplate', 'html generator'],
            'related' => ['code-templates', 'html-formatter', 'markdown-to-html']
        ],
        'invoice-generator' => [
            'name' => 'مولد الفواتير الاحترافية',
            'description' => 'إنشاء فواتير مهنية متكاملة ببنود وأسعار متعددة، العملات العربية، وتصديرها للطباعة',
            'category' => 'productivity',
            'icon' => 'fa-file-invoice-dollar',
            'keywords' => ['فاتورة', 'مولد فواتير', 'invoice generator', 'فاتورة مبيعات'],
            'featured' => true, 'popular' => true,
            'related' => ['budget-calculator', 'vat-calculator', 'discount-tax-calculator']
        ],
        'todo-list' => [
            'name' => 'قائمة المهام اليومية (To-Do List)',
            'description' => 'تنظيم مهامك وأولوياتك مع خاصية الحفظ المحلي والفرز والإنجاز الفوري',
            'category' => 'productivity',
            'icon' => 'fa-tasks',
            'keywords' => ['مهام', 'قائمة مهام', 'تودو', 'todo list', 'تنظيم الوقت'],
            'popular' => true,
            'related' => ['notes', 'time-tracker', 'daily-study-hours-calculator']
        ],
        'notes' => [
            'name' => 'الملاحظات السريعة والمفكرة',
            'description' => 'تدوين وحفظ وتنظيم ملاحظاتك وأفكارك بألوان مميزة وتثبيت الملاحظات الهامة',
            'category' => 'productivity',
            'icon' => 'fa-sticky-note',
            'keywords' => ['ملاحظات', 'مفكرة', 'تدوين', 'notes', 'حفظ ملاحظات'],
            'related' => ['todo-list', 'time-tracker', 'word-counter']
        ],
        'time-tracker' => [
            'name' => 'تتبع وقت العمل والمشاريع',
            'description' => 'ساعة إيقاف ومسجل زمني لتتبع وقت العمل على مشاريعك وإصدار تقارير الإنجاز',
            'category' => 'productivity',
            'icon' => 'fa-clock',
            'keywords' => ['تتبع الوقت', 'ساعة ايقاف', 'حساب وقت العمل', 'time tracker'],
            'related' => ['todo-list', 'project-hours-calculator', 'hourly-wage-calculator']
        ],
        'budget-calculator' => [
            'name' => 'حاسبة الميزانية البسيطة',
            'description' => 'تتبع الدخل والمصاريف وتسجيل المعاملات المالية لحساب الرصيد والموازنة الشخصية',
            'category' => 'productivity',
            'icon' => 'fa-calculator',
            'keywords' => ['ميزانية', 'دخل ومصاريف', 'رصيد', 'budget calculator'],
            'related' => ['family-monthly-budget-calculator', 'salary-division-calculator', 'invoice-generator']
        ]
    ];

    // إضافة معرف فرعي وترتيب تسلسلي
    $id = 1;
    $registry = [];
    foreach ($raw as $slug => $data) {
        $data['id'] = $id++;
        $data['slug'] = $slug;
        $data['status'] = 1;
        $data['sort_order'] = $data['id'];
        $data['usage_count'] = 0;
        $data['seoTitle'] = $data['name'] . ' | منصة الأدوات الشاملة';
        $data['seoDescription'] = $data['description'];
        $registry[$slug] = $data;
    }

    return $registry;
}

/**
 * جلب جميع الأدوات المسجلة
 */
function getAllRegisteredTools() {
    return array_values(getMasterToolsRegistry());
}

/**
 * جلب أداة عبر الـ slug
 */
function getRegisteredToolBySlug($slug) {
    $tools = getMasterToolsRegistry();
    return $tools[$slug] ?? null;
}

/**
 * جلب الأدوات حسب الفئة
 */
function getRegisteredToolsByCategory($category) {
    $tools = getMasterToolsRegistry();
    $result = [];
    foreach ($tools as $tool) {
        if ($tool['category'] === $category) {
            $result[] = $tool;
        }
    }
    return $result;
}

/**
 * البحث الذكي في سجل الأدوات
 */
function searchRegisteredTools($query) {
    $tools = getMasterToolsRegistry();
    $query = mb_strtolower(trim($query), 'UTF-8');
    if (empty($query)) return [];

    $results = [];
    
    // خريطة مرادفات عربية شائعة لربط الكلمات
    $synonyms = [
        'راتب' => ['مرتب', 'معاش', 'اجر', 'أجر', 'دخل', 'ساعي', 'يومي', 'خصومات', 'صافي'],
        'كهرباء' => ['واط', 'طاقة', 'امبير', 'انفرتر', 'بطارية', 'مولد', 'شمسية', 'استهلاك', 'كيلوواط'],
        'شمسية' => ['الواح', 'طاقة شمسية', 'الواح شمسية', 'انتاج', 'بطاريات شمسية'],
        'سفر' => ['رحلة', 'بنزين', 'وقود', 'مسافة', 'طيران', 'فندق', 'سيارة'],
        'بناء' => ['تشطيب', 'دهان', 'بلاط', 'اسمنت', 'رمل', 'طوب', 'بلوك', 'سيراميك'],
        'دراسة' => ['معدل', 'تراكمي', 'علامات', 'نجاح', 'امتحان', 'منهج', 'مذاكرة'],
        'مطور' => ['برمجة', 'كود', 'json', 'base64', 'jwt', 'html', 'css', 'sql'],
        'نص' => ['كلمات', 'احرف', 'ماركداون', 'تنظيف', 'تدقيق', 'سلاج', 'قراءة']
    ];

    $expandedQueries = [$query];
    foreach ($synonyms as $key => $syns) {
        if (mb_strpos($query, $key) !== false) {
            $expandedQueries = array_merge($expandedQueries, $syns);
        }
        foreach ($syns as $s) {
            if (mb_strpos($query, $s) !== false) {
                $expandedQueries[] = $key;
            }
        }
    }
    $expandedQueries = array_unique($expandedQueries);

    foreach ($tools as $tool) {
        $score = 0;
        $name = mb_strtolower($tool['name'], 'UTF-8');
        $desc = mb_strtolower($tool['description'], 'UTF-8');
        $slug = mb_strtolower($tool['slug'], 'UTF-8');
        $keywords = array_map(function($k) { return mb_strtolower($k, 'UTF-8'); }, $tool['keywords'] ?? []);

        // مطابقة الاسم مباشرة
        if (mb_strpos($name, $query) !== false) {
            $score += 50;
        }
        // مطابقة الكلمات المفتاحية
        foreach ($expandedQueries as $q) {
            if (in_array($q, $keywords)) {
                $score += 30;
            }
            if (mb_strpos($desc, $q) !== false) {
                $score += 15;
            }
            if (mb_strpos($name, $q) !== false) {
                $score += 20;
            }
            if (mb_strpos($slug, $q) !== false) {
                $score += 25;
            }
        }

        if ($score > 0) {
            $tool['search_score'] = $score;
            $results[] = $tool;
        }
    }

    // فرز النتائج حسب الأكثر صلة
    usort($results, function($a, $b) {
        return ($b['search_score'] ?? 0) <=> ($a['search_score'] ?? 0);
    });

    return $results;
}
