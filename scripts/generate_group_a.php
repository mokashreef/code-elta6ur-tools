<?php
/**
 * مولد أدوات المجموعة A: المال والعمل والتجارة (30 أداة)
 * Code Elta6ur Tools Platform
 */
require_once __DIR__ . '/../includes/tools_registry.php';

$outputDir = __DIR__ . '/../tools';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// قائمة تعريف أدوات المجموعة A
$toolsA = [
    'net-salary-calculator' => [
        'title' => 'حاسبة صافي الراتب بعد الخصومات',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'grossSalary', 'label' => 'الراتب الأساسي / الإجمالي', 'type' => 'number', 'default' => '6000', 'min' => '0', 'step' => '50'],
            ['id' => 'allowances', 'label' => 'البدلات والمكافآت الشهرية', 'type' => 'number', 'default' => '1000', 'min' => '0', 'step' => '50'],
            ['id' => 'socialRate', 'label' => 'نسبة التأمينات / التقاعد (%)', 'type' => 'number', 'default' => '9.75', 'min' => '0', 'max' => '100', 'step' => '0.25'],
            ['id' => 'taxRate', 'label' => 'نسبة ضريبة الدخل (%) إن وجدت', 'type' => 'number', 'default' => '0', 'min' => '0', 'max' => '100', 'step' => '0.5'],
            ['id' => 'otherDeductions', 'label' => 'خصومات أو أقساط أخرى ثابتة', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '10'],
        ],
        'calcJs' => "
            const gross = Math.max(0, parseFloat(document.getElementById('grossSalary').value) || 0);
            const allow = Math.max(0, parseFloat(document.getElementById('allowances').value) || 0);
            const socialR = Math.max(0, Math.min(100, parseFloat(document.getElementById('socialRate').value) || 0));
            const taxR = Math.max(0, Math.min(100, parseFloat(document.getElementById('taxRate').value) || 0));
            const other = Math.max(0, parseFloat(document.getElementById('otherDeductions').value) || 0);
            const curr = getSelectedCurrency();

            const totalGross = gross + allow;
            const socialDeduction = (gross * (socialR / 100));
            const taxableIncome = Math.max(0, totalGross - socialDeduction);
            const taxDeduction = (taxableIncome * (taxR / 100));
            const totalDeductions = socialDeduction + taxDeduction + other;
            const netSalary = Math.max(0, totalGross - totalDeductions);
            const yearlyNet = netSalary * 12;

            setPrimaryResult(formatMoney(netSalary, curr), 'صافي الراتب الشهري المستلم');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الدخل قبل الخصم', value: formatMoney(totalGross, curr), color: '#3b82f6' },
                { label: 'خصم التأمينات الاجتماعية', value: formatMoney(socialDeduction, curr), color: '#f59e0b' },
                { label: 'إجمالي الخصومات الشهرية', value: formatMoney(totalDeductions, curr), color: '#ef4444' },
                { label: 'صافي الدخل السنوي المتوقع', value: formatMoney(yearlyNet, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <div class=\"alert alert-info\" style=\"margin-top:1rem\">
                    <strong>نسبة ما تستلمه من الراتب:</strong> \${totalGross > 0 ? ((netSalary / totalGross) * 100).toFixed(1) : 0}% من إجمالي المستحقات.
                    الاستقطاعات تمثل \${totalGross > 0 ? ((totalDeductions / totalGross) * 100).toFixed(1) : 0}% من راتبك.
                </div>
            `);
        ",
        'points' => [
            'إجمالي الدخل = الراتب الأساسي + البدلات والمكافآت.',
            'خصم التأمينات = الراتب الأساسي × (نسبة التأمينات ÷ 100).',
            'صافي الراتب = إجمالي الدخل - (خصم التأمينات + ضريبة الدخل + الاستقطاعات الأخرى).',
            'صافي الدخل السنوي = صافي الراتب الشهري × 12 شهر.'
        ],
        'assumptions' => 'نسبة التأمينات الشائعة في السعودية للمواطن هي 9.75%، وفي مصر 11%، وفي الإمارات 5%. يمكنك تعديل النسبة بحسب نظام عملك وبلدك.',
        'faqs' => [
            ['q' => 'ما الفرق بين الراتب الأساسي وإجمالي الراتب وصافي الراتب؟', 'a' => 'الراتب الأساسي هو الراتب المتفق عليه قبل أي إضافات، وإجمالي الراتب يضاف إليه البدلات كالسكن والمواصلات، بينما صافي الراتب هو المبلغ الفعلي الذي يدخل حسابك البنكي بعد استقطاع التأمينات والضرائب.'],
            ['q' => 'هل تحسب التأمينات على البدلات أيضاً؟', 'a' => 'في بعض الدول مثل نظام التأمينات السعودي تشمل التأمينات الراتب الأساسي وبدل السكن بحد أقصى، بينما في دول أخرى تخصم من الأساسي فقط. يمكنك إدخال المبلغ الخاضع للتأمين في خانة الراتب الأساسي.']
        ],
        'related' => ['hourly-wage-calculator', 'daily-wage-calculator', 'employee-cost-calculator', 'overtime-calculator']
    ],

    'hourly-wage-calculator' => [
        'title' => 'حاسبة الراتب بالساعة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlySalary', 'label' => 'الراتب الشهري (صافي أو إجمالي)', 'type' => 'number', 'default' => '5000', 'min' => '0', 'step' => '50'],
            ['id' => 'hoursPerWeek', 'label' => 'ساعات العمل في الأسبوع', 'type' => 'number', 'default' => '40', 'min' => '1', 'max' => '100', 'step' => '1'],
            ['id' => 'weeksPerMonth', 'label' => 'متوسط الأسابيع في الشهر', 'type' => 'number', 'default' => '4.33', 'min' => '4', 'max' => '5', 'step' => '0.01'],
        ],
        'calcJs' => "
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const hpw = Math.max(1, parseFloat(document.getElementById('hoursPerWeek').value) || 40);
            const wpm = Math.max(1, parseFloat(document.getElementById('weeksPerMonth').value) || 4.33);
            const curr = getSelectedCurrency();

            const monthlyHours = hpw * wpm;
            const hourlyRate = monthlyHours > 0 ? (salary / monthlyHours) : 0;
            const dailyRate = hourlyRate * (hpw / 5);
            const minuteRate = hourlyRate / 60;

            setPrimaryResult(formatMoney(hourlyRate, curr), 'سعر ساعة العمل الواحدة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ساعات العمل شهرياً', value: formatNumber(monthlyHours, 1) + ' ساعة', color: '#3b82f6' },
                { label: 'أجر يوم العمل (على أساس 5 أيام)', value: formatMoney(dailyRate, curr), color: '#10b981' },
                { label: 'أجر دقيقة العمل', value: formatMoney(minuteRate, curr), color: '#8b5cf6' },
                { label: 'الأجر الأسبوعي', value: formatMoney(hourlyRate * hpw, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>كل ساعة تقضيها في وظيفتك تدر عليك <strong>\${formatMoney(hourlyRate, curr)}</strong> بناءً على دوام أسبوعي \${hpw} ساعة.</p>
            `);
        ",
        'points' => [
            'ساعات العمل الشهرية = ساعات العمل الأسبوعية × 4.33 (متوسط عدد أسابيع الشهر).',
            'أجر الساعة = الراتب الشهري ÷ ساعات العمل الشهرية.',
            'أجر الدقيقة = أجر الساعة ÷ 60.'
        ],
        'assumptions' => 'يحسب الشهر عالمياً على أنه 4.33 أسبوعاً (52 أسبوعاً في السنة ÷ 12 شهراً).',
        'faqs' => [
            ['q' => 'لماذا نستخدم 4.33 أسبوع وليس 4 أسابيع بالضبط؟', 'a' => 'لأن الشهر الميلادي يحتوي على 30 أو 31 يوماً وليس 28 يوماً، وبذلك يحتوي العام على 52 أسبوعاً، وعند قسمة 52 على 12 شهراً يكون الناتج 4.33 أسبوع لكل شهر.']
        ],
        'related' => ['daily-wage-calculator', 'net-salary-calculator', 'overtime-calculator', 'freelancer-hourly-rate-calculator']
    ],

    'daily-wage-calculator' => [
        'title' => 'حاسبة الراتب اليومي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlySalary', 'label' => 'الراتب الشهري', 'type' => 'number', 'default' => '6000', 'min' => '0', 'step' => '50'],
            ['id' => 'workDaysType', 'label' => 'طريقة حساب الأيام', 'type' => 'select', 'options' => [
                'calendar' => 'أيام الشهر التقويمي (30 يوماً - نظام العمل)',
                'actual22' => 'أيام العمل الفعلية فقط (22 يوماً - إجازة يومين أسبوعياً)',
                'actual26' => 'أيام العمل الفعلية (26 يوماً - إجازة يوم واحد أسبوعياً)'
            ], 'default' => 'calendar'],
            ['id' => 'dailyHours', 'label' => 'ساعات العمل اليومية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
        ],
        'calcJs' => "
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const daysType = document.getElementById('workDaysType').value;
            const hours = Math.max(1, parseFloat(document.getElementById('dailyHours').value) || 8);
            const curr = getSelectedCurrency();

            let divisor = 30;
            if (daysType === 'actual22') divisor = 22;
            if (daysType === 'actual26') divisor = 26;

            const dailyWage = salary / divisor;
            const hourlyWage = dailyWage / hours;

            setPrimaryResult(formatMoney(dailyWage, curr), 'الأجر اليومي');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأيام المعتمدة بالحساب', value: divisor + ' يوم', color: '#3b82f6' },
                { label: 'أجر ساعة العمل', value: formatMoney(hourlyWage, curr), color: '#10b981' },
                { label: 'تكلفة خصم غياب يوم', value: formatMoney(dailyWage, curr), color: '#ef4444' },
                { label: 'قيمة نصف يوم عمل', value: formatMoney(dailyWage / 2, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الأجر اليومي المستحق هو <strong>\${formatMoney(dailyWage, curr)}</strong>، وكل ساعة عمل تعادل <strong>\${formatMoney(hourlyWage, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'في معظم قوانين العمل (مثل قانون العمل السعودي والمصري)، يُقسم الراتب الشهري على 30 يوماً لحساب أجر اليوم لأغراض الخصومات والإجازات.',
            'إذا كنت تعمل بنظام اليومية أو الفريلانس يُفضل القسمة على أيام العمل الفعلية (22 أو 26 يوماً).'
        ],
        'assumptions' => 'القسمة على 30 هي المعيار الرسمي لمعظم عقود العمل المدفوعة شهرياً شاملة العطلات الأسبوعية.',
        'faqs' => [
            ['q' => 'هل يحسب يوم الغياب بقسمة الراتب على 30 أم على 22؟', 'a' => 'في أنظمة العمل للشركات الرسمية يتم حساب قيمة اليوم بقسمة الراتب على 30 يوماً لأن الإجازات الأسبوعية مدفوعة الأجر ضمن الراتب الشهري.']
        ],
        'related' => ['hourly-wage-calculator', 'net-salary-calculator', 'overtime-calculator']
    ],

    'employee-cost-calculator' => [
        'title' => 'حاسبة تكلفة الموظف الحقيقية على الشركة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'baseSalary', 'label' => 'الراتب الأساسي للموظف', 'type' => 'number', 'default' => '7000', 'min' => '0', 'step' => '100'],
            ['id' => 'housingAllow', 'label' => 'بدل السكن الشهري', 'type' => 'number', 'default' => '1500', 'min' => '0', 'step' => '50'],
            ['id' => 'transAllow', 'label' => 'بدل المواصلات الشهري', 'type' => 'number', 'default' => '500', 'min' => '0', 'step' => '50'],
            ['id' => 'employerGosi', 'label' => 'حصة الشركة في التأمينات الاجتماعية (%)', 'type' => 'number', 'default' => '11.75', 'min' => '0', 'max' => '50', 'step' => '0.25'],
            ['id' => 'healthInsurance', 'label' => 'التأمين الطبي السنوي للموظف', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '100'],
            ['id' => 'workPermitFees', 'label' => 'رسوم حكومية ورخص عمل سنوية (إن وجدت)', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '100'],
            ['id' => 'equipmentCosts', 'label' => 'تكاليف المعدات والبرامج والمكتب شهرياً', 'type' => 'number', 'default' => '400', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const base = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const housing = Math.max(0, parseFloat(document.getElementById('housingAllow').value) || 0);
            const trans = Math.max(0, parseFloat(document.getElementById('transAllow').value) || 0);
            const gosiRate = Math.max(0, parseFloat(document.getElementById('employerGosi').value) || 0) / 100;
            const healthAnnual = Math.max(0, parseFloat(document.getElementById('healthInsurance').value) || 0);
            const feesAnnual = Math.max(0, parseFloat(document.getElementById('workPermitFees').value) || 0);
            const officeMonthly = Math.max(0, parseFloat(document.getElementById('equipmentCosts').value) || 0);
            const curr = getSelectedCurrency();

            const directSalary = base + housing + trans;
            const monthlyGosi = (base + housing) * gosiRate;
            const monthlyHealth = healthAnnual / 12;
            const monthlyFees = feesAnnual / 12;
            const eosProvision = (base + housing) / 24; // مخصص نهاية خدمة تقديري نصف شهر سنوياً
            const annualLeaveProvision = directSalary / 12; // مخصص شهر إجازة سنوية

            const totalMonthlyCost = directSalary + monthlyGosi + monthlyHealth + monthlyFees + officeMonthly + eosProvision + annualLeaveProvision;
            const totalAnnualCost = totalMonthlyCost * 12;
            const costMultiplier = directSalary > 0 ? (totalMonthlyCost / directSalary) : 1;

            setPrimaryResult(formatMoney(totalMonthlyCost, curr), 'التكلفة الحقيقية للموظف شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الراتب المباشر للموظف', value: formatMoney(directSalary, curr), color: '#3b82f6' },
                { label: 'التكلفة الإجمالية سنوياً', value: formatMoney(totalAnnualCost, curr), color: '#10b981' },
                { label: 'مضاعف التكلفة مقارنة بالراتب', value: costMultiplier.toFixed(2) + 'x', color: '#f59e0b' },
                { label: 'مخصصات سنوية وإجازات ونهاية خدمة', value: formatMoney((eosProvision + annualLeaveProvision) * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلف الموظف الشركة فعلياً <strong>\${formatMoney(totalMonthlyCost, curr)}</strong> شهرياً، أي ما يعادل <strong>\${costMultiplier.toFixed(2)} ضعف</strong> راتبه الإجمالي المعلن، بسبب التأمينات ومخصصات الإجازات ومكافأة نهاية الخدمة والتأمين الطبي.</p>
            `);
        ",
        'points' => [
            'الراتب المباشر = الراتب الأساسي + البدلات النقدية.',
            'حصة الشركة في التأمينات تحسب غالباً على الأساسي + بدل السكن.',
            'مخصص نهاية الخدمة والإجازة السنوية يتم حسابهما كالتزامات شهرية مستحقة على الشركة.',
            'تضاف التكاليف غير المباشرة: الرعاية الصحية، رخص العمل، والأجهزة والمساحة المكتبية.'
        ],
        'assumptions' => 'مكافأة نهاية الخدمة محتسبة على أساس نصف راتب شهري لكل سنة من السنوات الخمس الأولى، مع شهر إجازة سنوية مدفوعة.',
        'faqs' => [
            ['q' => 'لماذا تزيد تكلفة الموظف على راتبه بنسبة 25% إلى 50%؟', 'a' => 'لأن صاحب العمل يتحمل قانونياً اشتراكات التأمينات، التأمين الصحي، مخصصات مكافأة نهاية الخدمة، أيام الإجازات المدفوعة، بالإضافة إلى تراخيص العمل وتجهيزات بيئة العمل.']
        ],
        'related' => ['employee-hourly-cost-calculator', 'net-salary-calculator', 'freelancer-hourly-rate-calculator']
    ],

    'employee-hourly-cost-calculator' => [
        'title' => 'حاسبة تكلفة الساعة للموظف',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalMonthlyCost', 'label' => 'التكلفة الحقيقية الشهرية للموظف على الشركة', 'type' => 'number', 'default' => '11500', 'min' => '0', 'step' => '100'],
            ['id' => 'workingDaysMonth', 'label' => 'أيام العمل الفعلية في الشهر (بدون الإجازات)', 'type' => 'number', 'default' => '21', 'min' => '1', 'max' => '31', 'step' => '1'],
            ['id' => 'dailyHours', 'label' => 'ساعات العمل اليومية الرسمية', 'type' => 'number', 'default' => '8', 'min' => '1', 'max' => '24', 'step' => '1'],
            ['id' => 'productiveRatio', 'label' => 'نسبة الإنتاجية الفعلية من ساعات الدوام (%)', 'type' => 'number', 'default' => '75', 'min' => '10', 'max' => '100', 'step' => '5'],
        ],
        'calcJs' => "
            const monthlyCost = Math.max(0, parseFloat(document.getElementById('totalMonthlyCost').value) || 0);
            const days = Math.max(1, parseFloat(document.getElementById('workingDaysMonth').value) || 21);
            const hours = Math.max(1, parseFloat(document.getElementById('dailyHours').value) || 8);
            const prod = Math.max(10, Math.min(100, parseFloat(document.getElementById('productiveRatio').value) || 75)) / 100;
            const curr = getSelectedCurrency();

            const totalHours = days * hours;
            const nominalHourlyCost = totalHours > 0 ? (monthlyCost / totalHours) : 0;
            const productiveHours = totalHours * prod;
            const realProductiveHourlyCost = productiveHours > 0 ? (monthlyCost / productiveHours) : 0;

            setPrimaryResult(formatMoney(realProductiveHourlyCost, curr), 'تكلفة ساعة العمل المنتجة الحقيقية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الساعة الاسمية (ساعات الدوام)', value: formatMoney(nominalHourlyCost, curr), color: '#3b82f6' },
                { label: 'ساعات العمل الاسمية شهرياً', value: totalHours + ' ساعة', color: '#10b981' },
                { label: 'ساعات الإنتاجية الصافية شهرياً', value: productiveHours.toFixed(1) + ' ساعة', color: '#f59e0b' },
                { label: 'فارق التكلفة بسبب وقت الضياع/الاستراحات', value: formatMoney(realProductiveHourlyCost - nominalHourlyCost, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>بينما تكلف ساعة الحضور الرسمية للموظف <strong>\${formatMoney(nominalHourlyCost, curr)}</strong>، فإن التكلفة الفعلية لكل ساعة إنجاز حقيقية تبلغ <strong>\${formatMoney(realProductiveHourlyCost, curr)}</strong> بافتراض إنتاجية \${(prod*100).toFixed(0)}%.</p>
            `);
        ",
        'points' => [
            'ساعات الدوام الشهرية = أيام العمل الفعلية × ساعات العمل اليومية.',
            'تكلفة الساعة الاسمية = إجمالي التكلفة الشهرية للموظف ÷ ساعات الدوام الشهرية.',
            'تكلفة الساعة الإنتاجية = إجمالي التكلفة الشهرية ÷ (ساعات الدوام × نسبة الإنتاجية).'
        ],
        'assumptions' => 'تشير الدراسات الإدارية إلى أن الموظف المكتبي ينتج فعلياً بين 60% إلى 80% من ساعات حضوره الرسمية بسبب الاجتماعات وفترات الراحة والتواصل.',
        'faqs' => [
            ['q' => 'كيف تفيد هذه الحاسبة في تسعير مشاريع الشركات؟', 'a' => 'عند تسعير مشروع لعميل، يجب احتساب تكلفة ساعة الموظف الحقيقية المنتجة وليس فقط راتبه بالساعة، لضمان عدم تكبد الشركة خسائر خفية.']
        ],
        'related' => ['employee-cost-calculator', 'freelancer-hourly-rate-calculator', 'project-hours-calculator']
    ],

    'overtime-calculator' => [
        'title' => 'حاسبة العمل الإضافي',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlySalary', 'label' => 'الراتب الإجمالي المعتمد للحساب', 'type' => 'number', 'default' => '6000', 'min' => '0', 'step' => '50'],
            ['id' => 'monthlyStandardHours', 'label' => 'ساعات العمل القياسية شهرياً', 'type' => 'number', 'default' => '240', 'min' => '100', 'max' => '300', 'step' => '8'],
            ['id' => 'regularOvertimeHours', 'label' => 'ساعات العمل الإضافي في الأيام العادية', 'type' => 'number', 'default' => '15', 'min' => '0', 'step' => '1'],
            ['id' => 'holidayOvertimeHours', 'label' => 'ساعات العمل الإضافي في العطلات الرسمية والأعياد', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '1'],
            ['id' => 'regularMultiplier', 'label' => 'معامل الإضافي العادي (غالباً 1.5)', 'type' => 'number', 'default' => '1.5', 'min' => '1', 'max' => '3', 'step' => '0.1'],
            ['id' => 'holidayMultiplier', 'label' => 'معامل إضافي العطلات (غالباً 2.0)', 'type' => 'number', 'default' => '2.0', 'min' => '1', 'max' => '3', 'step' => '0.1'],
        ],
        'calcJs' => "
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const standardHours = Math.max(1, parseFloat(document.getElementById('monthlyStandardHours').value) || 240);
            const regHours = Math.max(0, parseFloat(document.getElementById('regularOvertimeHours').value) || 0);
            const holHours = Math.max(0, parseFloat(document.getElementById('holidayOvertimeHours').value) || 0);
            const regMult = Math.max(1, parseFloat(document.getElementById('regularMultiplier').value) || 1.5);
            const holMult = Math.max(1, parseFloat(document.getElementById('holidayMultiplier').value) || 2.0);
            const curr = getSelectedCurrency();

            const baseHourlyRate = salary / standardHours;
            const regOvertimePay = regHours * (baseHourlyRate * regMult);
            const holOvertimePay = holHours * (baseHourlyRate * holMult);
            const totalOvertimePay = regOvertimePay + holOvertimePay;
            const grandTotalSalary = salary + totalOvertimePay;

            setPrimaryResult(formatMoney(totalOvertimePay, curr), 'مستحقات العمل الإضافي');
            showResultArea();

            setDetailStats([
                { label: 'أجر ساعة العمل الأساسية', value: formatMoney(baseHourlyRate, curr), color: '#3b82f6' },
                { label: 'أجر ساعة الإضافي العادي (' + regMult + 'x)', value: formatMoney(baseHourlyRate * regMult, curr), color: '#10b981' },
                { label: 'أجر ساعة إضافي العطلات (' + holMult + 'x)', value: formatMoney(baseHourlyRate * holMult, curr), color: '#f59e0b' },
                { label: 'إجمالي الراتب مع الإضافي', value: formatMoney(grandTotalSalary, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي ساعات العمل الإضافي: <strong>\${regHours + holHours} ساعة</strong>. تضاف <strong>\${formatMoney(totalOvertimePay, curr)}</strong> إلى راتبك الشهري ليصبح المجموع <strong>\${formatMoney(grandTotalSalary, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'أجر الساعة الأساسي = الراتب المعتمد ÷ عدد الساعات الشهرية النظامية (عادة 240 ساعة لنظام 8 ساعات/يوم × 30 يوماً).',
            'ساعة العمل الإضافي العادية = أجر الساعة الأساسي + 50% إضافية (أجر الساعة × 1.5) وفقاً لأغلب قوانين العمل العربية.',
            'ساعة العمل في أيام العطلات الأسبوعية والأعياد الرسمية = أجر الساعة × 2.0 (أو 100% زيادة).'
        ],
        'assumptions' => 'ينص نظام العمل السعودي والمصري والإماراتي على أن ساعة الإضافي تعادل أجر الساعة مضافاً إليه 50% من أجره الأساسي على الأقل.',
        'faqs' => [
            ['q' => 'كيف يُحسب الحد الأقصى لساعات الإضافي؟', 'a' => 'تنص قوانين العمل على حد أقصى لساعات الإضافي لا يتجاوز عادة ساعتين إلى 3 ساعات يومياً، أو 720 ساعة سنوياً لحماية صحة العامل وسلامته.']
        ],
        'related' => ['hourly-wage-calculator', 'net-salary-calculator', 'daily-wage-calculator']
    ],

    'commission-calculator' => [
        'title' => 'حاسبة العمولة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'salesAmount', 'label' => 'قيمة المبيعات أو الصفقة', 'type' => 'number', 'default' => '50000', 'min' => '0', 'step' => '100'],
            ['id' => 'commissionRate', 'label' => 'نسبة العمولة (%)', 'type' => 'number', 'default' => '5', 'min' => '0.1', 'max' => '100', 'step' => '0.1'],
            ['id' => 'baseSalary', 'label' => 'الراتب الأساسي الثابت (إن وجد)', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const sales = Math.max(0, parseFloat(document.getElementById('salesAmount').value) || 0);
            const rate = Math.max(0, parseFloat(document.getElementById('commissionRate').value) || 0) / 100;
            const base = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const curr = getSelectedCurrency();

            const commission = sales * rate;
            const totalEarnings = base + commission;

            setPrimaryResult(formatMoney(commission, curr), 'مبلغ العمولة المستحق');
            showResultArea();

            setDetailStats([
                { label: 'نسبة العمولة المطبقة', value: (rate * 100).toFixed(2) + '%', color: '#3b82f6' },
                { label: 'إجمالي المبيعات المحققة', value: formatMoney(sales, curr), color: '#10b981' },
                { label: 'الراتب الثابت', value: formatMoney(base, curr), color: '#f59e0b' },
                { label: 'إجمالي الدخل (الراتب + العمولة)', value: formatMoney(totalEarnings, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>على مبيعات قدرها <strong>\${formatMoney(sales, curr)}</strong> بنسبة عمولة <strong>\${(rate * 100).toFixed(1)}%</strong>، تبلغ عمولتك <strong>\${formatMoney(commission, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'العمولة = قيمة المبيعات × (نسبة العمولة ÷ 100).',
            'إجمالي المستحق = الراتب الأساسي + مبلغ العمولة.'
        ],
        'assumptions' => 'الحساب مبني على عمولة ذات نسبة ثابتة.',
        'faqs' => [
            ['q' => 'هل تحسب العمولة قبل الضريبة أم بعدها؟', 'a' => 'المتعارف عليه تجارياً هو احتساب العمولة على صافي قيمة المبيعات بعد استبعاد ضريبة القيمة المضافة ومصاريف الشحن المستردة.']
        ],
        'related' => ['sales-commission-calculator', 'profit-margin-calculator', 'roas-calculator']
    ],

    'sales-commission-calculator' => [
        'title' => 'حاسبة عمولة مندوب المبيعات',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetAmount', 'label' => 'الهدف البيعي الشهري (Target)', 'type' => 'number', 'default' => '100000', 'min' => '1', 'step' => '1000'],
            ['id' => 'actualSales', 'label' => 'المبيعات الفعلية المحققة', 'type' => 'number', 'default' => '120000', 'min' => '0', 'step' => '1000'],
            ['id' => 'baseCommissionRate', 'label' => 'نسبة العمولة الأساسية (%)', 'type' => 'number', 'default' => '3', 'min' => '0', 'max' => '100', 'step' => '0.25'],
            ['id' => 'bonusOverTarget', 'label' => 'نسبة بونص إضافية فوق الهدف (%)', 'type' => 'number', 'default' => '2', 'min' => '0', 'max' => '100', 'step' => '0.25'],
            ['id' => 'baseSalary', 'label' => 'الراتب الأساسي للمندوب', 'type' => 'number', 'default' => '4000', 'min' => '0', 'step' => '100'],
        ],
        'calcJs' => "
            const target = Math.max(1, parseFloat(document.getElementById('targetAmount').value) || 1);
            const actual = Math.max(0, parseFloat(document.getElementById('actualSales').value) || 0);
            const baseRate = Math.max(0, parseFloat(document.getElementById('baseCommissionRate').value) || 0) / 100;
            const bonusRate = Math.max(0, parseFloat(document.getElementById('bonusOverTarget').value) || 0) / 100;
            const salary = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const curr = getSelectedCurrency();

            const achievementRate = (actual / target) * 100;
            let commission = 0;
            let bonus = 0;

            if (actual <= target) {
                commission = actual * baseRate;
            } else {
                commission = target * baseRate;
                const excess = actual - target;
                bonus = excess * (baseRate + bonusRate);
            }

            const totalCommission = commission + bonus;
            const totalIncome = salary + totalCommission;

            setPrimaryResult(formatMoney(totalCommission, curr), 'إجمالي العمولة والمكافأة');
            showResultArea();

            setDetailStats([
                { label: 'نسبة تحقيق الهدف (Target)', value: achievementRate.toFixed(1) + '%', color: achievementRate >= 100 ? '#10b981' : '#f59e0b' },
                { label: 'العمولة الأساسية', value: formatMoney(commission, curr), color: '#3b82f6' },
                { label: 'حافز التميز الإضافي (Bonus)', value: formatMoney(bonus, curr), color: '#8b5cf6' },
                { label: 'إجمالي دخل المندوب لهذا الشهر', value: formatMoney(totalIncome, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>حقق المندوب <strong>\${achievementRate.toFixed(1)}%</strong> من هدفه البيعي. إجمالي المستحقات تشمل <strong>\${formatMoney(salary, curr)}</strong> راتباً ثابتاً + <strong>\${formatMoney(totalCommission, curr)}</strong> عمولات وحوافز.</p>
            `);
        ",
        'points' => [
            'نسبة الإنجاز = (المبيعات الفعلية ÷ الهدف البيعي) × 100.',
            'عند تجاوز 100% من الهدف، تُحسب المبيعات الإضافية بنسبة بونص تحفيزية أعلى.',
            'إجمالي دخل المندوب = الراتب الثابت + العمولة الأساسية + بونص التميز.'
        ],
        'assumptions' => 'النظام التحفيزي المعتمد يكافئ المندوب بنسبة أعلى على المبيعات التي تتخطى التارجت المطلوب.',
        'faqs' => [
            ['q' => 'ماذا يحدث إذا لم يحقق المندوب هدفه بالكامل؟', 'a' => 'يعتمد على سياسة الشركة؛ بعض الشركات تصرف العمولة بنسبة الإنجاز، وأخرى تشترط تحقيق 80% كحد أدنى لبدء استحقاق العمولة.']
        ],
        'related' => ['commission-calculator', 'profit-margin-calculator', 'roas-calculator']
    ],

    'profit-margin-calculator' => [
        'title' => 'حاسبة هامش الربح',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'costPrice', 'label' => 'سعر التكلفة (Cost)', 'type' => 'number', 'default' => '70', 'min' => '0', 'step' => '0.5'],
            ['id' => 'sellingPrice', 'label' => 'سعر البيع (Selling Price)', 'type' => 'number', 'default' => '100', 'min' => '0', 'step' => '0.5'],
        ],
        'calcJs' => "
            const cost = Math.max(0, parseFloat(document.getElementById('costPrice').value) || 0);
            const sell = Math.max(0, parseFloat(document.getElementById('sellingPrice').value) || 0);
            const curr = getSelectedCurrency();

            const profit = sell - cost;
            const margin = sell > 0 ? ((profit / sell) * 100) : 0;
            const markup = cost > 0 ? ((profit / cost) * 100) : 0;

            setPrimaryResult(margin.toFixed(2) + '%', 'هامش الربح (Profit Margin)');
            showResultArea();

            setDetailStats([
                { label: 'مبلغ الربح لكل قطعة', value: formatMoney(profit, curr), color: profit >= 0 ? '#10b981' : '#ef4444' },
                { label: 'نسبة الزيادة على التكلفة (Markup)', value: markup.toFixed(2) + '%', color: '#3b82f6' },
                { label: 'سعر التكلفة', value: formatMoney(cost, curr), color: '#6b7280' },
                { label: 'سعر البيع النهائي', value: formatMoney(sell, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>عند بيع منتج تكلفته <strong>\${formatMoney(cost, curr)}</strong> بسعر <strong>\${formatMoney(sell, curr)}</strong>، تربح <strong>\${formatMoney(profit, curr)}</strong>، بنسبة هامش ربح <strong>\${margin.toFixed(2)}%</strong> من سعر البيع، وزيادة <strong>\${markup.toFixed(2)}%</strong> فوق التكلفة.</p>
            `);
        ",
        'points' => [
            'الربح الإجمالي = سعر البيع - سعر التكلفة.',
            'هامش الربح (Margin) = (الربح ÷ سعر البيع) × 100. (يعبر عن نسبة الربح من كل ريال/دولار مبيعات).',
            'نسبة الزيادة (Markup) = (الربح ÷ التكلفة) × 100. (يعبر عن كم أضفت فوق تكلفة الشراء).'
        ],
        'assumptions' => 'لا يشمل هذا الحساب المصاريف الإدارية والتشغيلية الإضافية (يعبر عن الهامش الإجمالي Gross Margin).',
        'faqs' => [
            ['q' => 'ما هو الفرق الجوهري بين Margin و Markup؟', 'a' => 'الـ Margin يقيس كم ربحت كنسبة من سعر البيع، ولا يمكن أن يتجاوز 100%، بينما الـ Markup يقيس كم أضفت فوق التكلفة ويمكن أن يكون 150% أو 300% أو أكثر.']
        ],
        'related' => ['net-profit-margin-calculator', 'product-selling-price-calculator', 'break-even-calculator']
    ],

    'net-profit-margin-calculator' => [
        'title' => 'حاسبة هامش الربح الحقيقي بعد المصاريف',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalRevenue', 'label' => 'إجمالي الإيرادات / المبيعات', 'type' => 'number', 'default' => '100000', 'min' => '0', 'step' => '1000'],
            ['id' => 'cogs', 'label' => 'تكلفة البضاعة المباعة (COGS)', 'type' => 'number', 'default' => '45000', 'min' => '0', 'step' => '500'],
            ['id' => 'operatingExpenses', 'label' => 'المصاريف التشغيلية (رواتب، إيجار، برامج)', 'type' => 'number', 'default' => '25000', 'min' => '0', 'step' => '500'],
            ['id' => 'marketingExpenses', 'label' => 'مصاريف التسويق والإعلانات', 'type' => 'number', 'default' => '10000', 'min' => '0', 'step' => '500'],
            ['id' => 'taxesAndFees', 'label' => 'الضرائب ورسوم الدفع البنكية', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '100'],
        ],
        'calcJs' => "
            const rev = Math.max(0, parseFloat(document.getElementById('totalRevenue').value) || 0);
            const cogs = Math.max(0, parseFloat(document.getElementById('cogs').value) || 0);
            const opex = Math.max(0, parseFloat(document.getElementById('operatingExpenses').value) || 0);
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingExpenses').value) || 0);
            const tax = Math.max(0, parseFloat(document.getElementById('taxesAndFees').value) || 0);
            const curr = getSelectedCurrency();

            const grossProfit = rev - cogs;
            const grossMargin = rev > 0 ? (grossProfit / rev) * 100 : 0;
            const totalExpenses = cogs + opex + mkt + tax;
            const netProfit = rev - totalExpenses;
            const netMargin = rev > 0 ? (netProfit / rev) * 100 : 0;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الربح الفعلي');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي (Net Margin)', value: netMargin.toFixed(2) + '%', color: netMargin >= 10 ? '#10b981' : '#f59e0b' },
                { label: 'إجمالي الربح الأولي (Gross Profit)', value: formatMoney(grossProfit, curr), color: '#3b82f6' },
                { label: 'هامش الربح الأولي (Gross Margin)', value: grossMargin.toFixed(2) + '%', color: '#8b5cf6' },
                { label: 'إجمالي المصاريف الشاملة', value: formatMoney(totalExpenses, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>من إجمالي إيرادات <strong>\${formatMoney(rev, curr)}</strong>، يتبقى للنشاط <strong>\${formatMoney(netProfit, curr)}</strong> كربح صافٍ، أي بهامش <strong>\${netMargin.toFixed(2)}%</strong> بعد دفع جميع الالتزامات.</p>
            `);
        ",
        'points' => [
            'الربح الإجمالي (Gross Profit) = الإيرادات - تكلفة البضاعة المباعة.',
            'صافي الربح (Net Profit) = الإيرادات - (تكلفة البضاعة + المصاريف التشغيلية + الإعلانات + الضرائب والرسوم).',
            'هامش صافي الربح = (صافي الربح ÷ إجمالي الإيرادات) × 100.'
        ],
        'assumptions' => 'هامش الربح الصافي الممتاز يتراوح عموماً بين 15% إلى 25% حسب قطاع العمل.',
        'faqs' => [
            ['q' => 'ما هو الهامش الصافي الجيد للمتاجر الإلكترونية؟', 'a' => 'في التجارة الإلكترونية، يُعتبر هامش الربح الصافي بين 10% إلى 20% أداءً صحياً ومستداماً بعد احتساب الإعلانات والشحن وبوابات الدفع.']
        ],
        'related' => ['profit-margin-calculator', 'break-even-calculator', 'ecommerce-profit-calculator']
    ],

    'break-even-calculator' => [
        'title' => 'حاسبة نقطة التعادل',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'fixedCosts', 'label' => 'التكاليف الثابتة شهرياً (إيجار، رواتب، اشتراكات)', 'type' => 'number', 'default' => '15000', 'min' => '0', 'step' => '500'],
            ['id' => 'unitPrice', 'label' => 'سعر بيع الوحدة أو المنتج', 'type' => 'number', 'default' => '200', 'min' => '1', 'step' => '5'],
            ['id' => 'unitVariableCost', 'label' => 'التكلفة المتغيرة للوحدة (شراء، تغليف، عمولة بيع)', 'type' => 'number', 'default' => '80', 'min' => '0', 'step' => '5'],
        ],
        'calcJs' => "
            const fixed = Math.max(0, parseFloat(document.getElementById('fixedCosts').value) || 0);
            const price = Math.max(1, parseFloat(document.getElementById('unitPrice').value) || 1);
            const varCost = Math.max(0, parseFloat(document.getElementById('unitVariableCost').value) || 0);
            const curr = getSelectedCurrency();

            const contributionMargin = price - varCost;
            if (contributionMargin <= 0) {
                alert('سعر بيع الوحدة يجب أن يكون أكبر من تكلفتها المتغيرة لتحقيق نقطة التعادل!');
                return;
            }

            const breakEvenUnits = Math.ceil(fixed / contributionMargin);
            const breakEvenRevenue = breakEvenUnits * price;
            const cmRatio = (contributionMargin / price) * 100;

            setPrimaryResult(formatNumber(breakEvenUnits, 0) + ' قطعة / وحدة', 'كمية التعادل المطلوبة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'قيمة مبيعات التعادل النقدية', value: formatMoney(breakEvenRevenue, curr), color: '#3b82f6' },
                { label: 'هامش المساهمة لكل وحدة (CM)', value: formatMoney(contributionMargin, curr), color: '#10b981' },
                { label: 'نسبة هامش المساهمة', value: cmRatio.toFixed(1) + '%', color: '#8b5cf6' },
                { label: 'التكاليف الثابتة المغطاة', value: formatMoney(fixed, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تحتاج لبيع <strong>\${breakEvenUnits} وحدة</strong> شهرياً بمبيعات إجمالية <strong>\${formatMoney(breakEvenRevenue, curr)}</strong> لتغطية كافة مصاريفك دون ربح أو خسارة. أي مبيعات إضافية فوق هذا الرقم تمثل أرباحاً صافية.</p>
            `);
        ",
        'points' => [
            'هامش المساهمة للوحدة = سعر البيع - التكلفة المتغيرة.',
            'نقطة التعادل بالوحدات = التكاليف الثابتة ÷ هامش المساهمة للوحدة.',
            'نقطة التعادل بالقيمة النقدية = عدد وحدات التعادل × سعر البيع.'
        ],
        'assumptions' => 'التكاليف الثابتة لا تتغير مع حجم الإنتاج والمبيعات ضمن النطاق التشغيلي الحالي.',
        'faqs' => [
            ['q' => 'ماذا أفعل إذا كانت نقطة التعادل صعبة التحقيق؟', 'a' => 'يمكنك إما خفض التكاليف الثابتة (مثل مساحة مكتب أصغر)، أو خفض التكلفة المتغيرة بالتفاوض مع الموردين، أو رفع سعر بيع الوحدة مع تحسين قيمتها للعميل.']
        ],
        'related' => ['product-selling-price-calculator', 'profit-margin-calculator', 'net-profit-margin-calculator']
    ],

    'product-selling-price-calculator' => [
        'title' => 'حاسبة سعر بيع المنتج',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'productCost', 'label' => 'تكلفة شراء أو تصنيع المنتج', 'type' => 'number', 'default' => '80', 'min' => '0', 'step' => '1'],
            ['id' => 'shippingCost', 'label' => 'تكلفة الشحن والتغليف للقطعة', 'type' => 'number', 'default' => '20', 'min' => '0', 'step' => '1'],
            ['id' => 'marketingPerUnit', 'label' => 'تكلفة الإعلان المتوقعة لكل بيعة (CPA)', 'type' => 'number', 'default' => '25', 'min' => '0', 'step' => '1'],
            ['id' => 'targetProfitMargin', 'label' => 'هامش الربح الصافي المستهدف (%)', 'type' => 'number', 'default' => '25', 'min' => '1', 'max' => '90', 'step' => '1'],
            ['id' => 'vatRate', 'label' => 'نسبة ضريبة القيمة المضافة (%) إن وجدت', 'type' => 'number', 'default' => '15', 'min' => '0', 'max' => '50', 'step' => '1'],
        ],
        'calcJs' => "
            const cost = Math.max(0, parseFloat(document.getElementById('productCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingCost').value) || 0);
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingPerUnit').value) || 0);
            const margin = Math.max(1, Math.min(90, parseFloat(document.getElementById('targetProfitMargin').value) || 25)) / 100;
            const vat = Math.max(0, parseFloat(document.getElementById('vatRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const totalUnitCost = cost + ship + mkt;
            // Selling price before VAT to achieve target margin: Price = Cost / (1 - margin)
            const priceBeforeVat = totalUnitCost / (1 - margin);
            const profitPerUnit = priceBeforeVat - totalUnitCost;
            const vatAmount = priceBeforeVat * vat;
            const finalSellingPrice = priceBeforeVat + vatAmount;

            setPrimaryResult(formatMoney(finalSellingPrice, curr), 'سعر البيع المقترح للمستهلك شامل الضريبة');
            showResultArea();

            setDetailStats([
                { label: 'سعر البيع قبل الضريبة', value: formatMoney(priceBeforeVat, curr), color: '#3b82f6' },
                { label: 'صافي الربح في كل قطعة', value: formatMoney(profitPerUnit, curr), color: '#10b981' },
                { label: 'قيمة ضريبة القيمة المضافة', value: formatMoney(vatAmount, curr), color: '#f59e0b' },
                { label: 'إجمالي التكلفة الشاملة للقطعة', value: formatMoney(totalUnitCost, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>لتحقيق هامش ربح <strong>\${(margin * 100).toFixed(0)}%</strong> بعد مصاريف الإعلانات والشحن، يُوصى بتسعير المنتج عند <strong>\${formatMoney(finalSellingPrice, curr)}</strong> شامل الضريبة، لتربح <strong>\${formatMoney(profitPerUnit, curr)}</strong> صافياً في كل مبيعة.</p>
            `);
        ",
        'points' => [
            'التكلفة الإجمالية = تكلفة المنتج + الشحن والتغليف + تكلفة الاستحواذ الإعلاني.',
            'سعر البيع قبل الضريبة = التكلفة الإجمالية ÷ (1 - هامش الربح المستهدف).',
            'سعر البيع النهائي = السعر قبل الضريبة × (1 + نسبة الضريبة).'
        ],
        'assumptions' => 'التسعير يعتمد على تحقيق نسبة الربح المستهدفة من إجمالي سعر البيع، وليس مجرد إضافة النسبة فوق التكلفة.',
        'faqs' => [
            ['q' => 'لماذا نقسم على (1 - الهامش) بدلاً من ضرب التكلفة في النسبة؟', 'a' => 'لأن هامش الربح يُحسب كنسبة من سعر البيع النهائي؛ فإذا أردت هامش 20% وتكلفتك 80، فإن 80 ÷ 0.8 = 100، وبهذا يكون ربحك 20 من 100 وهو ما يمثل 20% فعلاً.']
        ],
        'related' => ['real-product-cost-calculator', 'profit-margin-calculator', 'ecommerce-profit-calculator']
    ],

    'real-product-cost-calculator' => [
        'title' => 'حاسبة تكلفة المنتج الحقيقية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'purchasePrice', 'label' => 'سعر شراء المنتج من المورد', 'type' => 'number', 'default' => '50', 'min' => '0', 'step' => '1'],
            ['id' => 'customsDuty', 'label' => 'رسوم الجمارك والشحن الدولي للقطعة', 'type' => 'number', 'default' => '8', 'min' => '0', 'step' => '0.5'],
            ['id' => 'packagingCost', 'label' => 'تكلفة التغليف والعلب والملصقات', 'type' => 'number', 'default' => '5', 'min' => '0', 'step' => '0.5'],
            ['id' => 'storageCost', 'label' => 'تكلفة التخزين والمناولة لكل قطعة', 'type' => 'number', 'default' => '3', 'min' => '0', 'step' => '0.5'],
            ['id' => 'defectReserveRate', 'label' => 'نسبة مخصص التوالف والمرتجعات (%)', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '30', 'step' => '0.5'],
        ],
        'calcJs' => "
            const buy = Math.max(0, parseFloat(document.getElementById('purchasePrice').value) || 0);
            const customs = Math.max(0, parseFloat(document.getElementById('customsDuty').value) || 0);
            const pack = Math.max(0, parseFloat(document.getElementById('packagingCost').value) || 0);
            const store = Math.max(0, parseFloat(document.getElementById('storageCost').value) || 0);
            const defectRate = Math.max(0, parseFloat(document.getElementById('defectReserveRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const directCost = buy + customs + pack + store;
            const defectCost = directCost * defectRate;
            const realLandedCost = directCost + defectCost;

            setPrimaryResult(formatMoney(realLandedCost, curr), 'التكلفة الحقيقية الكاملة (Landed Cost)');
            showResultArea();

            setDetailStats([
                { label: 'سعر الشراء الأساسي', value: formatMoney(buy, curr), color: '#3b82f6' },
                { label: 'مصاريف جمارك وتغليف وتخزين', value: formatMoney(customs + pack + store, curr), color: '#f59e0b' },
                { label: 'مخصص المرتجعات والتوالف', value: formatMoney(defectCost, curr), color: '#ef4444' },
                { label: 'نسبة الزيادة عن سعر الشراء', value: buy > 0 ? (((realLandedCost - buy) / buy) * 100).toFixed(1) + '%' : '0%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بينما تشتري القطعة بـ <strong>\${formatMoney(buy, curr)}</strong>، فإن تكلفتها الحقيقية بعد الشحن والجمارك والتغليف والتوالف تصل إلى <strong>\${formatMoney(realLandedCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'تكلفة الهبوط (Landed Cost) تشمل سعر الشراء + الشحن الدولي + الجمارك + التغليف والتخزين.',
            'تجاهل نسبة التوالف والمرتجعات يؤدي إلى تآكل أرباح المتجر دون معرفة السبب الحقيقي.'
        ],
        'assumptions' => 'مخصص التوالف يحمي رأس المال من المنتجات التالفة أثناء الشحن أو التي يسترجعها العميل متضررة.',
        'faqs' => [
            ['q' => 'ما أهمية حساب Landed Cost؟', 'a' => 'تسعير المنتجات بناءً على سعر الشراء من المصنع فقط هو الخطأ الأول الذي يتسبب في إفلاس المتاجر الإلكترونية الناشئة.']
        ],
        'related' => ['product-selling-price-calculator', 'shipping-cost-share-calculator', 'ecommerce-profit-calculator']
    ],

    'shipping-cost-share-calculator' => [
        'title' => 'حاسبة تكلفة الشحن ضمن سعر المنتج',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalShipInvoice', 'label' => 'إجمالي فاتورة الشحن للشحنة / الحاوية', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '50'],
            ['id' => 'totalUnits', 'label' => 'إجمالي عدد القطع في الشحنة', 'type' => 'number', 'default' => '500', 'min' => '1', 'step' => '10'],
            ['id' => 'allocationMethod', 'label' => 'طريقة التوزيع', 'type' => 'select', 'options' => [
                'equal' => 'بالتساوي على جميع القطع',
                'weight' => 'حسب الوزن النسبي للقطعة (كجم)'
            ], 'default' => 'equal'],
            ['id' => 'itemWeight', 'label' => 'وزن القطعة الواحدة (كجم) - إذا اخترت الوزن', 'type' => 'number', 'default' => '0.8', 'min' => '0.01', 'step' => '0.05'],
            ['id' => 'totalShipWeight', 'label' => 'إجمالي وزن الشحنة (كجم) - إذا اخترت الوزن', 'type' => 'number', 'default' => '400', 'min' => '1', 'step' => '5'],
        ],
        'calcJs' => "
            const totalShip = Math.max(0, parseFloat(document.getElementById('totalShipInvoice').value) || 0);
            const units = Math.max(1, parseFloat(document.getElementById('totalUnits').value) || 1);
            const method = document.getElementById('allocationMethod').value;
            const curr = getSelectedCurrency();

            let unitShipCost = 0;
            if (method === 'equal') {
                unitShipCost = totalShip / units;
            } else {
                const itemW = Math.max(0.01, parseFloat(document.getElementById('itemWeight').value) || 1);
                const totalW = Math.max(itemW, parseFloat(document.getElementById('totalShipWeight').value) || itemW);
                unitShipCost = totalShip * (itemW / totalW);
            }

            setPrimaryResult(formatMoney(unitShipCost, curr), 'نصيب القطعة الواحدة من الشحن');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي قيمة الشحن للشحنة', value: formatMoney(totalShip, curr), color: '#3b82f6' },
                { label: 'إجمالي عدد المنتجات', value: units + ' قطعة', color: '#10b981' },
                { label: 'طريقة التوزيع المطبقة', value: method === 'equal' ? 'بالتساوي' : 'بحسب الوزن', color: '#8b5cf6' },
                { label: 'تكلفة شحن 100 قطعة', value: formatMoney(unitShipCost * 100, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>يجب تحميل سعر بيع كل قطعة بمقدار <strong>\${formatMoney(unitShipCost, curr)}</strong> لتغطية فاتورة الشحن الإجمالية.</p>
            `);
        ",
        'points' => [
            'التوزيع بالتساوي يصلح للبضائع المتشابهة في الحجم والوزن.',
            'التوزيع بالوزن هو الأدق عندما تحتوي الشحنة على منتجات متفاوتة الثقل والحجم.'
        ],
        'assumptions' => 'التكاليف تشمل الشحن مع أي مصاريف تفريغ ومناولة مرتبطة به.',
        'faqs' => [
            ['q' => 'متى يجب توزيع الشحن بالحجم CBM بدلاً من الوزن؟', 'a' => 'عندما تكون المنتجات خفيفة ولكنها تأخذ حيزاً كبيراً في الشحن الجوي أو البحري (الوزن الحجمي Volumetric Weight).']
        ],
        'related' => ['real-product-cost-calculator', 'product-selling-price-calculator', 'ecommerce-profit-calculator']
    ],

    'ecommerce-profit-calculator' => [
        'title' => 'حاسبة الربح من بيع منتج أونلاين',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'sellingPrice', 'label' => 'سعر بيع المنتج للعميل', 'type' => 'number', 'default' => '180', 'min' => '1', 'step' => '5'],
            ['id' => 'productCost', 'label' => 'تكلفة شراء المنتج مع التغليف', 'type' => 'number', 'default' => '60', 'min' => '0', 'step' => '5'],
            ['id' => 'cpaAdCost', 'label' => 'تكلفة الإعلان لكل طلب مؤكد (CPA)', 'type' => 'number', 'default' => '35', 'min' => '0', 'step' => '1'],
            ['id' => 'shippingFee', 'label' => 'تكلفة شركة الشحن والتوصيل', 'type' => 'number', 'default' => '25', 'min' => '0', 'step' => '1'],
            ['id' => 'paymentFeeRate', 'label' => 'عمولة بوابة الدفع الإلكتروني (%)', 'type' => 'number', 'default' => '2.75', 'min' => '0', 'max' => '10', 'step' => '0.25'],
            ['id' => 'fixedPaymentFee', 'label' => 'رسم بوابة الدفع الثابت لكل عملية', 'type' => 'number', 'default' => '1', 'min' => '0', 'step' => '0.5'],
            ['id' => 'returnRate', 'label' => 'نسبة الإرجاع أو عدم الاستلام (%)', 'type' => 'number', 'default' => '8', 'min' => '0', 'max' => '50', 'step' => '1'],
        ],
        'calcJs' => "
            const price = Math.max(1, parseFloat(document.getElementById('sellingPrice').value) || 1);
            const cost = Math.max(0, parseFloat(document.getElementById('productCost').value) || 0);
            const cpa = Math.max(0, parseFloat(document.getElementById('cpaAdCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingFee').value) || 0);
            const payRate = Math.max(0, parseFloat(document.getElementById('paymentFeeRate').value) || 0) / 100;
            const payFixed = Math.max(0, parseFloat(document.getElementById('fixedPaymentFee').value) || 0);
            const returnR = Math.max(0, parseFloat(document.getElementById('returnRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const paymentGatewayDeduction = (price * payRate) + payFixed;
            // خسارة الشحن عند رجوع الشحنة
            const returnLossPerOrder = returnR * ship;
            const totalDeductions = cost + cpa + ship + paymentGatewayDeduction + returnLossPerOrder;
            const netProfit = price - totalDeductions;
            const netMargin = (netProfit / price) * 100;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الربح الفعلي لكل طلب');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي', value: netMargin.toFixed(1) + '%', color: netMargin > 0 ? '#10b981' : '#ef4444' },
                { label: 'رسوم بوابة الدفع', value: formatMoney(paymentGatewayDeduction, curr), color: '#3b82f6' },
                { label: 'تكلفة المرتجعات المقدرة', value: formatMoney(returnLossPerOrder, curr), color: '#f59e0b' },
                { label: 'صافي ربح 100 طلب شهرياً', value: formatMoney(netProfit * 100, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>من سعر بيع <strong>\${formatMoney(price, curr)}</strong>، يتبقى لك ربح حقيقي قدره <strong>\${formatMoney(netProfit, curr)}</strong> بعد خصم المنتج، الإعلانات، الشحن، وبوابات الدفع ومخاطر الإرجاع.</p>
            `);
        ",
        'points' => [
            'صافي الربح = سعر البيع - (تكلفة المنتج + الإعلانات + الشحن + رسوم بوابة الدفع + أثر المرتجعات).',
            'عند إرجاع العميل للطلب، يتحمل المتجر عادة تكلفة شحن الذهاب والإياب دون تحقيق بيعة.'
        ],
        'assumptions' => 'نسبة المرتجعات تؤثر مباشرة على متوسط تكلفة الشحن لكل طلب مكتمل.',
        'faqs' => [
            ['q' => 'كيف أخفض تكلفة الاستحواذ على الطلب (CPA)؟', 'a' => 'عبر تحسين متجرك لزيادة نسبة التحويل (CRO)، واستخدام عروض الحزم (Bundles) لرفع متوسط قيمة السلة الشرائية.']
        ],
        'related' => ['product-selling-price-calculator', 'roas-calculator', 'gateway-shipping-profit-calculator']
    ],

    'discount-tax-calculator' => [
        'title' => 'حاسبة الخصم والضريبة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'originalPrice', 'label' => 'السعر الأصلي', 'type' => 'number', 'default' => '500', 'min' => '0', 'step' => '10'],
            ['id' => 'discountPercent', 'label' => 'نسبة الخصم (%)', 'type' => 'number', 'default' => '20', 'min' => '0', 'max' => '100', 'step' => '1'],
            ['id' => 'taxPercent', 'label' => 'نسبة الضريبة (%) إن وجدت', 'type' => 'number', 'default' => '15', 'min' => '0', 'max' => '50', 'step' => '1'],
            ['id' => 'taxOrder', 'label' => 'ترتيب احتساب الضريبة', 'type' => 'select', 'options' => [
                'after_discount' => 'تطبيق الضريبة بعد الخصم (المعيار التجاري الصحيح)',
                'before_discount' => 'تطبيق الضريبة على السعر الأصلي قبل الخصم'
            ], 'default' => 'after_discount'],
        ],
        'calcJs' => "
            const orig = Math.max(0, parseFloat(document.getElementById('originalPrice').value) || 0);
            const discRate = Math.max(0, Math.min(100, parseFloat(document.getElementById('discountPercent').value) || 0)) / 100;
            const taxRate = Math.max(0, parseFloat(document.getElementById('taxPercent').value) || 0) / 100;
            const order = document.getElementById('taxOrder').value;
            const curr = getSelectedCurrency();

            const discountAmount = orig * discRate;
            const priceAfterDiscount = orig - discountAmount;
            let taxAmount = 0;
            let finalPrice = 0;

            if (order === 'after_discount') {
                taxAmount = priceAfterDiscount * taxRate;
                finalPrice = priceAfterDiscount + taxAmount;
            } else {
                taxAmount = orig * taxRate;
                finalPrice = priceAfterDiscount + taxAmount;
            }

            setPrimaryResult(formatMoney(finalPrice, curr), 'السعر النهائي للدفع');
            showResultArea();

            setDetailStats([
                { label: 'مبلغ الخصم (التوفير)', value: formatMoney(discountAmount, curr), color: '#10b981' },
                { label: 'السعر بعد الخصم قبل الضريبة', value: formatMoney(priceAfterDiscount, curr), color: '#3b82f6' },
                { label: 'مبلغ ضريبة القيمة المضافة', value: formatMoney(taxAmount, curr), color: '#f59e0b' },
                { label: 'السعر الأصلي الابتدائي', value: formatMoney(orig, curr), color: '#6b7280' }
            ]);

            setResultContent(`
                <p>وفرت <strong>\${formatMoney(discountAmount, curr)}</strong> بفضل الخصم، والسعر المطلوب سداده نهائياً شامل الضريبة هو <strong>\${formatMoney(finalPrice, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'الخصم يُحسب أولاً من السعر الأساسي.',
            'ضريبة القيمة المضافة تُطبق قانونياً على القيمة الفعلية المدفوعة بعد الخصم.',
            'السعر النهائي = (السعر الأصلي - الخصم) + الضريبة.'
        ],
        'assumptions' => 'الأنظمة الضريبية توجب احتساب الضريبة على السعر النهائي المخفض وليس على السعر قبل الخصم.',
        'faqs' => [
            ['q' => 'هل الضريبة تحسب قبل الخصم أم بعده؟', 'a' => 'حسب لوائح هيئات الزكاة والضرائب العربية، تُفرض ضريبة القيمة المضافة على السعر الفعلي بعد تطبيق أي خصم تجاري ممنوح للعميل.']
        ],
        'related' => ['vat-calculator', 'profit-margin-calculator', 'product-selling-price-calculator']
    ],

    'vat-calculator' => [
        'title' => 'حاسبة ضريبة القيمة المضافة (VAT)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'vatAmountInput', 'label' => 'المبلغ', 'type' => 'number', 'default' => '1000', 'min' => '0', 'step' => '10'],
            ['id' => 'vatRate', 'label' => 'نسبة ضريبة القيمة المضافة (%)', 'type' => 'select', 'options' => [
                '15' => '15% (المملكة العربية السعودية)',
                '5' => '5% (الإمارات، سلطنة عمان، البحرين)',
                '14' => '14% (جمهورية مصر العربية)',
                '16' => '16% (الأردن)',
                '19' => '19% (الجزائر)',
                '20' => '20% (المغرب)',
                'other' => 'نسبة مخصصة أخرى'
            ], 'default' => '15'],
            ['id' => 'customVatRate', 'label' => 'النسبة المخصصة (%) - إذا اخترت أخرى', 'type' => 'number', 'default' => '15', 'min' => '0', 'max' => '50', 'step' => '0.5'],
            ['id' => 'vatCalcType', 'label' => 'نوع الحساب', 'type' => 'select', 'options' => [
                'add' => 'إضافة الضريبة (المبلغ المدخل غير شامل الضريبة)',
                'extract' => 'فصل واستخراج الضريبة (المبلغ المدخل شامل الضريبة بالفعل)'
            ], 'default' => 'add'],
        ],
        'calcJs' => "
            const amt = Math.max(0, parseFloat(document.getElementById('vatAmountInput').value) || 0);
            let rateVal = document.getElementById('vatRate').value;
            let rate = rateVal === 'other' ? parseFloat(document.getElementById('customVatRate').value) || 15 : parseFloat(rateVal);
            rate = Math.max(0, rate) / 100;
            const calcType = document.getElementById('vatCalcType').value;
            const curr = getSelectedCurrency();

            let baseAmount = 0;
            let vatAmount = 0;
            let totalAmount = 0;

            if (calcType === 'add') {
                baseAmount = amt;
                vatAmount = amt * rate;
                totalAmount = amt + vatAmount;
            } else {
                totalAmount = amt;
                baseAmount = amt / (1 + rate);
                vatAmount = totalAmount - baseAmount;
            }

            setPrimaryResult(formatMoney(vatAmount, curr), 'قيمة ضريبة القيمة المضافة');
            showResultArea();

            setDetailStats([
                { label: 'المبلغ الأساسي (قبل الضريبة)', value: formatMoney(baseAmount, curr), color: '#3b82f6' },
                { label: 'قيمة الضريبة المضافة (' + (rate * 100) + '%)', value: formatMoney(vatAmount, curr), color: '#f59e0b' },
                { label: 'المبلغ الإجمالي (شامل الضريبة)', value: formatMoney(totalAmount, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>عند \${calcType === 'add' ? 'إضافة ضريبة' : 'استخراج ضريبة'} بنسبة <strong>\${(rate * 100).toFixed(1)}%</strong> على مبلغ <strong>\${formatMoney(amt, curr)}</strong>، تكون الضريبة <strong>\${formatMoney(vatAmount, curr)}</strong> والإجمالي شامل الضريبة <strong>\${formatMoney(totalAmount, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'لإضافة الضريبة: المبلغ الصافي × (1 + نسبة الضريبة).',
            'لاستخراج الضريبة من مبلغ إجمالي: المبلغ الصافي = المبلغ الإجمالي ÷ (1 + نسبة الضريبة).'
        ],
        'assumptions' => 'النسب الافتراضية مطابقة للنسب الرسمية المعتمدة في كل دولة عربية.',
        'faqs' => [
            ['q' => 'كيف أستخرج الضريبة من فاتورة شاملة الضريبة؟', 'a' => 'اقسم المبلغ الإجمالي على (1 + نسبة الضريبة)، فمثلاً في السعودية اقسم على 1.15 للحصول على المبلغ الأصلي ثم اطرحه من الإجمالي لمعرفة قيمة الضريبة.']
        ],
        'related' => ['discount-tax-calculator', 'invoice-generator', 'net-salary-calculator']
    ],

    'roas-calculator' => [
        'title' => 'حاسبة العائد على الإنفاق الإعلاني (ROAS)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'adSpend', 'label' => 'إجمالي الميزانية الإعلانية المنفقة', 'type' => 'number', 'default' => '2000', 'min' => '1', 'step' => '50'],
            ['id' => 'adRevenue', 'label' => 'المبيعات أو الإيرادات المحققة من الإعلانات', 'type' => 'number', 'default' => '8000', 'min' => '0', 'step' => '100'],
            ['id' => 'profitMargin', 'label' => 'متوسط هامش ربح منتجاتك (%) بدون إعلانات', 'type' => 'number', 'default' => '40', 'min' => '1', 'max' => '100', 'step' => '1'],
        ],
        'calcJs' => "
            const spend = Math.max(1, parseFloat(document.getElementById('adSpend').value) || 1);
            const rev = Math.max(0, parseFloat(document.getElementById('adRevenue').value) || 0);
            const margin = Math.max(1, Math.min(100, parseFloat(document.getElementById('profitMargin').value) || 40)) / 100;
            const curr = getSelectedCurrency();

            const roas = rev / spend;
            const breakEvenRoas = 1 / margin;
            const isProfitable = roas >= breakEvenRoas;
            const estimatedGrossProfit = rev * margin;
            const estimatedNetAdProfit = estimatedGrossProfit - spend;

            setPrimaryResult(roas.toFixed(2) + 'x (' + (roas * 100).toFixed(0) + '%)', 'العائد على الإعلانات (ROAS)');
            showResultArea();

            setDetailStats([
                { label: 'نقطة تعادل الإعلانات (Break-even ROAS)', value: breakEvenRoas.toFixed(2) + 'x', color: '#f59e0b' },
                { label: 'صافي الربح من الحملة الإعلانية', value: formatMoney(estimatedNetAdProfit, curr), color: isProfitable ? '#10b981' : '#ef4444' },
                { label: 'حالة الحملة', value: isProfitable ? 'حملة رابحة ✅' : 'حملة خاسرة ❌', color: isProfitable ? '#10b981' : '#ef4444' },
                { label: 'كل دولار إعلاني جلب مبيعات بـ', value: formatMoney(roas, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>حملتك حققت <strong>\${roas.toFixed(2)}x</strong> عائد إعلاني. بما أن هامش ربح منتجاتك <strong>\${(margin * 100).toFixed(0)}%</strong>، فإن نقطة التعادل لإعلاناتك هي <strong>\${breakEvenRoas.toFixed(2)}x</strong>. صافي ربح الحملة بعد تكلفة المنتج والإعلان: <strong>\${formatMoney(estimatedNetAdProfit, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'ROAS = الإيرادات الناتجة عن الإعلانات ÷ تكلفة الإعلانات.',
            'نقطة تعادل ROAS = 1 ÷ هامش ربح المنتج.',
            'إذا كان الـ ROAS أقل من نقطة التعادل فأنت تخسر مالاً حتى وإن كانت الإيرادات تبدو مرتفعة.'
        ],
        'assumptions' => 'تحقيق مبيعات بإعلانات ذات عائد مرتفع لا يعني بالضرورة ربحاً صافياً إذا كان هامش ربح المنتج منخفضاً.',
        'faqs' => [
            ['q' => 'ما هو الـ ROAS الجيد للمتاجر؟', 'a' => 'لا يوجد رقم ثابت، فالمنتجات بهامش ربح 20% تحتاج ROAS أعلى من 5x للتعادل، بينما المنتجات الرقمية بهامش 80% تربح حتى عند ROAS يعادل 2x.']
        ],
        'related' => ['cac-calculator', 'roi-calculator', 'ecommerce-profit-calculator']
    ],

    'cac-calculator' => [
        'title' => 'حاسبة تكلفة اكتساب العميل (CAC)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'marketingSpend', 'label' => 'إجمالي مصاريف التسويق والإعلانات في الفترة', 'type' => 'number', 'default' => '10000', 'min' => '0', 'step' => '500'],
            ['id' => 'salesSalaries', 'label' => 'رواتب وعمولات فريق المبيعات والتسويق', 'type' => 'number', 'default' => '8000', 'min' => '0', 'step' => '500'],
            ['id' => 'softwareCost', 'label' => 'اشتراكات برامج وأدوات التسويق والـ CRM', 'type' => 'number', 'default' => '1500', 'min' => '0', 'step' => '100'],
            ['id' => 'newCustomers', 'label' => 'عدد العملاء الجدد المكتسبين في نفس الفترة', 'type' => 'number', 'default' => '150', 'min' => '1', 'step' => '5'],
        ],
        'calcJs' => "
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingSpend').value) || 0);
            const sales = Math.max(0, parseFloat(document.getElementById('salesSalaries').value) || 0);
            const soft = Math.max(0, parseFloat(document.getElementById('softwareCost').value) || 0);
            const customers = Math.max(1, parseFloat(document.getElementById('newCustomers').value) || 1);
            const curr = getSelectedCurrency();

            const totalAcquisitionCost = mkt + sales + soft;
            const cac = totalAcquisitionCost / customers;

            setPrimaryResult(formatMoney(cac, curr), 'تكلفة اكتساب العميل الواحد (CAC)');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي تكاليف التسويق والمبيعات', value: formatMoney(totalAcquisitionCost, curr), color: '#3b82f6' },
                { label: 'عدد العملاء الجدد', value: customers + ' عميل', color: '#10b981' },
                { label: 'نصيب الإعلانات المباشرة من كل عميل', value: formatMoney(mkt / customers, curr), color: '#f59e0b' },
                { label: 'نصيب الرواتب والأدوات من كل عميل', value: formatMoney((sales + soft) / customers, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفك إضافة كل عميل جديد إلى نشاطك <strong>\${formatMoney(cac, curr)}</strong> شاملة الإعلانات وجهود فريق المبيعات والبرمجيات المستخدمة.</p>
            `);
        ",
        'points' => [
            'CAC = (إجمالي مصاريف التسويق + رواتب المبيعات + تكاليف الأدوات) ÷ عدد العملاء الجدد.',
            'يجب مقارنة الـ CAC بالقيمة الدائمة للعميل (LTV) للتأكد من ربحية نموذج العمل.'
        ],
        'assumptions' => 'الحساب يشمل العملاء الجدد فقط المكتسبين خلال نفس الفترة الزمنية للمصاريف.',
        'faqs' => [
            ['q' => 'ما هي النسبة الصحية بين LTV و CAC؟', 'a' => 'النسبة المثالية عالمياً هي 3:1 (أن تكون قيمة العميل الدائمة تعادل 3 أضعاف تكلفة اكتسابه على الأقل).']
        ],
        'related' => ['ltv-calculator', 'roas-calculator', 'roi-calculator']
    ],

    'ltv-calculator' => [
        'title' => 'حاسبة القيمة الدائمة للعميل (LTV)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'avgOrderValue', 'label' => 'متوسط قيمة الطلب الواحد (AOV)', 'type' => 'number', 'default' => '250', 'min' => '1', 'step' => '10'],
            ['id' => 'purchaseFrequency', 'label' => 'متوسط عدد مرات الشراء للعميل سنوياً', 'type' => 'number', 'default' => '4', 'min' => '0.1', 'step' => '0.5'],
            ['id' => 'customerLifespan', 'label' => 'متوسط مدة بقاء العميل مع المتجر (سنوات)', 'type' => 'number', 'default' => '3', 'min' => '0.1', 'step' => '0.5'],
            ['id' => 'grossMarginRate', 'label' => 'هامش الربح الإجمالي (%)', 'type' => 'number', 'default' => '35', 'min' => '1', 'max' => '100', 'step' => '1'],
        ],
        'calcJs' => "
            const aov = Math.max(1, parseFloat(document.getElementById('avgOrderValue').value) || 1);
            const freq = Math.max(0.1, parseFloat(document.getElementById('purchaseFrequency').value) || 1);
            const lifespan = Math.max(0.1, parseFloat(document.getElementById('customerLifespan').value) || 1);
            const margin = Math.max(1, Math.min(100, parseFloat(document.getElementById('grossMarginRate').value) || 35)) / 100;
            const curr = getSelectedCurrency();

            const annualSpend = aov * freq;
            const lifetimeRevenue = annualSpend * lifespan;
            const ltvProfit = lifetimeRevenue * margin;

            setPrimaryResult(formatMoney(ltvProfit, curr), 'صافي القيمة الدائمة للعميل (LTV Profit)');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي إيراد العميل خلال دورة حياته', value: formatMoney(lifetimeRevenue, curr), color: '#3b82f6' },
                { label: 'إنفاق العميل السنوي', value: formatMoney(annualSpend, curr), color: '#10b981' },
                { label: 'إجمالي عدد الطلبات المتوقعة للعميل', value: (freq * lifespan).toFixed(1) + ' طلب', color: '#f59e0b' },
                { label: 'الحد الأقصى الموصى به للاستحواذ (CAC)', value: formatMoney(ltvProfit / 3, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>كل عميل جديد تكتسبه يحقق لمتجرك صافي أرباح تراكمية قدرها <strong>\${formatMoney(ltvProfit, curr)}</strong> على مدار <strong>\${lifespan} سنوات</strong> بمعدل <strong>\${freq} طلبات سنوياً</strong>.</p>
            `);
        ",
        'points' => [
            'قيمة العميل السنوية = متوسط قيمة الطلب × عدد مرات الشراء سنوياً.',
            'إجمالي إيراد العميل = القيمة السنوية × عدد سنوات بقاء العميل.',
            'القيمة الدائمة الصافية (LTV) = إجمالي الإيراد × هامش الربح.'
        ],
        'assumptions' => 'الحساب يفترض استقرار متوسط الإنفاق ومعدل التكرار خلال فترة بقاء العميل.',
        'faqs' => [
            ['q' => 'كيف أرفع القيمة الدائمة للعميل LTV؟', 'a' => 'عبر برامج الولاء والمكافآت، وإعادة الاستهداف عبر البريد ورسائل الواتساب، وتقديم اشتراكات دورية وخدمة عملاء مميزة.']
        ],
        'related' => ['cac-calculator', 'roas-calculator', 'ecommerce-profit-calculator']
    ],

    'roi-calculator' => [
        'title' => 'حاسبة العائد على الاستثمار (ROI)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'investAmount', 'label' => 'مبلغ الاستثمار المبدئي', 'type' => 'number', 'default' => '50000', 'min' => '1', 'step' => '1000'],
            ['id' => 'returnAmount', 'label' => 'إجمالي العائد أو القيمة النهائية', 'type' => 'number', 'default' => '75000', 'min' => '0', 'step' => '1000'],
            ['id' => 'investPeriodMonths', 'label' => 'مدة الاستثمار (بالشهور)', 'type' => 'number', 'default' => '12', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const invest = Math.max(1, parseFloat(document.getElementById('investAmount').value) || 1);
            const returned = Math.max(0, parseFloat(document.getElementById('returnAmount').value) || 0);
            const months = Math.max(1, parseFloat(document.getElementById('investPeriodMonths').value) || 12);
            const curr = getSelectedCurrency();

            const netProfit = returned - invest;
            const roi = (netProfit / invest) * 100;
            const annualizedRoi = (Math.pow(returned / invest, 12 / months) - 1) * 100;

            setPrimaryResult(roi.toFixed(2) + '%', 'العائد على الاستثمار الإجمالي (ROI)');
            showResultArea();

            setDetailStats([
                { label: 'صافي الربح الاستثماري', value: formatMoney(netProfit, curr), color: netProfit >= 0 ? '#10b981' : '#ef4444' },
                { label: 'العائد السنوي المركب (Annualized ROI)', value: isFinite(annualizedRoi) ? annualizedRoi.toFixed(2) + '%' : 'غير متاح', color: '#3b82f6' },
                { label: 'مضاعف رأس المال', value: (returned / invest).toFixed(2) + 'x', color: '#8b5cf6' },
                { label: 'معدل الربح الشهري المتوسط', value: (roi / months).toFixed(2) + '%', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>حقق استثمارك ربحاً صافياً قدره <strong>\${formatMoney(netProfit, curr)}</strong> بنسبة عائد <strong>\${roi.toFixed(2)}%</strong> خلال مدة <strong>\${months} شهر</strong>.</p>
            `);
        ",
        'points' => [
            'صافي الربح = العائد الإجمالي - المبلغ المستثمر.',
            'عائد الاستثمار (ROI) = (صافي الربح ÷ رأس المال المستثمر) × 100.',
            'العائد السنوي (Annualized) يوضح الأداء السنوي الحقيقي للمقارنة بين الفرص الاستثمارية متفاوتة المدة.'
        ],
        'assumptions' => 'الحساب لا يخصم الضرائب أو الرسوم الإدارية إن وجدت ما لم تكن مدمجة في العائد النهائي.',
        'faqs' => [
            ['q' => 'ما هو الـ ROI الممتاز للاستثمارات التجارية؟', 'a' => 'يعتمد على المخاطر؛ الاستثمارات العقارية تتراوح عادة بين 8% و 12% سنوياً، بينما المشاريع الناشئة تستهدف 25% إلى 50% أو أكثر لتعويض المخاطرة.']
        ],
        'related' => ['payback-period-calculator', 'roas-calculator', 'break-even-calculator']
    ],

    'payback-period-calculator' => [
        'title' => 'حاسبة فترة استرداد الاستثمار (Payback Period)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'initialInvestment', 'label' => 'رأس المال المبدئي المستثمر', 'type' => 'number', 'default' => '60000', 'min' => '1', 'step' => '1000'],
            ['id' => 'monthlyCashflow', 'label' => 'صافي التدفق النقدي الشهري المتوقع', 'type' => 'number', 'default' => '3500', 'min' => '1', 'step' => '100'],
        ],
        'calcJs' => "
            const invest = Math.max(1, parseFloat(document.getElementById('initialInvestment').value) || 1);
            const cashflow = Math.max(1, parseFloat(document.getElementById('monthlyCashflow').value) || 1);
            const curr = getSelectedCurrency();

            const paybackMonths = invest / cashflow;
            const years = Math.floor(paybackMonths / 12);
            const remainingMonths = Math.ceil(paybackMonths % 12);
            const annualCashflow = cashflow * 12;
            const annualReturnPercent = (annualCashflow / invest) * 100;

            let timeStr = '';
            if (years > 0) timeStr += years + ' سنة ';
            if (remainingMonths > 0) timeStr += 'و ' + remainingMonths + ' شهر';
            if (timeStr === '') timeStr = 'أقل من شهر';

            setPrimaryResult(timeStr + ' (' + paybackMonths.toFixed(1) + ' شهر)', 'فترة استرداد رأس المال');
            showResultArea();

            setDetailStats([
                { label: 'صافي التدفق النقدي السنوي', value: formatMoney(annualCashflow, curr), color: '#3b82f6' },
                { label: 'نسبة الاسترداد السنوي من رأس المال', value: annualReturnPercent.toFixed(1) + '%', color: '#10b981' },
                { label: 'المبلغ المستثمر بالكامل', value: formatMoney(invest, curr), color: '#6b7280' },
                { label: 'عدد الشهور الإجمالي', value: paybackMonths.toFixed(1) + ' شهر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>ستتمكن من استعادة رأس مالك البالغ <strong>\${formatMoney(invest, curr)}</strong> بالكامل خلال <strong>\${timeStr}</strong>، وبعد هذه النقطة تصبح التدفقات النقدية أرباحاً صافية متراكمة.</p>
            `);
        ",
        'points' => [
            'فترة الاسترداد (بالشهور) = رأس المال المستثمر ÷ التدفق النقدي الصافي شهرياً.',
            'فترة الاسترداد بالسنوات = فترة الاسترداد بالشهور ÷ 12.'
        ],
        'assumptions' => 'يفترض تدفقاً نقدياً شهرياً منتظماً وثابتاً.',
        'faqs' => [
            ['q' => 'ما هي فترة الاسترداد المثالية للمشاريع الصغيرة؟', 'a' => 'تعتبر فترة الاسترداد بين سنة إلى 3 سنوات ممتازة لمعظم المشاريع الصغيرة والمتوسطة.']
        ],
        'related' => ['roi-calculator', 'break-even-calculator', 'online-store-profit-calculator']
    ],

    'online-store-profit-calculator' => [
        'title' => 'حاسبة أرباح المتجر الإلكتروني',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlyOrders', 'label' => 'عدد الطلبات الشهرية', 'type' => 'number', 'default' => '300', 'min' => '1', 'step' => '10'],
            ['id' => 'avgBasketValue', 'label' => 'متوسط قيمة السلة (AOV)', 'type' => 'number', 'default' => '220', 'min' => '10', 'step' => '10'],
            ['id' => 'cogsPercent', 'label' => 'نسبة تكلفة البضاعة من الإيرادات (%)', 'type' => 'number', 'default' => '40', 'min' => '5', 'max' => '90', 'step' => '1'],
            ['id' => 'adSpendMonthly', 'label' => 'ميزانية الإعلانات الشهرية', 'type' => 'number', 'default' => '12000', 'min' => '0', 'step' => '500'],
            ['id' => 'platformAndAppFees', 'label' => 'اشتراكات المنصة والتطبيقات والمستودع', 'type' => 'number', 'default' => '3500', 'min' => '0', 'step' => '200'],
            ['id' => 'salariesAndOther', 'label' => 'رواتب ومصاريف إدارية أخرى', 'type' => 'number', 'default' => '6000', 'min' => '0', 'step' => '500'],
        ],
        'calcJs' => "
            const orders = Math.max(1, parseFloat(document.getElementById('monthlyOrders').value) || 1);
            const aov = Math.max(10, parseFloat(document.getElementById('avgBasketValue').value) || 10);
            const cogsRate = Math.max(5, Math.min(90, parseFloat(document.getElementById('cogsPercent').value) || 40)) / 100;
            const ads = Math.max(0, parseFloat(document.getElementById('adSpendMonthly').value) || 0);
            const software = Math.max(0, parseFloat(document.getElementById('platformAndAppFees').value) || 0);
            const salaries = Math.max(0, parseFloat(document.getElementById('salariesAndOther').value) || 0);
            const curr = getSelectedCurrency();

            const totalRevenue = orders * aov;
            const totalCogs = totalRevenue * cogsRate;
            const grossProfit = totalRevenue - totalCogs;
            const totalExpenses = totalCogs + ads + software + salaries;
            const netProfit = totalRevenue - totalExpenses;
            const netMargin = (netProfit / totalRevenue) * 100;
            const profitPerOrder = netProfit / orders;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الأرباح الشهرية للمتجر');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المبيعات الشهرية', value: formatMoney(totalRevenue, curr), color: '#3b82f6' },
                { label: 'هامش الربح الصافي للمتجر', value: netMargin.toFixed(1) + '%', color: netMargin >= 10 ? '#10b981' : '#ef4444' },
                { label: 'صافي الربح لكل طلب', value: formatMoney(profitPerOrder, curr), color: '#8b5cf6' },
                { label: 'إجمالي المصاريف الشهرية', value: formatMoney(totalExpenses, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>عند تحقيق <strong>\${orders} طلب</strong> بمتوسط سلة <strong>\${formatMoney(aov, curr)}</strong>، يحقق متجرك مبيعات <strong>\${formatMoney(totalRevenue, curr)}</strong> وصافي ربح <strong>\${formatMoney(netProfit, curr)}</strong> بعد خصم البضاعة والإعلانات والتشغيل.</p>
            `);
        ",
        'points' => [
            'إجمالي المبيعات = عدد الطلبات × متوسط قيمة السلة.',
            'الربح الإجمالي = المبيعات - تكلفة المنتجات.',
            'صافي الربح = الربح الإجمالي - (الإعلانات + اشتراكات المنصة والتطبيقات + الرواتب والتكاليف الثابتة).'
        ],
        'assumptions' => 'الحساب يقدر المصاريف الشهرية الثابتة والمتغيرة لتحديد سلامة نموذج عمل المتجر.',
        'faqs' => [
            ['q' => 'كيف أزيد أرباح متجري الإلكتروني بدون زيادة ميزانية الإعلانات؟', 'a' => 'ركز على رفع متوسط قيمة السلة الشرائية (AOV) عبر عروض البيع التكميلي (Upselling & Cross-selling)، وتحسين نسبة الاحتفاظ بالعملاء لتكرار الشراء.']
        ],
        'related' => ['ecommerce-profit-calculator', 'gateway-shipping-profit-calculator', 'break-even-calculator']
    ],

    'gateway-shipping-profit-calculator' => [
        'title' => 'حاسبة الأرباح بعد بوابة الدفع والشحن والإعلانات',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'orderTotal', 'label' => 'قيمة الطلب الإجمالية المدفوعة من العميل', 'type' => 'number', 'default' => '250', 'min' => '1', 'step' => '5'],
            ['id' => 'productBaseCost', 'label' => 'تكلفة البضاعة المباعة في الطلب', 'type' => 'number', 'default' => '85', 'min' => '0', 'step' => '5'],
            ['id' => 'shippingCostPaid', 'label' => 'تكلفة الشحن الفعلية على المتجر', 'type' => 'number', 'default' => '28', 'min' => '0', 'step' => '1'],
            ['id' => 'gatewayPercentage', 'label' => 'نسبة عمولة بوابة الدفع (%)', 'type' => 'number', 'default' => '2.5', 'min' => '0', 'max' => '10', 'step' => '0.1'],
            ['id' => 'gatewayFixedFee', 'label' => 'رسم بوابة الدفع الثابت لكل طلب', 'type' => 'number', 'default' => '1', 'min' => '0', 'step' => '0.25'],
            ['id' => 'adSpendPerOrder', 'label' => 'تكلفة الإعلان المخصصة لكل طلب', 'type' => 'number', 'default' => '40', 'min' => '0', 'step' => '1'],
            ['id' => 'vatOnFees', 'label' => 'ضريبة القيمة المضافة على رسوم الدفع والشحن (%)', 'type' => 'number', 'default' => '15', 'min' => '0', 'max' => '25', 'step' => '1'],
        ],
        'calcJs' => "
            const total = Math.max(1, parseFloat(document.getElementById('orderTotal').value) || 1);
            const cogs = Math.max(0, parseFloat(document.getElementById('productBaseCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingCostPaid').value) || 0);
            const gRate = Math.max(0, parseFloat(document.getElementById('gatewayPercentage').value) || 0) / 100;
            const gFixed = Math.max(0, parseFloat(document.getElementById('gatewayFixedFee').value) || 0);
            const ads = Math.max(0, parseFloat(document.getElementById('adSpendPerOrder').value) || 0);
            const feeVat = Math.max(0, parseFloat(document.getElementById('vatOnFees').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const gatewayFee = (total * gRate) + gFixed;
            const gatewayFeeWithVat = gatewayFee * (1 + feeVat);
            const totalDeductions = cogs + ship + gatewayFeeWithVat + ads;
            const netProfit = total - totalDeductions;
            const profitMargin = (netProfit / total) * 100;

            setPrimaryResult(formatMoney(netProfit, curr), 'الصافي المتبقي في جيبك من الطلب');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي للطلب', value: profitMargin.toFixed(1) + '%', color: profitMargin > 0 ? '#10b981' : '#ef4444' },
                { label: 'عمولة بوابة الدفع شاملة ضريبتها', value: formatMoney(gatewayFeeWithVat, curr), color: '#f59e0b' },
                { label: 'إجمالي التكاليف والخصومات', value: formatMoney(totalDeductions, curr), color: '#ef4444' },
                { label: 'المبلغ الإجمالي المحصل', value: formatMoney(total, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>من أصل <strong>\${formatMoney(total, curr)}</strong> يدفعها العميل، تستقطع البوابة <strong>\${formatMoney(gatewayFeeWithVat, curr)}</strong>، ويكلف الشحن <strong>\${formatMoney(ship, curr)}</strong>، والإعلانات <strong>\${formatMoney(ads, curr)}</strong>، ويتبقى لك <strong>\${formatMoney(netProfit, curr)}</strong> صافياً.</p>
            `);
        ",
        'points' => [
            'رسوم بوابات الدفع الإلكترونية تخضع غالباً لضريبة القيمة المضافة ويجب احتسابها.',
            'حساب الصافي بعد الشحن والإعلانات يضمن عدم وجود خسائر خفية في الطلبات الصغيرة.'
        ],
        'assumptions' => 'الرسوم تحتسب بدقة مع الرسوم الثابتة والنسبية المطبقة لدى بوابات الدفع الشهيرة مثل ميسر وتاب وهايبرباي وسترايب.',
        'faqs' => [
            ['q' => 'هل تختلف رسوم بوابة الدفع حسب نوع البطاقة؟', 'a' => 'نعم؛ بطاقات مدى أو بطاقات الخصم المباشر المحلية تكون عمولتها أقل عادة (حوالي 1% إلى 1.75%) مقارنة بالبطاقات الائتمانية الدولية مثل Visa و Mastercard (حوالي 2.5% إلى 3%).']
        ],
        'related' => ['ecommerce-profit-calculator', 'online-store-profit-calculator', 'product-selling-price-calculator']
    ],

    // أدوات العمل الحر والتسعير (الـ 6 أدوات المتبقية من قسم A)
    'freelancer-hourly-rate-calculator' => [
        'title' => 'حاسبة سعر الساعة للفريلانسر',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'targetAnnualIncome', 'label' => 'الدخل السنوي الصافي المستهدف', 'type' => 'number', 'default' => '60000', 'min' => '1000', 'step' => '1000'],
            ['id' => 'annualBusinessCosts', 'label' => 'المصاريف السنوية (أجهزة، برامج، إنترنت، ضرائب)', 'type' => 'number', 'default' => '8000', 'min' => '0', 'step' => '500'],
            ['id' => 'vacationWeeks', 'label' => 'أسابيع الإجازات السنوية والمرضية غير المدفوعة', 'type' => 'number', 'default' => '4', 'min' => '0', 'max' => '20', 'step' => '1'],
            ['id' => 'billableHoursPerWeek', 'label' => 'ساعات العمل المدفوعة أسبوعياً (غير شاملة التسويق)', 'type' => 'number', 'default' => '25', 'min' => '5', 'max' => '60', 'step' => '1'],
        ],
        'calcJs' => "
            const income = Math.max(1000, parseFloat(document.getElementById('targetAnnualIncome').value) || 1000);
            const costs = Math.max(0, parseFloat(document.getElementById('annualBusinessCosts').value) || 0);
            const vacation = Math.max(0, Math.min(20, parseFloat(document.getElementById('vacationWeeks').value) || 4));
            const billableHours = Math.max(5, parseFloat(document.getElementById('billableHoursPerWeek').value) || 25);
            const curr = getSelectedCurrency();

            const workingWeeks = 52 - vacation;
            const annualBillableHours = workingWeeks * billableHours;
            const totalRequiredRevenue = income + costs;
            const minHourlyRate = totalRequiredRevenue / annualBillableHours;
            const recommendedHourlyRate = minHourlyRate * 1.25; // مع هامش أمان 25%

            setPrimaryResult(formatMoney(recommendedHourlyRate, curr), 'سعر الساعة الموصى به (مع هامش أمان)');
            showResultArea();

            setDetailStats([
                { label: 'الحد الأدنى لسعر الساعة', value: formatMoney(minHourlyRate, curr), color: '#f59e0b' },
                { label: 'إجمالي الساعات القابلة للفوترة سنوياً', value: annualBillableHours + ' ساعة', color: '#3b82f6' },
                { label: 'إجمالي الدخل الإجمالي المطلوب سنوياً', value: formatMoney(totalRequiredRevenue, curr), color: '#10b981' },
                { label: 'أسابيع العمل الفعلية', value: workingWeeks + ' أسبوع', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتحقيق صافي دخل سنوي <strong>\${formatMoney(income, curr)}</strong> مع تغطية مصاريفك وإجازاتك، يجب ألا يقل سعر ساعتك عن <strong>\${formatMoney(minHourlyRate, curr)}</strong>، ويُوصى بطلب <strong>\${formatMoney(recommendedHourlyRate, curr)}</strong> لكل ساعة عمل.</p>
            `);
        ",
        'points' => [
            'أسابيع العمل الفعلية = 52 - أسابيع الإجازات.',
            'الساعات القابلة للفوترة سنوياً = أسابيع العمل × الساعات المدفوعة أسبوعياً.',
            'سعر الساعة الأدنى = (الدخل المطلوب + المصاريف السنوية) ÷ إجمالي الساعات القابلة للفوترة.'
        ],
        'assumptions' => 'الفريلانسر لا يقضي 40 ساعة أسبوعياً في العمل المدفوع؛ فهناك ما بين 30% إلى 40% من وقته يضيع في التفاوض والمراسلات والتسويق غير المدفوع.',
        'faqs' => [
            ['q' => 'لماذا يُنصح باحتساب 25 ساعة فقط أسبوعياً للفوترة؟', 'a' => 'لأن المستقل يقضي ساعات إضافية في إدارة الفواتير، التواصل مع العملاء، وتحديث مهاراته، وهذه الساعات غير مدفوعة ويجب تحميل قيمتها على ساعات العمل الفعلي.']
        ],
        'related' => ['freelancer-project-price-calculator', 'hourly-wage-calculator', 'project-hours-calculator']
    ],

    'freelancer-project-price-calculator' => [
        'title' => 'حاسبة سعر المشروع للفريلانسر',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'estimatedHours', 'label' => 'عدد الساعات المقدرة لتنفيذ المشروع', 'type' => 'number', 'default' => '40', 'min' => '1', 'step' => '5'],
            ['id' => 'hourlyRate', 'label' => 'سعر ساعة العمل الخاصة بك', 'type' => 'number', 'default' => '35', 'min' => '1', 'step' => '5'],
            ['id' => 'revisionsBuffer', 'label' => 'هامش التعديلات والمراجعات غير المتوقعة (%)', 'type' => 'number', 'default' => '20', 'min' => '0', 'max' => '100', 'step' => '5'],
            ['id' => 'directExpenses', 'label' => 'مصاريف مباشرة للمشروع (خطوط، إضافات، صور مدفوعة)', 'type' => 'number', 'default' => '50', 'min' => '0', 'step' => '10'],
        ],
        'calcJs' => "
            const hours = Math.max(1, parseFloat(document.getElementById('estimatedHours').value) || 1);
            const rate = Math.max(1, parseFloat(document.getElementById('hourlyRate').value) || 1);
            const buffer = Math.max(0, parseFloat(document.getElementById('revisionsBuffer').value) || 0) / 100;
            const exp = Math.max(0, parseFloat(document.getElementById('directExpenses').value) || 0);
            const curr = getSelectedCurrency();

            const baseWorkCost = hours * rate;
            const bufferCost = baseWorkCost * buffer;
            const totalProjectPrice = baseWorkCost + bufferCost + exp;

            setPrimaryResult(formatMoney(totalProjectPrice, curr), 'سعر عرض السعر المقترح للمشروع');
            showResultArea();

            setDetailStats([
                { label: 'أجر ساعات التنفيذ الأساسية', value: formatMoney(baseWorkCost, curr), color: '#3b82f6' },
                { label: 'مخصص التعديلات والاجتماعات (' + (buffer * 100) + '%)', value: formatMoney(bufferCost, curr), color: '#f59e0b' },
                { label: 'المصاريف المباشرة المشتراة', value: formatMoney(exp, curr), color: '#8b5cf6' },
                { label: 'دفعة البداية الموصى بها (50%)', value: formatMoney(totalProjectPrice / 2, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>بناءً على <strong>\${hours} ساعة عمل</strong> بمعدل <strong>\${formatMoney(rate, curr)}</strong> للساعة، مع احتساب هامش أمان للتعديلات ومصاريف الأدوات، يكون السعر العادل للمشروع <strong>\${formatMoney(totalProjectPrice, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'سعر العمل الأساسي = ساعات العمل المقدرة × سعر ساعتك.',
            'مخصص التعديلات يحميك من طلبات العميل الإضافية وتوسيع نطاق العمل (Scope Creep).',
            'السعر الإجمالي = سعر العمل الأساسي + مخصص التعديلات + التكاليف المباشرة.'
        ],
        'assumptions' => 'يُوصى دائماً بأخذ دفعة مقدمة 50% قبل البدء، و50% عند التسليم النهائي.',
        'faqs' => [
            ['q' => 'هل أخبر العميل بعدد ساعاتي أم أقدم سعراً ثابتاً؟', 'a' => 'يُفضل دائماً تقديم سعر ثابت للمشروع (Fixed Price) مع تحديد نطاق العمل وعدد جولات التعديل، حيث يشعر العميل بالأمان ولا يشغل نفسه بعدد ساعاتك.']
        ],
        'related' => ['freelancer-hourly-rate-calculator', 'project-hours-calculator', 'proposal-generator']
    ],

    'project-hours-calculator' => [
        'title' => 'حاسبة عدد ساعات المشروع (طريقة PERT)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'optimisticHours', 'label' => 'الوقت المتفائل (إذا سار كل شيء بسلاسة وبدون أي عوائق)', 'type' => 'number', 'default' => '20', 'min' => '1', 'step' => '1'],
            ['id' => 'realisticHours', 'label' => 'الوقت الأكثر ترجيحاً وواقعية (التقدير المعتاد)', 'type' => 'number', 'default' => '35', 'min' => '1', 'step' => '1'],
            ['id' => 'pessimisticHours', 'label' => 'الوقت المتشائم (إذا حدثت مشاكل تقنية وتأخيرات غير متوقعة)', 'type' => 'number', 'default' => '65', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const opt = Math.max(1, parseFloat(document.getElementById('optimisticHours').value) || 1);
            const real = Math.max(opt, parseFloat(document.getElementById('realisticHours').value) || opt);
            const pess = Math.max(real, parseFloat(document.getElementById('pessimisticHours').value) || real);

            // PERT Formula: Expected = (O + 4M + P) / 6
            const expectedHours = (opt + (4 * real) + pess) / 6;
            // Standard deviation = (P - O) / 6
            const sd = (pess - opt) / 6;

            setPrimaryResult(expectedHours.toFixed(1) + ' ساعة عمل', 'الوقت المتوقع بدقة علمية (PERT)');
            showResultArea();

            setDetailStats([
                { label: 'النطاق الآمن للإنجاز (احتمالية 95%)', value: (expectedHours - 2*sd).toFixed(0) + ' إلى ' + (expectedHours + 2*sd).toFixed(0) + ' ساعة', color: '#10b981' },
                { label: 'التقدير الواقعي المباشر', value: real + ' ساعة', color: '#3b82f6' },
                { label: 'أيام العمل المقدرة (بمعدل 5 ساعات يومياً)', value: (expectedHours / 5).toFixed(1) + ' يوم', color: '#8b5cf6' },
                { label: 'عامل المخاطرة والتقلب في المشروع', value: '±' + sd.toFixed(1) + ' ساعة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>بناءً على نموذج PERT لإدارة المشاريع الهندسية والبرمجية، التقدير الزمني الأكثر أماناً للتسليم هو <strong>\${expectedHours.toFixed(1)} ساعة</strong>، ونطاق الأمان يتراوح بين <strong>\${(expectedHours - sd).toFixed(0)}</strong> و <strong>\${(expectedHours + sd).toFixed(0)} ساعة</strong>.</p>
            `);
        ",
        'points' => [
            'معادلة PERT المعتمدة دولياً: الوقت المتوقع = (الوقت المتفائل + 4 × الوقت الواقعي + الوقت المتشائم) ÷ 6.',
            'تمنع هذه الطريقة الوقوع في فخ التفاؤل المفرط وتضمن تسليم المشاريع في مواعيدها المحددة.'
        ],
        'assumptions' => 'تستخدم هذه الطريقة من قِبل مدراء المشاريع المحترفين (PMP) لتسعير وجدولة المهام المعقدة.',
        'faqs' => [
            ['q' => 'لماذا يُعطى الوقت الواقعي وزن 4 أضعاف؟', 'a' => 'لأنه في التوزيع الاحتمالي الإحصائي (Beta Distribution)، يكون السيناريو المرجح هو الأقرب للحدوث، مع مراعاة احتمالات الطوارئ المتشائمة بنسبة محسوبة.']
        ],
        'related' => ['freelancer-project-price-calculator', 'dev-pricing-calculator', 'design-pricing-calculator']
    ],

    'design-pricing-calculator' => [
        'title' => 'حاسبة تسعير خدمات التصميم',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'designType', 'label' => 'نوع خدمة التصميم', 'type' => 'select', 'options' => [
                'logo' => 'تصميم شعار وهوية بصرية (Logo & Brand Identity)',
                'uiux_screen' => 'تصميم واجهات تطبيقات ومواقع (UI/UX)',
                'social_posts' => 'بوستات وبنرات سوشيال ميديا',
                'print_brochure' => 'مطبوعات وبروشورات وكتالوجات',
                'presentation' => 'عروض تقديمية احترافية (Pitch Decks)'
            ], 'default' => 'logo'],
            ['id' => 'itemsCount', 'label' => 'عدد العناصر أو الشاشات أو التصاميم', 'type' => 'number', 'default' => '1', 'min' => '1', 'step' => '1'],
            ['id' => 'designerLevel', 'label' => 'مستوى خبرة المصمم', 'type' => 'select', 'options' => [
                'junior' => 'مبتدئ / صاعد (خبرة 1-2 سنة)',
                'mid' => 'متوسط الكفاءة (خبرة 3-5 سنوات)',
                'senior' => 'محترف متقدم / خبير (أكثر من 5 سنوات)'
            ], 'default' => 'mid'],
            ['id' => 'commercialRights', 'label' => 'حقوق الاستخدام التجاري والتنازل عن الملفات المفتوحة', 'type' => 'select', 'options' => [
                'yes' => 'نعم، نقل كامل الحقوق والملفات المصدرية (AI/PSD/Figma)',
                'no' => 'تصميم للاستخدام العادي وتسليم ملفات نهائية فقط'
            ], 'default' => 'yes'],
        ],
        'calcJs' => "
            const type = document.getElementById('designType').value;
            const count = Math.max(1, parseInt(document.getElementById('itemsCount').value) || 1);
            const level = document.getElementById('designerLevel').value;
            const rights = document.getElementById('commercialRights').value;
            const curr = getSelectedCurrency();

            let basePricePerItem = 100;
            if (type === 'logo') basePricePerItem = 400;
            if (type === 'uiux_screen') basePricePerItem = 60;
            if (type === 'social_posts') basePricePerItem = 25;
            if (type === 'print_brochure') basePricePerItem = 70;
            if (type === 'presentation') basePricePerItem = 15;

            let multiplier = 1.0;
            if (level === 'junior') multiplier = 0.7;
            if (level === 'senior') multiplier = 1.8;

            let totalPrice = basePricePerItem * count * multiplier;
            if (rights === 'yes') totalPrice *= 1.25;

            setPrimaryResult(formatMoney(totalPrice, curr), 'السعر المقترح للمشروع');
            showResultArea();

            setDetailStats([
                { label: 'متوسط سعر العنصر الواحد', value: formatMoney(totalPrice / count, curr), color: '#3b82f6' },
                { label: 'مستوى الخبرة المعتمد', value: level === 'senior' ? 'خبير' : (level === 'junior' ? 'مبتدئ' : 'متوسط'), color: '#10b981' },
                { label: 'قيمة الملفات المفتوحة والحقوق التجارية', value: rights === 'yes' ? '+25%' : 'غير مشمولة', color: '#f59e0b' },
                { label: 'عدد العناصر المطلوبة', value: count + ' عنصر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>السعر العادل لتنفيذ <strong>\${count} تصاميم</strong> بمستوى خبرة <strong>\${level}</strong> هو <strong>\${formatMoney(totalPrice, curr)}</strong> بما يضمن حقوقك والوقت المستغرق في الأفكار والتعديلات.</p>
            `);
        ",
        'points' => [
            'التسعير يعتمد على نوع التصميم، المجهود الإبداعي، ومستوى المصمم في السوق.',
            'الملفات المصدرية المفتوحة (Source Files) والتنازل عن الملكية الفكرية ترفع قيمة العرض بنسبة 25% إلى 50% كمعيار عالمي.'
        ],
        'assumptions' => 'الأسعار مبنية على استبيانات أجور المصممين المستقلين في السوق العربي لعام 2025/2026.',
        'faqs' => [
            ['q' => 'هل يحق للعميل طلب تعديلات غير محدودة؟', 'a' => 'لا؛ يجب أن يتضمن عرض السعر عدداً محدداً من جولات المراجعة (عادة جولتين إلى 3 جولات)، وأي تعديل إضافي يُحسب بسعر ساعة منفصل.']
        ],
        'related' => ['dev-pricing-calculator', 'social-media-pricing-calculator', 'freelancer-project-price-calculator']
    ],

    'dev-pricing-calculator' => [
        'title' => 'حاسبة تسعير خدمات البرمجة وتطوير المواقع',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'projectCategory', 'label' => 'نوع المشروع البرمجي', 'type' => 'select', 'options' => [
                'landing' => 'صفحة هبوط تسويقية سريعة (Landing Page)',
                'corporate' => 'موقع تعريفي للشركات (5-10 صفحات)',
                'ecommerce' => 'متجر إلكتروني متكامل مع بوابات دفع وشحن',
                'custom_web' => 'تطبيق ويب مخصص (Custom Web App / SaaS)',
                'mobile_app' => 'تطبيق هاتف ذكي (iOS & Android)'
            ], 'default' => 'corporate'],
            ['id' => 'integrationsCount', 'label' => 'عدد عمليات الربط الخارجي مع APIs وبوابات الدفع', 'type' => 'number', 'default' => '2', 'min' => '0', 'step' => '1'],
            ['id' => 'supportMonths', 'label' => 'أشهر الدعم الفني والصيانة المشمولة مجاناً', 'type' => 'number', 'default' => '2', 'min' => '0', 'max' => '12', 'step' => '1'],
            ['id' => 'urgencySpeed', 'label' => 'سرعة التسليم المطلوبة', 'type' => 'select', 'options' => [
                'normal' => 'وقت تسليم طبيعي ومعتاد',
                'urgent' => 'مستعجل جداً (+30% تكلفة ضغط عمل)'
            ], 'default' => 'normal'],
        ],
        'calcJs' => "
            const cat = document.getElementById('projectCategory').value;
            const apis = Math.max(0, parseInt(document.getElementById('integrationsCount').value) || 0);
            const support = Math.max(0, parseInt(document.getElementById('supportMonths').value) || 0);
            const speed = document.getElementById('urgencySpeed').value;
            const curr = getSelectedCurrency();

            let baseCost = 400;
            let estDays = 7;
            if (cat === 'corporate') { baseCost = 900; estDays = 14; }
            if (cat === 'ecommerce') { baseCost = 1800; estDays = 25; }
            if (cat === 'custom_web') { baseCost = 3500; estDays = 45; }
            if (cat === 'mobile_app') { baseCost = 4500; estDays = 60; }

            const apiCost = apis * 150;
            const supportCost = support * 100;
            let total = baseCost + apiCost + supportCost;
            if (speed === 'urgent') total *= 1.3;

            setPrimaryResult(formatMoney(total, curr), 'سعر التكلفة المقترح لتطوير المشروع');
            showResultArea();

            setDetailStats([
                { label: 'المدة الزمنية المتوقعة للتسليم', value: estDays + ' يوم عمل تقريباً', color: '#3b82f6' },
                { label: 'تكلفة ربط الخدمات و APIs', value: formatMoney(apiCost, curr), color: '#10b981' },
                { label: 'قيمة الدعم الفني والصيانة', value: formatMoney(supportCost, curr), color: '#8b5cf6' },
                { label: 'دفعة البداية (40%)', value: formatMoney(total * 0.4, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>السعر التقديري المتوازن لتنفيذ هذا المشروع هو <strong>\${formatMoney(total, curr)}</strong> بمدة تسليم متوقعة <strong>\${estDays} يوم عمل</strong> شاملة <strong>\${support} أشهر</strong> صيانة وضمان للأخطاء البرمجية.</p>
            `);
        ",
        'points' => [
            'التسعير يعتمد على نوع وحجم النظام وقواعد البيانات المطلوبة.',
            'عمليات الربط مع أنظمة خارجية (APIs) وبوابات الدفع تتطلب وقتاً واختبارات أمنية إضافية.',
            'فترة الضمان والصيانة بعد الإطلاق تحمي العميل وتزيد من موثوقية المطور.'
        ],
        'assumptions' => 'التسعير يفترض تنفيذاً برمجياً عالي الجودة وموثقاً (Clean Code) متوافقاً مع الهواتف الذكية ومحركات البحث.',
        'faqs' => [
            ['q' => 'كيف يتم تقسيم دفعات المشروع البرمجي عادة؟', 'a' => 'المعتاد هو: 40% دفعة مقدمة عند توقيع العقد، 40% عند الانتهاء من مرحلة التطوير والمعاينة على سيرفر تجريبي، و 20% عند التسليم النهائي ونقل الموقع لسيرفر العميل.']
        ],
        'related' => ['design-pricing-calculator', 'freelancer-project-price-calculator', 'project-hours-calculator']
    ],

    'social-media-pricing-calculator' => [
        'title' => 'حاسبة تسعير إدارة حسابات السوشيال ميديا',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'postsPerMonth', 'label' => 'عدد المنشورات والتصاميم الثابتة شهرياً', 'type' => 'number', 'default' => '16', 'min' => '0', 'step' => '2'],
            ['id' => 'reelsPerMonth', 'label' => 'عدد الفيديوهات القصيرة (Reels / TikTok) شهرياً', 'type' => 'number', 'default' => '8', 'min' => '0', 'step' => '1'],
            ['id' => 'platformsCount', 'label' => 'عدد المنصات المدارة (إنستغرام، إكس، تيك توك، إلخ)', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '6', 'step' => '1'],
            ['id' => 'adManagement', 'label' => 'هل الخدمة تشمل إدارة وإطلاق الحملات الإعلانية الممولة؟', 'type' => 'select', 'options' => [
                'yes' => 'نعم، إدارة وتحسين الحملات الإعلانية',
                'no' => 'لا، نشر وإدارة محتوى فقط'
            ], 'default' => 'yes'],
            ['id' => 'communityManagement', 'label' => 'الرد على التعليقات والرسائل الخاصة (خدمة العملاء)', 'type' => 'select', 'options' => [
                'yes' => 'نعم، ردود يومية ومتابعة التفاعل',
                'no' => 'لا، المحتوى والنشر فقط'
            ], 'default' => 'no'],
        ],
        'calcJs' => "
            const posts = Math.max(0, parseInt(document.getElementById('postsPerMonth').value) || 0);
            const reels = Math.max(0, parseInt(document.getElementById('reelsPerMonth').value) || 0);
            const platforms = Math.max(1, parseInt(document.getElementById('platformsCount').value) || 1);
            const ads = document.getElementById('adManagement').value;
            const comm = document.getElementById('communityManagement').value;
            const curr = getSelectedCurrency();

            const postPrice = 20; // سعر إعداد البوست مع الكابشن
            const reelPrice = 45; // سعر كتابة ومونتاج الريل
            const contentBase = (posts * postPrice) + (reels * reelPrice);
            const platformsMultiplier = 1 + ((platforms - 1) * 0.25);
            let total = contentBase * platformsMultiplier;

            if (ads === 'yes') total += 250;
            if (comm === 'yes') total += 150;

            setPrimaryResult(formatMoney(total, curr) + ' / شهرياً', 'الاشتراك الشهري المقترح لإدارة الحسابات');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المحتوى شهرياً', value: (posts + reels) + ' قطعة محتوى', color: '#3b82f6' },
                { label: 'عدد المنصات المدارة', value: platforms + ' منصات', color: '#10b981' },
                { label: 'تكلفة المحتوى والتصاميم', value: formatMoney(contentBase * platformsMultiplier, curr), color: '#8b5cf6' },
                { label: 'رسوم إدارة الإعلانات والتفاعل', value: formatMoney((ads==='yes'?250:0) + (comm==='yes'?150:0), curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الحزمة الشهرية المقترحة تشمل إعداد ونشر <strong>\${posts} بوست</strong> و <strong>\${reels} ريلز</strong> على <strong>\${platforms} منصات</strong> بتكلفة إجمالية <strong>\${formatMoney(total, curr)}</strong> شهرياً.</p>
            `);
        ",
        'points' => [
            'فيديوهات الـ Reels والمونتاج تتطلب جهداً ووقتاً أعلى من التصاميم الثابتة.',
            'نشر نفس المحتوى على منصات إضافية يترتب عليه تعديل القياسات والكلمات المفتاحية والجدولة.',
            'إدارة الإعلانات الممولة وخدمة الردود على العملاء هي خدمات قيمة مضافة تُحسب كرسوم شهرية منفصلة.'
        ],
        'assumptions' => 'الأسعار لا تشمل الميزانية الإعلانية التي تدفعها الشركة مباشرة للمنصات (Facebook/TikTok/Google).',
        'faqs' => [
            ['q' => 'هل الرسوم تشمل الميزانية الإعلانية المدفوعة للمنصات؟', 'a' => 'لا؛ الميزانية الإعلانية يدفعها العميل ببطاقته مباشرة لمنصات الإعلانات، وأتعاب المسوق تكون مقابل الاستراتيجية والإطلاق والتصاميم والتحسين المستمر.']
        ],
        'related' => ['design-pricing-calculator', 'freelancer-hourly-rate-calculator', 'roas-calculator']
    ],
];

// دالة توليد ملف الأداة
function generateToolFile($slug, $def, $outputDir) {
    $filePath = $outputDir . '/' . $slug . '.php';
    $title = $def['title'];
    $isFinancial = $def['isFinancial'] ?? false;
    $inputs = $def['inputs'];
    $calcJs = $def['calcJs'];
    $points = $def['points'] ?? [];
    $assumptions = $def['assumptions'] ?? '';
    $faqs = $def['faqs'] ?? [];
    $related = $def['related'] ?? [];

    $pointsExport = var_export($points, true);
    $faqsExport = var_export($faqs, true);
    $relatedExport = var_export($related, true);

    $htmlInputs = '';
    foreach ($inputs as $inp) {
        $id = $inp['id'];
        $label = $inp['label'];
        $type = $inp['type'] ?? 'number';
        $default = $inp['default'] ?? '';
        $min = isset($inp['min']) ? "min=\"{$inp['min']}\"" : '';
        $max = isset($inp['max']) ? "max=\"{$inp['max']}\"" : '';
        $step = isset($inp['step']) ? "step=\"{$inp['step']}\"" : '';

        $htmlInputs .= "    <div class=\"form-group\">\n";
        $htmlInputs .= "        <label class=\"form-label\" for=\"{$id}\">{$label}</label>\n";
        if ($type === 'select') {
            $htmlInputs .= "        <select id=\"{$id}\" class=\"form-control\" onchange=\"calculateTool()\">\n";
            foreach ($inp['options'] as $optVal => $optLabel) {
                $sel = ($optVal == $default) ? 'selected' : '';
                $htmlInputs .= "            <option value=\"{$optVal}\" {$sel}>{$optLabel}</option>\n";
            }
            $htmlInputs .= "        </select>\n";
        } else {
            $htmlInputs .= "        <input type=\"{$type}\" id=\"{$id}\" class=\"form-control\" value=\"{$default}\" {$min} {$max} {$step} oninput=\"calculateTool()\">\n";
        }
        $htmlInputs .= "    </div>\n";
    }

    $currencySelector = $isFinancial ? "    <?php renderCurrencySelector('calcCurrency', 'SAR', 'العملة المفضلة للنتائج'); ?>\n" : "";

    $code = <<<PHP
<?php
/**
 * أداة: {$title}
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
\$slug = '{$slug}';
\$tool = getToolBySlug(\$slug);
if (!\$tool) {
    redirect('index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container tool-container">
    <?php renderToolHeader(\$tool); ?>

    <div class="tool-content-grid">
        <!-- قسم إدخال البيانات -->
        <div class="tool-card card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h text-accent"></i> أدخل البيانات المطلوبة</h3>
            </div>
            <div class="card-body">
{$currencySelector}{$htmlInputs}
                <div class="tool-actions-bar" style="display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap">
                    <button type="button" class="btn btn-primary btn-lg" style="flex:1" onclick="calculateTool()">
                        <i class="fas fa-calculator"></i> احسب الآن
                    </button>
                    <button type="button" class="btn btn-ghost" onclick="resetToolInputs()">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قسم عرض النتيجة -->
        <div class="tool-result-wrapper">
            <?php renderResultArea('ملخص الحساب والنتائج', ['copy' => true, 'share' => true, 'download' => true, 'print' => true]); ?>
        </div>
    </div>

    <!-- قسم الشرح والمعادلات -->
    <?php 
    renderToolExplanation(
        'طريقة الحساب والمعادلات المستخدمة',
        {$pointsExport},
        '{$assumptions}'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ({$faqsExport}); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools({$relatedExport}); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        {$calcJs}
        saveLastInputs('{$slug}');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('{$slug}');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
PHP;

    file_put_contents($filePath, $code);
    echo "Generated: tools/{$slug}.php\n";
}

echo "Generating Group A: Finance & Business Tools (30 tools)...\n";
foreach ($toolsA as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group A!\n";
