<?php
/**
 * مولد أدوات المجموعة F: أدوات الحياة اليومية والمال الشخصي (17 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsF = [
    'is-salary-enough-calculator' => [
        'title' => 'حاسبة هل راتبي يكفيني؟ ومؤشر الأمان المالي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'netMonthlySalaryInput', 'label' => 'صافي الدخل الشهري المستلم', 'type' => 'number', 'default' => '7500', 'min' => '500', 'step' => '100'],
            ['id' => 'housingRentCost', 'label' => 'تكلفة السكن (الإيجار أو قسط التمويل العقاري شهرياً)', 'type' => 'number', 'default' => '2200', 'min' => '0', 'step' => '100'],
            ['id' => 'groceriesFoodCost', 'label' => 'مصاريف الطعام والتموين المنزلي شهرياً', 'type' => 'number', 'default' => '1800', 'min' => '0', 'step' => '50'],
            ['id' => 'billsUtilitiesCost', 'label' => 'الفواتير الأساسية (كهرباء، ماء، إنترنت، جوال)', 'type' => 'number', 'default' => '600', 'min' => '0', 'step' => '50'],
            ['id' => 'transportCostLife', 'label' => 'المواصلات والبنزين أو قسط السيارة', 'type' => 'number', 'default' => '800', 'min' => '0', 'step' => '50'],
            ['id' => 'debtInstallments', 'label' => 'أقساط قروض أو ديون وبطاقات ائتمانية شهرية', 'type' => 'number', 'default' => '500', 'min' => '0', 'step' => '50'],
            ['id' => 'entertainmentPersonal', 'label' => 'المصاريف الترفيهية والمطاعم والشراء الشخصي', 'type' => 'number', 'default' => '600', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const salary = Math.max(500, parseFloat(document.getElementById('netMonthlySalaryInput').value) || 7500);
            const rent = Math.max(0, parseFloat(document.getElementById('housingRentCost').value) || 2200);
            const food = Math.max(0, parseFloat(document.getElementById('groceriesFoodCost').value) || 1800);
            const bills = Math.max(0, parseFloat(document.getElementById('billsUtilitiesCost').value) || 600);
            const transport = Math.max(0, parseFloat(document.getElementById('transportCostLife').value) || 800);
            const debt = Math.max(0, parseFloat(document.getElementById('debtInstallments').value) || 500);
            const fun = Math.max(0, parseFloat(document.getElementById('entertainmentPersonal').value) || 600);
            const curr = getSelectedCurrency();

            const totalExpenses = rent + food + bills + transport + debt + fun;
            const netBalance = salary - totalExpenses;
            const savingsRate = (netBalance / salary) * 100;
            const rentRatio = (rent / salary) * 100;

            let status = 'وضع مالي ممتاز مع قدرة على الادخار والاستثمار 🌟';
            let color = '#10b981';
            if (netBalance < 0) {
                status = 'عجز مالي شهري يتطلب إعادة هيكلة المصاريف فوراً ❌';
                color = '#ef4444';
            } else if (savingsRate < 10) {
                status = 'الراتب يكفي بالكاد على الحافة دون هامش أمان مالي ⚠️';
                color = '#f59e0b';
            }

            setPrimaryResult(netBalance >= 0 ? 'فائض ادخار: ' + formatMoney(netBalance, curr) : 'عجز مالي: -' + formatMoney(Math.abs(netBalance), curr), 'المحصلة المالية الصافية شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'نسبة الادخار الصافي من الراتب', value: savingsRate.toFixed(1) + '%', color: color },
                { label: 'إجمالي المصاريف والالتزامات', value: formatMoney(totalExpenses, curr), color: '#ef4444' },
                { label: 'نسبة السكن من الراتب (الموصى به < 30%)', value: rentRatio.toFixed(1) + '%', color: rentRatio <= 30 ? '#10b981' : '#f59e0b' },
                { label: 'تقييم الأمان المالي', value: status, color: color }
            ]);

            setResultContent(`
                <p>من إجمالي راتب <strong>\${formatMoney(salary, curr)}</strong>، تنفق شهرياً <strong>\${formatMoney(totalExpenses, curr)}</strong>، ويتبقى لك <strong>\${formatMoney(netBalance, curr)}</strong> بنسبة ادخار <strong>\${savingsRate.toFixed(1)}%</strong> - \${status}.</p>
            `);
        ",
        'points' => [
            'المؤشر المالي السليم يتطلب ألا يتجاوز السكن 30% من صافي الراتب، وألا تتجاوز الديون 33% كحد أقصى.',
            'تحقيق فائض ادخار شهري لا يقل عن 15% إلى 20% هو الضمانة الحقيقية لبناء الثروة وتأمين المستقبل.'
        ],
        'assumptions' => 'يفترض عدم وجود مصاريف طارئة غير مدونة في المدخلات.',
        'faqs' => [
            ['q' => 'ما هي الخطوة الأولى إذا كان الراتب لا يكفي وهناك عجز؟', 'a' => 'ابدأ بإلغاء الاشتراكات غير المستخدمة فوراً، وتقليص مصاريف المطاعم والمقاهي الخارجية بنسبة 50%، واستبدال الماركات التجارية ببدائل محلية ذات سعر اقتصادي.']
        ],
        'related' => ['salary-division-calculator', 'family-monthly-budget-calculator', 'savings-goal-calculator']
    ],

    'independence-cost-calculator' => [
        'title' => 'حاسبة تكلفة الاستقلال عن الأهل وبدء السكن المنفرد',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlyRentExpected', 'label' => 'الإيجار الشهري المتوقع للشقة أو الاستديو', 'type' => 'number', 'default' => '1600', 'min' => '300', 'step' => '50'],
            ['id' => 'depositMonths', 'label' => 'تأمين وعربون الإيجار (المعتاد شهر إلى شهرين)', 'type' => 'number', 'default' => '1600', 'min' => '0', 'step' => '100'],
            ['id' => 'basicSetupFurnish', 'label' => 'الأثاث والأجهزة الأساسية للبداية (سرير، ثلاجة، غسالة، مكتب)', 'type' => 'number', 'default' => '5000', 'min' => '1000', 'step' => '500'],
            ['id' => 'monthlyBillsAndFood', 'label' => 'مصاريف المعيشة والفواتير التقديرية شهرياً', 'type' => 'number', 'default' => '1500', 'min' => '500', 'step' => '100'],
            ['id' => 'emergencyFundMonths', 'label' => 'صندوق طوارئ موصى به قبل الاستقلال (عدد الأشهر)', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '6', 'step' => '1'],
        ],
        'calcJs' => "
            const rent = Math.max(300, parseFloat(document.getElementById('monthlyRentExpected').value) || 1600);
            const deposit = Math.max(0, parseFloat(document.getElementById('depositMonths').value) || 1600);
            const setup = Math.max(1000, parseFloat(document.getElementById('basicSetupFurnish').value) || 5000);
            const living = Math.max(500, parseFloat(document.getElementById('monthlyBillsAndFood').value) || 1500);
            const emergencyMonths = Math.max(1, parseInt(document.getElementById('emergencyFundMonths').value) || 3);
            const curr = getSelectedCurrency();

            const recurringMonthlyCost = rent + living;
            const emergencyFundNeeded = recurringMonthlyCost * emergencyMonths;
            const upfrontStartCapital = deposit + setup + emergencyFundNeeded + rent; // تكاليف الانطلاق

            setPrimaryResult(formatMoney(upfrontStartCapital, curr), 'المبلغ المالي الموصى بادخاره قبل اتخاذ خطوة الاستقلال');
            showResultArea();

            setDetailStats([
                { label: 'المصاريف الشهرية المستمرة للاستقلال', value: formatMoney(recurringMonthlyCost, curr) + ' / شهرياً', color: '#ef4444' },
                { label: 'قيمة صندوق الطوارئ (' + emergencyMonths + ' أشهر)', value: formatMoney(emergencyFundNeeded, curr), color: '#10b981' },
                { label: 'أثاث وتجهيزات بداية السكن', value: formatMoney(setup, curr), color: '#3b82f6' },
                { label: 'الراتب الصافي المطلوب للاستقرار', value: formatMoney(recurringMonthlyCost * 1.4, curr) + ' / شهرياً', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لتستقل بسكن منفرد بأمان مالي تام، تحتاج لجمع <strong>\${formatMoney(upfrontStartCapital, curr)}</strong> كرأس مال مبدئي (يشمل التأمين والأثاث وصندوق طوارئ \${emergencyMonths} أشهر)، وأن يكون دخلك الشهري لا يقل عن <strong>\${formatMoney(recurringMonthlyCost * 1.4, curr)}</strong> لتغطية مصاريفك الشهرية البالغة <strong>\${formatMoney(recurringMonthlyCost, curr)}</strong> مع هامش أمان.</p>
            `);
        ",
        'points' => [
            'الاستقلال السكني الناجح يتطلب رأس مال انطلاق أولي (Upfront Costs) + دخلاً شهرياً مستقراً يغطي المصاريف المستمرة.',
            'تأسيس صندوق طوارئ يغطي 3 أشهر على الأقل من الإيجار والمعيشة يحميك من الإفلاس أو العودة الإجبارية عند حدوث أي ظرف طارئ في العمل.'
        ],
        'assumptions' => 'يفترض شقة غير مفروشة تتطلب تجهيزات أساسية عملية.',
        'faqs' => [
            ['q' => 'هل الشقة المفروشة أو السكن المشترك خيار أفضل في البداية؟', 'a' => 'نعم؛ السكن المشترك مع زملاء أو استئجار استوديو مفروش بالكامل يقلل تكاليف التأسيس المبدئية بنسبة تتجاوز 70% ويعتبر خطوة انتقالية ذكية لتجربة الاستقلال.']
        ],
        'related' => ['is-salary-enough-calculator', 'rent-split-calculator', 'salary-division-calculator']
    ],

    'baby-first-year-cost-calculator' => [
        'title' => 'حاسبة تكلفة الطفل في السنة الأولى',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'deliveryCostHospital', 'label' => 'تكاليف الولادة والمستشفى (بعد تغطية التأمين إن وجدت)', 'type' => 'number', 'default' => '4000', 'min' => '0', 'step' => '500'],
            ['id' => 'diapersMonthlyCost', 'label' => 'تكلفة الحفاضات والمناديل المبللة شهرياً', 'type' => 'number', 'default' => '250', 'min' => '50', 'step' => '25'],
            ['id' => 'milkFormulaMonthly', 'label' => 'تكلفة الحليب الصناعي والمكملات (0 إن كانت رضاعة طبيعية كاملة)', 'type' => 'number', 'default' => '300', 'min' => '0', 'step' => '50'],
            ['id' => 'gearCribStrollerCost', 'label' => 'مستلزمات البداية (سرير الطفل، عربة الأطفال، مقعد السيارة، جهاز مراقبة)', 'type' => 'number', 'default' => '2500', 'min' => '500', 'step' => '200'],
            ['id' => 'clothesDoctorMonthly', 'label' => 'ملابس دورية (الطفل ينمو بسرعة) وزيارات طبيب أطفال وتطعيمات شهرياً', 'type' => 'number', 'default' => '400', 'min' => '100', 'step' => '50'],
        ],
        'calcJs' => "
            const delivery = Math.max(0, parseFloat(document.getElementById('deliveryCostHospital').value) || 4000);
            const diapers = Math.max(50, parseFloat(document.getElementById('diapersMonthlyCost').value) || 250);
            const milk = Math.max(0, parseFloat(document.getElementById('milkFormulaMonthly').value) || 300);
            const gear = Math.max(500, parseFloat(document.getElementById('gearCribStrollerCost').value) || 2500);
            const monthlyOther = Math.max(100, parseFloat(document.getElementById('clothesDoctorMonthly').value) || 400);
            const curr = getSelectedCurrency();

            const recurringMonthly = diapers + milk + monthlyOther;
            const oneTimeCosts = delivery + gear;
            const totalFirstYear = oneTimeCosts + (recurringMonthly * 12);
            const avgMonthlyEquiv = totalFirstYear / 12;

            setPrimaryResult(formatMoney(totalFirstYear, curr), 'إجمالي ميزانية الطفل للسنة الأولى كاملة');
            showResultArea();

            setDetailStats([
                { label: 'المصاريف الشهرية المستمرة للطفل', value: formatMoney(recurringMonthly, curr) + ' / شهرياً', color: '#3b82f6' },
                { label: 'تكاليف التأسيس المبدئية لمرة واحدة', value: formatMoney(oneTimeCosts, curr), color: '#10b981' },
                { label: 'إجمالي تكلفة الحفاضات والحليب سنوياً', value: formatMoney((diapers + milk) * 12, curr), color: '#f59e0b' },
                { label: 'متوسط الأثر الشهري على ميزانية الأسرة', value: formatMoney(avgMonthlyEquiv, curr) + ' / شهرياً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تصل تكلفة رعاية وتجهيز المولود الجديد في عامه الأول إلى حوالي <strong>\${formatMoney(totalFirstYear, curr)}</strong>، بما يتطلب زيادة ميزانية الأسرة الشهرية بمقدار <strong>\${formatMoney(recurringMonthly, curr)} شهرياً</strong> بخلاف تكاليف الولادة ومعدات السرير والعربة.</p>
            `);
        ",
        'points' => [
            'الرضاعة الطبيعية توفر على ميزانية الأسرة ما بين 3000 إلى 5000 ريال/دولار سنوياً من تكلفة الحليب الصناعي والزجاجات المعقمة.',
            'الأطفال الرضع يغيرون مقاس ملابسهم كل شهرين إلى 3 أشهر في السنة الأولى، لذلك تجنب المبالغة في شراء ملابس المواليد بكميات كبيرة.'
        ],
        'assumptions' => 'يفترض عدم وجود متطلبات علاجية خاصة أو عمليات جراحية غير متوقعة.',
        'faqs' => [
            ['q' => 'كيف نوفر في مستلزمات المولود الأول؟', 'a' => 'شراء عربة الأطفال والسرير من ماركات موثوقة مستعملة بحالة ممتازة يوفر أكثر من 50% من التكلفة، حيث يستخدمها الأطفال لفترات زمنية قصيرة جداً.']
        ],
        'related' => ['family-monthly-budget-calculator', 'is-salary-enough-calculator', 'savings-goal-calculator']
    ],

    'family-monthly-budget-calculator' => [
        'title' => 'حاسبة المصاريف الشهرية للأسرة وميزانية البيت',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'familyTotalIncome', 'label' => 'إجمالي دخل الأسرة الشهري (الزوج والزوجة وأي دخل إضافي)', 'type' => 'number', 'default' => '12000', 'min' => '1000', 'step' => '500'],
            ['id' => 'housingCostFam', 'label' => 'السكن (إيجار أو قسط تمويل عقاري)', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '100'],
            ['id' => 'foodGroceryFam', 'label' => 'الطعام والتموين المنزلي ومستلزمات النظافة', 'type' => 'number', 'default' => '2500', 'min' => '0', 'step' => '100'],
            ['id' => 'educationFam', 'label' => 'التعليم ومصاريف المدارس والمواصلات المدرسية', 'type' => 'number', 'default' => '1500', 'min' => '0', 'step' => '100'],
            ['id' => 'utilitiesFam', 'label' => 'فواتير الكهرباء والمياه والإنترنت والجوالات', 'type' => 'number', 'default' => '900', 'min' => '0', 'step' => '50'],
            ['id' => 'transportCarFam', 'label' => 'وقود السيارات وصيانتها وقسط السيارة', 'type' => 'number', 'default' => '1400', 'min' => '0', 'step' => '50'],
            ['id' => 'healthcareFam', 'label' => 'الرعاية الصحية والأدوية والتأمين', 'type' => 'number', 'default' => '500', 'min' => '0', 'step' => '50'],
            ['id' => 'entertainmentFam', 'label' => 'الترفيه والزيارات والمطاعم والتسوق', 'type' => 'number', 'default' => '1000', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const income = Math.max(1000, parseFloat(document.getElementById('familyTotalIncome').value) || 12000);
            const housing = Math.max(0, parseFloat(document.getElementById('housingCostFam').value) || 3000);
            const food = Math.max(0, parseFloat(document.getElementById('foodGroceryFam').value) || 2500);
            const edu = Math.max(0, parseFloat(document.getElementById('educationFam').value) || 1500);
            const util = Math.max(0, parseFloat(document.getElementById('utilitiesFam').value) || 900);
            const trans = Math.max(0, parseFloat(document.getElementById('transportCarFam').value) || 1400);
            const health = Math.max(0, parseFloat(document.getElementById('healthcareFam').value) || 500);
            const ent = Math.max(0, parseFloat(document.getElementById('entertainmentFam').value) || 1000);
            const curr = getSelectedCurrency();

            const totalExpenses = housing + food + edu + util + trans + health + ent;
            const savings = income - totalExpenses;
            const savingsRate = (savings / income) * 100;

            setPrimaryResult(formatMoney(totalExpenses, curr) + ' شهرياً', 'إجمالي مصاريف الأسرة الشهرية');
            showResultArea();

            setDetailStats([
                { label: 'المتبقي للادخار والاستثمار', value: formatMoney(savings, curr), color: savings >= 0 ? '#10b981' : '#ef4444' },
                { label: 'نسبة الادخار من الدخل', value: savingsRate.toFixed(1) + '%', color: savingsRate >= 15 ? '#10b981' : '#f59e0b' },
                { label: 'أكبر بند في الميزانية', value: housing >= food ? 'السكن (' + ((housing/totalExpenses)*100).toFixed(0) + '%)' : 'الطعام (' + ((food/totalExpenses)*100).toFixed(0) + '%)', color: '#3b82f6' },
                { label: 'إجمالي الدخل الشهري', value: formatMoney(income, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك مصاريف الأسرة <strong>\${((totalExpenses/income)*100).toFixed(1)}%</strong> من الدخل الشهري. يتبقى لكم <strong>\${formatMoney(savings, curr)}</strong> شهرياً كفائض مالي يوجه للادخار أو صندوق طوارئ الأسرة.</p>
            `);
        ",
        'points' => [
            'الميزانية الأسرية الناجحة تعتمد على الشفافية والتخطيط المالي المشترك بين الزوجين.',
            'تسجيل المصاريف اليومية في نهاية كل أسبوع يكشف مواضع الهدر المالي الخفي في البقالة والمطاعم.'
        ],
        'assumptions' => 'يفترض عدم وجود ديون بنكية غير مسجلة ضمن البنود.',
        'faqs' => [
            ['q' => 'كيف نلتزم بميزانية الطعام دون حرمان؟', 'a' => 'التسوق مرة واحدة أسبوعياً بقائمة مشتريات محددة مسبقاً، وتجنب الذهاب للسوبرماركت وأنت جائع، وتجهيز الوجبات منزلياً (Meal Prep) لأيام العمل.']
        ],
        'related' => ['salary-division-calculator', 'is-salary-enough-calculator', 'baby-first-year-cost-calculator']
    ],

    'salary-division-calculator' => [
        'title' => 'حاسبة تقسيم الراتب (قاعدة 50/30/20 المالية العالمية)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlyNetSalaryInput', 'label' => 'صافي الراتب الشهري', 'type' => 'number', 'default' => '8000', 'min' => '500', 'step' => '100'],
            ['id' => 'customSplitRatio', 'label' => 'نمط التقسيم المالي المفضل', 'type' => 'select', 'options' => [
                'standard_50_30_20' => 'قاعدة 50/30/20 القياسية (50% احتياجات | 30% رغبات | 20% ادخار)',
                'conservative_50_20_30' => 'نمط الادخار المكثف (50% احتياجات | 20% رغبات | 30% استثمار وادخار)',
                'strict_70_20_10' => 'نمط الالتزامات المرتفعة (70% معيشة وإيجار | 20% ادخار | 10% ترفيه)'
            ], 'default' => 'standard_50_30_20'],
        ],
        'calcJs' => "
            const salary = Math.max(500, parseFloat(document.getElementById('monthlyNetSalaryInput').value) || 8000);
            const rule = document.getElementById('customSplitRatio').value;
            const curr = getSelectedCurrency();

            let needsPct = 0.50;
            let wantsPct = 0.30;
            let savingsPct = 0.20;

            if (rule === 'conservative_50_20_30') { needsPct = 0.50; wantsPct = 0.20; savingsPct = 0.30; }
            if (rule === 'strict_70_20_10') { needsPct = 0.70; wantsPct = 0.10; savingsPct = 0.20; }

            const needsAmt = salary * needsPct;
            const wantsAmt = salary * wantsPct;
            const savingsAmt = salary * savingsPct;

            setPrimaryResult(formatMoney(savingsAmt, curr) + ' ادخار شهرياً (' + (savingsPct*100) + '%)', 'المبلغ الموجه للادخار والاستثمار');
            showResultArea();

            setDetailStats([
                { label: 'الاحتياجات الأساسية (' + (needsPct*100) + '%) - سكن وطعام وفواتير', value: formatMoney(needsAmt, curr), color: '#3b82f6' },
                { label: 'الرغبات ونمط الحياة (' + (wantsPct*100) + '%) - ترفيه وسفر وتسوق', value: formatMoney(wantsAmt, curr), color: '#f59e0b' },
                { label: 'الادخار وبناء الثروة (' + (savingsPct*100) + '%) - أسهم وذهب وطوارئ', value: formatMoney(savingsAmt, curr), color: '#10b981' },
                { label: 'الادخار المتراكم في سنة واحدة', value: formatMoney(savingsAmt * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>وفق قاعدة التقسيم المالي، يوزع راتبك البالغ <strong>\${formatMoney(salary, curr)}</strong> على النحو التالي:<br>
                - <strong>\${formatMoney(needsAmt, curr)}</strong> للمصاريف الحتمية التي لا يمكن العيش بدونها.<br>
                - <strong>\${formatMoney(wantsAmt, curr)}</strong> للأنشطة الترفيهية والمطاعم والهوايات.<br>
                - <strong>\${formatMoney(savingsAmt, curr)}</strong> تُحوّل فوراً إلى حساب ادخاري استثماري في يوم نزول الراتب.</p>
            `);
        ",
        'points' => [
            'الاحتياجات (Needs 50%): الإيجار، فواتير الخدمات، البقالة الأساسية، أقساط الديون الإلزامية.',
            'الرغبات (Wants 30%): ارتياد المقاهي، وجبات المطاعم، التسوق الترفيهي، اشتراكات البث والنوادي.',
            'الادخار (Savings 20%): صندوق الطوارئ، الاستثمار في صناديق المؤشرات أو الذهب، وسداد أصل الديون.'
        ],
        'assumptions' => 'القاعدة المالية تفترض التزاماً بتحويل الادخار أولاً (Pay Yourself First) وليس ادخار ما يتبقى في نهاية الشهر.',
        'faqs' => [
            ['q' => 'ماذا أفعل إذا كانت احتياجاتي الأساسية تتجاوز 50% من راتبي؟', 'a' => 'هذا شائع في بداية المسار المهني أو في المدن الكبرى؛ استخدم النمط البديل (70/20/10) مع التركيز على زيادة دخلك ومهاراتك لتقليل نسبة السكن من الراتب تدريجياً.']
        ],
        'related' => ['is-salary-enough-calculator', 'savings-goal-calculator', 'family-monthly-budget-calculator']
    ],

    'savings-goal-calculator' => [
        'title' => 'حاسبة الادخار للوصول إلى هدف مالي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetGoalAmount', 'label' => 'المبلغ المستهدف الذي ترغب في جمعه', 'type' => 'number', 'default' => '30000', 'min' => '500', 'step' => '1000'],
            ['id' => 'monthsToReachGoal', 'label' => 'المدة الزمنية المحددة للوصول للهدف (بالأشهر)', 'type' => 'number', 'default' => '12', 'min' => '1', 'max' => '120', 'step' => '1'],
            ['id' => 'initialSavingsAvailable', 'label' => 'المبلغ المتوفر لديك بالفعل كدفعة بداية', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '500'],
            ['id' => 'expectedAnnualReturn', 'label' => 'العائد السنوي المتوقع إذا وُضعت الأموال في صندوق استثماري (%) - 0 للادخار النقدي', 'type' => 'number', 'default' => '0', 'min' => '0', 'max' => '20', 'step' => '0.5'],
        ],
        'calcJs' => "
            const goal = Math.max(500, parseFloat(document.getElementById('targetGoalAmount').value) || 30000);
            const months = Math.max(1, parseInt(document.getElementById('monthsToReachGoal').value) || 12);
            const initial = Math.max(0, parseFloat(document.getElementById('initialSavingsAvailable').value) || 3000);
            const annualReturn = Math.max(0, parseFloat(document.getElementById('expectedAnnualReturn').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const netGoalToSave = Math.max(0, goal - initial);
            let monthlyContribution = netGoalToSave / months;

            // حساب أثر العائد الاستثماري المركب إن وجد
            if (annualReturn > 0) {
                const r = annualReturn / 12;
                // PMT formula: P = FV * r / ((1 + r)^n - 1)
                const futureValueOfInitial = initial * Math.pow(1 + r, months);
                const remainingNeeded = Math.max(0, goal - futureValueOfInitial);
                monthlyContribution = (remainingNeeded * r) / (Math.pow(1 + r, months) - 1);
            }

            const dailySavings = monthlyContribution / 30;

            setPrimaryResult(formatMoney(monthlyContribution, curr) + ' شهرياً', 'المبلغ المطلوب ادخاره كل شهر');
            showResultArea();

            setDetailStats([
                { label: 'الادخار المطلوب يومياً', value: formatMoney(dailySavings, curr) + ' / يوم', color: '#3b82f6' },
                { label: 'المدة الزمنية المحددة', value: months + ' شهراً (' + (months/12).toFixed(1) + ' سنة)', color: '#10b981' },
                { label: 'المبلغ المتبقي للهدف بعد الرصيد الحالي', value: formatMoney(netGoalToSave, curr), color: '#f59e0b' },
                { label: 'الرصيد الابتدائي المتوفر', value: formatMoney(initial, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للوصول إلى هدفك المالي البالغ <strong>\${formatMoney(goal, curr)}</strong> خلال <strong>\${months} شهراً</strong>، تحتاج لادخار <strong>\${formatMoney(monthlyContribution, curr)} شهرياً</strong> (أي ما يعادل <strong>\${formatMoney(dailySavings, curr)} يومياً</strong>).</p>
            `);
        ",
        'points' => [
            'الادخار الشهري المطلوب = (المبلغ المستهدف - الرصيد المبدئي) ÷ عدد الشهور.',
            'تحويل الأهداف المالية الكبيرة إلى أرقام يومية صغيرة يجعل تحقيقها نفسياً أسهل وأكثر قابلية للتنفيذ.'
        ],
        'assumptions' => 'يفترض التزاماً ثابتاً بالإيداع الشهري دون سحب من الرصيد المتراكم.',
        'faqs' => [
            ['q' => 'أين أضع أموال الادخار أثناء تجميع الهدف؟', 'a' => 'إذا كان الهدف لأقل من سنة فضعه في حساب ادخاري عالي العائد أو صكوك وسندات حكومية قصيرة الأجل منخفضة المخاطر، ولا تضعه في أسهم متقلبة تجنباً للهبوط المفاجئ قبل موعد حاجتك للمال.']
        ],
        'related' => ['salary-division-calculator', 'when-can-i-buy-car-calculator', 'when-can-i-buy-house-calculator']
    ],

    'when-can-i-buy-car-calculator' => [
        'title' => 'حاسبة متى أستطيع شراء سيارة؟',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetCarPrice', 'label' => 'سعر السيارة المستهدفة', 'type' => 'number', 'default' => '65000', 'min' => '5000', 'step' => '2500'],
            ['id' => 'currentCarSavings', 'label' => 'المدخرات المتوفرة لديك حالياً لشراء السيارة', 'type' => 'number', 'default' => '10000', 'min' => '0', 'step' => '1000'],
            ['id' => 'monthlyCarSavingsPace', 'label' => 'المبلغ الذي تستطيع ادخاره شهرياً لشراء السيارة', 'type' => 'number', 'default' => '2000', 'min' => '100', 'step' => '100'],
            ['id' => 'purchaseStrategy', 'label' => 'طريقة الشراء المخططة', 'type' => 'select', 'options' => [
                'cash' => 'شراء كاش كامل بنسبة 100% (بدون أي فوائد أو أقساط)',
                'downpayment_20' => 'تجميع دفعة أولى 20% فقط وتقسيط الباقي',
                'downpayment_50' => 'تجميع دفعة أولى 50% لتقليل القسط الشهري'
            ], 'default' => 'downpayment_20'],
        ],
        'calcJs' => "
            const price = Math.max(5000, parseFloat(document.getElementById('targetCarPrice').value) || 65000);
            const current = Math.max(0, parseFloat(document.getElementById('currentCarSavings').value) || 10000);
            const pace = Math.max(100, parseFloat(document.getElementById('monthlyCarSavingsPace').value) || 2000);
            const strategy = document.getElementById('purchaseStrategy').value;
            const curr = getSelectedCurrency();

            let targetAmountNeeded = price;
            if (strategy === 'downpayment_20') targetAmountNeeded = price * 0.20;
            if (strategy === 'downpayment_50') targetAmountNeeded = price * 0.50;

            const remainingToSave = Math.max(0, targetAmountNeeded - current);
            const monthsNeeded = Math.ceil(remainingToSave / pace);

            const targetDate = new Date();
            targetDate.setMonth(targetDate.getMonth() + monthsNeeded);
            const dateStr = targetDate.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long' });

            setPrimaryResult(monthsNeeded + ' شهراً (' + dateStr + ')', 'الموعد المتوقع لشراء السيارة');
            showResultArea();

            setDetailStats([
                { label: 'المبلغ المطلوب تجميعه', value: formatMoney(targetAmountNeeded, curr), color: '#3b82f6' },
                { label: 'المبلغ المتبقي للادخار', value: formatMoney(remainingToSave, curr), color: '#ef4444' },
                { label: 'الادخار الشهري المعتمد', value: formatMoney(pace, curr) + ' / شهر', color: '#10b981' },
                { label: 'طريقة الشراء المحددة', value: strategy === 'cash' ? 'كاش بالكامل' : (strategy === 'downpayment_20' ? 'دفعة أولى 20%' : 'دفعة أولى 50%'), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتوفير مبلغ <strong>\${formatMoney(targetAmountNeeded, curr)}</strong> لشراء السيارة بمعدل ادخار <strong>\${formatMoney(pace, curr)} شهرياً</strong>، ستكون جاهزاً للشراء خلال <strong>\${monthsNeeded} شهراً</strong> بحلول <strong>\${dateStr}</strong>.</p>
            `);
        ",
        'points' => [
            'دفع دفعة أولى لا تقل عن 20% يخفض قيمة القسط الشهري ويوفر آلاف الريالات/الدولارات في الفوائد التمويلية.',
            'الشراء كاش يمنحك قوة تفاوضية في المعارض للحصول على خصومات إضافية.'
        ],
        'assumptions' => 'يفترض ثبات سعر السيارة وعدم حدوث تضخم مفاجئ في الموديلات القادمة.',
        'faqs' => [
            ['q' => 'هل من الأفضل شراء سيارة كاش أم بالتقسيط؟', 'a' => 'الشراء كاش يوفر فوائد التمويل والتأمين الشامل الإجباري المرهق، ولكن إذا كان لديك فرصة استثمارية تحقق عائداً أعلى من فائدة قرض السيارة فالتقسيط قد يكون خياراً منطقياً.']
        ],
        'related' => ['monthly-car-cost-calculator', 'savings-goal-calculator', 'real-car-cost-calculator']
    ],

    'when-can-i-buy-house-calculator' => [
        'title' => 'حاسبة متى أستطيع شراء منزل؟ (الدفعة الأولى للعقار)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetPropertyPrice', 'label' => 'سعر العقار المستهدف (فيلا أو شقة تمليك)', 'type' => 'number', 'default' => '800000', 'min' => '100000', 'step' => '25000'],
            ['id' => 'downpaymentRatioRequired', 'label' => 'نسبة الدفعة الأولى المطلوبة للتمويل العقاري (%) - الشائع 10% إلى 15%', 'type' => 'number', 'default' => '10', 'min' => '5', 'max' => '50', 'step' => '5'],
            ['id' => 'currentHomeSavings', 'label' => 'المدخرات المتاحة حالياً للمنزل', 'type' => 'number', 'default' => '25000', 'min' => '0', 'step' => '5000'],
            ['id' => 'monthlyHomeSavingsRate', 'label' => 'قدرتك على الادخار الشهري لشراء المنزل', 'type' => 'number', 'default' => '3500', 'min' => '500', 'step' => '250'],
            ['id' => 'purchaseFeesBuffer', 'label' => 'رسوم التصرفات العقارية وأتعاب السعي (%)- عادة 5% إلى 7.5%', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '10', 'step' => '0.5'],
        ],
        'calcJs' => "
            const propPrice = Math.max(100000, parseFloat(document.getElementById('targetPropertyPrice').value) || 800000);
            const downRatio = Math.max(5, parseFloat(document.getElementById('downpaymentRatioRequired').value) || 10) / 100;
            const current = Math.max(0, parseFloat(document.getElementById('currentHomeSavings').value) || 25000);
            const pace = Math.max(500, parseFloat(document.getElementById('monthlyHomeSavingsRate').value) || 3500);
            const feeRatio = Math.max(0, parseFloat(document.getElementById('purchaseFeesBuffer').value) || 5) / 100;
            const curr = getSelectedCurrency();

            const downPayment = propPrice * downRatio;
            const extraFees = propPrice * feeRatio;
            const totalCashNeeded = downPayment + extraFees;
            const remainingToSave = Math.max(0, totalCashNeeded - current);
            const monthsNeeded = Math.ceil(remainingToSave / pace);
            const yearsNeeded = (monthsNeeded / 12).toFixed(1);

            const targetDate = new Date();
            targetDate.setMonth(targetDate.getMonth() + monthsNeeded);
            const dateStr = targetDate.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long' });

            setPrimaryResult(yearsNeeded + ' سنة (' + monthsNeeded + ' شهراً)', 'المدة المقدرة لتملك المنزل');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي السيولة النقدية المطلوبة', value: formatMoney(totalCashNeeded, curr), color: '#3b82f6' },
                { label: 'قيمة الدفعة الأولى للعقار (' + (downRatio*100) + '%)', value: formatMoney(downPayment, curr), color: '#10b981' },
                { label: 'الرسوم العقارية والضرائب والسعي', value: formatMoney(extraFees, curr), color: '#f59e0b' },
                { label: 'التاريخ المستهدف لامتلاك العقار', value: dateStr, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لشراء عقار بقيمة <strong>\${formatMoney(propPrice, curr)}</strong>، تحتاج لتجهيز سيولة نقدية قدرها <strong>\${formatMoney(totalCashNeeded, curr)}</strong> (دفعة أولى ورسوم إدارية وضريبية). بادخار <strong>\${formatMoney(pace, curr)} شهرياً</strong>، ستصل لهدفك بحلول <strong>\${dateStr}</strong> (خلال <strong>\${yearsNeeded} سنة</strong>).</p>
            `);
        ",
        'points' => [
            'الدفعة الأولى للعقار ليست المصروف النقدي الوحيد؛ يجب احتساب ضريبة التصرفات العقارية (5%) وأتعاب الوسيط العقاري (السعي 2.5%) ورسوم التقييم.',
            'استثمار مدخرات المنزل في أصول آمنة مدرة للعائد (مثل الصكوك) يسرع موعد تملك المنزل بنسبة 15% إلى 20%.'
        ],
        'assumptions' => 'يفترض استحقاق المشتري لبرامج الدعم السكني أو الحصول على تمويل عقاري بنكي معتمد.',
        'faqs' => [
            ['q' => 'ما هو الحد الأقصى للاستقطاع الشهري للقسط العقاري من الراتب؟', 'a' => 'تحدد البنوك المركزية غالباً حداً أقصى للاستقطاع العقاري لا يتجاوز 50% إلى 65% من صافي الدخل الشهري لحماية المواطن من التعثر المالي.']
        ],
        'related' => ['savings-goal-calculator', 'house-building-cost-calculator', 'is-salary-enough-calculator']
    ],

    'immigration-cost-calculator' => [
        'title' => 'حاسبة تكلفة الهجرة والسفر الدولي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'immigrationCountry', 'label' => 'دولة الهجرة المستهدفة', 'type' => 'select', 'options' => [
                'canada' => 'كندا (Express Entry / PNP) ~ كشف حساب PoF مشروط',
                'australia' => 'أستراليا (Skilled Independent 189/190)',
                'germany' => 'ألمانيا (Chancenkarte / بطاقة الفرصة والفيزا المهنية)',
                'uk' => 'بريطانيا (Skilled Worker Visa)'
            ], 'default' => 'canada'],
            ['id' => 'familySizeImm', 'label' => 'عدد أفراد الأسرة المهاجرين', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '8', 'step' => '1'],
            ['id' => 'ieltsExamsCount', 'label' => 'عدد امتحانات اللغة المطلوبة (IELTS/TEF/PTE)', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '6', 'step' => '1'],
        ],
        'calcJs' => "
            const country = document.getElementById('immigrationCountry').value;
            const family = Math.max(1, parseInt(document.getElementById('familySizeImm').value) || 2);
            const exams = Math.max(1, parseInt(document.getElementById('ieltsExamsCount').value) || 2);
            const curr = getSelectedCurrency();

            // تكاليف الاختبارات والتقييم والترجمة
            const examsCost = exams * 260; // سعر امتحان الآيلتس حوالي 260 دولار
            const ecaCost = 250; // معادلة الشهادات WES
            const translationsAndMedical = family * 350; // فحص طبي وترجمة أوراق
            const flightTickets = family * 900; // تذاكر الطيران للوجهة

            // إثبات القدرة المالية المشروط (Proof of Funds) ورسوم الحكومة
            let governmentFees = family * 1100;
            let settlementFundsRequired = 10000;

            if (country === 'canada') {
                governmentFees = 1000 + (family > 1 ? (family - 1) * 700 : 0);
                settlementFundsRequired = family === 1 ? 14000 : (family === 2 ? 17500 : 21500);
            } else if (country === 'germany') {
                settlementFundsRequired = family * 12000; // حساب بنكي مغلق Sperrkonto
            }

            const totalProcessCost = examsCost + ecaCost + translationsAndMedical + governmentFees + flightTickets;
            const grandTotalWithProofOfFunds = totalProcessCost + settlementFundsRequired;

            setPrimaryResult(formatMoney(grandTotalWithProofOfFunds, curr), 'إجمالي السيولة المالية المطلوبة للهجرة');
            showResultArea();

            setDetailStats([
                { label: 'كشف الحساب البنكي المشروط (Proof of Funds)', value: formatMoney(settlementFundsRequired, curr), color: '#10b981' },
                { label: 'تكاليف الإجراءات والرسوم الحكومية والتذاكر', value: formatMoney(totalProcessCost, curr), color: '#ef4444' },
                { label: 'رسوم الفحص الطبي والترجمة ومعادلة الشهادات', value: formatMoney(translationsAndMedical + ecaCost, curr), color: '#f59e0b' },
                { label: 'رسوم امتحانات اللغة (' + exams + ' امتحانات)', value: formatMoney(examsCost, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>للهجرة إلى <strong>\${country === 'canada' ? 'كندا' : (country === 'australia' ? 'أستراليا' : 'ألمانيا')}</strong> لأسرة من <strong>\${family} أفراد</strong>، تحتاج إلى سيولة إجمالية قدرها <strong>\${formatMoney(grandTotalWithProofOfFunds, curr)}</strong>، تتضمن <strong>\${formatMoney(settlementFundsRequired, curr)}</strong> في كشف الحساب البنكي المشروط كأموال استقرار، و <strong>\${formatMoney(totalProcessCost, curr)}</strong> كرسوم إجراءات واختبارات وتذاكر طيران.</p>
            `);
        ",
        'points' => [
            'أموال الاستقرار (Proof of Funds) لا يتم دفعها كرسوم، بل يجب إثبات وجودها في حسابك البنكي لعدة أشهر لإثبات قدرتك على إعالة نفسك وأسرتك حتى تجد عملاً.',
            'تكاليف الهجرة المباشرة تشمل: امتحانات اللغة، تقييم الشهادات (WES)، الفحص الطبي، الرسوم الحكومية، وتذاكر الطيران.'
        ],
        'assumptions' => 'المبالغ تقديرية ومبنية على متطلبات برامج الهجرة الرسمية لعام 2025/2026.',
        'faqs' => [
            ['q' => 'هل يحق لي استخدام أموال كشف الحساب البنكي (PoF) بعد الوصول؟', 'a' => 'نعم؛ أموال إثبات القدرة المالية هي ملكك بالكامل ومخصصة للإنفاق منها على إيجار السكن والمعيشة في الأشهر الأولى بعد وصولك لدولة المهجر.']
        ],
        'related' => ['relocation-cost-calculator', 'cost-of-living-calculator', 'travel-cost-calculator']
    ],

    'relocation-cost-calculator' => [
        'title' => 'حاسبة تكلفة الانتقال لدولة أو مدينة أخرى',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetCityRent', 'label' => 'الإيجار الشهري المتوقع في المدينة الجديدة', 'type' => 'number', 'default' => '3200', 'min' => '500', 'step' => '100'],
            ['id' => 'shippingLuggageCost', 'label' => 'تكاليف شحن العفش أو الأمتعة الإضافية والسيارة', 'type' => 'number', 'default' => '2500', 'min' => '0', 'step' => '200'],
            ['id' => 'temporaryStayDays', 'label' => 'أيام الإقامة المؤقتة في فندق/Airbnb للبحث عن سكن', 'type' => 'number', 'default' => '14', 'min' => '0', 'max' => '60', 'step' => '1'],
            ['id' => 'tempStayNightRate', 'label' => 'سعر الليلة في الإقامة المؤقتة', 'type' => 'number', 'default' => '250', 'min' => '50', 'step' => '25'],
            ['id' => 'newSetupAllowance', 'label' => 'مخصص شراء مستلزمات وبداية المعيشة والتأمين', 'type' => 'number', 'default' => '4000', 'min' => '500', 'step' => '500'],
        ],
        'calcJs' => "
            const rent = Math.max(500, parseFloat(document.getElementById('targetCityRent').value) || 3200);
            const shipping = Math.max(0, parseFloat(document.getElementById('shippingLuggageCost').value) || 2500);
            const stayDays = Math.max(0, parseInt(document.getElementById('temporaryStayDays').value) || 14);
            const nightRate = Math.max(50, parseFloat(document.getElementById('tempStayNightRate').value) || 250);
            const setup = Math.max(500, parseFloat(document.getElementById('newSetupAllowance').value) || 4000);
            const curr = getSelectedCurrency();

            const tempStayCost = stayDays * nightRate;
            const upfrontRentDeposit = rent * 2; // إيجار أول شهر + تأمين
            const totalRelocation = shipping + tempStayCost + setup + upfrontRentDeposit;

            setPrimaryResult(formatMoney(totalRelocation, curr), 'الميزانية التقديرية الإجمالية للانتقال');
            showResultArea();

            setDetailStats([
                { label: 'حجز الإقامة المؤقتة (' + stayDays + ' يوماً)', value: formatMoney(tempStayCost, curr), color: '#3b82f6' },
                { label: 'إيجار أول شهر وتأمين السكن الجديد', value: formatMoney(upfrontRentDeposit, curr), color: '#10b981' },
                { label: 'شحن الأمتعة والمقتنيات', value: formatMoney(shipping, curr), color: '#f59e0b' },
                { label: 'تجهيزات البداية والرسوم الإدارية', value: formatMoney(setup, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلف الانتقال وبدء الاستقرار في المدينة الجديدة حوالي <strong>\${formatMoney(totalRelocation, curr)}</strong> تشمل الإقامة المؤقتة وشحن الأمتعة وتأمين أول شهرين للسكن الجديد.</p>
            `);
        ",
        'points' => [
            'الانتقال دائماً يحمل مصاريف مفاجئة كشراء أدوات المطبخ والإنترنت ودفع اشتراكات جديدة.',
            'حجز سكن مؤقت لمدة أسبوعين يمنحك الفرصة لمعاينة الأحياء والمدارس بنفسك قبل توقيع عقد إيجار سنوي ملزم.'
        ],
        'assumptions' => 'يفترض الحصول على السكن الدائم خلال فترة الإقامة المؤقتة.',
        'faqs' => [
            ['q' => 'هل الأفضل شحن الأثاث القديم أم بيعه وشراء أثاث جديد؟', 'a' => 'إذا كانت مسافة الانتقال بعيدة أو بين دول مختلفة، فبيع الأثاث وشراء أثاث جديد من المدينة الجديدة يكون أوفر بنسبة كبيرة جداً ويوفر مخاطر كسر وتلف العفش أثناء النقل.']
        ],
        'related' => ['immigration-cost-calculator', 'cost-of-living-calculator', 'city-income-requirement-calculator']
    ],

    'cost-of-living-calculator' => [
        'title' => 'حاسبة تكلفة المعيشة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'housingLivingCost', 'label' => 'السكن والإيجار الشهري المتوقع', 'type' => 'number', 'default' => '2800', 'min' => '300', 'step' => '100'],
            ['id' => 'foodGroceryLiving', 'label' => 'الطعام والتموين والمطاعم شهرياً', 'type' => 'number', 'default' => '2200', 'min' => '200', 'step' => '100'],
            ['id' => 'transportLiving', 'label' => 'المواصلات والبنزين وتطبيقات النقل', 'type' => 'number', 'default' => '900', 'min' => '50', 'step' => '50'],
            ['id' => 'servicesAndBills', 'label' => 'الخدمات (فواتير، إنترنت، نظافة، جوال)', 'type' => 'number', 'default' => '650', 'min' => '50', 'step' => '50'],
            ['id' => 'miscLivingCost', 'label' => 'رعاية صحية وملابس وترفيه شخصي', 'type' => 'number', 'default' => '950', 'min' => '50', 'step' => '50'],
        ],
        'calcJs' => "
            const rent = Math.max(300, parseFloat(document.getElementById('housingLivingCost').value) || 2800);
            const food = Math.max(200, parseFloat(document.getElementById('foodGroceryLiving').value) || 2200);
            const trans = Math.max(50, parseFloat(document.getElementById('transportLiving').value) || 900);
            const bills = Math.max(50, parseFloat(document.getElementById('servicesAndBills').value) || 650);
            const misc = Math.max(50, parseFloat(document.getElementById('miscLivingCost').value) || 950);
            const curr = getSelectedCurrency();

            const totalMonthly = rent + food + trans + bills + misc;
            const totalAnnual = totalMonthly * 12;
            const requiredSalaryToSave20 = totalMonthly / 0.8;

            setPrimaryResult(formatMoney(totalMonthly, curr) + ' شهرياً', 'تكلفة المعيشة الشهرية الأساسية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة المعيشة السنوية الإجمالية', value: formatMoney(totalAnnual, curr), color: '#3b82f6' },
                { label: 'الراتب الموصى به للعيش بأمان وادخار 20%', value: formatMoney(requiredSalaryToSave20, curr) + ' / شهرياً', color: '#10b981' },
                { label: 'نسبة بند السكن من المعيشة', value: ((rent / totalMonthly) * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'نسبة الطعام والخدمات', value: (((food + bills) / totalMonthly) * 100).toFixed(0) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفك المعيشة في هذا النمط حوالي <strong>\${formatMoney(totalMonthly, curr)} شهرياً</strong> (ما يعادل <strong>\${formatMoney(totalAnnual, curr)} سنوياً</strong>). للحفاظ على حياة مريحة وادخار 20%، يجب أن يكون راتبك لا يقل عن <strong>\${formatMoney(requiredSalaryToSave20, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'تكلفة المعيشة تتفاوت بشكل هائل بين العواصم والمدن الكبرى وبين المدن الهادئة والمحافظات.',
            'السكن والطعام يمثلان عادة بين 65% إلى 75% من تكلفة المعيشة الإجمالية في أي مدينة.'
        ],
        'assumptions' => 'يفترض نمط حياة متوازن لفرد أو أسرة صغيرة.',
        'faqs' => [
            ['q' => 'كيف أتحقق من تكلفة المعيشة لمدينة قبل الانتقال إليها؟', 'a' => 'تصفح مواقع تأجير العقارات المحلية لمعرفة إيجار الأحياء الجيدة، واطلع على أسعار السوبرماركت والمواصلات وتطبيقات توصيل الطعام في تلك المدينة.']
        ],
        'related' => ['city-income-requirement-calculator', 'is-salary-enough-calculator', 'family-monthly-budget-calculator']
    ],

    'rent-split-calculator' => [
        'title' => 'حاسبة تقسيم الإيجار العادل بين الشركاء في السكن',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalApartmentRent', 'label' => 'إجمالي إيجار الشقة بالكامل شهرياً', 'type' => 'number', 'default' => '3600', 'min' => '500', 'step' => '100'],
            ['id' => 'roomTypeOption', 'label' => 'مواصفات غرفتك مقارنة بالشقة', 'type' => 'select', 'options' => [
                'equal' => 'جميع الغرف متطابقة تماماً في المساحة والمزايا (تقسيم بالتساوي)',
                'master_ensuite' => 'غرفة ماستر كبيرة مع حمام خاص وشرفة (+35% نسبة إضافية)',
                'standard_room' => 'غرفة فردية عادية بحمام مشترك',
                'shared_room' => 'سرير في غرفة مشتركة مع شريك آخر (-35% خصم)'
            ], 'default' => 'master_ensuite'],
            ['id' => 'roommatesCount', 'label' => 'إجمالي عدد الساكنين في الشقة', 'type' => 'number', 'default' => '3', 'min' => '2', 'max' => '10', 'step' => '1'],
        ],
        'calcJs' => "
            const rent = Math.max(500, parseFloat(document.getElementById('totalApartmentRent').value) || 3600);
            const type = document.getElementById('roomTypeOption').value;
            const count = Math.max(2, parseInt(document.getElementById('roommatesCount').value) || 3);
            const curr = getSelectedCurrency();

            const equalShare = rent / count;
            let myShare = equalShare;

            if (type === 'master_ensuite') {
                myShare = equalShare * 1.30;
            } else if (type === 'shared_room') {
                myShare = equalShare * 0.70;
            } else {
                myShare = equalShare;
            }

            setPrimaryResult(formatMoney(myShare, curr) + ' شهرياً', 'حصتك العادلة من الإيجار');
            showResultArea();

            setDetailStats([
                { label: 'حصة غرفتك المقترحة', value: formatMoney(myShare, curr), color: '#10b981' },
                { label: 'الحصة المتساوية الافتراضية', value: formatMoney(equalShare, curr), color: '#3b82f6' },
                { label: 'إجمالي إيجار الشقة بالكامل', value: formatMoney(rent, curr), color: '#ef4444' },
                { label: 'عدد الشركاء بالسكن', value: count + ' شركاء', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>بناءً على مواصفات غرفتك \${type === 'master_ensuite' ? '(ماستر بحمام خاص)' : (type === 'shared_room' ? '(مشتركة)' : '(عادية)')}، تبلغ حصتك العادلة <strong>\${formatMoney(myShare, curr)} شهرياً</strong> بدلاً من التقسيم المتساوي البالغ <strong>\${formatMoney(equalShare, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'التقسيم العادل للإيجار يعتمد على مساحة الغرفة ووجود حمام داخلي خاص أو شرفة خاصة.',
            'الغرفة الماستر ذات الحمام الخاص تتحمل عادة بين 25% إلى 35% زيادة عن الغرفة العادية ذات الحمام المشترك.'
        ],
        'assumptions' => 'يفترض تقاسم فواتير الإنترنت والكهرباء بالتساوي بين جميع الأفراد.',
        'faqs' => [
            ['q' => 'كيف نقسم فواتير الخدمات (الكهرباء والإنترنت)؟', 'a' => 'فواتير الخدمات والمياه والإنترنت تُقسم بالتساوي دائماً على عدد الأشخاص، لأن استخدام الأجهزة والمرافق المشتركة يكون متقارباً للجميع.']
        ],
        'related' => ['electricity-bill-split-calculator', 'internet-bill-split-calculator', 'independence-cost-calculator']
    ],

    'restaurant-bill-split-calculator' => [
        'title' => 'حاسبة تقسيم فاتورة المطعم والإكرامية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'billAmountTotal', 'label' => 'قيمة الفاتورة الإجمالية للمطعم', 'type' => 'number', 'default' => '420', 'min' => '5', 'step' => '10'],
            ['id' => 'peopleCountSplit', 'label' => 'عدد الأشخاص المشاركين في الفاتورة', 'type' => 'number', 'default' => '4', 'min' => '2', 'max' => '50', 'step' => '1'],
            ['id' => 'tipPercentage', 'label' => 'نسبة الإكرامية / البقشيش (Tip) % إن رغبت', 'type' => 'number', 'default' => '0', 'min' => '0', 'max' => '30', 'step' => '5'],
            ['id' => 'alreadyIncludesVat', 'label' => 'هل الفاتورة شاملة ضريبة القيمة المضافة والخدمة؟', 'type' => 'select', 'options' => [
                'yes' => 'نعم، المبلغ شامل كل شيء',
                'no' => 'لا، إضافة 15% ضريبة فوق الفاتورة'
            ], 'default' => 'yes'],
        ],
        'calcJs' => "
            const bill = Math.max(5, parseFloat(document.getElementById('billAmountTotal').value) || 420);
            const people = Math.max(2, parseInt(document.getElementById('peopleCountSplit').value) || 4);
            const tipRate = Math.max(0, parseFloat(document.getElementById('tipPercentage').value) || 0) / 100;
            const incVat = document.getElementById('alreadyIncludesVat').value === 'yes';
            const curr = getSelectedCurrency();

            const vatAmount = incVat ? 0 : bill * 0.15;
            const billWithVat = bill + vatAmount;
            const tipAmount = billWithVat * tipRate;
            const finalTotal = billWithVat + tipAmount;
            const sharePerPerson = finalTotal / people;

            setPrimaryResult(formatMoney(sharePerPerson, curr) + ' للشخص', 'حصة الفرد الواحد من الفاتورة');
            showResultArea();

            setDetailStats([
                { label: 'حصة كل شخص بالضبط', value: formatMoney(sharePerPerson, curr), color: '#10b981' },
                { label: 'المبلغ الإجمالي النهائي للدفع', value: formatMoney(finalTotal, curr), color: '#3b82f6' },
                { label: 'قيمة الإكرامية المضافة (Tip)', value: formatMoney(tipAmount, curr), color: '#f59e0b' },
                { label: 'عدد الحاضرين على الطاولة', value: people + ' أشخاص', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي الفاتورة <strong>\${formatMoney(finalTotal, curr)}</strong> مقسمة على <strong>\${people} أشخاص</strong> تعطي حصة <strong>\${formatMoney(sharePerPerson, curr)}</strong> لكل فرد بالضبط.</p>
            `);
        ",
        'points' => [
            'التقسيم بالتساوي هو الأسرع والأكثر شيوعاً بين الأصدقاء في التجمعات والولائم.',
            'حساب الضريبة والإكرامية مسبقاً يمنع الإحراج والنقص المالي عند جمع الحساب.'
        ],
        'assumptions' => 'يفترض طلبات متقاربة القيمة بين الحاضرين.',
        'faqs' => [
            ['q' => 'ما العمل إذا طلب أحد الأشخاص وجبة مكلفة جداً بمفرده؟', 'a' => 'في هذه الحالة، يدفع صاحب الوجبة المميزة ثمن وجبته منفرداً، ويتم تقسيم باقي الأطباق والمقبلات والمشروبات المشتركة بالتساوي بين الجميع.']
        ],
        'related' => ['travel-expenses-split-calculator', 'rent-split-calculator', 'vat-calculator']
    ],

    'electricity-bill-split-calculator' => [
        'title' => 'حاسبة تقسيم فاتورة الكهرباء بين الساكنين',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalElectricBill', 'label' => 'قيمة فاتورة الكهرباء الإجمالية', 'type' => 'number', 'default' => '750', 'min' => '10', 'step' => '25'],
            ['id' => 'tenantsCount', 'label' => 'عدد الغرف أو الساكنين في الشقة', 'type' => 'number', 'default' => '3', 'min' => '2', 'max' => '15', 'step' => '1'],
            ['id' => 'heavyAcUserCount', 'label' => 'هل هناك غرفة تشغل مكيف 24 ساعة بمفردها؟', 'type' => 'select', 'options' => [
                'no' => 'لا، الاستخدام متقارب وعادل بين الجميع',
                'yes_one' => 'نعم، غرفة واحدة تشغيلها دائم ومضاعف (+50% حصة إضافية)'
            ], 'default' => 'no'],
        ],
        'calcJs' => "
            const bill = Math.max(10, parseFloat(document.getElementById('totalElectricBill').value) || 750);
            const tenants = Math.max(2, parseInt(document.getElementById('tenantsCount').value) || 3);
            const hasHeavy = document.getElementById('heavyAcUserCount').value === 'yes_one';
            const curr = getSelectedCurrency();

            let normalShare = bill / tenants;
            let heavyShare = normalShare;

            if (hasHeavy) {
                // حصة الغرفة الثقيلة 1.5x وحصص البقية 1.0x
                const totalUnits = (tenants - 1) + 1.5;
                normalShare = bill / totalUnits;
                heavyShare = normalShare * 1.5;
            }

            setPrimaryResult(hasHeavy ? formatMoney(normalShare, curr) + ' (والغرفة الكثيفة: ' + formatMoney(heavyShare, curr) + ')' : formatMoney(normalShare, curr) + ' للشخص', 'حصة الفرد من فاتورة الكهرباء');
            showResultArea();

            setDetailStats([
                { label: 'حصة الغرفة العادية', value: formatMoney(normalShare, curr), color: '#10b981' },
                { label: 'حصة غرفة الاستخدام الكثيف', value: hasHeavy ? formatMoney(heavyShare, curr) : 'غير مطبق', color: '#ef4444' },
                { label: 'إجمالي الفاتورة المستحقة', value: formatMoney(bill, curr), color: '#3b82f6' },
                { label: 'عدد الشركاء بالسكن', value: tenants + ' أفراد', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>فاتورة كهرباء بمبلغ <strong>\${formatMoney(bill, curr)}</strong> مقسمة بين <strong>\${tenants} أفراد</strong>: تبلغ حصة الساكن العادي <strong>\${formatMoney(normalShare, curr)}</strong>\${hasHeavy ? '، بينما تتحمل الغرفة ذات الاستهلاك المفرط ' + formatMoney(heavyShare, curr) : ''}.</p>
            `);
        ",
        'points' => [
            'المكيفات وسخانات الماء تمثل أكثر من 70% من فاتورة الكهرباء في السكن المشترك.',
            'التراضي والاتفاق المسبق على طريقة احتساب ساعات تشغيل التكييف يمنع الخلافات بين زملاء السكن.'
        ],
        'assumptions' => 'يفترض عدم وجود عدادات فرعية لكل غرفة.',
        'faqs' => [
            ['q' => 'هل يُنصح بتركيب عدادات كهرباء فرعية ديجيتال لكل غرفة؟', 'a' => 'نعم؛ العدادات الفرعية (Sub-meters) رخيصة الثمن وسهلة التركيب وتفصل استهلاك كل غرفة ومكيفها بدقة تامة وتنهي أي خلاف في السكن المشترك نهائياً.']
        ],
        'related' => ['rent-split-calculator', 'internet-bill-split-calculator', 'ac-consumption-calculator']
    ],

    'internet-bill-split-calculator' => [
        'title' => 'حاسبة تقسيم اشتراك الإنترنت المنزلي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlyInternetCost', 'label' => 'قيمة اشتراك باقة الألياف أو الراوتر شهرياً', 'type' => 'number', 'default' => '287.50', 'min' => '50', 'step' => '10'],
            ['id' => 'usersCountNet', 'label' => 'عدد الشقق أو المستخدمين المشاركين بالشبكة', 'type' => 'number', 'default' => '3', 'min' => '2', 'max' => '20', 'step' => '1'],
        ],
        'calcJs' => "
            const bill = Math.max(50, parseFloat(document.getElementById('monthlyInternetCost').value) || 287.50);
            const users = Math.max(2, parseInt(document.getElementById('usersCountNet').value) || 3);
            const curr = getSelectedCurrency();

            const share = bill / users;
            const annualShare = share * 12;

            setPrimaryResult(formatMoney(share, curr) + ' شهرياً للشخص', 'حصة الفرد من اشتراك الإنترنت');
            showResultArea();

            setDetailStats([
                { label: 'حصة الشخص الواحد شهرياً', value: formatMoney(share, curr), color: '#10b981' },
                { label: 'إجمالي ما يدفعه الشخص سنوياً', value: formatMoney(annualShare, curr), color: '#3b82f6' },
                { label: 'قيمة الاشتراك الإجمالي شهرياً', value: formatMoney(bill, curr), color: '#f59e0b' },
                { label: 'عدد المشتركين بالخط', value: users + ' مستخدمين', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>اشتراك إنترنت فايبر بقيمة <strong>\${formatMoney(bill, curr)}</strong> مقسم على <strong>\${users} أفراد</strong>، يكلف كل شخص <strong>\${formatMoney(share, curr)} شهرياً</strong> فقط، محققاً وفراً كبيراً بدلاً من الاشتراك الفردي المنفصل لكل شخص.</p>
            `);
        ",
        'points' => [
            'الاشتراك المشترك في باقة فايبر سريعة (مثل 300 أو 500 ميجابت) وتقسيم تكلفتها يوفر أكثر من 60% من تكلفة الباقات الفردية لكل ساكن.',
            'يُفضل استخدام أجهزة راوتر تدعم Mesh Wi-Fi لتوزيع الإشارة بقوة لكافة الغرف.'
        ],
        'assumptions' => 'يفترض اشتراكاً منزلياً ثابتاً غير محدود البيانات.',
        'faqs' => [
            ['q' => 'كيف نضمن عدم سحب أحد المشتركين لكامل سرعة الإنترنت بالتحميل؟', 'a' => 'يمكن من خلال إعدادات الراوتر تفعيل خاصية التحكم بجودة الخدمة (QoS - Quality of Service) لتوزيع السرعة بالتساوي ومنع هبوط السرعة أثناء بث الفيديوهات ومكالمات العمل.']
        ],
        'related' => ['rent-split-calculator', 'electricity-bill-split-calculator', 'is-salary-enough-calculator']
    ],

    'salary-to-hourly-daily-converter' => [
        'title' => 'حاسبة تحويل الراتب الشهري إلى يومي وساعي ودقيق',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlySalaryInputConv', 'label' => 'الراتب الشهري', 'type' => 'number', 'default' => '6500', 'min' => '100', 'step' => '100'],
            ['id' => 'daysBasisConv', 'label' => 'أساس احتساب الأيام بالشهر', 'type' => 'select', 'options' => [
                '30' => '30 يوماً (الأساس المعتمد في أنظمة العمل والخصومات)',
                '22' => '22 يوماً (أيام العمل الفعلية فقط - عطلة يومين أسبوعياً)',
                '26' => '26 يوماً (أيام العمل الفعلية - عطلة يوم واحد)'
            ], 'default' => '30'],
            ['id' => 'hoursPerDayConv', 'label' => 'ساعات العمل اليومية الرسمية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
        ],
        'calcJs' => "
            const salary = Math.max(100, parseFloat(document.getElementById('monthlySalaryInputConv').value) || 6500);
            const days = parseFloat(document.getElementById('daysBasisConv').value) || 30;
            const hours = Math.max(1, parseFloat(document.getElementById('hoursPerDayConv').value) || 8);
            const curr = getSelectedCurrency();

            const daily = salary / days;
            const hourly = daily / hours;
            const minute = hourly / 60;
            const weekly = hourly * hours * 5;

            setPrimaryResult(formatMoney(hourly, curr) + ' / ساعة عمل', 'أجرك في ساعة العمل الواحدة');
            showResultArea();

            setDetailStats([
                { label: 'أجر يوم العمل الكامل', value: formatMoney(daily, curr), color: '#3b82f6' },
                { label: 'أجر ساعة العمل', value: formatMoney(hourly, curr), color: '#10b981' },
                { label: 'أجر دقيقة العمل', value: formatMoney(minute, curr), color: '#f59e0b' },
                { label: 'الأجر الأسبوعي التقديري', value: formatMoney(weekly, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>براتب شهري <strong>\${formatMoney(salary, curr)}</strong>:<br>
                - اليوم يعادل <strong>\${formatMoney(daily, curr)}</strong>.<br>
                - الساعة تعادل <strong>\${formatMoney(hourly, curr)}</strong>.<br>
                - كل دقيقة تقضيها في وظيفتك تدر عليك <strong>\${formatMoney(minute, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'القسمة على 30 يوماً هي المعيار المعتمد في قانون العمل لمعظم العقود الشهرية شاملة الإجازات الأسبوعية.',
            'حساب أجر الساعة بدقة يفيد في حساب مستحقات العمل الإضافي، وساعات التأخير والخصومات.'
        ],
        'assumptions' => 'يفترض دواماً منتظماً بعدد الساعات المدخلة.',
        'faqs' => [
            ['q' => 'كيف أحسب قيمة خصم ساعة تأخير؟', 'a' => 'اقسم راتبك الشهري على 30، ثم اقسم الناتج على ساعات دوامك اليومي (عادة 8 ساعات)، لتعرف قيمة ساعة التأخير الدقيقة.']
        ],
        'related' => ['hourly-wage-calculator', 'daily-wage-calculator', 'net-salary-calculator']
    ],

    'city-income-requirement-calculator' => [
        'title' => 'حاسبة الدخل المطلوب للعيش في مدينة ومستوى الرفاهية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetCityTier', 'label' => 'مستوى غلاء المدينة', 'type' => 'select', 'options' => [
                'capital_prime' => 'عاصمة رئيسية كبرى (الرياض، دبي، الدوحة) - تكاليف مرتفعة',
                'large_city' => 'مدينة رئيسية كبيرة (جدة، أبوظبي، القاهرة الجديدة، عمّان)',
                'medium_city' => 'مدينة متوسطة / محافظة هادئة - تكاليف معتدلة اقتصادية'
            ], 'default' => 'capital_prime'],
            ['id' => 'lifestylePreference', 'label' => 'مستوى ونمط المعيشة المطلوب', 'type' => 'select', 'options' => [
                'basic_economy' => 'اقتصادي بسيط (سكن متواضع، طهي منزلي، مواصلات اقتصادية)',
                'comfortable' => 'متوسط ومريح (شقة ممتازة في حي جيد، ترفيه أسبوعي، سيارة جيدة)',
                'luxury_plus' => 'مرفه وفاخر (فيلا/شقة فخمة، مدارس دولية، سفر سنوي، مطاعم راقية)'
            ], 'default' => 'comfortable'],
            ['id' => 'familyMembersCountCity', 'label' => 'عدد أفراد الأسرة المقيمين في المدينة', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '10', 'step' => '1'],
        ],
        'calcJs' => "
            const tier = document.getElementById('targetCityTier').value;
            const lifestyle = document.getElementById('lifestylePreference').value;
            const members = Math.max(1, parseInt(document.getElementById('familyMembersCountCity').value) || 3);
            const curr = getSelectedCurrency();

            // دخل أساسي للفرد حسب تصنيف المدينة
            let baseSingleIncome = 4500;
            if (tier === 'large_city') baseSingleIncome = 3500;
            if (tier === 'medium_city') baseSingleIncome = 2500;

            // مضاعف نمط المعيشة
            let lifeMult = 1.6; // مريح
            if (lifestyle === 'basic_economy') lifeMult = 1.0;
            if (lifestyle === 'luxury_plus') lifeMult = 2.8;

            // أثر أفراد الأسرة
            const familyFactor = 1 + ((members - 1) * 0.45);
            const requiredMonthlyIncome = baseSingleIncome * lifeMult * familyFactor;
            const requiredAnnualIncome = requiredMonthlyIncome * 12;

            setPrimaryResult(formatMoney(requiredMonthlyIncome, curr) + ' شهرياً', 'الدخل الصافي الموصى به شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الدخل السنوي المطلوب', value: formatMoney(requiredAnnualIncome, curr), color: '#3b82f6' },
                { label: 'مخصص السكن التقريبي (30%)', value: formatMoney(requiredMonthlyIncome * 0.30, curr), color: '#10b981' },
                { label: 'مخصص الادخار والأمان المالي (20%)', value: formatMoney(requiredMonthlyIncome * 0.20, curr), color: '#f59e0b' },
                { label: 'مخصص المعيشة والخدمات والترفيه (50%)', value: formatMoney(requiredMonthlyIncome * 0.50, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للعيش بنمط <strong>\${lifestyle === 'comfortable' ? 'مريح ومستقر' : (lifestyle === 'luxury_plus' ? 'مرفه وفاخر' : 'اقتصادي')}</strong> لأسرة من <strong>\${members} أفراد</strong> في <strong>\${tier === 'capital_prime' ? 'عاصمة كبرى' : 'مدينة رئيسية'}</strong>، يوصى بدخل صافٍ لا يقل عن <strong>\${formatMoney(requiredMonthlyIncome, curr)} شهرياً</strong> لتغطية السكن والالتزامات مع ادخار 20% للأمان المالي.</p>
            `);
        ",
        'points' => [
            'السكن يمثل العنصر الأكثر تبايناً في تكلفة المعيشة بين العواصم والمدن الأخرى.',
            'مع كل فرد إضافي في الأسرة تزيد التكلفة المعيشية بمعدل 40% إلى 50% من تكلفة الفرد البالغ.'
        ],
        'assumptions' => 'التقديرات مبنية على دراسات تكلفة المعيشة ومؤشرات غلاء المدن في المنطقة العربية لعام 2025/2026.',
        'faqs' => [
            ['q' => 'كيف أضمن عدم استنزاف راتبي في المدن الكبرى؟', 'a' => 'احرص على ألا يتجاوز إيجار السكن ثلث دخلك الشهري حتى لو اضطررت للسكن في حي أبعد قليلاً عن مركز المدينة بالقرب من خطوط المترو أو الطرق السريعة.']
        ],
        'related' => ['cost-of-living-calculator', 'is-salary-enough-calculator', 'salary-division-calculator']
    ],
];

echo "Generating Group F: Daily Life & Personal Finance Tools (17 tools)...\n";
foreach ($toolsF as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group F!\n";
