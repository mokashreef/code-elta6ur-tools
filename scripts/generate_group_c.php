<?php
/**
 * مولد أدوات المجموعة C: الكهرباء والطاقة (25 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsC = [
    'electricity-consumption-calculator' => [
        'title' => 'حاسبة استهلاك الكهرباء الشاملة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'deviceWatt', 'label' => 'قدرة الجهاز الإجمالية (بالواط Watt)', 'type' => 'number', 'default' => '1500', 'min' => '1', 'step' => '50'],
            ['id' => 'dailyHours', 'label' => 'ساعات التشغيل اليومية', 'type' => 'number', 'default' => '6', 'min' => '0.1', 'max' => '24', 'step' => '0.5'],
            ['id' => 'devicesCount', 'label' => 'عدد الأجهزة المماثلة', 'type' => 'number', 'default' => '1', 'min' => '1', 'step' => '1'],
            ['id' => 'kwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh) في منطقتك', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const watt = Math.max(1, parseFloat(document.getElementById('deviceWatt').value) || 1500);
            const hours = Math.max(0.1, Math.min(24, parseFloat(document.getElementById('dailyHours').value) || 6));
            const count = Math.max(1, parseInt(document.getElementById('devicesCount').value) || 1);
            const price = Math.max(0.01, parseFloat(document.getElementById('kwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            const dailyKwh = (watt * hours * count) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;
            const yearlyCost = monthlyCost * 12;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة الاستهلاك الشهري');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك اليومي بالكيلوواط ساعة', value: dailyKwh.toFixed(2) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري (30 يوماً)', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'التكلفة السنوية التقديرية', value: formatMoney(yearlyCost, curr), color: '#8b5cf6' },
                { label: 'سعر الكيلوواط المعتمد', value: formatMoney(price, curr) + ' / kWh', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تشغيل \${count > 1 ? count + ' أجهزة' : 'جهاز'} بقدرة إجمالية <strong>\${watt * count} واط</strong> لمدة <strong>\${hours} ساعات يومياً</strong> يستهلك <strong>\${monthlyKwh.toFixed(1)} كيلوواط ساعة شهرياً</strong> وتكلفته <strong>\${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'الاستهلاك بالكيلوواط ساعة (kWh) = (القدرة بالواط × ساعات التشغيل) ÷ 1000.',
            'التكلفة الشهرية = الاستهلاك الشهري بالكيلوواط ساعة × سعر الكيلوواط في شريحتك.',
            'سعر الكيلوواط ساعة يختلف حسب شرائح الاستهلاك السكني في كل دولة.'
        ],
        'assumptions' => 'سعر الكيلوواط الافتراضي 0.18 ر.س في السعودية للشريحة الأولى (حتى 6000 كيلوواط). يمكنك تغييره حسب فاتورتك.',
        'faqs' => [
            ['q' => 'كيف أعرف قدرة جهازي بالواط؟', 'a' => 'تجد ملصقاً خلف الجهاز أو أسفله مكتوباً عليه القوة برمز (W) مثل 2000W، أو مكتوباً عليه الفولتية (220V) والتيار بالأمبير (A)، وحاصل ضربهما يعطي الواط.']
        ],
        'related' => ['ac-consumption-calculator', 'fridge-consumption-calculator', 'device-monthly-cost-calculator']
    ],

    'fridge-consumption-calculator' => [
        'title' => 'حاسبة استهلاك الثلاجة للكهرباء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'fridgeSizeCategory', 'label' => 'حجم الثلاجة', 'type' => 'select', 'options' => [
                'small' => 'ثلاجة ميني بار صغيرة (4 إلى 6 قدم) ~ 150 kWh سنوياً',
                'medium' => 'ثلاجة عائلية متوسطة (12 إلى 16 قدم) ~ 350 kWh سنوياً',
                'large' => 'ثلاجة كبيرة بابين / دولابي Side-by-Side (18 إلى 24 قدم) ~ 550 kWh سنوياً'
            ], 'default' => 'medium'],
            ['id' => 'efficiencyStars', 'label' => 'مستوى كفاءة الطاقة للثلاجة (النجوم)', 'type' => 'select', 'options' => [
                'inverter_top' => 'إنفرتر حديث موفر جداً (أعلى تصنيف A / 5-6 نجوم)',
                'standard' => 'تصنيف متوسط عادي (3-4 نجوم)',
                'old' => 'ثلاجة قديمة عادية (نجمة إلى نجمتين / موديل قديم +40% استهلاك)'
            ], 'default' => 'inverter_top'],
            ['id' => 'kwhCostFridge', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const size = document.getElementById('fridgeSizeCategory').value;
            const stars = document.getElementById('efficiencyStars').value;
            const price = Math.max(0.01, parseFloat(document.getElementById('kwhCostFridge').value) || 0.18);
            const curr = getSelectedCurrency();

            let baseAnnualKwh = 350;
            if (size === 'small') baseAnnualKwh = 150;
            if (size === 'large') baseAnnualKwh = 550;

            let mult = 0.8; // موفر انفرتر
            if (stars === 'standard') mult = 1.0;
            if (stars === 'old') mult = 1.45;

            const annualKwh = baseAnnualKwh * mult;
            const monthlyKwh = annualKwh / 12;
            const dailyKwh = annualKwh / 365;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الثلاجة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك السنوي الإجمالي', value: annualKwh.toFixed(0) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري التقديري', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'الاستهلاك اليومي المتوسط', value: dailyKwh.toFixed(2) + ' kWh', color: '#f59e0b' },
                { label: 'التكلفة السنوية الكاملة', value: formatMoney(annualKwh * price, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك هذه الثلاجة حوالي <strong>\${annualKwh.toFixed(0)} كيلوواط ساعة سنوياً</strong>، وتكلفك تقريباً <strong>\${formatMoney(monthlyCost, curr)} شهرياً</strong>. كمبروسر الثلاجة لا يعمل طوال الـ 24 ساعة، بل يفصل ويعمل دورياً للحفاظ على البرودة.</p>
            `);
        ",
        'points' => [
            'الثلاجة تعمل متصلة بالكهرباء 24 ساعة ولكن الضاغط (الكمبروسر) يعمل فعلياً بين 6 إلى 10 ساعات فقط يومياً حسب حرارة الجو وتكرار فتح الباب.',
            'الثلاجات بتقنية الإنفرتر توفر ما بين 30% إلى 45% من استهلاك الكهرباء مقارنة بالموديلات القديمة.'
        ],
        'assumptions' => 'يفترض ضبط الترموستات على درجة حرارة معتدلة (4 درجات للثلاجة و -18 للفريزر).',
        'faqs' => [
            ['q' => 'كيف أقلل استهلاك الثلاجة للكهرباء؟', 'a' => 'اترك مسافة 10 سم خلف الثلاجة لتهوية المكثف، وتأكد من سلامة الجوان المطاطي للباب لمنع تسرب البرودة، وتجنب وضع الأطعمة الساخنة مباشرة بداخلها.']
        ],
        'related' => ['electricity-consumption-calculator', 'ac-consumption-calculator', 'washing-machine-consumption-calculator']
    ],

    'ac-consumption-calculator' => [
        'title' => 'حاسبة استهلاك المكيف للكهرباء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'acCapacity', 'label' => 'سعة المكيف التبريدية', 'type' => 'select', 'options' => [
                '12000' => '1 طن (12,000 وحدة BTU) - للغرف الصغيرة حتى 14 م²',
                '18000' => '1.5 طن (18,000 وحدة BTU) - للغرف المتوسطة حتى 22 م²',
                '24000' => '2 طن (24,000 وحدة BTU) - للصالات والغرف الكبيرة حتى 32 م²',
                '30000' => '2.5 طن (30,000 وحدة BTU) - للمجالس والصالات المفتوحة',
                '36000' => '3 طن (36,000 وحدة BTU) - وحدات دولابية أو كونسيلد كبيرة'
            ], 'default' => '18000'],
            ['id' => 'acTechnology', 'label' => 'تقنية التكييف', 'type' => 'select', 'options' => [
                'inverter' => 'إنفرتر موفر للطاقة (Inverter) - يوفر 35% إلى 50%',
                'conventional' => 'عادي بدون إنفرتر (نظام On/Off التقليدي)'
            ], 'default' => 'inverter'],
            ['id' => 'acDailyHours', 'label' => 'متوسط ساعات التشغيل يومياً', 'type' => 'number', 'default' => '10', 'min' => '1', 'max' => '24', 'step' => '1'],
            ['id' => 'acKwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const btu = parseInt(document.getElementById('acCapacity').value) || 18000;
            const tech = document.getElementById('acTechnology').value;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('acDailyHours').value) || 10));
            const price = Math.max(0.01, parseFloat(document.getElementById('acKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            // القدرة القصوى بالواط تقريباً: 18000 btu ~ 1600-1800 واط
            let fullPowerWatts = (btu / 12000) * 1200;
            // متوسط معامل التحميل الفعلي (الكمبروسر يفصل بعد الوصول للحرارة)
            let loadFactor = tech === 'inverter' ? 0.55 : 0.75;
            const actualHourlyWatt = fullPowerWatts * loadFactor;

            const dailyKwh = (actualHourlyWatt * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل المكيف في الشهر');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري للكهرباء', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'متوسط سحب الطاقة أثناء التشغيل', value: actualHourlyWatt.toFixed(0) + ' واط', color: '#f59e0b' },
                { label: 'التكلفة لموسم الصيف (5 أشهر)', value: formatMoney(monthlyCost * 5, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مكيف بسعة <strong>\${btu.toLocaleString()} BTU</strong> بتقنية <strong>\${tech === 'inverter' ? 'إنفرتر موفر' : 'تقليدي'}</strong> يعمل <strong>\${hours} ساعات يومياً</strong>، يستهلك حوالي <strong>\${monthlyKwh.toFixed(1)} kWh شهرياً</strong> وتكلفته <strong>\${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'المكيف يمثل ما بين 60% إلى 70% من فاتورة الكهرباء المنزلية في أشهر الصيف.',
            'ضبط درجة الحرارة على 24° مئوية بدلاً من 18° يوفر حوالي 25% من استهلاك المكيف للطاقة دون المساس بالراحة.'
        ],
        'assumptions' => 'يفترض غرفة معزولة حرارياً بأبواب ونوافذ محكمة الإغلاق.',
        'faqs' => [
            ['q' => 'هل مكيف الإنفرتر يستحق فارق السعر عند الشراء؟', 'a' => 'نعم بكل تأكيد؛ فإذا كنت تشغل المكيف أكثر من 8 ساعات يومياً في الصيف، فإن الوفر في فاتورة الكهرباء يسترد فارق سعر جهاز الإنفرتر خلال سنة ونصف إلى سنتين فقط.']
        ],
        'related' => ['electricity-consumption-calculator', 'device-monthly-cost-calculator', 'solar-panels-calculator']
    ],

    'washing-machine-consumption-calculator' => [
        'title' => 'حاسبة استهلاك الغسالة للكهرباء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'washTempMode', 'label' => 'درجة حرارة مياه الغسيل', 'type' => 'select', 'options' => [
                'cold' => 'غسيل بماء بارد (بدون سخان كهربائي) ~ 0.25 kWh للدورة',
                'warm40' => 'غسيل بماء دافئ 40 درجة مئوية ~ 0.70 kWh للدورة',
                'hot60' => 'غسيل بماء ساخن 60-90 درجة (تعقيم كامل) ~ 1.80 kWh للدورة'
            ], 'default' => 'warm40'],
            ['id' => 'washesPerWeek', 'label' => 'عدد دورات الغسيل في الأسبوع', 'type' => 'number', 'default' => '5', 'min' => '1', 'max' => '30', 'step' => '1'],
            ['id' => 'washKwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const temp = document.getElementById('washTempMode').value;
            const washes = Math.max(1, parseInt(document.getElementById('washesPerWeek').value) || 5);
            const price = Math.max(0.01, parseFloat(document.getElementById('washKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let kwhPerCycle = 0.70;
            if (temp === 'cold') kwhPerCycle = 0.25;
            if (temp === 'hot60') kwhPerCycle = 1.80;

            const monthlyCycles = washes * 4.33;
            const monthlyKwh = monthlyCycles * kwhPerCycle;
            const monthlyCost = monthlyKwh * price;
            const costPerCycle = kwhPerCycle * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الغسالة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة دورة الغسيل الواحدة', value: formatMoney(costPerCycle, curr), color: '#3b82f6' },
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'عدد دورات الغسيل شهرياً', value: Math.round(monthlyCycles) + ' غسلة', color: '#f59e0b' },
                { label: 'استهلاك الدورة الواحدة', value: kwhPerCycle.toFixed(2) + ' kWh', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشغيل الغسالة بمعدل <strong>\${washes} غسلات أسبوعياً</strong> يستهلك حوالي <strong>\${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة <strong>\${formatMoney(monthlyCost, curr)}</strong>. معظم استهلاك الغسالة يذهب لتسخين المياه وليس لتحريك الحلة.']</p>
            `);
        ",
        'points' => [
            'أكثر من 85% إلى 90% من استهلاك الغسالة للكهرباء يذهب لتسخين المياه عبر السخان المدمج (Heater).',
            'الغسيل بماء بارد (30 درجة أو أقل) يوفر حتى 70% من كهرباء الغسالة مع الحفاظ على نظافة الملابس باستخدام مساحيق حديثة.'
        ],
        'assumptions' => 'القيم لغسالة أوتوماتيك أمامية قياسية سعة 7 إلى 9 كجم.',
        'faqs' => [
            ['q' => 'هل الغسالة ذات الفتحة العلوية تستهلك كهرباء أقل؟', 'a' => 'الغسالات العلوية لا تحتوي غالباً على سخان مدمج وتسحب ماء ساخناً من سخان المنزل، لذلك تستهلك كهرباء مباشرة أقل ولكنها تستهلك كمية أكبر من المياه.']
        ],
        'related' => ['water-heater-consumption-calculator', 'electricity-consumption-calculator', 'fridge-consumption-calculator']
    ],

    'water-heater-consumption-calculator' => [
        'title' => 'حاسبة استهلاك السخان الكهربائي للماء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'heaterCapacity', 'label' => 'سعة خزان السخان (باللتر)', 'type' => 'select', 'options' => [
                '50' => 'سخان 50 لتر (1200 واط - لشخص إلى شخصين)',
                '80' => 'سخان 80 لتر (1500 واط - لأسرة متوسطة)',
                '100' => 'سخان 100 لتر (2000 واط - سعة كبيرة)',
                'instant' => 'سخان فوري بدون خزان (Tankless Instant) ~ 6000 إلى 8000 واط'
            ], 'default' => '80'],
            ['id' => 'heaterOperatingHours', 'label' => 'ساعات عمل الهيتر الفعلي يومياً (إعادة التسخين)', 'type' => 'number', 'default' => '3.5', 'min' => '0.5', 'max' => '12', 'step' => '0.5'],
            ['id' => 'heaterKwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const cap = document.getElementById('heaterCapacity').value;
            const hours = Math.max(0.5, Math.min(12, parseFloat(document.getElementById('heaterOperatingHours').value) || 3.5));
            const price = Math.max(0.01, parseFloat(document.getElementById('heaterKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let watts = 1500;
            if (cap === '50') watts = 1200;
            if (cap === '100') watts = 2000;
            if (cap === 'instant') watts = 6500;

            let actualHours = hours;
            if (cap === 'instant') actualHours = 0.5; // الفوري يعمل فقط وقت الاستحمام

            const dailyKwh = (watts * actualHours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة سخان الماء شهرياً في الشتاء');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'قدرة عنصر التسخين (الهيتر)', value: watts + ' واط', color: '#f59e0b' },
                { label: 'تكلفة الاستهلاك لموسم الشتاء (4 أشهر)', value: formatMoney(monthlyCost * 4, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>سخان بقدرة <strong>\${watts} واط</strong> يعمل بمعدل <strong>\${actualHours} ساعات تسخين يومياً</strong> يستهلك <strong>\${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة تقدر بـ <strong>\${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'سخان الخزان يفقد حرارة باستمرار من خلال الجدران ويعيد التسخين حتى لو لم يُستخدم الماء (Standby Heat Loss).',
            'ضبط ترموستات السخان عند 60 درجة مئوية يوفر الأمان ويمنع ترسب الأملاح الكلسية ويوفر الطاقة.'
        ],
        'assumptions' => 'استهلاك الشتاء يكون أعلى بسبب انخفاض درجة حرارة مياه الشبكة القادمة من الخزانات.',
        'faqs' => [
            ['q' => 'هل من الأفضل ترك السخان يعمل 24 ساعة أم تشغيله قبل الاستخدام فقط؟', 'a' => 'إذا كان السخان معزولاً جيداً وتستخدمه الأسرة دورياً فالأفضل تركه على الترموستات 60°، أما إذا كان الاستخدام قليلاً فالأفضل تشغيله قبل الاستحمام بساعة عبر مؤقت ذكي (Smart Plug).']
        ],
        'related' => ['electricity-consumption-calculator', 'washing-machine-consumption-calculator', 'solar-panels-calculator']
    ],

    'pc-power-consumption-calculator' => [
        'title' => 'حاسبة استهلاك الكمبيوتر للكهرباء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'pcType', 'label' => 'نوع الكمبيوتر والمواصفات', 'type' => 'select', 'options' => [
                'laptop_office' => 'لابتوب مكتبي أو دراسي خفيف ~ 45 إلى 65 واط',
                'laptop_gaming' => 'لابتوب ألعاب وتصميم قوي ~ 150 إلى 230 واط',
                'desktop_office' => 'كمبيوتر مكتبي PC للأعمال المكتبية ~ 120 واط',
                'desktop_gaming_mid' => 'كمبيوتر ألعاب متوسط (RTX 4060) ~ 300 واط',
                'desktop_workstation' => 'كمبيوتر ألعاب ورندر فائق (RTX 4080/4090) ~ 550 إلى 750 واط'
            ], 'default' => 'desktop_gaming_mid'],
            ['id' => 'monitorsCount', 'label' => 'عدد الشاشات المتصلة', 'type' => 'number', 'default' => '1', 'min' => '0', 'max' => '4', 'step' => '1'],
            ['id' => 'pcDailyHours', 'label' => 'ساعات الاستخدام اليومية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
            ['id' => 'pcKwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const type = document.getElementById('pcType').value;
            const monitors = Math.max(0, parseInt(document.getElementById('monitorsCount').value) || 1);
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('pcDailyHours').value) || 8));
            const price = Math.max(0.01, parseFloat(document.getElementById('pcKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let pcWatts = 300;
            if (type === 'laptop_office') pcWatts = 50;
            if (type === 'laptop_gaming') pcWatts = 180;
            if (type === 'desktop_office') pcWatts = 120;
            if (type === 'desktop_workstation') pcWatts = 650;

            const monitorsWatts = monitors * 35; // الشاشة تستهلك 30-40 واط
            const totalWatts = pcWatts + monitorsWatts;

            const dailyKwh = (totalWatts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الكمبيوتر شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري الإجمالي', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'مجموع سحب الطاقة الفعلي', value: totalWatts + ' واط', color: '#f59e0b' },
                { label: 'التكلفة السنوية للتشغيل', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يستهلك جهازك مع \${monitors} شاشة حوالي <strong>\${totalWatts} واط</strong> في الساعة. عند تشغيله <strong>\${hours} ساعات يومياً</strong>، يستهلك <strong>\${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة <strong>\${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'باور سبلاي الكمبيوتر (مثلاً 750W) لا يسحب 750 واط طوال الوقت، بل يسحب فقط ما تطلبه القطع بناءً على ضغط المعالج وكرت الشاشة.',
            'اللابتوبات تستهلك طاقة أقل بنسبة 70% إلى 80% مقارنة بأجهزة الكمبيوتر المكتبية المماثلة في الأداء.'
        ],
        'assumptions' => 'يفترض مزيجاً بين التصفح الخفيف وساعات تشغيل الألعاب أو برامج التصميم.',
        'faqs' => [
            ['q' => 'كم واط تستهلك الشاشة في وضع الاستعداد (Sleep Mode)؟', 'a' => 'في وضع السكون تستهلك الشاشة والكمبيوتر أقل من 1 إلى 2 واط فقط بفضل معايير كفاءة الطاقة الحديثة (Energy Star).']
        ],
        'related' => ['tv-power-consumption-calculator', 'electricity-consumption-calculator', 'ups-size-calculator']
    ],

    'tv-power-consumption-calculator' => [
        'title' => 'حاسبة استهلاك التلفزيون للكهرباء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'tvScreenSize', 'label' => 'حجم شاشة التلفزيون وتقنيتها', 'type' => 'select', 'options' => [
                '32_led' => 'شاشة 32 بوصة LED ~ 35 إلى 45 واط',
                '43_led' => 'شاشة 43 بوصة 4K LED ~ 65 واط',
                '55_led' => 'شاشة 55 بوصة 4K LED ~ 95 واط',
                '65_led' => 'شاشة 65 بوصة 4K LED ~ 130 واط',
                '65_oled' => 'شاشة 65 بوصة OLED / QLED فائقة السطوع ~ 170 واط',
                '75_plus' => 'شاشة ضخمة 75-85 بوصة ~ 220 إلى 280 واط'
            ], 'default' => '55_led'],
            ['id' => 'tvDailyHours', 'label' => 'ساعات المشاهدة والتشغيل يومياً', 'type' => 'number', 'default' => '6', 'min' => '1', 'max' => '24', 'step' => '1'],
            ['id' => 'tvKwhPrice', 'label' => 'سعر الكيلوواط ساعة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const size = document.getElementById('tvScreenSize').value;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('tvDailyHours').value) || 6));
            const price = Math.max(0.01, parseFloat(document.getElementById('tvKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let watts = 95;
            if (size === '32_led') watts = 40;
            if (size === '43_led') watts = 65;
            if (size === '65_led') watts = 130;
            if (size === '65_oled') watts = 170;
            if (size === '75_plus') watts = 250;

            const dailyKwh = (watts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل التلفزيون شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'قدرة التلفزيون الفعلية', value: watts + ' واط', color: '#f59e0b' },
                { label: 'التكلفة السنوية الإجمالية', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشغيل الشاشة بقدرة <strong>\${watts} واط</strong> لمدة <strong>\${hours} ساعات يومياً</strong> يستهلك <strong>\${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة زهيدة تقدر بـ <strong>\${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'التلفزيونات الحديثة المعتمدة على إضاءة LED اقتصادية جداً في استهلاك الطاقة مقارنة بالمكيفات والسخانات.',
            'تفعيل خاصية السطوع التلقائي (Eco Sensor) يوفر حوالي 20% من استهلاك الشاشة في الغرف المظلمة ليلاً.'
        ],
        'assumptions' => 'يفترض مستوى سطوع قياسي متوسط وتفعيل وضع توفير الطاقة الذكي.',
        'faqs' => [
            ['q' => 'هل شاشات OLED تستهلك كهرباء أكثر من LED؟', 'a' => 'شاشات OLED تستهلك طاقة أعلى قليلاً عند عرض مشاهد بيضاء وساطعة بالكامل، لكنها تصبح فائقة التوفير وتطفي البكسلات تماماً عند عرض المشاهد السوداء الداكنة.']
        ],
        'related' => ['pc-power-consumption-calculator', 'electricity-consumption-calculator', 'device-monthly-cost-calculator']
    ],

    'device-monthly-cost-calculator' => [
        'title' => 'حاسبة تكلفة تشغيل جهاز شهرياً',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'inputWatts', 'label' => 'استهلاك الجهاز بالواط (Watt)', 'type' => 'number', 'default' => '2000', 'min' => '1', 'step' => '10'],
            ['id' => 'inputHoursDay', 'label' => 'ساعات التشغيل اليومية', 'type' => 'number', 'default' => '4', 'min' => '0.1', 'max' => '24', 'step' => '0.5'],
            ['id' => 'inputTariff', 'label' => 'سعر الكيلوواط ساعة في الشريحة (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const watts = Math.max(1, parseFloat(document.getElementById('inputWatts').value) || 2000);
            const hours = Math.max(0.1, Math.min(24, parseFloat(document.getElementById('inputHoursDay').value) || 4));
            const tariff = Math.max(0.01, parseFloat(document.getElementById('inputTariff').value) || 0.18);
            const curr = getSelectedCurrency();

            const dailyKwh = (watts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * tariff;
            const hourlyCost = (watts / 1000) * tariff;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الجهاز في الشهر');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة تشغيل الجهاز في الساعة الواحدة', value: formatMoney(hourlyCost, curr), color: '#3b82f6' },
                { label: 'الاستهلاك الشهري (kWh)', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'الاستهلاك اليومي (kWh)', value: dailyKwh.toFixed(2) + ' kWh', color: '#f59e0b' },
                { label: 'التكلفة السنوية للجهاز', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>كل ساعة تشغيل لهذا الجهاز تكلفك <strong>\${formatMoney(hourlyCost, curr)}</strong>. إجمالي الفاتورة الشهرية الخاصة به <strong>\${formatMoney(monthlyCost, curr)}</strong> بناءً على <strong>\${hours} ساعات يومياً</strong>.</p>
            `);
        ",
        'points' => [
            'تكلفة الساعة = (الواط ÷ 1000) × سعر الكيلوواط.',
            'التكلفة الشهرية = تكلفة الساعة × عدد الساعات اليومية × 30 يوماً.'
        ],
        'assumptions' => 'يفترض تشغيل مستمر بثبات للحمل المكتوب.',
        'faqs' => [
            ['q' => 'ما هي أكثر الأجهزة استهلاكاً للكهرباء في المنزل؟', 'a' => 'المكيفات، سخانات المياه، أفران الكهرباء، والمكواة، ومجففات الملابس لأنها تعتمد على عناصر تسخين أو ضواغط تستهلك ما بين 1500 إلى 3000 واط.']
        ],
        'related' => ['electricity-consumption-calculator', 'ac-consumption-calculator', 'water-heater-consumption-calculator']
    ],

    'ups-size-calculator' => [
        'title' => 'حاسبة حجم UPS المناسب',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalLoadWatts', 'label' => 'إجمالي قدرة الأجهزة المراد حمايتها (بالواط Watt)', 'type' => 'number', 'default' => '600', 'min' => '50', 'step' => '50'],
            ['id' => 'powerFactor', 'label' => 'معامل القدرة (Power Factor) - المعتاد 0.7 إلى 0.8', 'type' => 'number', 'default' => '0.7', 'min' => '0.5', 'max' => '1.0', 'step' => '0.05'],
            ['id' => 'safetyMargin', 'label' => 'هامش الأمان والتوسع المستقبلي (%)- الموصى به 25%', 'type' => 'number', 'default' => '25', 'min' => '10', 'max' => '50', 'step' => '5'],
        ],
        'calcJs' => "
            const watts = Math.max(50, parseFloat(document.getElementById('totalLoadWatts').value) || 600);
            const pf = Math.max(0.5, Math.min(1.0, parseFloat(document.getElementById('powerFactor').value) || 0.7));
            const margin = Math.max(10, parseFloat(document.getElementById('safetyMargin').value) || 25) / 100;

            const wattsWithMargin = watts * (1 + margin);
            const requiredVA = wattsWithMargin / pf;
            // أحجام الـ UPS القياسية في السوق
            const standardSizes = [650, 850, 1000, 1200, 1500, 2000, 3000, 5000, 6000, 10000];
            let recommendedSize = standardSizes.find(s => s >= requiredVA) || Math.ceil(requiredVA / 1000) * 1000;

            setPrimaryResult(recommendedSize + ' VA (فولت أمبير)', 'الحجم القياسي الموصى به لـ UPS');
            showResultArea();

            setDetailStats([
                { label: 'القدرة الحسابية الدقيقة المطلوبة', value: Math.ceil(requiredVA) + ' VA', color: '#3b82f6' },
                { label: 'إجمالي الحمل بالواط مع الأمان', value: Math.ceil(wattsWithMargin) + ' واط', color: '#10b981' },
                { label: 'أقصى حمل صافي للجهاز المقترح', value: Math.round(recommendedSize * pf) + ' واط', color: '#f59e0b' },
                { label: 'معامل القدرة المعتمد', value: pf.toFixed(2), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتشغيل أحمال فعلية قدرها <strong>\${watts} واط</strong> بأمان واستقرار، تحتاج إلى وحدة UPS بقدرة لا تقل عن <strong>\${recommendedSize} VA</strong> لتفادي التحميل الزائد عند انقطاع التيار.</p>
            `);
        ",
        'points' => [
            'القدرة الظاهرية (VA) = القدرة الفعالة (الواط) ÷ معامل القدرة (Power Factor).',
            'إضافة هامش أمان 25% ضروري جداً لتحمل تيارات البدء المفاجئة ولإمكانية إضافة أجهزة جديدة مستقبلاً.'
        ],
        'assumptions' => 'معظم أجهزة الكمبيوتر والسيرفرات المنزلية لها معامل قدرة يتراوح بين 0.65 إلى 0.75.',
        'faqs' => [
            ['q' => 'ما الفرق بين الواط (Watt) والفولت أمبير (VA)؟', 'a' => 'الواط هو القدرة الحقيقية المستهلكة من الجهاز، بينما الـ VA هو حاصل ضرب الجهد في التيار في دوائر التيار المتردد، وعادة ما تكون قيمة الـ VA أعلى من الواط بسبب المقاومة الحثية والسعوية.']
        ],
        'related' => ['ups-runtime-calculator', 'inverter-size-calculator', 'battery-count-calculator']
    ],

    'ups-runtime-calculator' => [
        'title' => 'حاسبة مدة تشغيل UPS والبطاريات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'batteryAh', 'label' => 'سعة البطارية الإجمالية (أمبير-ساعة Ah)', 'type' => 'number', 'default' => '100', 'min' => '7', 'step' => '5'],
            ['id' => 'systemVoltage', 'label' => 'جهد بنك البطاريات (فولت V)', 'type' => 'select', 'options' => [
                '12' => '12 فولت (بطارية واحدة)',
                '24' => '24 فولت (بطاريتين على التوالي)',
                '48' => '48 فولت (4 بطاريات على التوالي)'
            ], 'default' => '12'],
            ['id' => 'connectedLoadWatts', 'label' => 'الحمل الفعلي المتصل (بالواط Watt)', 'type' => 'number', 'default' => '250', 'min' => '10', 'step' => '10'],
            ['id' => 'inverterEfficiency', 'label' => 'كفاءة محول الـ UPS (%)- المعتاد 85%', 'type' => 'number', 'default' => '85', 'min' => '70', 'max' => '95', 'step' => '5'],
            ['id' => 'batteryTypeDod', 'label' => 'نوع البطارية وعمق التفريغ المسموح (DoD)', 'type' => 'select', 'options' => [
                'lead_50' => 'بطارية رصاص / جيل / AGM عادية (تفريغ 50% لحمايتها)',
                'lead_70' => 'بطارية جيل ديب سايكل عالية الجودة (تفريغ 70%)',
                'lithium_90' => 'بطارية ليثيوم LiFePO4 حديثة (تفريغ 90%)'
            ], 'default' => 'lead_50'],
        ],
        'calcJs' => "
            const ah = Math.max(7, parseFloat(document.getElementById('batteryAh').value) || 100);
            const volt = parseFloat(document.getElementById('systemVoltage').value) || 12;
            const load = Math.max(10, parseFloat(document.getElementById('connectedLoadWatts').value) || 250);
            const eff = Math.max(70, parseFloat(document.getElementById('inverterEfficiency').value) || 85) / 100;
            const dodType = document.getElementById('batteryTypeDod').value;

            let dod = 0.50;
            if (dodType === 'lead_70') dod = 0.70;
            if (dodType === 'lithium_90') dod = 0.90;

            const totalWattHours = ah * volt;
            const usableWattHours = totalWattHours * dod * eff;
            const runtimeHours = usableWattHours / load;

            const hoursInt = Math.floor(runtimeHours);
            const minutesInt = Math.round((runtimeHours - hoursInt) * 60);

            let timeStr = '';
            if (hoursInt > 0) timeStr += hoursInt + ' ساعة ';
            if (minutesInt > 0) timeStr += 'و ' + minutesInt + ' دقيقة';
            if (timeStr === '') timeStr = 'أقل من دقيقة';

            setPrimaryResult(timeStr, 'مدة التشغيل المتوقعة للأجهزة');
            showResultArea();

            setDetailStats([
                { label: 'سعة التخزين الكلية للبطارية', value: totalWattHours + ' واط-ساعة (Wh)', color: '#3b82f6' },
                { label: 'الطاقة الفعلية القابلة للاستخدام', value: Math.round(usableWattHours) + ' Wh', color: '#10b981' },
                { label: 'سحب التيار من البطارية', value: ((load / eff) / volt).toFixed(1) + ' أمبير DC', color: '#f59e0b' },
                { label: 'عمق التفريغ المعتمد (DoD)', value: (dod * 100) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بنك بطاريات بسعة <strong>\${ah} Ah</strong> عند جهد <strong>\${volt}V</strong>، يشغل حملاً قدره <strong>\${load} واط</strong> لمدة <strong>\${timeStr}</strong> متواصلة مع الحفاظ على عمر البطارية من التلف.</p>
            `);
        ",
        'points' => [
            'الطاقة الكلية للبطارية (واط-ساعة Wh) = سعة البطارية (Ah) × جهد البطارية (V).',
            'مدة التشغيل = (الطاقة الكلية × نسبة عمق التفريغ DoD × كفاءة الانفرتر) ÷ قدرة الحمل بالواط.',
            'تفريغ بطاريات الرصاص لأكثر من 50% يقلل عدد دورات حياتها بشكل حاد ويؤدي إلى تلفها السريع.'
        ],
        'assumptions' => 'يفترض بطارية جديدة بحالة ممتازة وكفاءة تحويل 85% لدارات الـ UPS.',
        'faqs' => [
            ['q' => 'لماذا تدوم بطاريات الليثيوم مدة أطول في التشغيل؟', 'a' => 'لأن بطاريات الليثيوم (LiFePO4) تسمح بتفريغ 90% من طاقتها بأمان دون ضرر، بينما بطاريات الجيل والرصاص تفرغ 50% فقط، إضافة إلى عدم تأثر الليثيوم بانخفاض الجهد السريع (Peukert Effect).']
        ],
        'related' => ['ups-size-calculator', 'battery-count-calculator', 'battery-charging-time-calculator']
    ],

    'inverter-size-calculator' => [
        'title' => 'حاسبة حجم الانفرتر (المحول)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'continuousLoad', 'label' => 'الأحمال المستمرة العادية (إضاءة، شاشات، مراوح، كمبيوتر) بالواط', 'type' => 'number', 'default' => '800', 'min' => '50', 'step' => '50'],
            ['id' => 'inductiveLoad', 'label' => 'أحمال محركات ومضخات وثلاجات (لها تيار إقلاع Surge) بالواط', 'type' => 'number', 'default' => '400', 'min' => '0', 'step' => '50'],
            ['id' => 'surgeMultiplier', 'label' => 'معامل تيار بدء التشغيل للمحركات (Surge Multiplier)', 'type' => 'number', 'default' => '2.5', 'min' => '1.5', 'max' => '5.0', 'step' => '0.5'],
        ],
        'calcJs' => "
            const cont = Math.max(50, parseFloat(document.getElementById('continuousLoad').value) || 800);
            const ind = Math.max(0, parseFloat(document.getElementById('inductiveLoad').value) || 400);
            const mult = Math.max(1.5, parseFloat(document.getElementById('surgeMultiplier').value) || 2.5);

            const totalContinuousWatts = cont + ind;
            // القدرة القصوى اللحظية
            const surgeWatts = cont + (ind * mult);
            // الحجم المستمر المطلوب مع هامش أمان 25%
            const recommendedContinuousRating = Math.ceil((totalContinuousWatts * 1.25) / 100) * 100;
            // الحجم القياسي بالـ KVA
            const kvaRating = (recommendedContinuousRating / 0.8) / 1000;

            setPrimaryResult(recommendedContinuousRating + ' واط مستمر (' + kvaRating.toFixed(1) + ' KVA)', 'حجم الانفرتر المقترح');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الأحمال المستمرة العادية', value: totalContinuousWatts + ' واط', color: '#3b82f6' },
                { label: 'تيار الإقلاع اللحظي المتوقع (Surge)', value: Math.ceil(surgeWatts) + ' واط', color: '#ef4444' },
                { label: 'الجهد الموصى به لبنك البطاريات', value: recommendedContinuousRating > 2000 ? '48 فولت' : (recommendedContinuousRating > 1000 ? '24 فولت' : '12 فولت'), color: '#10b981' },
                { label: 'نوع الموجة الموصى به', value: 'موجة جيبية نقية (Pure Sine Wave)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج إلى انفرتر بقدرة مستمرة <strong>\${recommendedContinuousRating} واط</strong> وقدرة إقلاع لحظية لا تقل عن <strong>\${Math.ceil(surgeWatts)} واط</strong> لتشغيل محركات الثلاجة والمضخة دون انقطاع أو إعادة تشغيل الجهاز.</p>
            `);
        ",
        'points' => [
            'المحركات والكمبروسرات (مثل الثلاجات والمضخات ومكيفات الهواء) تسحب عند الإقلاع تياراً يعادل 2.5 إلى 4 أضعاف قدرتها الاسمية لجزء من الثانية.',
            'يجب دائماً اختيار انفرتر بموجة جيبية نقية (Pure Sine Wave) لحماية الأجهزة الإلكترونية والمحركات من الاحتراق والضجيج.'
        ],
        'assumptions' => 'يفترض تشغيل محرك واحد كبير في نفس اللحظة مع بقية الأجهزة المستمرة.',
        'faqs' => [
            ['q' => 'ما الفرق بين الموجة الجيبية النقية (Pure Sine Wave) والموجة المعدلة (Modified Sine Wave)؟', 'a' => 'الموجة النقية تطابق كهرباء الدولة تماماً وتشغل جميع الأجهزة بأمان، بينما الموجة المعدلة تتسبب في سخونة المحركات وضجيج المراوح وتلف الشواحن الذكية.']
        ],
        'related' => ['ups-size-calculator', 'battery-count-calculator', 'solar-panels-calculator']
    ],

    'battery-count-calculator' => [
        'title' => 'حاسبة عدد البطاريات المطلوبة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'dailyEnergyNeedWh', 'label' => 'إجمالي الاستهلاك اليومي المطلوب تغطيته بالبطاريات (واط-ساعة Wh)', 'type' => 'number', 'default' => '3000', 'min' => '200', 'step' => '100'],
            ['id' => 'selectedBatteryAh', 'label' => 'سعة البطارية الواحدة المتوفرة في السوق (Ah)', 'type' => 'select', 'options' => [
                '100' => '100 أمبير-ساعة Ah',
                '150' => '150 أمبير-ساعة Ah',
                '200' => '200 أمبير-ساعة Ah (المقاس الأكثر شيوعاً)',
                '250' => '250 أمبير-ساعة Ah'
            ], 'default' => '200'],
            ['id' => 'batteryTypeChemistry', 'label' => 'نوع البطارية وعمق التفريغ (DoD)', 'type' => 'select', 'options' => [
                'gel_50' => 'بطارية جيل / تيوبلار Tublar (عمق تفريغ 50%)',
                'lithium_85' => 'بطارية ليثيوم LiFePO4 (عمق تفريغ 85%)'
            ], 'default' => 'gel_50'],
            ['id' => 'targetSysVoltage', 'label' => 'جهد نظام الانفرتر المستهدف', 'type' => 'select', 'options' => [
                '12' => '12 فولت',
                '24' => '24 فولت',
                '48' => '48 فولت'
            ], 'default' => '24'],
        ],
        'calcJs' => "
            const wh = Math.max(200, parseFloat(document.getElementById('dailyEnergyNeedWh').value) || 3000);
            const ah = parseFloat(document.getElementById('selectedBatteryAh').value) || 200;
            const chem = document.getElementById('batteryTypeChemistry').value;
            const sysVolt = parseFloat(document.getElementById('targetSysVoltage').value) || 24;

            const dod = chem === 'lithium_85' ? 0.85 : 0.50;
            const inverterLosses = 0.85; // كفاءة الانفرتر

            // سعة البطارية الواحدة بالواط-ساعة
            const singleBatteryWh = ah * 12; // معظم البطاريات الفردية 12V
            const singleBatteryUsableWh = singleBatteryWh * dod * inverterLosses;
            const rawCount = wh / singleBatteryUsableWh;
            // يجب أن يكون العدد مضاعفاً لجهد النظام
            const seriesCount = sysVolt / 12;
            let finalCount = Math.ceil(rawCount / seriesCount) * seriesCount;
            if (finalCount < seriesCount) finalCount = seriesCount;

            const totalAhBank = (finalCount / seriesCount) * ah;

            setPrimaryResult(finalCount + ' بطاريات (سعة ' + ah + ' Ah)', 'عدد البطاريات المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'سعة بنك البطاريات عند ' + sysVolt + 'V', value: totalAhBank.toFixed(0) + ' Ah (' + (totalAhBank * sysVolt) + ' Wh)', color: '#3b82f6' },
                { label: 'طريقة الربط المطلوبة', value: seriesCount + ' على التوالي × ' + (finalCount / seriesCount) + ' توازي', color: '#10b981' },
                { label: 'عمق التفريغ المعتمد (DoD)', value: (dod * 100) + '%', color: '#f59e0b' },
                { label: 'الطاقة القابلة للاستخدام يومياً', value: Math.round(finalCount * singleBatteryUsableWh) + ' Wh', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية استهلاك <strong>\${wh} واط-ساعة</strong> على نظام <strong>\${sysVolt} فولت</strong>، تحتاج إلى <strong>\${finalCount} بطاريات سعة \${ah} Ah</strong> (12V) بنظام توصيل <strong>\${seriesCount} توالي × \${finalCount/seriesCount} توازي</strong>.</p>
            `);
        ",
        'points' => [
            'الاستهلاك بالواط-ساعة (Wh) = مجموع قدرات الأجهزة × ساعات تشغيلها.',
            'سعة البطارية القابلة للاستخدام = سعة البطارية (Wh) × عمق التفريغ المسموح به × كفاءة التحويل.',
            'عدد البطاريات يجب أن يكون دائماً من مضاعفات جهد النظام (بطاريتان لنظام 24V، و 4 بطاريات لنظام 48V).'
        ],
        'assumptions' => 'يفترض بطاريات فردية بجهد اسمي 12 فولت موصولة بمحول مناسب.',
        'faqs' => [
            ['q' => 'لماذا يُفضل نظام 24V أو 48V بدلاً من 12V؟', 'a' => 'لأنه عند رفع الجهد يقل التيار المار في الأسلاك للنصف أو للربع، مما يقلل من فواقد الطاقة ويسمح باستخدام كابلات أقل سماكة ويحمي الانفرتر من السخونة الزائدة.']
        ],
        'related' => ['battery-wiring-calculator', 'battery-charging-time-calculator', 'solar-batteries-calculator']
    ],

    'battery-wiring-calculator' => [
        'title' => 'حاسبة توصيل البطاريات تسلسلي / توازي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'individualVolt', 'label' => 'جهد البطارية الفردية (فولت V) - عادة 12V', 'type' => 'number', 'default' => '12', 'min' => '2', 'max' => '48', 'step' => '2'],
            ['id' => 'individualAh', 'label' => 'سعة البطارية الفردية (أمبير-ساعة Ah)', 'type' => 'number', 'default' => '150', 'min' => '10', 'step' => '10'],
            ['id' => 'batteriesInSeries', 'label' => 'عدد البطاريات الموصلة على التوالي (Series)', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '8', 'step' => '1'],
            ['id' => 'parallelStrings', 'label' => 'عدد السلاسل الموصلة على التوازي (Parallel Strings)', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '8', 'step' => '1'],
        ],
        'calcJs' => "
            const v = Math.max(2, parseFloat(document.getElementById('individualVolt').value) || 12);
            const ah = Math.max(10, parseFloat(document.getElementById('individualAh').value) || 150);
            const series = Math.max(1, parseInt(document.getElementById('batteriesInSeries').value) || 2);
            const parallel = Math.max(1, parseInt(document.getElementById('parallelStrings').value) || 2);

            const totalBatteries = series * parallel;
            const finalVoltage = v * series;
            const finalAh = ah * parallel;
            const totalWh = finalVoltage * finalAh;
            const totalKwh = totalWh / 1000;

            setPrimaryResult(finalVoltage + 'V @ ' + finalAh + 'Ah (' + totalKwh.toFixed(2) + ' kWh)', 'المواصفات الإجمالية لبنك البطاريات');
            showResultArea();

            setDetailStats([
                { label: 'الجهد الإجمالي الناتج (Voltage)', value: finalVoltage + ' فولت V', color: '#3b82f6' },
                { label: 'السعة الإجمالية الناتجة (Capacity)', value: finalAh + ' أمبير-ساعة Ah', color: '#10b981' },
                { label: 'إجمالي عدد البطاريات المستخدمة', value: totalBatteries + ' بطاريات', color: '#f59e0b' },
                { label: 'إجمالي الطاقة المخزنة بالكامل', value: totalKwh.toFixed(2) + ' kWh (' + totalWh + ' Wh)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بتوصيل <strong>\${totalBatteries} بطاريات</strong> (\${series} توالي × \${parallel} توازي)، يرتفع الجهد إلى <strong>\${finalVoltage} فولت</strong> والسعة إلى <strong>\${finalAh} Ah</strong>، ما يوفر طاقة تخزينية إجمالية قدرها <strong>\${totalKwh.toFixed(2)} كيلوواط ساعة</strong>.</p>
            `);
        ",
        'points' => [
            'التوصيل على التوالي (Series: موجب بسالب): يجمع الفولتية وتبقى السعة (Ah) ثابتة.',
            'التوصيل على التوازي (Parallel: موجب بموجب وسالب بسالب): تبقى الفولتية ثابتة وتُجمع السعة (Ah).',
            'التوصيل المركب (توالي وتوازي معاً): يرفع الجهد والسعة في نفس الوقت لتغذية محولات الطاقة الكبيرة.'
        ],
        'assumptions' => 'يجب أن تكون جميع البطاريات المربوطة من نفس النوع والماركة والسعة والعمر الافتراضي تماماً.',
        'faqs' => [
            ['q' => 'ما هو خطر توصيل بطاريات بسعات أو أعمار مختلفة معاً؟', 'a' => 'البطارية الأضعف أو الأقدم ستفرغ أسرع وتسحب شحنة البطاريات الأقوى، مما يتسبب في تلف المنظومة بالكامل وحدوث حرارة وسخونة مفرطة.']
        ],
        'related' => ['battery-count-calculator', 'battery-charging-time-calculator', 'inverter-size-calculator']
    ],

    'battery-charging-time-calculator' => [
        'title' => 'حاسبة مدة شحن البطارية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'chargeBatteryAh', 'label' => 'سعة البطارية (أمبير-ساعة Ah)', 'type' => 'number', 'default' => '150', 'min' => '7', 'step' => '5'],
            ['id' => 'chargerCurrentAmps', 'label' => 'تيار الشاحن أو منظم الشحن الشمسي (أمبير A)', 'type' => 'number', 'default' => '20', 'min' => '1', 'max' => '150', 'step' => '1'],
            ['id' => 'remainingChargePercent', 'label' => 'نسبة الشحن الحالية في البطارية (%)', 'type' => 'number', 'default' => '30', 'min' => '0', 'max' => '95', 'step' => '5'],
            ['id' => 'chargingEfficiency', 'label' => 'كفاءة الشحن (عادة 80% للرصاص و 95% لليثيوم)', 'type' => 'number', 'default' => '85', 'min' => '70', 'max' => '98', 'step' => '1'],
        ],
        'calcJs' => "
            const ah = Math.max(7, parseFloat(document.getElementById('chargeBatteryAh').value) || 150);
            const amps = Math.max(1, parseFloat(document.getElementById('chargerCurrentAmps').value) || 20);
            const remaining = Math.max(0, Math.min(95, parseFloat(document.getElementById('remainingChargePercent').value) || 30)) / 100;
            const eff = Math.max(70, Math.min(98, parseFloat(document.getElementById('chargingEfficiency').value) || 85)) / 100;

            const neededAh = ah * (1 - remaining);
            // وقت الشحن = الأمبير-ساعة المطلوبة / (تيار الشحن * الكفاءة)
            const chargeHours = neededAh / (amps * eff);
            const hoursInt = Math.floor(chargeHours);
            const minutesInt = Math.round((chargeHours - hoursInt) * 60);

            // تيار الشحن الموصى به لسلامة البطارية (0.1C إلى 0.2C)
            const minSafeAmps = ah * 0.10;
            const maxSafeAmps = ah * 0.20;

            setPrimaryResult(hoursInt + ' ساعات و ' + minutesInt + ' دقيقة', 'الوقت المتوقع لاكتمال الشحن 100%');
            showResultArea();

            setDetailStats([
                { label: 'الأمبير المطلوب تعويضه', value: neededAh.toFixed(1) + ' Ah', color: '#3b82f6' },
                { label: 'التيار الآمن الموصى به للبطارية (0.1C-0.2C)', value: minSafeAmps.toFixed(0) + ' إلى ' + maxSafeAmps.toFixed(0) + ' أمبير', color: '#10b981' },
                { label: 'نسبة الشحن المفقودة المعوضة', value: ((1 - remaining) * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'إجمالي الساعات العشرية', value: chargeHours.toFixed(1) + ' ساعة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لشحن بطارية سعة <strong>\${ah} Ah</strong> من نسبة <strong>\${(remaining*100).toFixed(0)}%</strong> حتى الامتلاء بتيار <strong>\${amps} أمبير</strong>، يستغرق الشحن حوالي <strong>\${hoursInt} ساعات و \${minutesInt} دقيقة</strong>.</p>
            `);
        ",
        'points' => [
            'التيار المثالي لشحن بطاريات الجيل والرصاص هو 10% إلى 15% من سعتها (قاعدة C/10).',
            'الشحن بتيار مرتفع جداً يقلل وقت الشحن ولكنه يرفع حرارة البطارية ويتلف ألواح الرصاص الداخلية.'
        ],
        'assumptions' => 'يفترض شاحناً ذكياً متعدد المراحل (Bulk, Absorption, Float).',
        'faqs' => [
            ['q' => 'هل يمكن شحن بطارية 100 Ah بشاحن 40 أمبير؟', 'a' => 'لا يُنصح بذلك لبطاريات الرصاص لأن تيار 40A يمثل 40% من السعة وهو تيار مفرط يتلفها، بينما بطاريات الليثيوم تقبل تيارات شحن عالية تصل إلى 0.5C بأمان تام.']
        ],
        'related' => ['ups-runtime-calculator', 'battery-count-calculator', 'solar-batteries-calculator']
    ],

    'generator-fuel-calculator' => [
        'title' => 'حاسبة استهلاك المولد للوقود',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'genKvaRating', 'label' => 'قدرة المولد (بالـ KVA)', 'type' => 'number', 'default' => '15', 'min' => '1', 'step' => '1'],
            ['id' => 'fuelTypeGen', 'label' => 'نوع وقود المولد', 'type' => 'select', 'options' => [
                'diesel' => 'ديزل (سولار) - كفاءة أعلى واستهلاك أقل ~ 0.28 لتر / KVA / ساعة',
                'gasoline' => 'بنزين (غازولين) - استهلاك أعلى ~ 0.38 لتر / KVA / ساعة'
            ], 'default' => 'diesel'],
            ['id' => 'loadPercentageGen', 'label' => 'نسبة التحميل على المولد', 'type' => 'select', 'options' => [
                '25' => 'تحميل خفيف (25%)',
                '50' => 'تحميل متوسط (50%)',
                '75' => 'تحميل قياسي موصى به (75%)',
                '100' => 'تحميل كامل أقصى (100%)'
            ], 'default' => '75'],
            ['id' => 'genDailyRunHours', 'label' => 'ساعات التشغيل اليومية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
        ],
        'calcJs' => "
            const kva = Math.max(1, parseFloat(document.getElementById('genKvaRating').value) || 15);
            const fuel = document.getElementById('fuelTypeGen').value;
            const loadPercent = parseFloat(document.getElementById('loadPercentageGen').value) || 75;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('genDailyRunHours').value) || 8));

            // معدل استهلاك اللتر لكل KVA عند الحمل الكامل
            const fullLoadRate = fuel === 'diesel' ? 0.28 : 0.38;
            // الاستهلاك الفعلي يتناسب تقريباً مع التحميل + نسبة احتكاك المحرك
            const factor = (loadPercent / 100) * 0.75 + 0.25;
            const litersPerHour = kva * fullLoadRate * factor;

            const dailyLiters = litersPerHour * hours;
            const monthlyLiters = dailyLiters * 30;

            setPrimaryResult(litersPerHour.toFixed(2) + ' لتر وقود في الساعة', 'معدل استهلاك المولد للوقود');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك اليومي (' + hours + ' ساعات)', value: dailyLiters.toFixed(1) + ' لتر', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري التقديري', value: monthlyLiters.toFixed(0) + ' لتر', color: '#10b981' },
                { label: 'القدرة الفعلية المولدة بالكيلوواط (kW)', value: (kva * 0.8 * (loadPercent/100)).toFixed(1) + ' kW', color: '#f59e0b' },
                { label: 'نوع الوقود المعتمد', value: fuel === 'diesel' ? 'ديزل (سولار)' : 'بنزين', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مولد بقدرة <strong>\${kva} KVA</strong> يعمل بوقود <strong>\${fuel === 'diesel' ? 'الديزل' : 'البنزين'}</strong> عند نسبة تحميل <strong>\${loadPercent}%</strong>، يستهلك حوالي <strong>\${litersPerHour.toFixed(2)} لتر/ساعة</strong>، أي ما يعادل <strong>\${dailyLiters.toFixed(1)} لتر يومياً</strong>.</p>
            `);
        ",
        'points' => [
            'مولدات الديزل أكثر كفاءة في استهلاك الوقود بنسبة 30% إلى 40% مقارنة بمولدات البنزين ذات القدرة المماثلة.',
            'أفضل كفاءة تشغيلية للمولد وعمر أطول للمحرك تكون عند نسبة تحميل بين 70% إلى 80% من قدرته القصوى.'
        ],
        'assumptions' => 'يفترض محرك مولد سليم مع فلاتر هواء ووقود نظيفة.',
        'faqs' => [
            ['q' => 'هل تشغيل المولد بدون حمل (Idle) يوفر الوقود بشكل كامل؟', 'a' => 'لا؛ فالمحرك يستهلك حوالي 25% إلى 30% من استهلاكه الأقصى فقط للحفاظ على دورانه وتوليد التردد 50Hz حتى لو لم تكن هناك أجهزة متصلة.']
        ],
        'related' => ['generator-cost-calculator', 'generator-capacity-calculator', 'power-source-comparison-calculator']
    ],

    'generator-cost-calculator' => [
        'title' => 'حاسبة تكلفة تشغيل المولد',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'genFuelPerHour', 'label' => 'استهلاك المولد من الوقود (لتر في الساعة)', 'type' => 'number', 'default' => '3.5', 'min' => '0.2', 'step' => '0.1'],
            ['id' => 'fuelLiterPrice', 'label' => 'سعر لتر الوقود', 'type' => 'number', 'default' => '0.80', 'min' => '0.05', 'step' => '0.05'],
            ['id' => 'dailyHoursGenCost', 'label' => 'ساعات التشغيل اليومية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
            ['id' => 'oilChangeCostPer100h', 'label' => 'تكلفة غيار الزيت والفلاتر الدورية (كل 100 ساعة تشغيل)', 'type' => 'number', 'default' => '35', 'min' => '0', 'step' => '5'],
        ],
        'calcJs' => "
            const lph = Math.max(0.2, parseFloat(document.getElementById('genFuelPerHour').value) || 3.5);
            const literPrice = Math.max(0.05, parseFloat(document.getElementById('fuelLiterPrice').value) || 0.80);
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('dailyHoursGenCost').value) || 8));
            const oilMaint = Math.max(0, parseFloat(document.getElementById('oilChangeCostPer100h').value) || 35);
            const curr = getSelectedCurrency();

            const hourlyFuelCost = lph * literPrice;
            const hourlyMaintCost = oilMaint / 100;
            const totalHourlyCost = hourlyFuelCost + hourlyMaintCost;

            const dailyCost = totalHourlyCost * hours;
            const monthlyCost = dailyCost * 30;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'إجمالي تكلفة تشغيل المولد شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الساعة الواحدة (وقود وصيانة)', value: formatMoney(totalHourlyCost, curr), color: '#3b82f6' },
                { label: 'تكلفة الوقود اليومية', value: formatMoney(hourlyFuelCost * hours, curr), color: '#10b981' },
                { label: 'تكلفة الصيانة والزيوت شهرياً', value: formatMoney(hourlyMaintCost * hours * 30, curr), color: '#f59e0b' },
                { label: 'استهلاك الوقود الشهري باللتر', value: (lph * hours * 30).toFixed(0) + ' لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلفك تشغيل المولد <strong>\${formatMoney(totalHourlyCost, curr)} لكل ساعة</strong>. تبلغ الفاتورة الشهرية <strong>\${formatMoney(monthlyCost, curr)}</strong> شاملة الوقود وغيارات الزيت والفلاتر الدورية.</p>
            `);
        ",
        'points' => [
            'تكلفة المولد لا تقتصر على الوقود فقط؛ فتكاليف استهلاك الزيت والفلاتر والإهلاك تمثل بين 15% إلى 25% من تكلفة التشغيل الحقيقية.',
            'زيت المولد يتطلب تغييراً دورياً كل 100 إلى 150 ساعة تشغيل لحماية بساتم المحرك من الاحتراق.'
        ],
        'assumptions' => 'يفترض أسعار وقود وزيوت محلية مطابقة للمدخلات.',
        'faqs' => [
            ['q' => 'أيهما أرخص لتشغيل المنزل: المولد أم منظومة الطاقة الشمسية؟', 'a' => 'على المدى المتوسط (سنتين فأكثر)، تكون الطاقة الشمسية مع البطاريات أرخص بكثير؛ لأن تكلفة وقود وصيانة المولد شهرياً تتجاوز قيمة شراء الألواح الشمسية في فترة وجيزة.']
        ],
        'related' => ['generator-fuel-calculator', 'generator-capacity-calculator', 'power-source-comparison-calculator']
    ],

    'generator-capacity-calculator' => [
        'title' => 'حاسبة القدرة المطلوبة للمولد (KVA)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'runningWattsApp', 'label' => 'مجموع قدرات الأجهزة العادية (إنارة، شاشات، كمبيوتر) بالواط', 'type' => 'number', 'default' => '1200', 'min' => '50', 'step' => '50'],
            ['id' => 'motorsWattsApp', 'label' => 'مجموع قدرات المحركات والمكيفات والثلاجات بالواط', 'type' => 'number', 'default' => '2200', 'min' => '0', 'step' => '100'],
            ['id' => 'motorSurgeFactor', 'label' => 'معامل بدء التشغيل للمحرك الأكبر (Starting Surge)', 'type' => 'number', 'default' => '2.5', 'min' => '1.5', 'max' => '4.0', 'step' => '0.5'],
            ['id' => 'futureExpansion', 'label' => 'هامش التوسع الاحتياطي (%)- عادة 20%', 'type' => 'number', 'default' => '20', 'min' => '10', 'max' => '50', 'step' => '5'],
        ],
        'calcJs' => "
            const running = Math.max(50, parseFloat(document.getElementById('runningWattsApp').value) || 1200);
            const motors = Math.max(0, parseFloat(document.getElementById('motorsWattsApp').value) || 2200);
            const surge = Math.max(1.5, parseFloat(document.getElementById('motorSurgeFactor').value) || 2.5);
            const margin = Math.max(10, parseFloat(document.getElementById('futureExpansion').value) || 20) / 100;

            const totalContinuousWatts = (running + motors) * (1 + margin);
            // القدرة القصوى المطلوبة لحظة إقلاع المحركات
            const peakSurgeWatts = running + (motors * surge);
            // تحويل الواط إلى KVA (Power Factor للمولدات عادة 0.8)
            const kvaContinuous = (totalContinuousWatts / 0.8) / 1000;
            const kvaPeak = (peakSurgeWatts / 0.8) / 1000;

            const requiredKva = Math.max(kvaContinuous, kvaPeak * 0.8);
            const standardKvaSizes = [3.5, 5, 7.5, 10, 12.5, 15, 20, 25, 30, 45, 60, 100];
            const recommendedKva = standardKvaSizes.find(s => s >= requiredKva) || Math.ceil(requiredKva);

            setPrimaryResult(recommendedKva + ' KVA', 'قدرة المولد القياسي الموصى به');
            showResultArea();

            setDetailStats([
                { label: 'القدرة بالواط المستمر (kW)', value: (recommendedKva * 0.8).toFixed(1) + ' kW (' + (recommendedKva * 800) + ' واط)', color: '#3b82f6' },
                { label: 'أقصى حمل لحظي عند الإقلاع', value: Math.ceil(peakSurgeWatts) + ' واط', color: '#ef4444' },
                { label: 'الأحمال المستمرة المطلوبة', value: Math.ceil(running + motors) + ' واط', color: '#10b981' },
                { label: 'معامل القدرة المعتمد (PF)', value: '0.8 Cosφ', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتشغيل هذه الأحمال مع تيار إقلاع المحركات وأجهزة التكييف بأمان، تحتاج إلى مولد كهربائي بقدرة لا تقل عن <strong>\${recommendedKva} KVA</strong> (ما يعادل <strong>\${(recommendedKva * 0.8).toFixed(1)} كيلوواط صافي</strong>).</p>
            `);
        ",
        'points' => [
            'القدرة بالـ KVA = القدرة بالكيلوواط (kW) ÷ 0.8 (معامل القدرة القياسي للمولدات).',
            'مولدات الكهرباء لا يجب أن تعمل باستمرار عند 100% من طاقتها؛ بل بنسبة 75% إلى 80% لضمان عمر أطول وتفادي الانطفاء عند بدء تشغيل الأجهزة.'
        ],
        'assumptions' => 'يفترض تشغيل محرك تكييف أو ثلاجة واحدة في لحظة الإقلاع ذاتها.',
        'faqs' => [
            ['q' => 'لماذا ينطفئ المولد فجأة عند تشغيل الثلاجة أو الغطاس؟', 'a' => 'لأن تيار إقلاع المحرك يسحب للحظة طاقة تعادل 3 أضعاف طاقة المولد، وإذا لم تكن قدرة المولد كافية يهبط الجهد الكهربائي ويفصل قاطع الحماية (Overload).']
        ],
        'related' => ['generator-fuel-calculator', 'generator-cost-calculator', 'ups-size-calculator']
    ],

    'solar-panels-calculator' => [
        'title' => 'حاسبة الألواح الشمسية المطلوبة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'dailyKwhConsumption', 'label' => 'الاستهلاك اليومي المطلوب توليده (كيلوواط ساعة kWh)', 'type' => 'number', 'default' => '15', 'min' => '1', 'step' => '1'],
            ['id' => 'peakSunHours', 'label' => 'ساعات ذروة الشمس اليومية في منطقتك (Peak Sun Hours) - عربياً 5 إلى 6 ساعات', 'type' => 'number', 'default' => '5.5', 'min' => '3', 'max' => '8', 'step' => '0.5'],
            ['id' => 'panelWattRating', 'label' => 'قدرة اللوح الشمسي الواحد (واط Watt)', 'type' => 'select', 'options' => [
                '450' => 'لوح 450 واط مونو كريستالين',
                '550' => 'لوح 550 واط حديث (المقاس الأوسع انتشاراً حالياً)',
                '600' => 'لوح 600 واط تقنية TOPCon / Bifacial'
            ], 'default' => '550'],
            ['id' => 'systemLossesRate', 'label' => 'نسبة الفواقد البيئية والحرارة والتوصيل (%)- عادة 20%', 'type' => 'number', 'default' => '20', 'min' => '10', 'max' => '35', 'step' => '5'],
        ],
        'calcJs' => "
            const kwh = Math.max(1, parseFloat(document.getElementById('dailyKwhConsumption').value) || 15);
            const sunHours = Math.max(3, parseFloat(document.getElementById('peakSunHours').value) || 5.5);
            const panelW = parseFloat(document.getElementById('panelWattRating').value) || 550;
            const losses = Math.max(10, parseFloat(document.getElementById('systemLossesRate').value) || 20) / 100;

            // القدرة الإجمالية المطلوبة للألواح بالواط مع تعويض الفواقد
            const effectiveFactor = 1 - losses;
            const totalSystemWatts = (kwh * 1000) / (sunHours * effectiveFactor);
            const panelsCount = Math.ceil(totalSystemWatts / panelW);
            const actualTotalKw = (panelsCount * panelW) / 1000;
            const roofAreaNeeded = panelsCount * 2.5; // متوسط مساحة اللوح مع الممرات 2.5 م²

            setPrimaryResult(panelsCount + ' ألواح شمسية (' + actualTotalKw.toFixed(2) + ' kW)', 'عدد الألواح الشمسية المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'القدرة الإجمالية للألواح', value: actualTotalKw.toFixed(2) + ' kW (' + Math.round(panelsCount * panelW) + ' واط)', color: '#3b82f6' },
                { label: 'الإنتاج اليومي المتوقع في الصيف', value: (actualTotalKw * sunHours * effectiveFactor).toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'المساحة التقريبية المطلوبة على السطح', value: Math.ceil(roofAreaNeeded) + ' م²', color: '#f59e0b' },
                { label: 'قدرة اللوح المعتمد', value: panelW + ' واط', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتوليد <strong>\${kwh} كيلوواط ساعة يومياً</strong> في منطقة بساعات شمس <strong>\${sunHours} ساعات</strong>، تحتاج إلى <strong>\${panelsCount} لوح شمسي قدرة \${panelW}W</strong>، بإجمالي قدرة <strong>\${actualTotalKw.toFixed(2)} kW</strong> ومساحة سطح تقدر بـ <strong>\${Math.ceil(roofAreaNeeded)} م²</strong>.</p>
            `);
        ",
        'points' => [
            'القدرة الإجمالية للمنظومة (واط) = (الاستهلاك اليومي بالواط-ساعة) ÷ (ساعات ذروة الشمس × معامل كفاءة النظام).',
            'ساعات ذروة الشمس في الدول العربية والشرق الأوسط من بين الأعلى عالمياً وتتراوح بين 5.0 إلى 6.5 ساعة يومياً.',
            'فواقد النظام تشمل تأثير حرارة الصيف على كفاءة السيليكون، الغبار، وفواقد كابلات التيار المستمر DC والانفرتر.'
        ],
        'assumptions' => 'يفترض توجيه الألواح نحو الجنوب الجغرافي بزاوية ميل مطابقة لخط عرض المدينة (بين 25° إلى 32°).',
        'faqs' => [
            ['q' => 'هل تؤثر الحرارة الشديدة سلباً على كفاءة الألواح الشمسية؟', 'a' => 'نعم؛ يفقد اللوح الشمسي حوالي 0.35% من قدرته لكل درجة مئوية ترتفع فوق 25°، لذلك فالألواح تنتج في الأيام الربيعية المشمسة المعتدلة طاقة أكبر من أيام الصيف الحارقة جداً.']
        ],
        'related' => ['solar-batteries-calculator', 'solar-area-calculator', 'solar-yield-calculator']
    ],

    'solar-batteries-calculator' => [
        'title' => 'حاسبة البطاريات للطاقة الشمسية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'nightLoadKwh', 'label' => 'الاستهلاك الليلي المطلوب تغطيته من البطاريات (kWh)', 'type' => 'number', 'default' => '8', 'min' => '0.5', 'step' => '0.5'],
            ['id' => 'solarBattType', 'label' => 'نوع تقنية البطاريات', 'type' => 'select', 'options' => [
                'lithium' => 'ليثيوم فوسفات الحديد (LiFePO4) - تفريغ 85% وعمر 10 سنوات (الخيار الأفضل)',
                'tubular_gel' => 'جيل أو تيوبلار عميق التفريغ (Tubular Gel) - تفريغ 50% وعمر 3-4 سنوات'
            ], 'default' => 'lithium'],
            ['id' => 'solarBattVolt', 'label' => 'جهد نظام الانفرتر', 'type' => 'select', 'options' => [
                '24' => '24 فولت (للمنظومات الصغيرة والمتوسطة حتى 3kW)',
                '48' => '48 فولت (للمنظومات المنزلية الكبيرة 5kW فما فوق - القياسي)'
            ], 'default' => '48'],
            ['id' => 'solarBattAhSize', 'label' => 'سعة البطارية الواحدة المتوفرة', 'type' => 'select', 'options' => [
                '100' => '100 أمبير-ساعة Ah',
                '200' => '200 أمبير-ساعة Ah'
            ], 'default' => '100'],
        ],
        'calcJs' => "
            const kwh = Math.max(0.5, parseFloat(document.getElementById('nightLoadKwh').value) || 8);
            const type = document.getElementById('solarBattType').value;
            const volt = parseFloat(document.getElementById('solarBattVolt').value) || 48;
            const ah = parseFloat(document.getElementById('solarBattAhSize').value) || 100;

            const dod = type === 'lithium' ? 0.85 : 0.50;
            const inverterEff = 0.90;

            const neededWh = (kwh * 1000) / (dod * inverterEff);
            const totalAhAtSysVolt = neededWh / volt;

            // بطارية الليثيوم 48V 100Ah تمثل وحدة 5 kWh (Wall Mount)
            const lithiumModules5kwh = Math.ceil(kwh / (5 * dod * inverterEff));
            const gelBatteriesCount = Math.ceil((neededWh / (ah * 12)) / (volt / 12)) * (volt / 12);

            let primaryResultText = '';
            if (type === 'lithium') {
                primaryResultText = lithiumModules5kwh + ' بطارية ليثيوم حائطية (5 kWh / 48V)';
            } else {
                primaryResultText = gelBatteriesCount + ' بطاريات جيل (' + ah + 'Ah / 12V)';
            }

            setPrimaryResult(primaryResultText, 'سعة وبنك البطاريات المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'السعة الإجمالية المطلوبة لبنك البطاريات', value: Math.ceil(totalAhAtSysVolt) + ' Ah @ ' + volt + 'V', color: '#3b82f6' },
                { label: 'إجمالي طاقة التخزين الاسمية', value: (neededWh / 1000).toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'عمق التفريغ الآمن المعتمد', value: (dod * 100) + '%', color: '#f59e0b' },
                { label: 'العمر الافتراضي المتوقع للبطاريات', value: type === 'lithium' ? '8 إلى 12 سنة (6000 دورة)' : '2 إلى 4 سنوات (1200 دورة)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية استهلاك ليلي <strong>\${kwh} kWh</strong> مع مراعاة عمق التفريغ وكفاءة الانفرتر، تحتاج إلى \${type === 'lithium' ? '<strong>' + lithiumModules5kwh + ' وحدات بطاريات ليثيوم 5.12 kWh</strong>' : '<strong>' + gelBatteriesCount + ' بطارية جيل 12V سعة ' + ah + 'Ah</strong>'}.</p>
            `);
        ",
        'points' => [
            'بطاريات الليثيوم (LiFePO4) توفر أكثر من 5000 إلى 6000 دورة شحن وتفريغ، مقارنة بـ 1200 دورة فقط لبطاريات الجيل والرصاص.',
            'حجم البطارية يجب أن يغطي الأحمال الليلية الأساسية وأيام الطقس الغائم الجزئي.'
        ],
        'assumptions' => 'يفترض تغذية أحمال الإضاءة والتبريد الأساسية أثناء غياب الشمس.',
        'faqs' => [
            ['q' => 'هل بطاريات الليثيوم آمنة داخل المنازل؟', 'a' => 'نعم؛ خلايا ليثيوم فوسفات الحديد (LiFePO4) هي أكثر تقنيات الليثيوم أماناً واستقراراً كيميائياً في العالم، وتأتي بنظام إدارة ذكي مدمج (BMS) يمنع الشحن الزائد والحرارة والتماس الكهربائي.']
        ],
        'related' => ['solar-panels-calculator', 'battery-count-calculator', 'solar-system-cost-calculator']
    ],

    'solar-area-calculator' => [
        'title' => 'حاسبة مساحة الألواح الشمسية على السطح',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'panelsCountInput', 'label' => 'عدد الألواح الشمسية المخطط تركيبها', 'type' => 'number', 'default' => '16', 'min' => '1', 'step' => '1'],
            ['id' => 'roofTypeTilt', 'label' => 'نوع السطح وزاوية التركيب', 'type' => 'select', 'options' => [
                'flat_tilt' => 'سطح خرساني مستوٍ مع قواعد شاسيهات مائلة (يحتاج مسافات تباعد لمنع الظل) ~ 2.8 م²/لوح',
                'tilted_roof' => 'سطح مائل (قرميد أو زنك) يركب عليه اللوح مباشرة بدون تباعد ~ 2.4 م²/لوح'
            ], 'default' => 'flat_tilt'],
        ],
        'calcJs' => "
            const count = Math.max(1, parseInt(document.getElementById('panelsCountInput').value) || 16);
            const type = document.getElementById('roofTypeTilt').value;

            // مساحة اللوح الواحد 550 واط أبعاده 2.28 م × 1.13 م = 2.58 م²
            const netPanelArea = 2.58;
            const factor = type === 'flat_tilt' ? 2.8 : 2.58;
            const totalRequiredArea = count * factor;

            setPrimaryResult(Math.ceil(totalRequiredArea) + ' متر مربع', 'المساحة المطلوبة على السطح');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للألواح فقط', value: (count * netPanelArea).toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة ممرات الصيانة والتباعد لمنع الظلال', value: (totalRequiredArea - (count * netPanelArea)).toFixed(1) + ' م²', color: '#10b981' },
                { label: 'عدد الألواح الإجمالي', value: count + ' لوح', color: '#f59e0b' },
                { label: 'الوزن التقريبي للألواح والشاسيهات', value: Math.round(count * 32) + ' كجم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتركيب <strong>\${count} ألواح شمسية</strong> على \${type === 'flat_tilt' ? 'سطح خرساني بقواعد مائلة' : 'سطح مائل مباشر'}، تحتاج إلى مساحة حرة خالية من العوائق والظلال قدرها <strong>\${Math.ceil(totalRequiredArea)} متر مربع</strong> لضمان ممرات تنظيف وتفادي ظلال الصفوف الأمامية على الخلفية.</p>
            `);
        ",
        'points' => [
            'الأسطح المستوية تتطلب مسافة تباعد بين صفوف الألواح تعادل مرتين إلى مرتين ونصف ارتفاع اللوح لمنع إلقاء الظل في الشتاء.',
            'يجب ترك ممرات كافية بين المصفوفات (بعرض 60-80 سم) لتسهيل عمليات الغسيل والصيانة الدورية.'
        ],
        'assumptions' => 'أبعاد اللوح الشمسي المعتمدة: 2.28 متر طول × 1.13 متر عرض (ألواح 550-600W الحديثة).',
        'faqs' => [
            ['q' => 'هل يمكن تركيب الألواح فوق المظلات أو برجولات السطح؟', 'a' => 'نعم؛ ويعتبر خياراً معمارياً رائعاً لاستغلال مساحة السطح كجلسة مظللة وفي نفس الوقت توليد الطاقة الكهربائية النظيفة.']
        ],
        'related' => ['solar-panels-calculator', 'solar-yield-calculator', 'solar-system-cost-calculator']
    ],

    'solar-yield-calculator' => [
        'title' => 'حاسبة إنتاج الألواح الشمسية السنوي والشهري',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'systemSizeKw', 'label' => 'حجم المنظومة الشمسية بالكيلوواط (kWp)', 'type' => 'number', 'default' => '8.0', 'min' => '0.5', 'step' => '0.5'],
            ['id' => 'tiltAngleOrientation', 'label' => 'زاوية التوجيه والميل', 'type' => 'select', 'options' => [
                'optimal' => 'توجيه مثالي نحو الجنوب مع زاوية ميل مثالية (100% كفاءة)',
                'east_west' => 'توجيه شرق - غرب (إنتاج ممتد مع فاقد 12%)',
                'flat' => 'ألواح أفقية مسطحة تماماً بدون ميل (فاقد 15% وصعوبة تنظيف)'
            ], 'default' => 'optimal'],
            ['id' => 'yearlySunIndex', 'label' => 'معدل الإنتاج النوعي للمنطقة (kWh / kWp سنوياً) - المعتاد عربياً 1650 إلى 1850', 'type' => 'number', 'default' => '1750', 'min' => '1200', 'max' => '2200', 'step' => '50'],
        ],
        'calcJs' => "
            const kw = Math.max(0.5, parseFloat(document.getElementById('systemSizeKw').value) || 8.0);
            const orient = document.getElementById('tiltAngleOrientation').value;
            const index = Math.max(1200, parseFloat(document.getElementById('yearlySunIndex').value) || 1750);

            let factor = 1.0;
            if (orient === 'east_west') factor = 0.88;
            if (orient === 'flat') factor = 0.85;

            const annualKwh = kw * index * factor;
            const monthlyKwhAvg = annualKwh / 12;
            const dailyKwhAvg = annualKwh / 365;

            // وفر انبعاثات الكربون: حوالي 0.65 كجم CO2 لكل كيلوواط ساعة
            const co2SavedTons = (annualKwh * 0.65) / 1000;

            setPrimaryResult(Math.round(annualKwh).toLocaleString() + ' kWh سنوياً', 'إجمالي إنتاج الطاقة الكهربائية المتوقع');
            showResultArea();

            setDetailStats([
                { label: 'متوسط الإنتاج الشهري', value: Math.round(monthlyKwhAvg).toLocaleString() + ' kWh', color: '#3b82f6' },
                { label: 'متوسط الإنتاج اليومي', value: dailyKwhAvg.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'وفر انبعاثات الكربون السنوي', value: co2SavedTons.toFixed(1) + ' طن CO2', color: '#10b981' },
                { label: 'حجم المنظومة المعتمد', value: kw + ' kWp', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>منظومة شمسية بقدرة <strong>\${kw} كيلوواط</strong> تنتج حوالي <strong>\${Math.round(annualKwh).toLocaleString()} كيلوواط ساعة سنوياً</strong> (بمعدل <strong>\${dailyKwhAvg.toFixed(1)} kWh يومياً</strong>)، وتوفر انبعاث <strong>\${co2SavedTons.toFixed(1)} طن من غاز الكربون</strong> في الغلاف الجوي سنوياً.</p>
            `);
        ",
        'points' => [
            'الإنتاجية النوعية (Specific Yield) في المنطقة العربية تتراوح بين 1600 إلى 1900 كيلوواط ساعة لكل 1 كيلوواط من الألواح سنوياً.',
            'التوجيه جنوباً بزاوية 25°-30° يضمن تعامداً أمثل لأشعة الشمس واستغلالاً كاملاً لذروة الإنتاج.'
        ],
        'assumptions' => 'يفترض تنظيف دوري للألواح من الغبار كل أسبوعين إلى شهر.',
        'faqs' => [
            ['q' => 'كم تبلغ نسبة انخفاض إنتاج الألواح بسبب الغبار؟', 'a' => 'تراكم الغبار في المناطق الصحراوية والجافة قد يخفض إنتاج الألواح بنسبة تتراوح بين 10% إلى 25% إذا لم يتم تنظيفها بانتظام بالماء النظيف وممسحة السيليكون.']
        ],
        'related' => ['solar-panels-calculator', 'solar-payback-calculator', 'grid-vs-solar-calculator']
    ],

    'solar-system-cost-calculator' => [
        'title' => 'حاسبة تكلفة المنظومة الشمسية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'solarSysType', 'label' => 'نوع المنظومة الشمسية', 'type' => 'select', 'options' => [
                'ongrid' => 'منظومة متصلة بالشبكة الحكومية بدون بطاريات (On-Grid)',
                'hybrid_lithium' => 'منظومة هجينة متطورة مع بطاريات ليثيوم (Hybrid + LiFePO4)',
                'offgrid' => 'منظومة معزولة تماماً عن الشبكة مع بطاريات جيل (Off-Grid)'
            ], 'default' => 'hybrid_lithium'],
            ['id' => 'systemCapKw', 'label' => 'حجم المنظومة بالكيلوواط (kW)', 'type' => 'number', 'default' => '6.0', 'min' => '1', 'step' => '1'],
            ['id' => 'batteryCapacityKwh', 'label' => 'سعة البطاريات المطلوبة (كيلوواط ساعة kWh) - إذا كانت مع بطاريات', 'type' => 'number', 'default' => '10', 'min' => '0', 'step' => '5'],
        ],
        'calcJs' => "
            const type = document.getElementById('solarSysType').value;
            const kw = Math.max(1, parseFloat(document.getElementById('systemCapKw').value) || 6.0);
            const battKwh = Math.max(0, parseFloat(document.getElementById('batteryCapacityKwh').value) || 10);
            const curr = getSelectedCurrency();

            // تكلفة الكيلوواط ألواح مع الشاسيهات والأسلاك والتركيب حوالي 350-450 دولار
            const solarPanelsAndMountingCost = kw * 400;
            // تكلفة الانفرتر
            let inverterCost = 600;
            if (type === 'hybrid_lithium') inverterCost = 1300;
            if (type === 'ongrid') inverterCost = 800;

            // تكلفة البطاريات
            let batteryCost = 0;
            if (type === 'hybrid_lithium') {
                batteryCost = battKwh * 250; // سعر كيلوواط الليثيوم مع BMS
            } else if (type === 'offgrid') {
                batteryCost = battKwh * 160; // سعر بطاريات الجيل
            }

            const laborAndPermits = 400;
            const totalUSD = solarPanelsAndMountingCost + inverterCost + batteryCost + laborAndPermits;
            // التحويل للعملة
            const total = totalUSD;

            setPrimaryResult(formatMoney(total, curr), 'التكلفة الإجمالية التقديرية للمنظومة مع التركيب');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الألواح والهياكل المعدنية', value: formatMoney(solarPanelsAndMountingCost, curr), color: '#3b82f6' },
                { label: 'تكلفة بنك البطاريات (' + battKwh + ' kWh)', value: formatMoney(batteryCost, curr), color: '#10b981' },
                { label: 'تكلفة الانفرتر الذكي ولوحة القواطع', value: formatMoney(inverterCost, curr), color: '#f59e0b' },
                { label: 'أجور التركيب والكابلات والحماية', value: formatMoney(laborAndPermits, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفة تركيب منظومة <strong>\${kw} kW</strong> من نوع <strong>\${type === 'ongrid' ? 'متصلة بالشبكة' : 'هجينة مع بطاريات'}</strong> تبلغ حوالي <strong>\${formatMoney(total, curr)}</strong> شاملة كافة المعدات والتوصيلات وضمان الألواح لمدة 25 سنة.</p>
            `);
        ",
        'points' => [
            'المنظومات المربوطة بالشبكة (On-Grid) هي الأقل تكلفة لأنها لا تحتوي على بطاريات وتبيع الفائض لشركة الكهرباء.',
            'البطاريات تمثل بين 35% إلى 50% من تكلفة المنظومات الهجينة والمعزولة ولكنها توفر كهرباء مستمرة 24/7 دون انقطاع.'
        ],
        'assumptions' => 'الأسعار مبنية على متوسط أسعار السوق للطاقة الشمسية للعام 2025/2026.',
        'faqs' => [
            ['q' => 'كم تبلغ مدة ضمان الألواح والانفرتر؟', 'a' => 'الألواح الشمسية ذات الجودة العالية تأتي بضمان أداء كفاءة لمدة 25 إلى 30 سنة، بينما الانفرترات تأتي بضمان 5 سنوات، وبطاريات الليثيوم 5 إلى 10 سنوات.']
        ],
        'related' => ['solar-payback-calculator', 'grid-vs-solar-calculator', 'solar-panels-calculator']
    ],

    'solar-payback-calculator' => [
        'title' => 'حاسبة فترة استرداد الطاقة الشمسية (ROI)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'solarTotalCost', 'label' => 'تكلفة المنظومة الشمسية الإجمالية', 'type' => 'number', 'default' => '25000', 'min' => '1000', 'step' => '500'],
            ['id' => 'monthlyBillSavings', 'label' => 'التوفير الشهري المتوقع في فاتورة الكهرباء', 'type' => 'number', 'default' => '650', 'min' => '50', 'step' => '25'],
            ['id' => 'tariffInflationRate', 'label' => 'الزيادة السنوية المتوقعة في أسعار الكهرباء الحكومية (%)', 'type' => 'number', 'default' => '3', 'min' => '0', 'max' => '15', 'step' => '1'],
        ],
        'calcJs' => "
            const cost = Math.max(1000, parseFloat(document.getElementById('solarTotalCost').value) || 25000);
            const savings = Math.max(50, parseFloat(document.getElementById('monthlyBillSavings').value) || 650);
            const inflation = Math.max(0, parseFloat(document.getElementById('tariffInflationRate').value) || 3) / 100;
            const curr = getSelectedCurrency();

            const annualSavings = savings * 12;
            const paybackYears = cost / annualSavings;
            const years25TotalSavings = annualSavings * 25 * (1 + inflation);
            const netProfit25Years = years25TotalSavings - cost;

            setPrimaryResult(paybackYears.toFixed(1) + ' سنوات استرداد', 'فترة استرداد رأس مال المنظومة');
            showResultArea();

            setDetailStats([
                { label: 'التوفير السنوي في السنة الأولى', value: formatMoney(annualSavings, curr), color: '#3b82f6' },
                { label: 'صافي الوفر المالي على مدار 25 سنة', value: formatMoney(netProfit25Years, curr), color: '#10b981' },
                { label: 'العائد السنوي على الاستثمار (ROI)', value: ((annualSavings / cost) * 100).toFixed(1) + '% سنوياً', color: '#f59e0b' },
                { label: 'تاريخ بداية الأرباح المجانية', value: 'بعد ' + Math.ceil(paybackYears) + ' سنوات', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تسترد كامل تكلفة المنظومة البالغة <strong>\${formatMoney(cost, curr)}</strong> خلال <strong>\${paybackYears.toFixed(1)} سنوات</strong>، وتحقق بعدها كهرباء مجانية بالكامل وصافي وفر تراكمي يتجاوز <strong>\${formatMoney(netProfit25Years, curr)}</strong> على مدار العمر الافتراضي للمنظومة (25 سنة).</p>
            `);
        ",
        'points' => [
            'فترة الاسترداد = تكلفة المنظومة ÷ التوفير السنوي في الفاتورة.',
            'عائد استثمار الطاقة الشمسية (15% إلى 25% سنوياً) يتفوق على معظم عوائد الودائع البنكية وصناديق الاستثمار التقليدية.'
        ],
        'assumptions' => 'الألواح تعمل بكفاءة تفوق 80% حتى بعد مرور 25 سنة.',
        'faqs' => [
            ['q' => 'ماذا يحدث بعد انتهاء فترة استرداد تكلفة المنظومة؟', 'a' => 'تصبح الكهرباء المولدة مجانية بالكامل بنسبة 100%، وتستمر الألواح في إنتاج الطاقة لعشرين سنة إضافية دون أي تكاليف سوى الصيانة البسيطة.']
        ],
        'related' => ['solar-system-cost-calculator', 'grid-vs-solar-calculator', 'solar-yield-calculator']
    ],

    'grid-vs-solar-calculator' => [
        'title' => 'حاسبة الكهرباء الحكومية مقابل الطاقة الشمسية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'currentMonthlyBill', 'label' => 'فاتورة الكهرباء الحكومية الحالية شهرياً', 'type' => 'number', 'default' => '700', 'min' => '50', 'step' => '25'],
            ['id' => 'solarSystemInstallCost', 'label' => 'تكلفة تركيب المنظومة الشمسية', 'type' => 'number', 'default' => '26000', 'min' => '1000', 'step' => '500'],
            ['id' => 'yearsHorizon', 'label' => 'فترة المقارنة الزمنية (بالسنوات)', 'type' => 'select', 'options' => [
                '10' => '10 سنوات',
                '15' => '15 سنة',
                '20' => '20 سنة (المعيار طويل الأمد)'
            ], 'default' => '20'],
        ],
        'calcJs' => "
            const bill = Math.max(50, parseFloat(document.getElementById('currentMonthlyBill').value) || 700);
            const solarCost = Math.max(1000, parseFloat(document.getElementById('solarSystemInstallCost').value) || 26000);
            const years = parseInt(document.getElementById('yearsHorizon').value) || 20;
            const curr = getSelectedCurrency();

            // بافتراض زيادة طفيفة سنوية في تعرفة الشبكة 2%
            let gridTotal = 0;
            let currentYearBill = bill * 12;
            for (let i = 0; i < years; i++) {
                gridTotal += currentYearBill;
                currentYearBill *= 1.02;
            }

            // صيانة شمسية وتغيير انفرتر مرة كل 10 سنوات
            const solarMaintenance = (years / 10) * 1500;
            const totalSolarExpenditure = solarCost + solarMaintenance;
            const totalSavings = gridTotal - totalSolarExpenditure;

            setPrimaryResult(formatMoney(totalSavings, curr), 'صافي الوفر المالي مع الطاقة الشمسية');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ما ستدفعه لشركة الكهرباء (' + years + ' سنة)', value: formatMoney(gridTotal, curr), color: '#ef4444' },
                { label: 'إجمالي تكاليف الطاقة الشمسية والصيانة', value: formatMoney(totalSolarExpenditure, curr), color: '#10b981' },
                { label: 'نسبة التوفير المالي الإجمالية', value: ((totalSavings / gridTotal) * 100).toFixed(0) + '% وفر', color: '#3b82f6' },
                { label: 'المتوسط الشهري للوفر المالي', value: formatMoney(totalSavings / (years * 12), curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>خلال <strong>\${years} سنة</strong>، ستدفع لشركة الكهرباء حوالي <strong>\${formatMoney(gridTotal, curr)}</strong>، بينما تكلفك الطاقة الشمسية <strong>\${formatMoney(totalSolarExpenditure, curr)}</strong> فقط شاملة الصيانة، محققاً وفراً هائلاً قدره <strong>\${formatMoney(totalSavings, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'الكهرباء الحكومية عبارة عن مصروف مستمر متصاعد مدى الحياة لا يبني أي أصل مالي للمستهلك.',
            'الطاقة الشمسية تحول الفاتورة الشهرية إلى استثمار في أصل إنتاجي مملوك بالكامل يرفع القيمة العقارية لمنزلك.'
        ],
        'assumptions' => 'يفترض منظومة تغطي 85% إلى 95% من استهلاك المنزل.',
        'faqs' => [
            ['q' => 'هل ترتفع قيمة العقار عند تركيب طاقة شمسية؟', 'a' => 'نعم؛ تؤكد الدراسات العقارية أن المنازل المزودة بمنظومات طاقة شمسية موثقة تُباع أسرع وبسعر أعلى بنسبة 4% إلى 6% بسبب انخفاض تكاليف تشغيلها السنوية.']
        ],
        'related' => ['solar-payback-calculator', 'solar-system-cost-calculator', 'electricity-consumption-calculator']
    ],

    'power-source-comparison-calculator' => [
        'title' => 'حاسبة المولد مقابل البطاريات مقابل الطاقة الشمسية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'dailyEnergyCompare', 'label' => 'الاستهلاك اليومي المطلوب تأمينه (كيلوواط ساعة kWh)', 'type' => 'number', 'default' => '10', 'min' => '2', 'step' => '1'],
            ['id' => 'comparisonYears', 'label' => 'مدة المقارنة بالسنوات', 'type' => 'select', 'options' => [
                '1' => 'سنة واحدة',
                '3' => '3 سنوات (المعيار الأكثر واقعية)',
                '5' => '5 سنوات'
            ], 'default' => '3'],
            ['id' => 'fuelLiterCost', 'label' => 'سعر لتر وقود المولد (بنزين/ديزل)', 'type' => 'number', 'default' => '0.85', 'min' => '0.1', 'step' => '0.05'],
        ],
        'calcJs' => "
            const kwhDaily = Math.max(2, parseFloat(document.getElementById('dailyEnergyCompare').value) || 10);
            const years = parseInt(document.getElementById('comparisonYears').value) || 3;
            const fuelPrice = Math.max(0.1, parseFloat(document.getElementById('fuelLiterCost').value) || 0.85);
            const curr = getSelectedCurrency();

            const totalKwh = kwhDaily * 365 * years;

            // 1. تكلفة المولد: شراء أولي + وقود (0.4 لتر/kWh) + صيانة وزيوت 20%
            const genBuyCost = 1200;
            const genFuelTotal = (totalKwh * 0.40 * fuelPrice) * 1.25;
            const totalGenCost = genBuyCost + genFuelTotal;

            // 2. تكلفة بنك البطاريات مع انفرتر وشحن كهرباء حكومية
            const battBuyCost = 2200;
            const battReplacement = years > 2 ? 1500 : 0;
            const battChargingElectricity = totalKwh * 0.18 * 1.2;
            const totalBattCost = battBuyCost + battReplacement + battChargingElectricity;

            // 3. تكلفة الطاقة الشمسية مع بطاريات
            const solarInstallCost = 3800;
            const solarMaint = years * 100;
            const totalSolarCost = solarInstallCost + solarMaint;

            // الفائز بالأقل تكلفة
            let winner = 'الطاقة الشمسية مع البطاريات ☀️';
            let winnerCost = totalSolarCost;
            if (totalBattCost < winnerCost && years === 1) { winner = 'البطاريات فقط 🔋'; winnerCost = totalBattCost; }

            setPrimaryResult(winner, 'الخيار الأكثر جدوى وتوفيراً على مدى ' + years + ' سنوات');
            showResultArea();

            setDetailStats([
                { label: 'التكلفة الإجمالية للطاقة الشمسية', value: formatMoney(totalSolarCost, curr), color: '#10b981' },
                { label: 'التكلفة الإجمالية للبطاريات والانفرتر', value: formatMoney(totalBattCost, curr), color: '#3b82f6' },
                { label: 'التكلفة الإجمالية للمولد والوقود', value: formatMoney(totalGenCost, curr), color: '#ef4444' },
                { label: 'وفر الطاقة الشمسية مقارنة بالمولد', value: formatMoney(totalGenCost - totalSolarCost, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>على مدار <strong>\${years} سنوات</strong> لتأمين <strong>\${kwhDaily} kWh يومياً</strong>:<br>
                - المولد بالوقود يكلف <strong>\${formatMoney(totalGenCost, curr)}</strong> (تكاليف وقود محروقة وصيانة وضجيج).<br>
                - البطاريات مع الشاحن تكلف <strong>\${formatMoney(totalBattCost, curr)}</strong>.<br>
                - الطاقة الشمسية تكلف <strong>\${formatMoney(totalSolarCost, curr)}</strong> وهي الأوفر والأكثر هدوءاً واستقراراً وتستمر في العمل لـ 20 سنة إضافية مجاناً.</p>
            `);
        ",
        'points' => [
            'المولد يبدو رخيصاً عند الشراء الأولي ولكنه يتحول إلى محرقة للمال في تكاليف الوقود والصيانة والأعطال.',
            'البطاريات وحدها تحتاج كهرباء شبكة مستقرة للشحن واستبدالاً متكرراً كل سنتين إلى 3 سنوات.',
            'الطاقة الشمسية مع بطاريات الليثيوم تمثل الحل المستدام الأمثل من حيث راحة البال والتوفير الاقتصادي الشامل.'
        ],
        'assumptions' => 'المقارنة تشمل تكلفة الشراء والصيانة والمحروقات أو فواتير شحن البطاريات.',
        'faqs' => [
            ['q' => 'هل يمكن الدمج بين الطاقة الشمسية والمولد؟', 'a' => 'نعم؛ الانفرترات الهجينة الحديثة تدعم مدخلاً ذكياً للمولد (Dry Contact) لتشغيل المولد أوتوماتيكياً فقط عند الطوارئ القصوى إذا نفذت البطاريات وتواصل الطقس الغائم عدة أيام.']
        ],
        'related' => ['generator-cost-calculator', 'solar-system-cost-calculator', 'grid-vs-solar-calculator']
    ],
];

echo "Generating Group C: Energy & Electricity Tools (25 tools)...\n";
foreach ($toolsC as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group C!\n";
