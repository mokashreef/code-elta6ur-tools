<?php
/**
 * مولد أدوات المجموعة B: المنزل والبناء (30 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsB = [
    'marriage-cost-calculator' => [
        'title' => 'حاسبة تكلفة الزواج الشاملة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'dowryAmount', 'label' => 'المهر والشبكة والهدايا المبدئية', 'type' => 'number', 'default' => '40000', 'min' => '0', 'step' => '1000'],
            ['id' => 'hallCost', 'label' => 'تكلفة قاعة الأفراح والضيافة والعشاء', 'type' => 'number', 'default' => '25000', 'min' => '0', 'step' => '1000'],
            ['id' => 'clothesAndPrep', 'label' => 'فستان الزفاف، البدلة، وتجهيزات العروسين', 'type' => 'number', 'default' => '10000', 'min' => '0', 'step' => '500'],
            ['id' => 'honeymoonCost', 'label' => 'رحلة شهر العسل (تذاكر وإقامة)', 'type' => 'number', 'default' => '15000', 'min' => '0', 'step' => '500'],
            ['id' => 'apartmentDeposit', 'label' => 'مقدم إيجار الشقة أو العربون', 'type' => 'number', 'default' => '12000', 'min' => '0', 'step' => '500'],
            ['id' => 'otherEmergencies', 'label' => 'مصاريف طارئة وضيافة إضافية', 'type' => 'number', 'default' => '5000', 'min' => '0', 'step' => '500'],
        ],
        'calcJs' => "
            const dowry = Math.max(0, parseFloat(document.getElementById('dowryAmount').value) || 0);
            const hall = Math.max(0, parseFloat(document.getElementById('hallCost').value) || 0);
            const prep = Math.max(0, parseFloat(document.getElementById('clothesAndPrep').value) || 0);
            const honey = Math.max(0, parseFloat(document.getElementById('honeymoonCost').value) || 0);
            const rent = Math.max(0, parseFloat(document.getElementById('apartmentDeposit').value) || 0);
            const other = Math.max(0, parseFloat(document.getElementById('otherEmergencies').value) || 0);
            const curr = getSelectedCurrency();

            const total = dowry + hall + prep + honey + rent + other;
            const ceremonyTotal = hall + prep;

            setPrimaryResult(formatMoney(total, curr), 'الميزانية التقديرية الإجمالية للزواج');
            showResultArea();

            setDetailStats([
                { label: 'تكاليف الحفل والضيافة', value: formatMoney(ceremonyTotal, curr), color: '#3b82f6' },
                { label: 'المهر والشبكة', value: formatMoney(dowry, curr), color: '#10b981' },
                { label: 'شهر العسل', value: formatMoney(honey, curr), color: '#8b5cf6' },
                { label: 'تأمين السكن والبنود الطارئة', value: formatMoney(rent + other, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>إجمالي تكلفة مراسم الزواج وبداية الحياة الزوجية تقدر بـ <strong>\${formatMoney(total, curr)}</strong>. تمثل حفلة الزفاف والضيافة حوالي <strong>\${total > 0 ? ((ceremonyTotal/total)*100).toFixed(0) : 0}%</strong> من الميزانية.</p>
            `);
        ",
        'points' => [
            'إجمالي التكلفة = المهر + حفل الزفاف + التجهيزات الشخصية + شهر العسل + سكن البداية + الطوارئ.',
            'يُوصى دائماً برصد بند طوارئ بنسبة 10% إلى 15% للمصاريف غير المتوقعة أثناء التحضيرات.'
        ],
        'assumptions' => 'التكاليف تختلف باختلاف التقاليد الاجتماعية والدولة ومستوى الحفل المختار.',
        'faqs' => [
            ['q' => 'كيف يمكن تقليل ميزانية الزواج دون التأثير على الفرحة؟', 'a' => 'التركيز على حفل عائلي دافئ ومختصر، والحجز المبكر لقاعات الأفراح وتذاكر شهر العسل في غير مواسم الذروة.']
        ],
        'related' => ['apartment-furnishing-cost-calculator', 'family-monthly-budget-calculator', 'savings-goal-calculator']
    ],

    'apartment-furnishing-cost-calculator' => [
        'title' => 'حاسبة تكلفة تجهيز شقة للزواج',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'furnishLevel', 'label' => 'مستوى الفرش والأجهزة', 'type' => 'select', 'options' => [
                'budget' => 'اقتصادي وعملي (ماركات موثوقة بسعر مناسب)',
                'medium' => 'متوسط وفاخر (جودة عالية وتصاميم حديثة)',
                'luxury' => 'فاخر ومميز (أعلى المواصفات وماركات عالمية)'
            ], 'default' => 'medium'],
            ['id' => 'roomsCount', 'label' => 'عدد الغرف (نوم + صالة + مجالس)', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '10', 'step' => '1'],
            ['id' => 'appliancesIncluded', 'label' => 'هل التجهيز يشمل الأجهزة الكهربائية الكبرى (مكيفات، ثلاجة، غسالة، شاشات)؟', 'type' => 'select', 'options' => [
                'yes' => 'نعم، يشمل كافة الأجهزة والمطبخ',
                'no' => 'أثاث ومفروشات وديكور فقط'
            ], 'default' => 'yes'],
        ],
        'calcJs' => "
            const level = document.getElementById('furnishLevel').value;
            const rooms = Math.max(1, parseInt(document.getElementById('roomsCount').value) || 3);
            const hasAppliances = document.getElementById('appliancesIncluded').value;
            const curr = getSelectedCurrency();

            let roomCost = 6000;
            let applianceCost = 15000;
            if (level === 'budget') { roomCost = 3500; applianceCost = 9000; }
            if (level === 'luxury') { roomCost = 12000; applianceCost = 30000; }

            const furnitureTotal = rooms * roomCost;
            const appliancesTotal = hasAppliances === 'yes' ? applianceCost : 0;
            const kitchenSupplies = level === 'budget' ? 2000 : (level === 'luxury' ? 8000 : 4000);
            const total = furnitureTotal + appliancesTotal + kitchenSupplies;

            setPrimaryResult(formatMoney(total, curr), 'التكلفة الإجمالية لتجهيز الشقة');
            showResultArea();

            setDetailStats([
                { label: 'أثاث الغرف والمجالس', value: formatMoney(furnitureTotal, curr), color: '#3b82f6' },
                { label: 'الأجهزة الكهربائية والمكيفات', value: formatMoney(appliancesTotal, curr), color: '#10b981' },
                { label: 'مستلزمات المطبخ والحمام والديكور', value: formatMoney(kitchenSupplies, curr), color: '#8b5cf6' },
                { label: 'متوسط تكلفة الغرفة الواحدة', value: formatMoney(roomCost, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تكلفة تجهيز شقة مكونة من <strong>\${rooms} غرف</strong> بمستوى <strong>\${level === 'luxury' ? 'فاخر' : (level === 'budget' ? 'اقتصادي' : 'متوسط')}</strong> تقدر بـ <strong>\${formatMoney(total, curr)}</strong> تشمل الأثاث والأجهزة والمطبخ.</p>
            `);
        ",
        'points' => [
            'تشمل التكلفة الأثاث الخشبي والمفروشات والمطابخ والأجهزة الكهربائية المنزلية.',
            'الأجهزة والمكيفات تمثل عادة بين 30% إلى 45% من إجمالي ميزانية تجهيز السكن الجديد.'
        ],
        'assumptions' => 'المبالغ مبنية على متوسط أسعار التجزئة في معارض الأثاث والأجهزة المنزلية العربية.',
        'faqs' => [
            ['q' => 'ما هي أولويات شراء أثاث الشقة عند محدودية الميزانية؟', 'a' => 'ابدأ بغرفة النوم الرئيسية والمطبخ والثلاجة والغسالة ومكيفات الغرف الأساسية، ويمكن تأجيل الصالون الإضافي والكماليات لاحقاً.']
        ],
        'related' => ['marriage-cost-calculator', 'furniture-quantity-calculator', 'room-furniture-area-calculator']
    ],

    'house-building-cost-calculator' => [
        'title' => 'حاسبة تكلفة بناء منزل',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'landArea', 'label' => 'مساحة الأرض (متر مربع)', 'type' => 'number', 'default' => '400', 'min' => '50', 'step' => '10'],
            ['id' => 'floorsCount', 'label' => 'عدد الطوابق / الأدوار', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '10', 'step' => '1'],
            ['id' => 'buildCoverageRate', 'label' => 'نسبة البناء من مساحة الأرض (%) - عادة 60%', 'type' => 'number', 'default' => '60', 'min' => '30', 'max' => '100', 'step' => '5'],
            ['id' => 'buildScope', 'label' => 'مرحلة البناء المطلوبة', 'type' => 'select', 'options' => [
                'bone' => 'بناء عظم فقط مع المواد (الهيكل الخرساني والمباني)',
                'turnkey_standard' => 'تسليم مفتاح - تشطيب قياسي متوازن',
                'turnkey_deluxe' => 'تسليم مفتاح - تشطيب ديلوكس فاخر'
            ], 'default' => 'turnkey_standard'],
            ['id' => 'meterPriceOverride', 'label' => 'سعر المتر المربع التقديري (اتركه 0 لاستخدام السعر القياسي)', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const land = Math.max(50, parseFloat(document.getElementById('landArea').value) || 400);
            const floors = Math.max(1, parseInt(document.getElementById('floorsCount').value) || 2);
            const coverage = Math.max(30, Math.min(100, parseFloat(document.getElementById('buildCoverageRate').value) || 60)) / 100;
            const scope = document.getElementById('buildScope').value;
            const override = parseFloat(document.getElementById('meterPriceOverride').value) || 0;
            const curr = getSelectedCurrency();

            const floorArea = land * coverage;
            const totalBuiltArea = floorArea * floors;

            let meterRate = 600; // عظم
            if (scope === 'turnkey_standard') meterRate = 1400;
            if (scope === 'turnkey_deluxe') meterRate = 2200;
            if (override > 0) meterRate = override;

            const totalCost = totalBuiltArea * meterRate;
            const engineeringFees = totalCost * 0.04; // 4% رخص وإشراف هندسي

            setPrimaryResult(formatMoney(totalCost, curr), 'التكلفة التقديرية لإجمالي مسطحات البناء');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي مسطحات البناء (م²)', value: formatNumber(totalBuiltArea, 1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الدور الواحد (م²)', value: formatNumber(floorArea, 1) + ' م²', color: '#10b981' },
                { label: 'سعر المتر المعتمد', value: formatMoney(meterRate, curr) + ' / م²', color: '#f59e0b' },
                { label: 'أتعاب المخططات والإشراف الهندسي المقدرة', value: formatMoney(engineeringFees, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء مسطح إجمالي <strong>\${formatNumber(totalBuiltArea, 1)} متر مربع</strong> على <strong>\${floors} أدوار</strong> بمستوى <strong>\${scope === 'bone' ? 'عظم' : 'تسليم مفتاح'}</strong>، تقدر التكلفة بـ <strong>\${formatMoney(totalCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'مسطح الدور = مساحة الأرض × نسبة البناء النظامية.',
            'إجمالي مسطحات البناء = مسطح الدور × عدد الأدوار (مع الملاحق والأسوار).',
            'التكلفة الإجمالية = إجمالي مسطحات البناء × سعر المتر المربع للمرحلة.'
        ],
        'assumptions' => 'الأسعار تقديرية وتتأثر بأسعار الحديد والخرسانة وتضاريس الأرض.',
        'faqs' => [
            ['q' => 'ما الفرق بين بناء عظم وتسليم مفتاح؟', 'a' => 'العظم يشمل الحفر والخرسانات المسلحة وبناء البلوك وعزل القواعد فقط، بينما تسليم المفتاح يشمل التشطيب الكامل من سباكة وكهرباء وأرضيات ودهانات وأبواب ونوافذ.']
        ],
        'related' => ['house-finishing-cost-calculator', 'cement-calculator', 'block-calculator']
    ],

    'house-finishing-cost-calculator' => [
        'title' => 'حاسبة تكلفة تشطيب منزل',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'builtArea', 'label' => 'إجمالي مسطح البناء المراد تشطيبه (م²)', 'type' => 'number', 'default' => '250', 'min' => '20', 'step' => '10'],
            ['id' => 'finishQuality', 'label' => 'مستوى جودة التشطيب', 'type' => 'select', 'options' => [
                'economy' => 'اقتصادي (خامات قياسية مناسبة للإيجار)',
                'medium' => 'متوسط / سوبر ديلوكس (جودة ممتازة وسيراميك فرز أول)',
                'vip' => 'فاخر جداً / ألترا VIP (رخام، جبس بورد معلق، سمارت هوم)'
            ], 'default' => 'medium'],
            ['id' => 'airConditioning', 'label' => 'نوع التكييف المطلوب', 'type' => 'select', 'options' => [
                'split' => 'مكيفات سبليت جدارية عادية',
                'concealed' => 'تكييف كونسيلد مخفي (Concealed Ducted)',
                'central' => 'تكييف مركزي متكامل (Package)'
            ], 'default' => 'split'],
        ],
        'calcJs' => "
            const area = Math.max(20, parseFloat(document.getElementById('builtArea').value) || 250);
            const quality = document.getElementById('finishQuality').value;
            const ac = document.getElementById('airConditioning').value;
            const curr = getSelectedCurrency();

            let baseMeter = 600;
            if (quality === 'medium') baseMeter = 950;
            if (quality === 'vip') baseMeter = 1600;

            let acExtraPerMeter = 50;
            if (ac === 'concealed') acExtraPerMeter = 120;
            if (ac === 'central') acExtraPerMeter = 200;

            const totalMeterRate = baseMeter + acExtraPerMeter;
            const total = area * totalMeterRate;

            setPrimaryResult(formatMoney(total, curr), 'إجمالي ميزانية التشطيب المتوقعة');
            showResultArea();

            setDetailStats([
                { label: 'سعر متر التشطيب شامل التكييف', value: formatMoney(totalMeterRate, curr) + ' / م²', color: '#3b82f6' },
                { label: 'بند التأسيس والكهرباء والسباكة (30%)', value: formatMoney(total * 0.3, curr), color: '#f59e0b' },
                { label: 'بند الأرضيات والسيراميك والدهانات (45%)', value: formatMoney(total * 0.45, curr), color: '#10b981' },
                { label: 'بند الأبواب والنوافذ والإنارة (25%)', value: formatMoney(total * 0.25, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشطيب مسطح <strong>\${area} م²</strong> بمستوى <strong>\${quality}</strong> وتكييف <strong>\${ac}</strong> يكلف حوالي <strong>\${formatMoney(total, curr)}</strong> بمعدل <strong>\${formatMoney(totalMeterRate, curr)}</strong> لكل متر مربع.</p>
            `);
        ",
        'points' => [
            'السباكة والكهرباء تمثل حوالي 25% إلى 30% من تكلفة التشطيب.',
            'الأرضيات (سيراميك/بورسلان/رخام) والدهانات تمثل حوالي 40% إلى 45%.',
            'الأبواب والنوافذ والتكييف والإنارات تمثل النسبة المتبقية.'
        ],
        'assumptions' => 'التشطيب يفترض استلام المبنى عظم بلياسة أو بدونها.',
        'faqs' => [
            ['q' => 'أين تكمن أكبر بنود هدر الميزانية في التشطيب؟', 'a' => 'في التعديل على مخططات السباكة والكهرباء بعد تأسيسها، واختيار بورسلان مستورد ذي مقاسات غير قياسية ينتج عنها هالك قص كبير.']
        ],
        'related' => ['house-building-cost-calculator', 'floor-tiles-calculator', 'paint-calculator']
    ],

    'furniture-quantity-calculator' => [
        'title' => 'حاسبة كمية الأثاث المطلوبة للمنزل',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'bedroomsCount', 'label' => 'عدد غرف النوم', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '10', 'step' => '1'],
            ['id' => 'familyMembers', 'label' => 'عدد أفراد الأسرة المقيمين', 'type' => 'number', 'default' => '4', 'min' => '1', 'max' => '20', 'step' => '1'],
            ['id' => 'livingRoomsCount', 'label' => 'عدد الصالات والمجالس', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '5', 'step' => '1'],
            ['id' => 'diningType', 'label' => 'نوع طاولة الطعام المفضلة', 'type' => 'select', 'options' => [
                'family' => 'طاولة عائلية تسع أفراد الأسرة فقط',
                'guests' => 'طاولة كبيرة تسع الأسرة والضيوف (أفراد الأسرة + 4 كراسي)'
            ], 'default' => 'guests'],
        ],
        'calcJs' => "
            const beds = Math.max(1, parseInt(document.getElementById('bedroomsCount').value) || 3);
            const family = Math.max(1, parseInt(document.getElementById('familyMembers').value) || 4);
            const living = Math.max(1, parseInt(document.getElementById('livingRoomsCount').value) || 2);
            const dining = document.getElementById('diningType').value;

            const totalBeds = family;
            const wardrobes = beds;
            const livingSeats = living * 7; // متوسط مقاعد الكنب
            const diningChairs = dining === 'guests' ? family + 4 : family;

            setPrimaryResult(livingSeats + ' مقعد كنب + ' + diningChairs + ' كراسي طعام', 'كمية المقاعد والجلسات المقترحة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسِرّة المطلوبة', value: totalBeds + ' سرير', color: '#3b82f6' },
                { label: 'عدد خزائن الملابس (دولاب)', value: wardrobes + ' دولاب', color: '#10b981' },
                { label: 'سعة طقم كنب الصالات والمجالس', value: livingSeats + ' أشخاص', color: '#f59e0b' },
                { label: 'كراسي طاولة الطعام', value: diningChairs + ' كراسي', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتأثيث المنزل بشكل مريح لأسرة من <strong>\${family} أفراد</strong> في <strong>\${beds} غرف نوم</strong> و <strong>\${living} صالات</strong>، تحتاج إلى <strong>\${totalBeds} أسرة</strong>، و <strong>\${wardrobes} دواليب ملابس</strong>، وأطقم كنب تتسع لـ <strong>\${livingSeats} شخصاً</strong>، وطاولة طعام مع <strong>\${diningChairs} كراسٍ</strong>.</p>
            `);
        ",
        'points' => [
            'عدد الأسرة ودواليب الملابس يُحسب حسب عدد الأفراد وتوزيع الغرف.',
            'الصالون الرئيسي يحتاج كنب يتسع لـ 7 إلى 9 أشخاص كمعيار للضيافة العربية.'
        ],
        'assumptions' => 'التقدير يلبي الاحتياجات الأساسية مع مراعاة استقبال الضيوف.',
        'faqs' => [
            ['q' => 'كيف أمنع تكديس الأثاث في الغرف؟', 'a' => 'التزم بقاعدة ألا يزيد الأثاث عن 40% إلى 45% من مساحة الغرفة الإجمالية لترك مسارات حركة مريحة.']
        ],
        'related' => ['room-furniture-area-calculator', 'apartment-furnishing-cost-calculator', 'carpet-area-calculator']
    ],

    'room-furniture-area-calculator' => [
        'title' => 'حاسبة مساحة الغرفة المطلوبة للأثاث',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roomLength', 'label' => 'طول الغرفة (متر)', 'type' => 'number', 'default' => '4.5', 'min' => '2', 'step' => '0.1'],
            ['id' => 'roomWidth', 'label' => 'عرض الغرفة (متر)', 'type' => 'number', 'default' => '4.0', 'min' => '2', 'step' => '0.1'],
            ['id' => 'furnitureAreaManual', 'label' => 'مساحة الأثاث الفعلية التقديرية (متر مربع)', 'type' => 'number', 'default' => '7.5', 'min' => '1', 'step' => '0.5'],
        ],
        'calcJs' => "
            const len = Math.max(2, parseFloat(document.getElementById('roomLength').value) || 4.5);
            const wid = Math.max(2, parseFloat(document.getElementById('roomWidth').value) || 4.0);
            const furn = Math.max(1, parseFloat(document.getElementById('furnitureAreaManual').value) || 7.5);

            const totalRoomArea = len * wid;
            const furnitureRatio = (furn / totalRoomArea) * 100;
            const freeArea = totalRoomArea - furn;

            let status = 'ممتاز ومريح جداً ✅';
            let color = '#10b981';
            if (furnitureRatio > 40 && furnitureRatio <= 55) { status = 'مقبول ومتوازن ⚠️'; color = '#f59e0b'; }
            if (furnitureRatio > 55) { status = 'مزدحم جداً ويعيق الحركة ❌'; color = '#ef4444'; }

            setPrimaryResult(furnitureRatio.toFixed(1) + '% من مساحة الغرفة', 'نسبة إشغال الأثاث للغرفة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الكلية للغرفة', value: totalRoomArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الفراغ والحركة الحرة', value: freeArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'مساحة الأثاث المشغولة', value: furn.toFixed(1) + ' م²', color: '#8b5cf6' },
                { label: 'تقييم الراحة والحركة', value: status, color: color }
            ]);

            setResultContent(`
                <p>تشغل قطع الأثاث <strong>\${furnitureRatio.toFixed(1)}%</strong> من الغرفة. التوصية المعمارية القياسية هي أن تشغل المفروشات ما بين <strong>30% إلى 40%</strong> كحد أقصى لضمان راحة العين وسهولة فتح الأبواب والدواليب.</p>
            `);
        ",
        'points' => [
            'مساحة الغرفة = الطول × العرض.',
            'نسبة الإشغال = (مساحة الأثاث ÷ مساحة الغرفة) × 100.',
            'المساحة الحرة للحركة يجب ألا تقل عن 60% من مساحة الغرفة.'
        ],
        'assumptions' => 'المعايير المعمارية تشترط مسار حركة لا يقل عن 80-90 سم بين السرير والدولاب أو الجدار.',
        'faqs' => [
            ['q' => 'ما هو الحل إذا كانت مساحة الأثاث تزيد عن 50%؟', 'a' => 'استخدم أثاثاً متعدد الوظائف، مثل الأسرة ذات الأدراج السفلية، والدواليب ذات الأبواب السحابة (Sliding Doors) بدلاً من الأبواب المفصلية.']
        ],
        'related' => ['furniture-quantity-calculator', 'carpet-area-calculator', 'wall-area-calculator']
    ],

    'paint-calculator' => [
        'title' => 'حاسبة كمية الدهان',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roomPerimeter', 'label' => 'إجمالي محيط الجدران (متر) - مجموع أطوال الحوائط', 'type' => 'number', 'default' => '16', 'min' => '1', 'step' => '0.5'],
            ['id' => 'wallHeight', 'label' => 'ارتفاع الجدار حتى السقف (متر)', 'type' => 'number', 'default' => '3.0', 'min' => '1.5', 'max' => '8', 'step' => '0.1'],
            ['id' => 'doorsWindowsDeduct', 'label' => 'مساحة الأبواب والنوافذ المخصومة (م²)', 'type' => 'number', 'default' => '4.5', 'min' => '0', 'step' => '0.5'],
            ['id' => 'coatsCount', 'label' => 'عدد أوجه / طبقات الدهان (غالباً وجهين)', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '5', 'step' => '1'],
            ['id' => 'paintSpreadRate', 'label' => 'معدل فرد اللتر (م²/لتر) - عادة 10 إلى 12', 'type' => 'number', 'default' => '11', 'min' => '5', 'max' => '20', 'step' => '0.5'],
        ],
        'calcJs' => "
            const perim = Math.max(1, parseFloat(document.getElementById('roomPerimeter').value) || 16);
            const height = Math.max(1.5, parseFloat(document.getElementById('wallHeight').value) || 3.0);
            const deduct = Math.max(0, parseFloat(document.getElementById('doorsWindowsDeduct').value) || 4.5);
            const coats = Math.max(1, parseInt(document.getElementById('coatsCount').value) || 2);
            const spread = Math.max(5, parseFloat(document.getElementById('paintSpreadRate').value) || 11);

            const grossArea = perim * height;
            const netWallArea = Math.max(1, grossArea - deduct);
            const totalCoatsArea = netWallArea * coats;
            const litersNeeded = totalCoatsArea / spread;
            const gallonsNeeded = litersNeeded / 3.75; // جالون أمريكي قياسي 3.75 لتر
            const bucketsNeeded = Math.ceil(litersNeeded / 18); // برميل كبير 18 لتر

            setPrimaryResult(Math.ceil(litersNeeded) + ' لتر دهان (' + gallonsNeeded.toFixed(1) + ' جالون)', 'كمية الدهان الصافية المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للجدران', value: netWallArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'إجمالي مساحة الطلاء (' + coats + ' طبقات)', value: totalCoatsArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'عدد الجالونات القياسية (3.75 لتر)', value: Math.ceil(gallonsNeeded) + ' جالون', color: '#f59e0b' },
                { label: 'عدد البراميل الكبيرة (18 لتر)', value: bucketsNeeded + ' برميل', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لدهان جدران بمساحة صافية <strong>\${netWallArea.toFixed(1)} م²</strong> بوجهين (طبقتين)، تحتاج إلى <strong>\${Math.ceil(litersNeeded)} لتر</strong>، وهو ما يعادل تقريباً <strong>\${Math.ceil(gallonsNeeded)} جالون</strong> أو <strong>\${bucketsNeeded} برميل سعة 18 لتر</strong>.</p>
            `);
        ",
        'points' => [
            'مساحة الجدران الإجمالية = محيط الغرفة × الارتفاع.',
            'المساحة الصافية = المساحة الإجمالية - مساحات الأبواب والنوافذ.',
            'لترات الدهان = (المساحة الصافية × عدد الأوجه) ÷ معدل تغطية اللتر.'
        ],
        'assumptions' => 'يفترض جدران مجهزة بأساس ومعجون؛ الجدران الخشنة غير المدهونة قد تستهلك 20% دهان إضافي.',
        'faqs' => [
            ['q' => 'هل يحتاج السقف لحساب منفصل؟', 'a' => 'نعم؛ يفضل حساب السقف بشكل مستقل لأنه غالباً يُدهن بلون أبيض مطفي بنوع دهان مخصص للأسقف.']
        ],
        'related' => ['putty-calculator', 'wall-area-calculator', 'ceiling-area-calculator']
    ],

    'putty-calculator' => [
        'title' => 'حاسبة كمية المعجون للجدران',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'wallNetArea', 'label' => 'المساحة الصافية للجدران المراد سحبها معجون (م²)', 'type' => 'number', 'default' => '45', 'min' => '1', 'step' => '1'],
            ['id' => 'knivesCount', 'label' => 'عدد طبقات (سكاكين) المعجون', 'type' => 'select', 'options' => [
                '2' => 'سكينتين (طبقتين - للجدران الناعمة مسبقاً)',
                '3' => '3 سكاكين (المعيار الهندسي لتسوية اللياسة الجديدة)',
                '4' => '4 سكاكين (للتشطيبات الفاخرة جداً وأسطح الجبس بورد)'
            ], 'default' => '3'],
            ['id' => 'puttyType', 'label' => 'نوع المعجون المستخدم', 'type' => 'select', 'options' => [
                'paste_bucket' => 'معجون مجهز جاهز (براميل بلاستيك سعة 15-20 كجم)',
                'powder_bag' => 'معجون بودرة يتم خلطه بالماء (شكائر سعة 20 كجم)'
            ], 'default' => 'paste_bucket'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('wallNetArea').value) || 45);
            const knives = parseInt(document.getElementById('knivesCount').value) || 3;
            const type = document.getElementById('puttyType').value;

            // استهلاك المتر المربع للسكين الواحد حوالي 0.5 إلى 0.6 كجم
            const kgPerM2PerCoat = 0.55;
            const totalKg = area * knives * kgPerM2PerCoat;
            const packageWeight = 20; // كجم للعبوة الواحدة
            const packagesCount = Math.ceil(totalKg / packageWeight);

            setPrimaryResult(Math.ceil(totalKg) + ' كجم معجون (' + packagesCount + ' ' + (type === 'paste_bucket' ? 'برميل' : 'شيكارة') + ')', 'كمية المعجون المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الإجمالية للمحارة', value: area + ' م²', color: '#3b82f6' },
                { label: 'عدد طبقات وسكاكين المعجون', value: knives + ' سكاكين', color: '#10b981' },
                { label: 'إجمالي الوزن المطلوب', value: Math.ceil(totalKg) + ' كجم', color: '#f59e0b' },
                { label: 'عدد العبوات سعة 20 كجم', value: packagesCount + ' عبوة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتنفيذ <strong>\${knives} طبقات معجون</strong> على مساحة <strong>\${area} م²</strong>، تحتاج حوالي <strong>\${Math.ceil(totalKg)} كجم</strong> من المعجون، أي ما يعادل <strong>\${packagesCount} عبوة سعة 20 كجم</strong>.</p>
            `);
        ",
        'points' => [
            'السكين الأول يملأ مسامات اللياسة ويستهلك كمية أكبر، بينما السكين الثاني والثالث ينعمان السطح.',
            'متوسط استهلاك المتر المربع حوالي 0.5 كجم لكل طبقة معجون.'
        ],
        'assumptions' => 'يفترض سطح لياسة مستوٍ؛ الأسطح شديدة التعرج تحتاج زيادة 15% في كمية المعجون.',
        'faqs' => [
            ['q' => 'أيهما أفضل: معجون البودرة أم المعجون الجاهز؟', 'a' => 'معجون البودرة ممتاز للطبقات الأولى لملء الفراغات وتوفير التكلفة، بينما المعجون الجاهز ممتاز للطبقة النهائية لأنه يعطي ملمساً فائق النعومة وسهل الصنفرة.']
        ],
        'related' => ['paint-calculator', 'wall-area-calculator', 'gypsum-board-calculator']
    ],

    'gypsum-board-calculator' => [
        'title' => 'حاسبة كمية الجبس بورد ومستلزماته',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'ceilingLength', 'label' => 'طول السقف المراد تغطيته (متر)', 'type' => 'number', 'default' => '6.0', 'min' => '1', 'step' => '0.2'],
            ['id' => 'ceilingWidth', 'label' => 'عرض السقف (متر)', 'type' => 'number', 'default' => '4.5', 'min' => '1', 'step' => '0.2'],
            ['id' => 'sheetSize', 'label' => 'مقاس لوح الجبس بورد', 'type' => 'select', 'options' => [
                'standard' => '1.20 م × 2.40 م (المقاس القياسي الأكثر شيوعاً - 2.88 م²)',
                'long' => '1.20 م × 3.00 م (ألواح طويلة - 3.60 م²)'
            ], 'default' => 'standard'],
            ['id' => 'wasteRate', 'label' => 'نسبة الهالك والقص (%)- عادة 10%', 'type' => 'number', 'default' => '10', 'min' => '5', 'max' => '25', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('ceilingLength').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('ceilingWidth').value) || 4.5);
            const size = document.getElementById('sheetSize').value;
            const waste = Math.max(5, parseFloat(document.getElementById('wasteRate').value) || 10) / 100;

            const area = len * wid;
            const sheetArea = size === 'long' ? 3.60 : 2.88;
            const areaWithWaste = area * (1 + waste);
            const sheetsCount = Math.ceil(areaWithWaste / sheetArea);

            // حسابات تقريبية للإكسسوارات لكل لوح جبس بورد
            const omegaRails = Math.ceil(sheetsCount * 1.5); // قطاع أوميجا 3 متر
            const cStuds = Math.ceil(sheetsCount * 1.2); // قطاع سي 3 متر
            const angles = Math.ceil((2 * (len + wid)) / 3); // زوايا جدارية محيطية طول 3 م
            const screwsBoxes = Math.ceil(sheetsCount / 12); // علبة مسامير جبس لكل 12 لوح

            setPrimaryResult(sheetsCount + ' لوح جبس بورد', 'عدد ألواح الجبس بورد المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة السقف الصافية', value: area.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'قواطع أوميجا (Omega rails)', value: omegaRails + ' عود (طول 3م)', color: '#10b981' },
                { label: 'زوايا جدارية محيطية', value: angles + ' عود (طول 3م)', color: '#f59e0b' },
                { label: 'براغي تثبيت وشريط فواصل', value: screwsBoxes + ' علبة مسامير + رول فيبر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية سقف بمساحة <strong>\${area.toFixed(1)} م²</strong> مع نسبة هالك <strong>\${(waste*100).toFixed(0)}%</strong>، تحتاج إلى <strong>\${sheetsCount} لوح</strong> بمقاس \${sheetArea} م²، بالإضافة إلى هيكل الحديد ومستلزمات التثبيت.</p>
            `);
        ",
        'points' => [
            'مساحة السقف = الطول × العرض.',
            'عدد الألواح = (مساحة السقف × (1 + نسبة الهالك)) ÷ مساحة اللوح الواحد.',
            'مساحة اللوح القياسي = 1.20 × 2.40 = 2.88 م².'
        ],
        'assumptions' => 'النسب تشمل الهياكل المعدنية المعلقة (أوميجا وزوايا وتيش تعليق) وفق المعايير الإنشائية.',
        'faqs' => [
            ['q' => 'ما هو نوع الجبس بورد المناسب للمطابخ والحمامات؟', 'a' => 'يجب استخدام الألواح الخضراء المقاومة للرطوبة، أو الألواح الأسمنتية (Cement Board) المعزولة لمنع تكون العفن والتلف الناتج عن البخار.']
        ],
        'related' => ['ceiling-area-calculator', 'putty-calculator', 'paint-calculator']
    ],

    'cement-calculator' => [
        'title' => 'حاسبة كمية الإسمنت للخرسانة والمحارة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'workType', 'label' => 'نوع العمل الخرساني', 'type' => 'select', 'options' => [
                'reinforced' => 'خرسانة مسلحة (أسقف، أعمدة، كمرات) - 350 كجم/م³ (7 شكائر)',
                'plain' => 'خرسانة عادية (نظافة، أرضيات) - 250 كجم/م³ (5 شكائر)',
                'plaster' => 'محارة ولياسة جدران - 300 كجم/م³ رمل',
                'mortar' => 'مونة بناء البلوك والطوب'
            ], 'default' => 'reinforced'],
            ['id' => 'volumeOrArea', 'label' => 'الحجم بالمتر المكعب (م³) - أو المساحة للمحارة بالمتر المربع (م²)', 'type' => 'number', 'default' => '15', 'min' => '0.5', 'step' => '0.5'],
            ['id' => 'plasterThickness', 'label' => 'سماكة المحارة (سم) - إذا اخترت محارة فقط', 'type' => 'number', 'default' => '2.5', 'min' => '1', 'max' => '5', 'step' => '0.5'],
        ],
        'calcJs' => "
            const type = document.getElementById('workType').value;
            const inputVal = Math.max(0.5, parseFloat(document.getElementById('volumeOrArea').value) || 15);
            const thick = Math.max(1, parseFloat(document.getElementById('plasterThickness').value) || 2.5);

            let totalVolumeM3 = inputVal;
            let kgPerM3 = 350;

            if (type === 'plain') kgPerM3 = 250;
            if (type === 'plaster') {
                // سمك المحارة يحول المساحة لحجم
                totalVolumeM3 = inputVal * (thick / 100);
                kgPerM3 = 300;
            }
            if (type === 'mortar') kgPerM3 = 300;

            const totalKg = totalVolumeM3 * kgPerM3;
            const bagsCount = Math.ceil(totalKg / 50); // شيكارة الإسمنت 50 كجم
            const tonsCount = totalKg / 1000;

            setPrimaryResult(bagsCount + ' شيكارة إسمنت (' + tonsCount.toFixed(2) + ' طن)', 'كمية الإسمنت المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي حجم المونة أو الخرسانة', value: totalVolumeM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن الصافي للإسمنت', value: Math.ceil(totalKg) + ' كجم', color: '#10b981' },
                { label: 'معيار الإسمنت المعتمد', value: kgPerM3 + ' كجم/م³', color: '#f59e0b' },
                { label: 'عدد الشكائر سعة 50 كجم', value: bagsCount + ' شيكارة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتنفيذ حجم قدره <strong>\${totalVolumeM3.toFixed(2)} متر مكعب</strong> بمعدل <strong>\${kgPerM3} كجم/م³</strong>، تحتاج إلى <strong>\${bagsCount} شيكارة إسمنت</strong> (ما يعادل <strong>\${tonsCount.toFixed(2)} طن</strong>).</p>
            `);
        ",
        'points' => [
            'الخرسانة المسلحة القياسية تستهلك 7 شكائر إسمنت (350 كجم) لكل متر مكعب.',
            'الخرسانة العادية تستهلك 5 شكائر إسمنت (250 كجم) لكل متر مكعب.',
            'وزن شيكارة الإسمنت القياسية عالمياً هو 50 كجم (20 شيكارة تعادل 1 طن).'
        ],
        'assumptions' => 'النسب مطابقة للمواصفات الهندسية والكود العربي الموحد للخرسانة المسلحة.',
        'faqs' => [
            ['q' => 'كم شيكارة إسمنت في الطن الواحد؟', 'a' => 'الطن يحتوي بالضبط على 20 شيكارة إسمنت سعة كل شيكارة 50 كجم.']
        ],
        'related' => ['sand-calculator', 'gravel-calculator', 'house-building-cost-calculator']
    ],

    'sand-calculator' => [
        'title' => 'حاسبة كمية الرمل للبناء والخرسانة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'concreteVolume', 'label' => 'حجم الخرسانة أو المونة المطلوب (متر مكعب)', 'type' => 'number', 'default' => '20', 'min' => '0.5', 'step' => '0.5'],
            ['id' => 'sandRatio', 'label' => 'نسبة الرمل في المزيج (عادة 0.40 إلى 0.45 م³ لكل م³ خرسانة)', 'type' => 'number', 'default' => '0.42', 'min' => '0.3', 'max' => '1.0', 'step' => '0.02'],
        ],
        'calcJs' => "
            const volume = Math.max(0.5, parseFloat(document.getElementById('concreteVolume').value) || 20);
            const ratio = Math.max(0.3, parseFloat(document.getElementById('sandRatio').value) || 0.42);

            const sandM3 = volume * ratio;
            // كثافة الرمل المتوسطة حوالي 1.5 إلى 1.6 طن / م³
            const sandTons = sandM3 * 1.55;
            const trucksCount = Math.ceil(sandM3 / 16); // لوري سعة 16 م³

            setPrimaryResult(sandM3.toFixed(1) + ' م³ رمل (' + sandTons.toFixed(1) + ' طن)', 'كمية الرمل المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'حجم الرمل بالمتر المكعب', value: sandM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن التقريبي بالطن', value: sandTons.toFixed(2) + ' طن', color: '#10b981' },
                { label: 'عدد سيارات النقل الكبيرة (تريلا 16م³)', value: trucksCount + ' سيارة', color: '#f59e0b' },
                { label: 'حجم الخرسانة الإجمالي المغطى', value: volume + ' م³', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لصب <strong>\${volume} م³</strong> خرسانة، تحتاج إلى <strong>\${sandM3.toFixed(1)} متر مكعب رمل</strong> نظيف خالي من الشوائب والأملاح، بوزن يعادل تقريباً <strong>\${sandTons.toFixed(1)} طن</strong>.</p>
            `);
        ",
        'points' => [
            'الخلطة الخرسانية القياسية تتكون من: 0.8 م³ سن + 0.4 م³ رمل + 350 كجم إسمنت + ماء.',
            'كثافة الرمل الجاف تتراوح بين 1.5 إلى 1.6 طن لكل متر مكعب.'
        ],
        'assumptions' => 'يفترض رمل سيليكا حرش مغسول مناسب للأعمال الخرسانية الإنشائية.',
        'faqs' => [
            ['q' => 'لماذا يُشترط غسل الرمل قبل استخدامه في الخرسانة؟', 'a' => 'للتخلص من الأملاح والطمي التي تضعف تماسك الإسمنت وتتسبب في تآكل وصدأ حديد التسليح لاحقاً.']
        ],
        'related' => ['gravel-calculator', 'cement-calculator', 'house-building-cost-calculator']
    ],

    'gravel-calculator' => [
        'title' => 'حاسبة كمية الحصى والسن للخرسانة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'concreteVol', 'label' => 'حجم الخرسانة المطلوب صبها (متر مكعب)', 'type' => 'number', 'default' => '20', 'min' => '0.5', 'step' => '0.5'],
            ['id' => 'gravelRatio', 'label' => 'نسبة الحصى في المزيج (عادة 0.80 إلى 0.85 م³ لكل م³ خرسانة)', 'type' => 'number', 'default' => '0.82', 'min' => '0.6', 'max' => '1.0', 'step' => '0.02'],
        ],
        'calcJs' => "
            const vol = Math.max(0.5, parseFloat(document.getElementById('concreteVol').value) || 20);
            const ratio = Math.max(0.6, parseFloat(document.getElementById('gravelRatio').value) || 0.82);

            const gravelM3 = vol * ratio;
            // كثافة السن/الحصى حوالي 1.6 إلى 1.7 طن / م³
            const gravelTons = gravelM3 * 1.65;
            const trucks = Math.ceil(gravelM3 / 16);

            setPrimaryResult(gravelM3.toFixed(1) + ' م³ سن وحصى (' + gravelTons.toFixed(1) + ' طن)', 'كمية الحصى (الركام الخشن)');
            showResultArea();

            setDetailStats([
                { label: 'حجم الحصى بالمتر المكعب', value: gravelM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن التقريبي بالطن', value: gravelTons.toFixed(2) + ' طن', color: '#10b981' },
                { label: 'عدد شاحنات النقل الكبيرة (16م³)', value: trucks + ' نقلة', color: '#f59e0b' },
                { label: 'نسبة الحصى من الخلطة', value: (ratio * 100).toFixed(0) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لخلط <strong>\${vol} م³</strong> خرسانة، تحتاج إلى <strong>\${gravelM3.toFixed(1)} متر مكعب من الحصى المتدرج (السن)</strong>، ما يزن حوالي <strong>\${gravelTons.toFixed(1)} طن</strong>.</p>
            `);
        ",
        'points' => [
            'الركام الخشن (الحصى أو السن) يمثل الهيكل العظمي للخرسانة ويشكل حوالي 70% إلى 80% من حجمها.',
            'حجم الحصى المطلوب يعادل ضعف حجم الرمل تقريباً (نسبة 2 : 1).'
        ],
        'assumptions' => 'يفترض استخدام حصى متدرج مقاس 1 و 2 خالي من الأتربة.',
        'faqs' => [
            ['q' => 'ما هو المقاس الأفضل للسن في الأسقف والأعمدة؟', 'a' => 'يُفضل خليط متوازن بين سن 1 وسن 2 (مقاس من 10 مم إلى 20 مم) لضمان سهولة الانسياب بين أسياخ الحديد ومنع التعشيش.']
        ],
        'related' => ['sand-calculator', 'cement-calculator', 'house-building-cost-calculator']
    ],

    'block-calculator' => [
        'title' => 'حاسبة كمية البلوك الإسمنتي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'wallsLengthTotal', 'label' => 'إجمالي أطوال الجدران المراد بناؤها (متر)', 'type' => 'number', 'default' => '30', 'min' => '1', 'step' => '0.5'],
            ['id' => 'wallHeightBlock', 'label' => 'ارتفاع الجدار (متر)', 'type' => 'number', 'default' => '3.0', 'min' => '1', 'max' => '10', 'step' => '0.1'],
            ['id' => 'openingsAreaDeduct', 'label' => 'مساحة الفتحات المخصومة (أبواب وشبابيك م²)', 'type' => 'number', 'default' => '8', 'min' => '0', 'step' => '0.5'],
            ['id' => 'blockTypeSize', 'label' => 'مقاس البلوك المستخدم', 'type' => 'select', 'options' => [
                '20x40' => 'بلوك قياسي 20×40 سم (12.5 بلوكة للمتر المربع)',
                '15x40' => 'بلوك قواطع 15×40 سم (12.5 بلوكة للمتر المربع)',
                '10x40' => 'بلوك رفيع 10×40 سم (12.5 بلوكة للمتر المربع)'
            ], 'default' => '20x40'],
            ['id' => 'wastePercent', 'label' => 'نسبة الهالك والكسر (%)- عادة 5%', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '15', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('wallsLengthTotal').value) || 30);
            const height = Math.max(1, parseFloat(document.getElementById('wallHeightBlock').value) || 3.0);
            const openings = Math.max(0, parseFloat(document.getElementById('openingsAreaDeduct').value) || 8);
            const waste = Math.max(0, parseFloat(document.getElementById('wastePercent').value) || 5) / 100;

            const grossArea = len * height;
            const netArea = Math.max(1, grossArea - openings);
            // المقاس القياسي 40 سم طول × 20 سم ارتفاع = 0.08 م² للبلوكة -> 12.5 بلوكة/م²
            const blocksPerM2 = 12.5;
            const baseBlocks = netArea * blocksPerM2;
            const totalBlocks = Math.ceil(baseBlocks * (1 + waste));

            setPrimaryResult(totalBlocks + ' بلوكة إسمنتية', 'إجمالي عدد البلوك المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للبناء', value: netArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'عدد البلوك بدون هالك', value: Math.ceil(baseBlocks) + ' بلوكة', color: '#10b981' },
                { label: 'مخصص الهالك والقص (' + (waste * 100) + '%)', value: Math.ceil(totalBlocks - baseBlocks) + ' بلوكة', color: '#f59e0b' },
                { label: 'إجمالي مساحة الفتحات المخصومة', value: openings + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء جدران بمساحة صافية <strong>\${netArea.toFixed(1)} م²</strong>، تحتاج إلى <strong>\${totalBlocks} بلوكة</strong> شاملة <strong>\${(waste*100).toFixed(0)}%</strong> نسبة هالك للقص والتشبيك مع الأعمدة.</p>
            `);
        ",
        'points' => [
            'أبعاد البلوكة القياسية: 40 سم طول × 20 سم ارتفاع.',
            'المتر المربع يحتوي بالضبط على: 1 ÷ (0.40 × 0.20) = 12.5 بلوكة.',
            'إجمالي البلوك = المساحة الصافية × 12.5 × (1 + نسبة الهالك).'
        ],
        'assumptions' => 'سماكة عراميس المونة الإسمنتية 1 سم مأخوذة في الاعتبار ضمن الأبعاد القياسية للبلوك.',
        'faqs' => [
            ['q' => 'أيهما أفضل للجدران الخارجية: البلوك البركاني أم الإسمنتي المصمت؟', 'a' => 'البلوك البركاني المعزول بالبوليستيرين هو الخيار الأفضل للجدران الخارجية لتميزه بخفة الوزن وعزله الحراري الممتاز المطابق لكود البناء.']
        ],
        'related' => ['brick-calculator', 'cement-calculator', 'wall-area-calculator']
    ],

    'brick-calculator' => [
        'title' => 'حاسبة كمية الطوب الأحمر والطفلي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'brickWallLength', 'label' => 'طول الحائط (متر)', 'type' => 'number', 'default' => '20', 'min' => '1', 'step' => '0.5'],
            ['id' => 'brickWallHeight', 'label' => 'ارتفاع الحائط (متر)', 'type' => 'number', 'default' => '2.8', 'min' => '1', 'step' => '0.1'],
            ['id' => 'brickOpenings', 'label' => 'مساحة الفتحات والأبواب المخصومة (م²)', 'type' => 'number', 'default' => '5', 'min' => '0', 'step' => '0.5'],
            ['id' => 'wallThicknessType', 'label' => 'سماكة الجدار المبني', 'type' => 'select', 'options' => [
                'half_brick' => 'جدار نصف طوبة (سمك 12 سم - قواطع داخلية) ~ 55 طوبة/م²',
                'full_brick' => 'جدار طوبة كاملة (سمك 25 سم - جدران خارجية أو حاملة) ~ 110 طوبة/م²'
            ], 'default' => 'half_brick'],
            ['id' => 'brickWasteRate', 'label' => 'نسبة الهالك والكسر (%)- عادة 5%', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '15', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('brickWallLength').value) || 20);
            const height = Math.max(1, parseFloat(document.getElementById('brickWallHeight').value) || 2.8);
            const openings = Math.max(0, parseFloat(document.getElementById('brickOpenings').value) || 5);
            const thick = document.getElementById('wallThicknessType').value;
            const waste = Math.max(0, parseFloat(document.getElementById('brickWasteRate').value) || 5) / 100;

            const netArea = Math.max(1, (len * height) - openings);
            const factor = thick === 'full_brick' ? 110 : 55;
            const baseBricks = netArea * factor;
            const totalBricks = Math.ceil(baseBricks * (1 + waste));
            const thousandsCount = totalBricks / 1000;

            setPrimaryResult(totalBricks + ' طوبة (' + thousandsCount.toFixed(2) + ' ألف طوبة)', 'إجمالي كمية الطوب الأحمر المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للجدار', value: netArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'نوع الجدار المبني', value: thick === 'full_brick' ? 'طوبة كاملة (25 سم)' : 'نصف طوبة (12 سم)', color: '#10b981' },
                { label: 'معدل الطوب في المتر المربع', value: factor + ' طوبة/م²', color: '#f59e0b' },
                { label: 'عدد الألف طوبة', value: thousandsCount.toFixed(2) + ' ألف', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء جدار صافي بمساحة <strong>\${netArea.toFixed(1)} م²</strong> بنظام <strong>\${thick === 'full_brick' ? 'طوبة كاملة' : 'نصف طوبة'}</strong>، تحتاج إلى <strong>\${totalBricks} طوبة</strong> (حوالي <strong>\${thousandsCount.toFixed(2)} ألف طوبة</strong>).</p>
            `);
        ",
        'points' => [
            'مقاس الطوبة الحمراء القياسي: 25 × 12 × 6 سم.',
            'جدار نصف طوبة (12 سم) يحتاج حوالي 55 طوبة لكل متر مربع.',
            'جدار طوبة كاملة (25 سم) يحتاج حوالي 110 طوبة لكل متر مربع.'
        ],
        'assumptions' => 'الحساب يشمل فواصل المونة الإسمنتية القياسية بسماكة 1 سم.',
        'faqs' => [
            ['q' => 'كم كمية الأسمنت والرمل اللازمة لبناء 1000 طوبة؟', 'a' => 'يحتاج كل ألف طوبة حمراء لحوالي 3 شكائر إسمنت و 0.6 م³ رمل ناعم للمونة.']
        ],
        'related' => ['block-calculator', 'cement-calculator', 'sand-calculator']
    ],

    'floor-tiles-calculator' => [
        'title' => 'حاسبة كمية البلاط للأرضيات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roomLenTile', 'label' => 'طول الأرضية (متر)', 'type' => 'number', 'default' => '6.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'roomWidTile', 'label' => 'عرض الأرضية (متر)', 'type' => 'number', 'default' => '4.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'tileWidthCm', 'label' => 'عرض البلاطة (سم)', 'type' => 'number', 'default' => '60', 'min' => '10', 'step' => '5'],
            ['id' => 'tileLengthCm', 'label' => 'طول البلاطة (سم)', 'type' => 'number', 'default' => '60', 'min' => '10', 'step' => '5'],
            ['id' => 'tileInstallPattern', 'label' => 'طريقة تركيب البلاط', 'type' => 'select', 'options' => [
                'straight' => 'تركيب مستقيم عادي (هالك 5% إلى 7%)',
                'diagonal' => 'تركيب قطري / مائل 45 درجة (سمبوسة) - هالك 12% إلى 15%'
            ], 'default' => 'straight'],
            ['id' => 'tilesPerBox', 'label' => 'عدد البلاطات في الكرتونة الواحدة', 'type' => 'number', 'default' => '4', 'min' => '1', 'max' => '30', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('roomLenTile').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('roomWidTile').value) || 4.0);
            const tileW = Math.max(10, parseFloat(document.getElementById('tileWidthCm').value) || 60) / 100;
            const tileL = Math.max(10, parseFloat(document.getElementById('tileLengthCm').value) || 60) / 100;
            const pattern = document.getElementById('tileInstallPattern').value;
            const boxCount = Math.max(1, parseInt(document.getElementById('tilesPerBox').value) || 4);

            const floorArea = len * wid;
            const singleTileArea = tileW * tileL;
            const wasteRate = pattern === 'diagonal' ? 0.12 : 0.06;
            const totalAreaWithWaste = floorArea * (1 + wasteRate);
            const totalTiles = Math.ceil(totalAreaWithWaste / singleTileArea);
            const boxesNeeded = Math.ceil(totalTiles / boxCount);

            setPrimaryResult(boxesNeeded + ' كرتونة بلاط (' + totalTiles + ' بلاطة)', 'كمية البلاط المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الأرضية الصافية', value: floorArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'إجمالي المساحة المطلوبة مع الهالك', value: totalAreaWithWaste.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'نسبة هالك القص المعتمدة', value: (wasteRate * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'مساحة البلاطة الواحدة', value: singleTileArea.toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية أرضية بمساحة <strong>\${floorArea.toFixed(2)} م²</strong> ببلاط مقاس \${(tileW*100)}×\${(tileL*100)} سم، تحتاج إلى <strong>\${totalTiles} بلاطة</strong> معبأة في <strong>\${boxesNeeded} كرتونة</strong> لضمان تغطية هالك القص والزوايا.</p>
            `);
        ",
        'points' => [
            'مساحة الأرضية = الطول × العرض.',
            'مساحة البلاطة = الطول (بالمتر) × العرض (بالمتر).',
            'التركيب المائل يتطلب دائماً نسبة هالك أعلى (12-15%) بسبب كثرة المثلثات والقصات الجدارية.'
        ],
        'assumptions' => 'يفترض عدم وجود عيوب مصنعية في البلاط ومهارة فني تركيب جيدة.',
        'faqs' => [
            ['q' => 'هل يجب الاحتفاظ بكرتونة إضافية بعد انتهاء التبليط؟', 'a' => 'نعم؛ يُوصى بشدة بالاحتفاظ بكرتونة إضافية من نفس رقم الطبخة (Batch Number / Shade) لأي إصلاحات مستقبلية في السباكة، لأن الألوان تختلف من إنتاج لآخر.']
        ],
        'related' => ['ceramic-calculator', 'tile-adhesive-calculator', 'grout-calculator']
    ],

    'ceramic-calculator' => [
        'title' => 'حاسبة كمية السيراميك والبورسلان',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'ceramicSurfaceArea', 'label' => 'المساحة المراد تبليطها (أرضيات أو جدران) بالمتر المربع', 'type' => 'number', 'default' => '50', 'min' => '1', 'step' => '1'],
            ['id' => 'boxMeters', 'label' => 'المساحة التي تغطيها الكرتونة الواحدة بالمتر المربع (مكتوبة على العلبة)', 'type' => 'number', 'default' => '1.44', 'min' => '0.5', 'step' => '0.04'],
            ['id' => 'ceramicWastePercent', 'label' => 'نسبة هالك القص والزوائد (%)- عادة 8%', 'type' => 'number', 'default' => '8', 'min' => '5', 'max' => '20', 'step' => '1'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('ceramicSurfaceArea').value) || 50);
            const mPerBox = Math.max(0.5, parseFloat(document.getElementById('boxMeters').value) || 1.44);
            const waste = Math.max(5, parseFloat(document.getElementById('ceramicWastePercent').value) || 8) / 100;

            const areaWithWaste = area * (1 + waste);
            const boxesNeeded = Math.ceil(areaWithWaste / mPerBox);
            const actualTotalArea = boxesNeeded * mPerBox;

            setPrimaryResult(boxesNeeded + ' كرتونة سيراميك / بورسلان', 'عدد الكراتين المطلوبة للشراء');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للمشروع', value: area.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'المساحة الإجمالية مع الهالك', value: areaWithWaste.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'المساحة المشتراة فعلياً بالكراتين', value: actualTotalArea.toFixed(2) + ' م²', color: '#8b5cf6' },
                { label: 'مساحة الكرتونة الواحدة', value: mPerBox + ' م²', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لتغطية مساحة صافية <strong>\${area} م²</strong> بنسبة هالك <strong>\${(waste*100).toFixed(0)}%</strong>، تحتاج إلى <strong>\${boxesNeeded} كرتونة</strong> تشتمل على <strong>\${actualTotalArea.toFixed(2)} متر مربع</strong> فعلياً.</p>
            `);
        ",
        'points' => [
            'الكرتونة من مقاس 60×60 غالباً تحتوي على 4 قطع وتغطي 1.44 م².',
            'كرتونة مقاس 60×120 غالباً تحتوي على قطعتين وتغطي 1.44 م².',
            'كرتونة مقاس 80×80 غالباً تحتوي على قطعتين أو 3 وتغطي حوالي 1.28 أو 1.92 م².'
        ],
        'assumptions' => 'سيراميك الجدران للحمامات والمطابخ يتطلب دقة في خصم مساحة الباب والشباك وحساب الوزرات.',
        'faqs' => [
            ['q' => 'ما الفرق بين كرتونة السيراميك والبورسلان؟', 'a' => 'البورسلان أكثر كثافة وصلابة ووزناً وأقل امتصاصاً للماء، ويحتاج دائماً إلى غراء مخصص (وليس أسمنت عادي) للتثبيت على الأرضيات.']
        ],
        'related' => ['floor-tiles-calculator', 'tile-adhesive-calculator', 'grout-calculator']
    ],

    'tile-adhesive-calculator' => [
        'title' => 'حاسبة كمية الغراء للبلاط والبورسلان',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'tileAreaAdhesive', 'label' => 'المساحة المطلوب تبليطها بالغراء (متر مربع)', 'type' => 'number', 'default' => '40', 'min' => '1', 'step' => '1'],
            ['id' => 'notchTrowelSize', 'label' => 'حجم أسنان المالج (البروة) وسمك الغراء', 'type' => 'select', 'options' => [
                'small' => 'مالج 6 مم (بلاط صغير وسيراميك عادي) ~ 3.5 كجم/م²',
                'medium' => 'مالج 8-10 مم (بورسلان 60×60 سم) ~ 5 كجم/م²',
                'large' => 'مالج 12 مم أو دبل دهان (بورسلان كبير 60×120 سم) ~ 7 كجم/م²'
            ], 'default' => 'medium'],
            ['id' => 'bagWeight', 'label' => 'وزن شيكارة الغراء (كجم) - الشائع 20 أو 25 كجم', 'type' => 'number', 'default' => '20', 'min' => '10', 'max' => '50', 'step' => '5'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('tileAreaAdhesive').value) || 40);
            const trowel = document.getElementById('notchTrowelSize').value;
            const bag = Math.max(10, parseFloat(document.getElementById('bagWeight').value) || 20);

            let rate = 5.0; // كجم / م²
            if (trowel === 'small') rate = 3.5;
            if (trowel === 'large') rate = 7.0;

            const totalKg = area * rate;
            const bagsCount = Math.ceil(totalKg / bag);

            setPrimaryResult(bagsCount + ' شيكارة غراء (' + totalKg + ' كجم)', 'كمية غراء البلاط المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الوزن الصافي المطلوب', value: totalKg + ' كجم', color: '#3b82f6' },
                { label: 'المساحة المراد لصقها', value: area + ' م²', color: '#10b981' },
                { label: 'معدل الاستهلاك لكل م²', value: rate + ' كجم / م²', color: '#f59e0b' },
                { label: 'وزن الشيكارة المعتمدة', value: bag + ' كجم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للصق مساحة <strong>\${area} م²</strong> بمعدل استهلاك <strong>\${rate} كجم/م²</strong>، تحتاج إلى <strong>\${bagsCount} شيكارة غراء</strong> سعة \${bag} كجم.</p>
            `);
        ",
        'points' => [
            'البورسلان قليل الامتصاص ويجب تركيبه بغراء بوليمري مخصص C2TE.',
            'البلاط كبير الحجم (أكبر من 60×60) يتطلب دهان الغراء على الأرضية وظهر البلاطة معاً (Back Buttering).'
        ],
        'assumptions' => 'يفترض أرضية مستوية تماماً مجهزة بصبة سكريد ناعمة.',
        'faqs' => [
            ['q' => 'هل يمكن لصق البورسلان بالإسمنت العادي؟', 'a' => 'ممنوع هندسياً؛ لأن البورسلان لا يمتص الماء بنسبة 99% وبالتالي ينفصل (يطبل) بعد فترة قصيرة إذا رُكب بالأسمنت بدون غراء عالي الجودة.']
        ],
        'related' => ['ceramic-calculator', 'floor-tiles-calculator', 'grout-calculator']
    ],

    'grout-calculator' => [
        'title' => 'حاسبة كمية الجراوت والترويبة للبلاط',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'tilingAreaGrout', 'label' => 'المساحة المطلوب ترويبها (متر مربع)', 'type' => 'number', 'default' => '50', 'min' => '1', 'step' => '1'],
            ['id' => 'tileLengthMm', 'label' => 'طول البلاطة (مم)', 'type' => 'number', 'default' => '600', 'min' => '50', 'step' => '50'],
            ['id' => 'tileWidthMm', 'label' => 'عرض البلاطة (مم)', 'type' => 'number', 'default' => '600', 'min' => '50', 'step' => '50'],
            ['id' => 'tileThicknessMm', 'label' => 'سماكة البلاطة (مم) - عادة 8 إلى 10 مم', 'type' => 'number', 'default' => '9', 'min' => '4', 'max' => '30', 'step' => '1'],
            ['id' => 'jointWidthMm', 'label' => 'عرض الفاصل بين البلاطات (مم) - عادة 2 إلى 3 مم', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '15', 'step' => '0.5'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('tilingAreaGrout').value) || 50);
            const L = Math.max(50, parseFloat(document.getElementById('tileLengthMm').value) || 600);
            const W = Math.max(50, parseFloat(document.getElementById('tileWidthMm').value) || 600);
            const T = Math.max(4, parseFloat(document.getElementById('tileThicknessMm').value) || 9);
            const J = Math.max(1, parseFloat(document.getElementById('jointWidthMm').value) || 2);

            // معادلة الترويبة القياسية: kg/m² = ((L + W) / (L * W)) * T * J * 1.7
            const kgPerM2 = ((L + W) / (L * W)) * T * J * 1.7;
            const totalKg = area * kgPerM2;
            const bagSize = 5; // كيس ترويبة قياسي 5 كجم
            const bags = Math.ceil(totalKg / bagSize);

            setPrimaryResult(Math.ceil(totalKg) + ' كجم ترويبة (' + bags + ' أكياس سعة 5 كجم)', 'كمية مادة الجراوت المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'معدل استهلاك المتر المربع', value: kgPerM2.toFixed(3) + ' كجم / م²', color: '#3b82f6' },
                { label: 'المساحة الإجمالية للمشروع', value: area + ' م²', color: '#10b981' },
                { label: 'عرض الفاصل المعتمد', value: J + ' مم', color: '#f59e0b' },
                { label: 'أكياس الترويبة سعة 5 كجم', value: bags + ' أكياس', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لترويبة مساحة <strong>\${area} م²</strong> ببلاط مقاس \${L/10}×\${W/10} سم وفواصل بعرض <strong>\${J} مم</strong>، تحتاج إلى <strong>\${Math.ceil(totalKg)} كجم ترويبة</strong> (\${bags} أكياس سعة 5 كجم).</p>
            `);
        ",
        'points' => [
            'معادلة حساب الترويبة: الاستهلاك = ((طول البلاطة + عرضها) ÷ (طولها × عرضها)) × سماكتها × عرض الفاصل × 1.7 (كثافة المادة).',
            'كلما كبر مقاس البلاطة قل عدد الفواصل وبالتالي قل استهلاك الترويبة.'
        ],
        'assumptions' => 'يفترض استخدام ترويبة إسمنتية مقاومة للرطوبة والبكتيريا ملائمة للحمامات والأرضيات.',
        'faqs' => [
            ['q' => 'متى يجب استخدام الترويبة الإيبوكسية (Epoxy Grout)؟', 'a' => 'في حمامات السباحة، والمطابخ التجارية، وأرضيات المستشفيات لأنها لا تمتص السوائل والدهون نهائياً ولا يتغير لونها مع الزمن.']
        ],
        'related' => ['floor-tiles-calculator', 'ceramic-calculator', 'tile-adhesive-calculator']
    ],

    'wallpaper-calculator' => [
        'title' => 'حاسبة كمية ورق الجدران',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roomPerimWallpaper', 'label' => 'محيط الجدران المراد تغطيتها (متر)', 'type' => 'number', 'default' => '14', 'min' => '1', 'step' => '0.5'],
            ['id' => 'wallHeightWallpaper', 'label' => 'ارتفاع الجدار (متر)', 'type' => 'number', 'default' => '2.8', 'min' => '1.5', 'max' => '5', 'step' => '0.1'],
            ['id' => 'rollWidth', 'label' => 'عرض رول ورق الجدران (متر) - القياسي 0.53 م', 'type' => 'number', 'default' => '0.53', 'min' => '0.4', 'max' => '1.5', 'step' => '0.01'],
            ['id' => 'rollLength', 'label' => 'طول رول ورق الجدران (متر) - القياسي 10.0 م', 'type' => 'number', 'default' => '10.0', 'min' => '5', 'max' => '25', 'step' => '0.5'],
            ['id' => 'patternRepeat', 'label' => 'تطابق النقشة (Pattern Repeat)', 'type' => 'select', 'options' => [
                'none' => 'سادة بدون نقشة متكررة (هالك قليل جداً)',
                'repeat' => 'نقشة متكررة تحتاج مطابقة أفقية (هالك 15%)'
            ], 'default' => 'repeat'],
        ],
        'calcJs' => "
            const perim = Math.max(1, parseFloat(document.getElementById('roomPerimWallpaper').value) || 14);
            const height = Math.max(1.5, parseFloat(document.getElementById('wallHeightWallpaper').value) || 2.8);
            const rWidth = Math.max(0.4, parseFloat(document.getElementById('rollWidth').value) || 0.53);
            const rLen = Math.max(5, parseFloat(document.getElementById('rollLength').value) || 10.0);
            const pattern = document.getElementById('patternRepeat').value;

            // عدد الشرائح الرأسية في المحيط
            const stripsNeeded = Math.ceil(perim / rWidth);
            // عدد الشرائح التي يخرجها الرول الواحد
            let effectiveHeight = height + (pattern === 'repeat' ? 0.3 : 0.1);
            const stripsPerRoll = Math.floor(rLen / effectiveHeight) || 1;
            const rollsCount = Math.ceil(stripsNeeded / stripsPerRoll);
            const totalWallArea = perim * height;

            setPrimaryResult(rollsCount + ' رول ورق جدران', 'عدد الرولات المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الجدران الإجمالية', value: totalWallArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'عدد الشرائح الرأسية المطلوبة', value: stripsNeeded + ' شريحة', color: '#10b981' },
                { label: 'عدد الشرائح الناتجة من الرول الواحد', value: stripsPerRoll + ' شرائح', color: '#f59e0b' },
                { label: 'مساحة الرول الواحد', value: (rWidth * rLen).toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية جدران بمحيط <strong>\${perim} متر</strong> وارتفاع <strong>\${height} متر</strong>، تحتاج إلى <strong>\${stripsNeeded} شريحة رأسية</strong>، ما يتطلب شراء <strong>\${rollsCount} رول قياسي</strong> لضمان استمرار النقشة ومحاذاتها بدقة.</p>
            `);
        ",
        'points' => [
            'الرول القياسي الأكثر انتشاراً عالمياً هو 0.53 متر عرض × 10 أمتار طول (يغطي حوالي 5.3 م²).',
            'الرول الواحد يعطي في الغالب 3 شرائح كاملة لارتفاع السقف المعتاد (2.7 إلى 2.9 م).'
        ],
        'assumptions' => 'يفترض عدم خصم النوافذ والأبواب الصغيرة كاحتياطي لتطابق الرسم والنقشات.',
        'faqs' => [
            ['q' => 'لماذا يُنصح بعدم خصم الأبواب والنوافذ عند حساب ورق الجدران؟', 'a' => 'لأن قص الشريحة عند النافذة أو الباب لا يسمح غالباً بإعادة استخدام باقي الشريحة في مكان آخر بسبب ضرورة تطابق ارتفاع النقشة مع الشريحة المجاورة.']
        ],
        'related' => ['wall-area-calculator', 'paint-calculator', 'putty-calculator']
    ],

    'wall-area-calculator' => [
        'title' => 'حاسبة مساحة الجدران والدهانات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roomLenArea', 'label' => 'طول الغرفة (متر)', 'type' => 'number', 'default' => '5.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'roomWidArea', 'label' => 'عرض الغرفة (متر)', 'type' => 'number', 'default' => '4.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'roomHeightArea', 'label' => 'ارتفاع السقف (متر)', 'type' => 'number', 'default' => '3.0', 'min' => '1.5', 'max' => '8', 'step' => '0.1'],
            ['id' => 'doorsCountArea', 'label' => 'عدد الأبواب (المعتاد 2 م² لكل باب)', 'type' => 'number', 'default' => '1', 'min' => '0', 'step' => '1'],
            ['id' => 'windowsCountArea', 'label' => 'عدد النوافذ (المعتاد 1.5 م² لكل نافذة)', 'type' => 'number', 'default' => '1', 'min' => '0', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('roomLenArea').value) || 5.0);
            const wid = Math.max(1, parseFloat(document.getElementById('roomWidArea').value) || 4.0);
            const height = Math.max(1.5, parseFloat(document.getElementById('roomHeightArea').value) || 3.0);
            const doors = Math.max(0, parseInt(document.getElementById('doorsCountArea').value) || 0);
            const windows = Math.max(0, parseInt(document.getElementById('windowsCountArea').value) || 0);

            const perimeter = 2 * (len + wid);
            const grossWallArea = perimeter * height;
            const doorsArea = doors * 2.0;
            const windowsArea = windows * 1.5;
            const totalDeductions = doorsArea + windowsArea;
            const netWallArea = Math.max(1, grossWallArea - totalDeductions);

            setPrimaryResult(netWallArea.toFixed(2) + ' م²', 'المساحة الصافية الإجمالية للجدران');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الإجمالية قبل الخصم', value: grossWallArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'محيط الغرفة الكلي', value: perimeter.toFixed(1) + ' متر', color: '#10b981' },
                { label: 'مساحة الأبواب والنوافذ المخصومة', value: totalDeductions.toFixed(1) + ' م²', color: '#ef4444' },
                { label: 'مساحة السقف المقابلة', value: (len * wid).toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>محيط الغرفة <strong>\${perimeter.toFixed(1)} متر</strong>. المساحة الإجمالية للحوائط <strong>\${grossWallArea.toFixed(2)} م²</strong>، وبعد خصم <strong>\${totalDeductions.toFixed(1)} م²</strong> للأبواب والشبابيك، تصبح المساحة الصافية للدهان والتشطيب <strong>\${netWallArea.toFixed(2)} متر مربع</strong>.</p>
            `);
        ",
        'points' => [
            'محيط الغرفة = 2 × (الطول + العرض).',
            'مساحة الجدران الإجمالية = محيط الغرفة × الارتفاع.',
            'المساحة الصافية = المساحة الإجمالية - مساحات الفتحات (الأبواب والشبابيك).'
        ],
        'assumptions' => 'متوسط مساحة الباب القياسي 2 م² (عرض 1م × ارتفاع 2م)، ومساحة النافذة المعتادة 1.5 م².',
        'faqs' => [
            ['q' => 'كيف أحسب جدار له شكل مثلث أو سقف مائل؟', 'a' => 'احسب الجزء المستطيل أولاً (الطول × الارتفاع الأصغر)، ثم احسب المثلث العلوي (نصف القاعدة × الارتفاع المتبقي).']
        ],
        'related' => ['ceiling-area-calculator', 'paint-calculator', 'wallpaper-calculator']
    ],

    'ceiling-area-calculator' => [
        'title' => 'حاسبة مساحة السقف',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'ceilingLen', 'label' => 'طول السقف (متر)', 'type' => 'number', 'default' => '6.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'ceilingWid', 'label' => 'عرض السقف (متر)', 'type' => 'number', 'default' => '4.5', 'min' => '1', 'step' => '0.1'],
            ['id' => 'hasDrops', 'label' => 'هل السقف يحتوي على جبس ساقط (بيت نور / كرانيش بارزة)؟', 'type' => 'select', 'options' => [
                'flat' => 'سقف مستوٍ عادي (Flat Ceiling)',
                'cove' => 'سقف معلق مع بيت نور وكرانيش (+20% مساحة إضافية للدهان)'
            ], 'default' => 'flat'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('ceilingLen').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('ceilingWid').value) || 4.5);
            const type = document.getElementById('hasDrops').value;

            const flatArea = len * wid;
            const paintArea = type === 'cove' ? flatArea * 1.2 : flatArea;
            const perimeter = 2 * (len + wid);

            setPrimaryResult(flatArea.toFixed(2) + ' م²', 'مساحة السقف الصافية');
            showResultArea();

            setDetailStats([
                { label: 'المساحة المسطحة المستوية', value: flatArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الدهان المقدرة (مع الكرانيش)', value: paintArea.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'محيط السقف (طول الكرنيشة المطلوبة)', value: perimeter.toFixed(1) + ' متر طولي', color: '#f59e0b' },
                { label: 'ألواح الجبس بورد اللازمة للتغطية', value: Math.ceil((flatArea*1.1)/2.88) + ' لوح', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مساحة السقف الصافية هي <strong>\${flatArea.toFixed(2)} متر مربع</strong>، ومحيطه <strong>\${perimeter.toFixed(1)} متر طولي</strong> (وهو طول الكرانيش المطلوبة لزوايا السقف).</p>
            `);
        ",
        'points' => [
            'مساحة السقف المسطح المستوي تطابق مساحة الأرضية بالضبط.',
            'الأسقف المعلقة المعمارية ذات البيوت الساقطة والإنارة المخفية تزيد مساحة الدهان بمقدار 15% إلى 25% بسبب السقوط الجانبي للجبس.'
        ],
        'assumptions' => 'الغرفة مستطيلة أو مربعة؛ الأشكال غير المنتظمة يمكن تقسيمها لمستطيلات.',
        'faqs' => [
            ['q' => 'كيف أحسب طول شريط الليد (LED Strip) للإنارة المخفية؟', 'a' => 'طول شريط الليد يعادل محيط السقف الداخلي لبيت النور مطروحاً منه فتحات الصيانة.']
        ],
        'related' => ['gypsum-board-calculator', 'wall-area-calculator', 'paint-calculator']
    ],

    'skirting-board-calculator' => [
        'title' => 'حاسبة طول الوزرة والنعلة للأرضيات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'skirtingRoomLen', 'label' => 'طول الغرفة أو الصالة (متر)', 'type' => 'number', 'default' => '6.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'skirtingRoomWid', 'label' => 'عرض الغرفة (متر)', 'type' => 'number', 'default' => '4.5', 'min' => '1', 'step' => '0.1'],
            ['id' => 'doorsWidthDeduct', 'label' => 'إجمالي عرض فتحات الأبواب المخصومة (متر)', 'type' => 'number', 'default' => '1.0', 'min' => '0', 'step' => '0.1'],
            ['id' => 'skirtingWasteRate', 'label' => 'نسبة الهالك لقص وتوصيل الزوايا (%)- عادة 5%', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '15', 'step' => '1'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('skirtingRoomLen').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('skirtingRoomWid').value) || 4.5);
            const deduct = Math.max(0, parseFloat(document.getElementById('doorsWidthDeduct').value) || 1.0);
            const waste = Math.max(0, parseFloat(document.getElementById('skirtingWasteRate').value) || 5) / 100;

            const perimeter = 2 * (len + wid);
            const netLength = Math.max(1, perimeter - deduct);
            const totalWithWaste = netLength * (1 + waste);
            const standardPieces = Math.ceil(totalWithWaste / 2.4); // طول القطعة 2.4 م للخشب والفوم

            setPrimaryResult(totalWithWaste.toFixed(2) + ' متر طولي', 'إجمالي طول الوزرة المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'الطول الصافي للوزرة', value: netLength.toFixed(2) + ' متر', color: '#3b82f6' },
                { label: 'محيط الغرفة الكلي', value: perimeter.toFixed(1) + ' متر', color: '#10b981' },
                { label: 'عرض الأبواب المخصوم', value: deduct.toFixed(1) + ' متر', color: '#ef4444' },
                { label: 'عدد الأعواد (للقطع الخشبية/الفوم 2.4م)', value: standardPieces + ' عود', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج إلى <strong>\${totalWithWaste.toFixed(2)} متر طولي</strong> من النعلة / الوزرة لتغطية محيط الغرفة بالكامل بعد خصم فتحات الأبواب واحتساب <strong>\${(waste*100).toFixed(0)}%</strong> هالك لزوايا الأركان 45 درجة.</p>
            `);
        ",
        'points' => [
            'الوزرة تُحسب بالمتر الطولي وليس بالمتر المربع.',
            'طول الوزرة الصافي = محيط الغرفة - عرض الأبواب.',
            'زوايا الأركان 45 درجة تتطلب قص أطراف الوزرة مما يستوجب زيادة هالك 5% إلى 10%.'
        ],
        'assumptions' => 'يفترض عدم وجود فتحات أرضية أخرى مثل النوافذ الساقطة حتى الأرض.',
        'faqs' => [
            ['q' => 'أيهما أفضل: نعلة السيراميك البارزة أم النعلة المخفية (Flush Baseboard)؟', 'a' => 'النعلة المخفية تمنح مظهراً عصرياً فائق الأناقة وتمنع تراكم الغبار وتسمح بملاصقة الأثاث للجدار تماماً، لكنها تتطلب تخطيطاً مسبقاً أثناء مرحلة اللياسة.']
        ],
        'related' => ['floor-tiles-calculator', 'wall-area-calculator', 'carpet-area-calculator']
    ],

    'insulation-calculator' => [
        'title' => 'حاسبة كمية العزل الشاملة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'insulationArea', 'label' => 'المساحة المراد عزلها (متر مربع)', 'type' => 'number', 'default' => '120', 'min' => '1', 'step' => '5'],
            ['id' => 'insulationLayers', 'label' => 'عدد طبقات العزل', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '4', 'step' => '1'],
            ['id' => 'overlapRate', 'label' => 'نسبة ركوب الفواصل (Overlap) والرقبة (%)- عادة 10%', 'type' => 'number', 'default' => '10', 'min' => '5', 'max' => '25', 'step' => '1'],
            ['id' => 'rollCoverage', 'label' => 'المساحة الصافية للرول الواحد (م²) - الشائع 10 م²', 'type' => 'number', 'default' => '10', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('insulationArea').value) || 120);
            const layers = Math.max(1, parseInt(document.getElementById('insulationLayers').value) || 2);
            const overlap = Math.max(5, parseFloat(document.getElementById('overlapRate').value) || 10) / 100;
            const rollCov = Math.max(1, parseFloat(document.getElementById('rollCoverage').value) || 10);

            const totalCoverageArea = area * layers;
            const totalWithOverlap = totalCoverageArea * (1 + overlap);
            const rollsCount = Math.ceil(totalWithOverlap / rollCov);

            setPrimaryResult(rollsCount + ' رول عزل (' + totalWithOverlap.toFixed(1) + ' م²)', 'كمية لفائف العزل المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للسطح', value: area + ' م²', color: '#3b82f6' },
                { label: 'إجمالي مساحة الطبقات (' + layers + ' طبقات)', value: totalCoverageArea + ' م²', color: '#10b981' },
                { label: 'مخصص ركوب الفواصل (10 سم)', value: (totalCoverageArea * overlap).toFixed(1) + ' م²', color: '#f59e0b' },
                { label: 'مساحة الرول الواحد', value: rollCov + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل مساحة <strong>\${area} م²</strong> بعدد <strong>\${layers} طبقات</strong> مع ركوب فواصل 10 سم، تحتاج إلى <strong>\${rollsCount} رول</strong> لتغطية إجمالي مساحة <strong>\${totalWithOverlap.toFixed(1)} متر مربع</strong>.</p>
            `);
        ",
        'points' => [
            'لفائف الممبرين العازل تتطلب ركوباً لا يقل عن 10 سم بين اللفة والأخرى لضمان منع التسرب.',
            'يجب رفع العزل على الجدران المحيطة (الوزرة / رقبة الزجاجة) بارتفاع لا يقل عن 20 إلى 30 سم.'
        ],
        'assumptions' => 'يفترض سطحاً نظيفاً معالجاً بالبرايمر البيتوميني قبل اللحام باللهب.',
        'faqs' => [
            ['q' => 'كم مدة اختبار العزل بالماء للأسطح والحمامات؟', 'a' => 'يجب غمر السطح أو الحمام بالماء لارتفاع 10-15 سم لمدة 48 ساعة متواصلة على الأقل للتأكد من عدم وجود أي رشح أو تنميل.']
        ],
        'related' => ['waterproofing-calculator', 'thermal-insulation-calculator', 'roof-slope-calculator']
    ],

    'thermal-insulation-calculator' => [
        'title' => 'حاسبة كمية العزل الحراري',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'thermalArea', 'label' => 'مساحة الأسطح أو الجدران المطلوب عزلها حرارياً (م²)', 'type' => 'number', 'default' => '150', 'min' => '1', 'step' => '5'],
            ['id' => 'boardType', 'label' => 'نوع العازل الحراري', 'type' => 'select', 'options' => [
                'xps' => 'بوليستيرين مبثوق (XPS أزرق/وردي) - كثافة 35 كجم/م³',
                'rockwool' => 'صوف صخري (Rockwool) - عزل حراري وصوتي ومقاوم للحريق',
                'polyurethane' => 'رغوة فوم بولي يوريثان رش (Spray Foam)'
            ], 'default' => 'xps'],
            ['id' => 'thicknessCm', 'label' => 'سماكة العازل (سم) - الموصى به 5 إلى 7 سم', 'type' => 'number', 'default' => '5.0', 'min' => '2', 'max' => '15', 'step' => '0.5'],
            ['id' => 'boardSize', 'label' => 'مقاس لوح العزل (متر) - الشائع 1.25 × 0.60 م = 0.75 م²', 'type' => 'number', 'default' => '0.75', 'min' => '0.5', 'max' => '3', 'step' => '0.05'],
        ],
        'calcJs' => "
            const area = Math.max(1, parseFloat(document.getElementById('thermalArea').value) || 150);
            const type = document.getElementById('boardType').value;
            const thick = Math.max(2, parseFloat(document.getElementById('thicknessCm').value) || 5.0);
            const bSize = Math.max(0.5, parseFloat(document.getElementById('boardSize').value) || 0.75);

            const areaWithWaste = area * 1.05; // 5% هالك
            const boardsCount = Math.ceil(areaWithWaste / bSize);
            const volumeM3 = area * (thick / 100);

            setPrimaryResult(type === 'polyurethane' ? volumeM3.toFixed(2) + ' م³ فوم' : boardsCount + ' لوح عازل', 'الكمية المطلوبة من العازل الحراري');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية', value: area + ' م²', color: '#3b82f6' },
                { label: 'حجم العازل بالمتر المكعب', value: volumeM3.toFixed(2) + ' م³', color: '#10b981' },
                { label: 'سماكة العزل المعتمدة', value: thick + ' سم', color: '#f59e0b' },
                { label: 'عدد الألواح التقريبي', value: boardsCount + ' لوح', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل مساحة <strong>\${area} م²</strong> بسماكة <strong>\${thick} سم</strong>، تحتاج إلى <strong>\${boardsCount} لوح</strong> بمقاس \${bSize} م² (أو <strong>\${volumeM3.toFixed(2)} متر مكعب</strong> من مادة العزل)، وهو ما يوفر حتى 40% من استهلاك مكيفات الهواء صيفاً.</p>
            `);
        ",
        'points' => [
            'سماكة 5 سم من البوليستيرين المبثوق (XPS) تعادل جداراً خرسانياً بسماكة تزيد عن متر في كفاءة العزل الحراري.',
            'عزل الأسطح والجدران يخفض فاتورة كهرباء التكييف بنسبة تصل إلى 40%.'
        ],
        'assumptions' => 'مطابق للاشتراطات الفنية لكود البناء السعودي ولائحة كفاءة الطاقة للمباني السكنية.',
        'faqs' => [
            ['q' => 'أيهما يوضع أولاً على السطح: العازل المائي أم الحراري؟', 'a' => 'في نظام السطح المقلوب (Inverted Roof) الأكثر أماناً، يوضع العازل المائي أولاً فوق الخرسانة وميول التصريف، ثم يوضع فوقه العازل الحراري لحماية المائي من حرارة الشمس المباشرة.']
        ],
        'related' => ['waterproofing-calculator', 'insulation-calculator', 'roof-slope-calculator']
    ],

    'waterproofing-calculator' => [
        'title' => 'حاسبة كمية العزل المائي للأسطح والحمامات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'waterproofArea', 'label' => 'مساحة الأرضية الصافية (متر مربع)', 'type' => 'number', 'default' => '60', 'min' => '1', 'step' => '2'],
            ['id' => 'perimeterWalls', 'label' => 'محيط الجدران لرفع رقبة الزجاجة والوزرة (متر)', 'type' => 'number', 'default' => '32', 'min' => '1', 'step' => '1'],
            ['id' => 'upstandHeightCm', 'label' => 'ارتفاع رفع العزل على الجدران (سم) - عادة 25 إلى 30 سم', 'type' => 'number', 'default' => '30', 'min' => '15', 'max' => '60', 'step' => '5'],
            ['id' => 'membraneLayers', 'label' => 'عدد طبقات الممبرين (لفائف بيتومين 4 مم)', 'type' => 'number', 'default' => '1', 'min' => '1', 'max' => '3', 'step' => '1'],
        ],
        'calcJs' => "
            const floor = Math.max(1, parseFloat(document.getElementById('waterproofArea').value) || 60);
            const perim = Math.max(1, parseFloat(document.getElementById('perimeterWalls').value) || 32);
            const upstand = Math.max(15, parseFloat(document.getElementById('upstandHeightCm').value) || 30) / 100;
            const layers = Math.max(1, parseInt(document.getElementById('membraneLayers').value) || 1);

            const upstandArea = perim * upstand;
            const totalNetArea = floor + upstandArea;
            const totalWithOverlap = totalNetArea * layers * 1.12; // 12% ركوب أطراف وهالك
            const rollsCount = Math.ceil(totalWithOverlap / 10); // الرول الصافي 10 م²
            const primerBuckets = Math.ceil(totalNetArea / 40); // برميل برايمر 40 م²

            setPrimaryResult(rollsCount + ' رول ممبرين عازل (4 مم)', 'كمية رولات العزل المائي');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الأرضية الصافية', value: floor.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الرفع على الحوائط (الرقبة)', value: upstandArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'إجمالي المساحة المطلوبة مع الركوب', value: totalWithOverlap.toFixed(1) + ' م²', color: '#f59e0b' },
                { label: 'عدد براميل دهان الأساس (Primer)', value: primerBuckets + ' برميل (18 لتر)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل أرضية بمساحة <strong>\${floor} م²</strong> مع رفع رقبة العزل بارتفاع <strong>\${(upstand*100)} سم</strong>، تحتاج إلى <strong>\${rollsCount} رول ممبرين</strong> و <strong>\${primerBuckets} برميل برايمر</strong> تأسيسي.</p>
            `);
        ",
        'points' => [
            'رفع العزل على الجدران بارتفاع 25-30 سم يمنع تسرب المياه للأدوار السفلية عند حدوث أي تجمع للمياه.',
            'يلزم عمل رقبة زجاجة (شطفة خرسانية مثلثة) في زاوية التقاء الأرضية بالحائط لضمان عدم انكسار لفائف العزل.'
        ],
        'assumptions' => 'يفترض استخدام رولات ممبرين بوليستر 4 مم مسلحة ذات كفاءة عالية.',
        'faqs' => [
            ['q' => 'هل يكفي دهان البيتومين السائل بدون لفائف ممبرين؟', 'a' => 'الدهان السائل لا يكفي وحده في الأسطح المعرضة للشمس والحركة؛ يجب استخدام لفائف ممبرين ملحومة بالنار لتتحمل التمدد والانكماش بدون تشقق.']
        ],
        'related' => ['insulation-calculator', 'thermal-insulation-calculator', 'roof-slope-calculator']
    ],

    'roof-slope-calculator' => [
        'title' => 'حاسبة ميلان السطح وتصريف المياه (خرسانة الميول)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'roofRunLength', 'label' => 'المسافة من أعلى نقطة في السطح إلى المزراب / المزراب (متر)', 'type' => 'number', 'default' => '12', 'min' => '1', 'step' => '0.5'],
            ['id' => 'slopePercentage', 'label' => 'نسبة الميل الموصى بها (%) - القياسي 1% إلى 1.5%', 'type' => 'number', 'default' => '1.0', 'min' => '0.5', 'max' => '5.0', 'step' => '0.25'],
            ['id' => 'minThicknessAtDrain', 'label' => 'أقل سماكة لخرسانة الميول عند المزراب (سم) - عادة 3 إلى 5 سم', 'type' => 'number', 'default' => '4.0', 'min' => '2', 'max' => '10', 'step' => '0.5'],
            ['id' => 'roofTotalArea', 'label' => 'إجمالي مساحة السطح المراد صبه (متر مربع)', 'type' => 'number', 'default' => '150', 'min' => '10', 'step' => '5'],
        ],
        'calcJs' => "
            const run = Math.max(1, parseFloat(document.getElementById('roofRunLength').value) || 12);
            const slope = Math.max(0.5, parseFloat(document.getElementById('slopePercentage').value) || 1.0) / 100;
            const minThick = Math.max(2, parseFloat(document.getElementById('minThicknessAtDrain').value) || 4.0);
            const area = Math.max(10, parseFloat(document.getElementById('roofTotalArea').value) || 150);

            const dropHeightCm = (run * slope) * 100;
            const maxThicknessCm = minThick + dropHeightCm;
            const avgThicknessCm = (minThick + maxThicknessCm) / 2;
            const foamConcreteVolumeM3 = area * (avgThicknessCm / 100);

            setPrimaryResult(dropHeightCm.toFixed(1) + ' سم فارق منسوب الميول', 'فارق الارتفاع المطلوب لتصريف المياه');
            showResultArea();

            setDetailStats([
                { label: 'أعلى سماكة للصبة (عند أعلى نقطة)', value: maxThicknessCm.toFixed(1) + ' سم', color: '#ef4444' },
                { label: 'أقل سماكة (عند مخرج المزراب)', value: minThick.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'متوسط سماكة صبة الميول', value: avgThicknessCm.toFixed(1) + ' سم', color: '#f59e0b' },
                { label: 'حجم الخرسانة الرغوية المطلوبة', value: foamConcreteVolumeM3.toFixed(2) + ' م³', color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>على مسافة <strong>\${run} متر</strong> بنسبة ميل <strong>\${(slope*100).toFixed(1)}%</strong>، يجب أن يرتفع السطح بمقدار <strong>\${dropHeightCm.toFixed(1)} سم</strong> فوق المزراب. يتطلب صب السطح حوالي <strong>\${foamConcreteVolumeM3.toFixed(2)} م³</strong> من الخرسانة الرغوية الخفيفة.</p>
            `);
        ",
        'points' => [
            'فارق المنسوب = المسافة الأفقية × نسبة الميل.',
            'نسبة الميل القياسية لتصريف مياه الأمطار المعمارية تتراوح بين 1% (1 سم لكل متر) إلى 1.5% (1.5 سم لكل متر).',
            'يُفضل استخدام الخرسانة الرغوية (Foam Concrete) لعمل الميول لأنها خفيفة الوزن وتوفر عزلاً حرارياً إضافياً دون تحميل السقف أوزاناً زائدة.'
        ],
        'assumptions' => 'يفترض توزيع مدروس لمزاريب تصريف الأمطار (مزراب لكل 80-100 م² كحد أقصى).',
        'faqs' => [
            ['q' => 'ماذا يحدث إذا كانت نسبة ميل السطح أقل من 1%؟', 'a' => 'ستتكون برك مياه راكدة على السطح بعد الأمطار (Water Ponding)، وهو ما يؤدي بمرور الوقت إلى تحلل وتلف طبقات العزل وتسرب المياه للمنزل.']
        ],
        'related' => ['waterproofing-calculator', 'insulation-calculator', 'cement-calculator']
    ],

    'stair-calculator' => [
        'title' => 'حاسبة الدرج المعمارية (معادلة بلونديل)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalFloorHeight', 'label' => 'ارتفاع الطابق الكلي من التشطيب للتشطيب (سم)', 'type' => 'number', 'default' => '300', 'min' => '100', 'max' => '600', 'step' => '5'],
            ['id' => 'idealRiserHeight', 'label' => 'ارتفاع القائمة المستهدف (Riser) - المريح 15 إلى 17 سم', 'type' => 'number', 'default' => '16.5', 'min' => '14', 'max' => '21', 'step' => '0.5'],
            ['id' => 'stairWidthCm', 'label' => 'عرض شاحط الدرج (سم) - القياسي 110 إلى 130 سم', 'type' => 'number', 'default' => '120', 'min' => '80', 'max' => '250', 'step' => '5'],
        ],
        'calcJs' => "
            const totalH = Math.max(100, parseFloat(document.getElementById('totalFloorHeight').value) || 300);
            const targetR = Math.max(14, parseFloat(document.getElementById('idealRiserHeight').value) || 16.5);
            const stairW = Math.max(80, parseFloat(document.getElementById('stairWidthCm').value) || 120);

            const stepsCount = Math.round(totalH / targetR);
            const exactRiser = totalH / stepsCount;
            // معادلة بلونديل: 2R + G = 63 سم -> G = 63 - 2R
            const exactTread = 63 - (2 * exactRiser);
            // طول الشاحط الأفقي = (عدد الدرجات - 1) * النائمة
            const horizontalRun = (stepsCount - 1) * exactTread;

            let comfortStatus = 'مريح جداً ومطابق للمواصفات الدولية ✅';
            let color = '#10b981';
            if (exactRiser > 18) { comfortStatus = 'شديد الانحدار ومرهق في الصعود ⚠️'; color = '#ef4444'; }

            setPrimaryResult(stepsCount + ' درجة (قائمة: ' + exactRiser.toFixed(1) + ' سم | نائمة: ' + exactTread.toFixed(1) + ' سم)', 'المواصفات المعمارية للدرج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الدرجات الكلي', value: stepsCount + ' درجات', color: '#3b82f6' },
                { label: 'ارتفاع الدرجة (القائمة Riser)', value: exactRiser.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'عرض موطئ القدم (النائمة Tread)', value: exactTread.toFixed(1) + ' سم', color: '#f59e0b' },
                { label: 'المسافة الأفقية المطلوبة للدرج', value: (horizontalRun / 100).toFixed(2) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتجاوز ارتفاع <strong>\${totalH} سم</strong>، يقسم الدرج إلى <strong>\${stepsCount} درجة</strong> بارتفاع قائمة <strong>\${exactRiser.toFixed(1)} سم</strong> وعرض نائمة <strong>\${exactTread.toFixed(1)} سم</strong>. المعادلة تحقق شرط بلونديل (2R + G = \${(2*exactRiser + exactTread).toFixed(1)} سم) - \${comfortStatus}.</p>
            `);
        ",
        'points' => [
            'معادلة بلونديل المعمارية الشهيرة: 2 × القائمة + النائمة = من 62 إلى 64 سم.',
            'ارتفاع القائمة المريح للإنسان يتراوح بين 15 سم إلى 17 سم كحد أقصى.',
            'عرض النائمة (موطئ القدم) يجب ألا يقل عن 28 إلى 30 سم لسلامة النزول.'
        ],
        'assumptions' => 'يفترض ارتفاع الطابق مقاساً من منسوب تشطيب البلاط السفلي إلى منسوب تشطيب البلاط العلوي.',
        'faqs' => [
            ['q' => 'كم يجب أن يكون الحد الأدنى لارتفاع الرأس (Headroom) فوق الدرج؟', 'a' => 'يجب ألا يقل الارتفاع الحر العمودي بين أي درجة وسقف الطابق العلوي عن 2.10 متر إلى 2.20 متر لضمان عدم اصطدام رأس الشخص أثناء النزول.']
        ],
        'related' => ['stair-steps-calculator', 'house-building-cost-calculator', 'floor-tiles-calculator']
    ],

    'stair-steps-calculator' => [
        'title' => 'حاسبة عدد درجات السلم',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'floorHeightSteps', 'label' => 'ارتفاع الدور من الأرضية للأرضية (سم)', 'type' => 'number', 'default' => '320', 'min' => '50', 'max' => '600', 'step' => '5'],
            ['id' => 'riserTarget', 'label' => 'ارتفاع الدرجة المفضل (سم)', 'type' => 'number', 'default' => '16.0', 'min' => '13', 'max' => '22', 'step' => '0.5'],
            ['id' => 'landingsCount', 'label' => 'عدد البسطات / الصدفات (الاستراحات)', 'type' => 'number', 'default' => '1', 'min' => '0', 'max' => '3', 'step' => '1'],
        ],
        'calcJs' => "
            const h = Math.max(50, parseFloat(document.getElementById('floorHeightSteps').value) || 320);
            const r = Math.max(13, parseFloat(document.getElementById('riserTarget').value) || 16.0);
            const landings = parseInt(document.getElementById('landingsCount').value) || 1;

            const totalSteps = Math.round(h / r);
            const exactRiserHeight = h / totalSteps;
            const stepsPerFlight = Math.ceil(totalSteps / (landings + 1));

            setPrimaryResult(totalSteps + ' درجة', 'إجمالي عدد درجات السلم المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'الارتفاع الفعلي لكل درجة', value: exactRiserHeight.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'عدد القلبات / الشواحط', value: (landings + 1) + ' قلبات', color: '#3b82f6' },
                { label: 'متوسط الدرجات في كل قلبة', value: stepsPerFlight + ' درجات', color: '#f59e0b' },
                { label: 'الارتفاع الكلي للطابق', value: (h / 100).toFixed(2) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية ارتفاع <strong>\${h} سم</strong>، تحتاج إلى <strong>\${totalSteps} درجة</strong> بارتفاع دقيق <strong>\${exactRiserHeight.toFixed(1)} سم</strong> لكل درجة، موزعة على <strong>\${landings + 1} قلبات</strong> مع <strong>\${landings} استراحة</strong>.</p>
            `);
        ",
        'points' => [
            'عدد الدرجات = الارتفاع الكلي للطابق مقسوماً على ارتفاع القائمة.',
            'يجب أن تكون جميع الدرجات في السلم متطابقة في الارتفاع تماماً دون أي مليمتر فارق لمنع تعثر المستخدمين.'
        ],
        'assumptions' => 'الكود الهندسي يمنع وجود أكثر من 14 إلى 16 درجة متتالية دون صدفة / استراحة للراحة.',
        'faqs' => [
            ['q' => 'لماذا يُمنع اختلاف ارتفاع الدرجات في نفس السلم؟', 'a' => 'لأن العقل البشري يبرمج حركة القدمين لا شعورياً بعد أول درجتين على نفس الإيقاع، وأي تغيير بمقدار 1 سم يؤدي إلى التعثر والسقوط فوراً.']
        ],
        'related' => ['stair-calculator', 'floor-tiles-calculator', 'house-building-cost-calculator']
    ],

    'carpet-area-calculator' => [
        'title' => 'حاسبة مساحة السجاد والموكيت',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'carpetRoomLen', 'label' => 'طول الغرفة (متر)', 'type' => 'number', 'default' => '5.5', 'min' => '1', 'step' => '0.1'],
            ['id' => 'carpetRoomWid', 'label' => 'عرض الغرفة (متر)', 'type' => 'number', 'default' => '4.0', 'min' => '1', 'step' => '0.1'],
            ['id' => 'carpetType', 'label' => 'نوع السجاد المطلوب', 'type' => 'select', 'options' => [
                'wall_to_wall' => 'موكيت يغطي الغرفة بالكامل من الجدار للجدار (Wall-to-Wall)',
                'area_rug' => 'سجادة مركزية في وسط الغرفة (تترك 40 سم من الأطراف)'
            ], 'default' => 'wall_to_wall'],
            ['id' => 'rollStandardWidth', 'label' => 'عرض رول الموكيت القياسي في السوق (المعتاد 4.0 متر)', 'type' => 'number', 'default' => '4.0', 'min' => '2', 'max' => '5', 'step' => '0.5'],
        ],
        'calcJs' => "
            const len = Math.max(1, parseFloat(document.getElementById('carpetRoomLen').value) || 5.5);
            const wid = Math.max(1, parseFloat(document.getElementById('carpetRoomWid').value) || 4.0);
            const type = document.getElementById('carpetType').value;
            const rWidth = Math.max(2, parseFloat(document.getElementById('rollStandardWidth').value) || 4.0);

            let netArea = len * wid;
            let finalArea = netArea;
            let linearMeters = 0;

            if (type === 'area_rug') {
                const rugL = Math.max(1, len - 0.8);
                const rugW = Math.max(1, wid - 0.8);
                finalArea = rugL * rugW;
            } else {
                // موكيت كامل: يحسب على رول بعرض 4 أمتار
                linearMeters = len;
                finalArea = linearMeters * rWidth;
            }

            setPrimaryResult(finalArea.toFixed(2) + ' متر مربع', 'مساحة الموكيت / السجاد المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة أرضية الغرفة الصافية', value: netArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'نوع الفرش المعتمد', value: type === 'area_rug' ? 'سجادة وسطية (Rug)' : 'موكيت جدار لجدار', color: '#10b981' },
                { label: 'الأمتار الطولية من رول عرض ' + rWidth + 'م', value: (finalArea / rWidth).toFixed(2) + ' م طولي', color: '#f59e0b' },
                { label: 'مساحة اللباد الإسفنجي (Underlay)', value: netArea.toFixed(1) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية الغرفة \${type === 'area_rug' ? 'بسجادة وسطية أنيقة' : 'بموكيت كامل'}، تحتاج إلى <strong>\${finalArea.toFixed(2)} م²</strong>. يُنصح بإضافة طبقة لباد عازل تحت الموكيت لراحة القدمين وإطالة عمر السجاد.</p>
            `);
        ",
        'points' => [
            'رولات الموكيت تباع في السوق العربي بعرض قياسي قدره 4 أمتار.',
            'السجادة الوسطية (Area Rug) تترك مسافة 40 إلى 50 سم بين حافة السجادة والجدار لإظهار جمالية أرضية الباركيه أو البورسلان.'
        ],
        'assumptions' => 'يفترض غرفة مستطيلة منتظمة بدون أعمدة وزوايا حادة.',
        'faqs' => [
            ['q' => 'ما أهمية اللباد (Underlay) تحت الموكيت؟', 'a' => 'اللباد يحمي خيوط الموكيت من التلف نتيجة الاحتكاك بالأرضية الصلبة، ويعطي شعوراً وثيراً وناعماً عند المشي، ويعمل كعازل للصوت والحرارة.']
        ],
        'related' => ['floor-tiles-calculator', 'room-furniture-area-calculator', 'skirting-board-calculator']
    ],

    'curtain-calculator' => [
        'title' => 'حاسبة كمية الستائر والقماش',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'windowWidth', 'label' => 'عرض شباك / نافذة الغرفة (متر)', 'type' => 'number', 'default' => '2.5', 'min' => '0.5', 'step' => '0.1'],
            ['id' => 'windowHeight', 'label' => 'ارتفاع الستارة المطلوب حتى الأرض (متر)', 'type' => 'number', 'default' => '2.8', 'min' => '1.0', 'max' => '5.0', 'step' => '0.1'],
            ['id' => 'fullnessRatio', 'label' => 'كثافة الكشكشة والكسرات (Fullness)', 'type' => 'select', 'options' => [
                '1.5' => 'كسرات خفيفة بسيطة (1.5x عرض الشباك)',
                '2.0' => 'كسرات قياسية متوسطة (2.0x عرض الشباك - المعيار الأكثر جمالاً)',
                '2.5' => 'كسرات فندقية كثيفة وفخمة (2.5x عرض الشباك)',
                '3.0' => 'ستائر ملكية ممتلئة جداً (3.0x)'
            ], 'default' => '2.0'],
            ['id' => 'curtainLayersCount', 'label' => 'عدد طبقات الستارة', 'type' => 'select', 'options' => [
                '1' => 'طبقة واحدة (قماش خفيف أو بلاك آوت فقط)',
                '2' => 'طبقتين (طبقة شيفون خفيف + طبقة قماش ثقيل/بلاك آوت)'
            ], 'default' => '2'],
        ],
        'calcJs' => "
            const winW = Math.max(0.5, parseFloat(document.getElementById('windowWidth').value) || 2.5);
            const winH = Math.max(1.0, parseFloat(document.getElementById('windowHeight').value) || 2.8);
            const fullness = parseFloat(document.getElementById('fullnessRatio').value) || 2.0;
            const layers = parseInt(document.getElementById('curtainLayersCount').value) || 2;

            // إضافة 20 سم من كل جانب للمجرى
            const rodWidth = winW + 0.40;
            const fabricWidthPerLayer = rodWidth * fullness;
            const fabricHeightWithHem = winH + 0.30; // 30 سم للثنيات العلوية والسفلية
            const totalMeters = fabricWidthPerLayer * layers;

            setPrimaryResult(fabricWidthPerLayer.toFixed(1) + ' متر عرض قماش لكل طبقة', 'عرض قماش الستارة المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'طول مسار الستارة (الماسورة / المجرى)', value: rodWidth.toFixed(2) + ' متر', color: '#3b82f6' },
                { label: 'معامل الكشكشة المعتمد', value: fullness + 'x', color: '#10b981' },
                { label: 'ارتفاع القماش المطلوب مع الثنيات', value: fabricHeightWithHem.toFixed(2) + ' متر', color: '#f59e0b' },
                { label: 'إجمالي عرض القماش للطبقتين', value: totalMeters.toFixed(1) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لنافذة بعرض <strong>\${winW} م</strong> وارتفاع <strong>\${winH} م</strong>، يوصى بمجرى ستارة بعرض <strong>\${rodWidth.toFixed(2)} م</strong>. تحتاج كل طبقة إلى <strong>\${fabricWidthPerLayer.toFixed(1)} متر قماش</strong> لتحقيق كشكشة فخمة بمضاعف \${fullness}x.</p>
            `);
        ",
        'points' => [
            'ماسورة أو مجرى الستارة يجب أن يزيد بمقدار 15 إلى 20 سم من كل طرف خارج إطار الشباك.',
            'معامل الكشكشة 2x يعني أن عرض القماش المشترى يساوي ضعف عرض المسار ليظهر بثنيات متموجة أنيقة.',
            'يجب زيادة 25 إلى 30 سم لارتفاع القماش لاحتساب ثنية الكفة العلوية وشريط الحلقات والكفة السفلية.'
        ],
        'assumptions' => 'يفترض ستارة ممتدة من قرب السقف إلى قبل الأرضية بـ 1 سم كمعيار ديكوري حديث.',
        'faqs' => [
            ['q' => 'لماذا يُفضل تعليق الستارة من أعلى نقطة قريبة من السقف؟', 'a' => 'تعليق الستارة قرب السقف وجعلها تلامس الأرضية يعطي إيحاءً بصرياً رائعاً بأن سقف الغرفة أعلى وبأن النافذة أوسع وأفخم بكثير.']
        ],
        'related' => ['wallpaper-calculator', 'room-furniture-area-calculator', 'carpet-area-calculator']
    ],
];

echo "Generating Group B: Home & Construction Tools (30 tools)...\n";
foreach ($toolsB as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group B!\n";
