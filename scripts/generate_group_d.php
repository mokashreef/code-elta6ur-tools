<?php
/**
 * مولد أدوات المجموعة D: السيارات والسفر (11 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsD = [
    'monthly-gas-cost-calculator' => [
        'title' => 'حاسبة تكلفة البنزين الشهرية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'dailyCommuteKm', 'label' => 'المسافة المقطوعة يومياً (ذهاب وإياب للعمل والمشاوير بالكم)', 'type' => 'number', 'default' => '45', 'min' => '1', 'step' => '5'],
            ['id' => 'fuelConsumptionRate', 'label' => 'معدل استهلاك سيارتك (لتر لكل 100 كم) - المتوسط 8 إلى 11 لتر', 'type' => 'number', 'default' => '9.0', 'min' => '3', 'max' => '25', 'step' => '0.5'],
            ['id' => 'gasLiterPrice', 'label' => 'سعر لتر الوقود (بنزين 91 أو 95 أو ديزل)', 'type' => 'number', 'default' => '2.18', 'min' => '0.1', 'step' => '0.05'],
            ['id' => 'monthlyWorkDays', 'label' => 'عدد أيام القيادة في الشهر (المعتاد 30 يوماً شاملة عطلة الأسبوع)', 'type' => 'number', 'default' => '30', 'min' => '1', 'max' => '31', 'step' => '1'],
        ],
        'calcJs' => "
            const kmDay = Math.max(1, parseFloat(document.getElementById('dailyCommuteKm').value) || 45);
            const rate = Math.max(3, parseFloat(document.getElementById('fuelConsumptionRate').value) || 9.0);
            const price = Math.max(0.1, parseFloat(document.getElementById('gasLiterPrice').value) || 2.18);
            const days = Math.max(1, Math.min(31, parseInt(document.getElementById('monthlyWorkDays').value) || 30));
            const curr = getSelectedCurrency();

            const monthlyKm = kmDay * days;
            const litersPerKm = rate / 100;
            const monthlyLiters = monthlyKm * litersPerKm;
            const monthlyCost = monthlyLiters * price;
            const yearlyCost = monthlyCost * 12;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'فاتورة البنزين الشهرية');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المسافة المقطوعة شهرياً', value: monthlyKm.toLocaleString() + ' كم', color: '#3b82f6' },
                { label: 'كمية الوقود المستهلكة شهرياً', value: monthlyLiters.toFixed(1) + ' لتر', color: '#10b981' },
                { label: 'تكلفة الكيلومتر الواحد', value: formatMoney((monthlyCost / monthlyKm), curr) + ' / كم', color: '#f59e0b' },
                { label: 'التكلفة السنوية التقديرية', value: formatMoney(yearlyCost, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>\${monthlyKm.toLocaleString()} كم شهرياً</strong> بمعدل استهلاك <strong>\${rate} لتر/100 كم</strong>، تستهلك سيارتك <strong>\${monthlyLiters.toFixed(1)} لتر بنزين</strong> بقيمة <strong>\${formatMoney(monthlyCost, curr)}</strong> شهرياً.</p>
            `);
        ",
        'points' => [
            'الاستهلاك الشهري باللتر = (المسافة الشهرية بالكم ÷ 100) × معدل استهلاك السيارة (لتر/100 كم).',
            'التكلفة الشهرية = اللترات المستهلكة شهرياً × سعر لتر البنزين.'
        ],
        'assumptions' => 'يفترض أسلوب قيادة متوازن واستخدام التكييف في الأجواء الحارة.',
        'faqs' => [
            ['q' => 'كيف أعرف استهلاك سيارتي الفعلي لكل 100 كم؟', 'a' => 'املأ التانكي بالكامل وصفر عداد المسافات (Trip A)، وقُد حتى ينخفض التانكي ثم املأه مرة أخرى بالكامل؛ اقسم عدد اللترات المعبأة على عدد الكيلومترات المقطوعة واضرب الناتج في 100.']
        ],
        'related' => ['fuel-by-distance-calculator', 'monthly-car-cost-calculator', 'car-consumption-calculator']
    ],

    'monthly-car-cost-calculator' => [
        'title' => 'حاسبة تكلفة السيارة الشهرية الشاملة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'monthlyLoanPayment', 'label' => 'قسط التمويل أو الإيجار المنتهي بالتمليك شهرياً (0 إن كانت مسددة)', 'type' => 'number', 'default' => '1200', 'min' => '0', 'step' => '50'],
            ['id' => 'monthlyFuelCost', 'label' => 'تكلفة البنزين الشهرية التقديرية', 'type' => 'number', 'default' => '600', 'min' => '0', 'step' => '50'],
            ['id' => 'annualInsuranceCost', 'label' => 'التأمين السنوي (شامل أو ضد الغير)', 'type' => 'number', 'default' => '2400', 'min' => '0', 'step' => '100'],
            ['id' => 'annualMaintenanceRepairs', 'label' => 'الصيانة الدورية والإطارات والزيوت سنوياً', 'type' => 'number', 'default' => '2000', 'min' => '0', 'step' => '100'],
            ['id' => 'parkingAndWash', 'label' => 'غسيل السيارة، المواقف، ورسوم الطرق شهرياً', 'type' => 'number', 'default' => '150', 'min' => '0', 'step' => '25'],
        ],
        'calcJs' => "
            const loan = Math.max(0, parseFloat(document.getElementById('monthlyLoanPayment').value) || 0);
            const fuel = Math.max(0, parseFloat(document.getElementById('monthlyFuelCost').value) || 0);
            const insAnnual = Math.max(0, parseFloat(document.getElementById('annualInsuranceCost').value) || 0);
            const maintAnnual = Math.max(0, parseFloat(document.getElementById('annualMaintenanceRepairs').value) || 0);
            const wash = Math.max(0, parseFloat(document.getElementById('parkingAndWash').value) || 0);
            const curr = getSelectedCurrency();

            const monthlyInsurance = insAnnual / 12;
            const monthlyMaint = maintAnnual / 12;
            const totalMonthly = loan + fuel + monthlyInsurance + monthlyMaint + wash;
            const totalYearly = totalMonthly * 12;

            setPrimaryResult(formatMoney(totalMonthly, curr) + ' شهرياً', 'التكلفة الإجمالية لامتلاك وتشغيل السيارة');
            showResultArea();

            setDetailStats([
                { label: 'قسط السيارة والوقود المباشر', value: formatMoney(loan + fuel, curr), color: '#3b82f6' },
                { label: 'التأمين الموزع شهرياً', value: formatMoney(monthlyInsurance, curr), color: '#10b981' },
                { label: 'مخصص الصيانة والإطارات شهرياً', value: formatMoney(monthlyMaint, curr), color: '#f59e0b' },
                { label: 'إجمالي ما تنفقه سنوياً على السيارة', value: formatMoney(totalYearly, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>تكلفك السيارة فعلياً <strong>\${formatMoney(totalMonthly, curr)} شهرياً</strong> (ما يعادل <strong>\${formatMoney(totalYearly, curr)} سنوياً</strong>). التكاليف المخفية كالتأمين والصيانة الدورية تمثل حوالي <strong>\${totalMonthly > 0 ? (((monthlyInsurance + monthlyMaint)/totalMonthly)*100).toFixed(0) : 0}%</strong> من المصروف الشهري.</p>
            `);
        ",
        'points' => [
            'تكلفة السيارة لا تنتهي عند دفع ثمنها أو قسطها؛ بل تمتد لتشمل التأمين الإلزامي والصيانة الدورية ورسوم الطرق وتجديد الرخص.',
            'تخصيص صندوق شهري للصيانة الدورية يحميك من الصدمات المالية عند الحاجة لتغيير الإطارات أو البطارية.'
        ],
        'assumptions' => 'يفترض عدم وقوع حوادث كبرى غير مغطاة بالتأمين.',
        'faqs' => [
            ['q' => 'ما النسبة الآمنة لمصاريف السيارة من الراتب الشهري؟', 'a' => 'القاعدة المالية الموصى بها هي ألا تتجاوز التكاليف الشاملة للسيارة (القسط + الوقود + التأمين + الصيانة) نسبة 15% إلى 20% كحد أقصى من صافي دخلك الشهري.']
        ],
        'related' => ['monthly-gas-cost-calculator', 'real-car-cost-calculator', 'buy-car-vs-transport-calculator']
    ],

    'road-trip-cost-calculator' => [
        'title' => 'حاسبة تكلفة الرحلة بالسيارة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'oneWayDistanceKm', 'label' => 'المسافة باتجاه واحد (كم)', 'type' => 'number', 'default' => '450', 'min' => '10', 'step' => '25'],
            ['id' => 'isRoundTrip', 'label' => 'نوع الرحلة', 'type' => 'select', 'options' => [
                'round' => 'ذهاب وعودة (مضاعفة المسافة 2x)',
                'oneway' => 'اتجاه واحد فقط'
            ], 'default' => 'round'],
            ['id' => 'roadFuelRate', 'label' => 'استهلاك السيارة على الخطوط السريعة (لتر/100 كم) - عادة 7 إلى 9', 'type' => 'number', 'default' => '8.0', 'min' => '3', 'max' => '20', 'step' => '0.5'],
            ['id' => 'roadFuelPrice', 'label' => 'سعر لتر الوقود', 'type' => 'number', 'default' => '2.18', 'min' => '0.1', 'step' => '0.05'],
            ['id' => 'tollsAndFees', 'label' => 'رسوم الطرق وبوابات العبور (Tolls)', 'type' => 'number', 'default' => '0', 'min' => '0', 'step' => '10'],
            ['id' => 'snacksAndMeals', 'label' => 'وجبات واستراحات الطريق والمشروبات', 'type' => 'number', 'default' => '120', 'min' => '0', 'step' => '20'],
        ],
        'calcJs' => "
            const oneWay = Math.max(10, parseFloat(document.getElementById('oneWayDistanceKm').value) || 450);
            const isRound = document.getElementById('isRoundTrip').value === 'round';
            const rate = Math.max(3, parseFloat(document.getElementById('roadFuelRate').value) || 8.0);
            const price = Math.max(0.1, parseFloat(document.getElementById('roadFuelPrice').value) || 2.18);
            const tolls = Math.max(0, parseFloat(document.getElementById('tollsAndFees').value) || 0);
            const meals = Math.max(0, parseFloat(document.getElementById('snacksAndMeals').value) || 120);
            const curr = getSelectedCurrency();

            const totalKm = isRound ? oneWay * 2 : oneWay;
            const totalLiters = (totalKm / 100) * rate;
            const totalFuelCost = totalLiters * price;
            const grandTotalCost = totalFuelCost + tolls + meals;

            setPrimaryResult(formatMoney(grandTotalCost, curr), 'إجمالي التكلفة المقدرة للرحلة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المسافة المقطوعة', value: totalKm.toLocaleString() + ' كم', color: '#3b82f6' },
                { label: 'تكلفة الوقود والبنزين', value: formatMoney(totalFuelCost, curr), color: '#10b981' },
                { label: 'كمية البنزين المطلوبة', value: totalLiters.toFixed(1) + ' لتر', color: '#f59e0b' },
                { label: 'الوجبات ورسوم الطرق', value: formatMoney(tolls + meals, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للقيام برحلة مسافتها <strong>\${totalKm.toLocaleString()} كم</strong> \${isRound ? '(ذهاباً وإياباً)' : '(اتجاه واحد)'}، تحتاج إلى حوالي <strong>\${totalLiters.toFixed(1)} لتر بنزين</strong> بتكلفة وقود <strong>\${formatMoney(totalFuelCost, curr)}</strong>، وإجمالي تكلفة شاملة الوجبات <strong>\${formatMoney(grandTotalCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'القيادة على الطرق السريعة بسرعة معتدلة (100 إلى 120 كم/س) توفر ما يصل إلى 20% من استهلاك الوقود مقارنة بالسرعات العالية (140+ كم/س).',
            'التأكد من ضغط الإطارات المناسب قبل السفر يوفر الوقود ويضمن أعلى درجات السلامة.'
        ],
        'assumptions' => 'يفترض طريقاً سريعاً مفتوحاً مع ثبات نسبي للسرعة.',
        'faqs' => [
            ['q' => 'أيهما أوفر: السفر بالسيارة أم الطيران لأسرة من 4 أفراد؟', 'a' => 'السفر بالسيارة لرحلات تقل عن 800 كم يكون أرخص بنسبة تتجاوز 60% إلى 70% للعائلات مقارنة بتذاكر الطيران، بالإضافة لتوفير تكلفة استئجار سيارة في وجهة الوصول.']
        ],
        'related' => ['fuel-by-distance-calculator', 'travel-cost-calculator', 'monthly-gas-cost-calculator']
    ],

    'travel-cost-calculator' => [
        'title' => 'حاسبة تكلفة السفر الشاملة للرحلات السياحية',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'travelersCount', 'label' => 'عدد المسافرين', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '20', 'step' => '1'],
            ['id' => 'tripDaysCount', 'label' => 'مدة الرحلة (عدد الأيام)', 'type' => 'number', 'default' => '7', 'min' => '1', 'max' => '90', 'step' => '1'],
            ['id' => 'flightTicketPerPerson', 'label' => 'سعر تذكرة الطيران ذهاب وعودة للشخص', 'type' => 'number', 'default' => '1800', 'min' => '0', 'step' => '100'],
            ['id' => 'hotelNightCost', 'label' => 'سعر الغرفة الفندقية في الليلة', 'type' => 'number', 'default' => '450', 'min' => '0', 'step' => '50'],
            ['id' => 'dailySpendPerPerson', 'label' => 'المصروف اليومي التقديري للشخص (طعام، تذاكر فعاليات، مواصلات)', 'type' => 'number', 'default' => '200', 'min' => '20', 'step' => '25'],
            ['id' => 'visaInsurancePerPerson', 'label' => 'رسوم التأشيرة والتأمين الطبي لكل مسافر', 'type' => 'number', 'default' => '250', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const people = Math.max(1, parseInt(document.getElementById('travelersCount').value) || 2);
            const days = Math.max(1, parseInt(document.getElementById('tripDaysCount').value) || 7);
            const flight = Math.max(0, parseFloat(document.getElementById('flightTicketPerPerson').value) || 1800);
            const hotel = Math.max(0, parseFloat(document.getElementById('hotelNightCost').value) || 450);
            const daily = Math.max(20, parseFloat(document.getElementById('dailySpendPerPerson').value) || 200);
            const visa = Math.max(0, parseFloat(document.getElementById('visaInsurancePerPerson').value) || 250);
            const curr = getSelectedCurrency();

            // عدد الغرف التقديري: غرفة لكل شخصين
            const rooms = Math.ceil(people / 2);
            const nights = Math.max(1, days - 1);

            const totalFlights = flight * people;
            const totalHotels = hotel * rooms * nights;
            const totalDaily = daily * people * days;
            const totalVisas = visa * people;
            const grandTotal = totalFlights + totalHotels + totalDaily + totalVisas;
            const costPerPerson = grandTotal / people;

            setPrimaryResult(formatMoney(grandTotal, curr), 'الميزانية التقديرية الإجمالية للرحلة');
            showResultArea();

            setDetailStats([
                { label: 'متوسط تكلفة الشخص الواحد', value: formatMoney(costPerPerson, curr), color: '#3b82f6' },
                { label: 'تذاكر الطيران لجميع المسافرين', value: formatMoney(totalFlights, curr), color: '#10b981' },
                { label: 'إجمالي الإقامة والفنادق (' + nights + ' ليالٍ)', value: formatMoney(totalHotels, curr), color: '#f59e0b' },
                { label: 'المصروف اليومي والأنشطة لجميع الأيام', value: formatMoney(totalDaily, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>ميزانية السفر لـ <strong>\${people} مسافرين</strong> لمدة <strong>\${days} أيام</strong> تقدر بـ <strong>\${formatMoney(grandTotal, curr)}</strong> (بمتوسط <strong>\${formatMoney(costPerPerson, curr)}</strong> للشخص الواحد) شاملة الطيران والإقامة والمصروف اليومي والتأشيرات.</p>
            `);
        ",
        'points' => [
            'إجمالي تكلفة الرحلة = تذاكر الطيران + الفنادق + (المصروف اليومي × الأيام × الأفراد) + التأشيرات.',
            'حجز الطيران والفنادق قبل السفر بشهرين إلى 3 أشهر يوفر ما بين 25% إلى 40% من تكلفة التذاكر والإقامة.'
        ],
        'assumptions' => 'يفترض غرفة فندقية مزدوجة مشتركة لكل شخصين.',
        'faqs' => [
            ['q' => 'كم تبلغ ميزانية الطوارئ الموصى بها في السفر الدولي؟', 'a' => 'يُوصى دائماً بتخصيص بطاقة ائتمانية باحتياطي لا يقل عن 20% إلى 25% من ميزانية السفر لمواجهة أي ظروف طبية أو تغيير في مواعيد الطيران.']
        ],
        'related' => ['travel-expenses-split-calculator', 'road-trip-cost-calculator', 'cost-of-living-calculator']
    ],

    'travel-expenses-split-calculator' => [
        'title' => 'حاسبة تقسيم مصاريف السفر بين الأصدقاء',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'totalGroupExpense', 'label' => 'إجمالي الفواتير والمصاريف المشتركة للرحلة', 'type' => 'number', 'default' => '8400', 'min' => '10', 'step' => '100'],
            ['id' => 'travelersCountSplit', 'label' => 'عدد أفراد الرحلة أو الأصدقاء', 'type' => 'number', 'default' => '4', 'min' => '2', 'max' => '30', 'step' => '1'],
            ['id' => 'userPaidAlready', 'label' => 'المبلغ الذي دفعته أنت من جيبك حتى الآن للمجموعة', 'type' => 'number', 'default' => '3000', 'min' => '0', 'step' => '50'],
        ],
        'calcJs' => "
            const total = Math.max(10, parseFloat(document.getElementById('totalGroupExpense').value) || 8400);
            const count = Math.max(2, parseInt(document.getElementById('travelersCountSplit').value) || 4);
            const paid = Math.max(0, parseFloat(document.getElementById('userPaidAlready').value) || 0);
            const curr = getSelectedCurrency();

            const perPersonShare = total / count;
            const balance = paid - perPersonShare;

            let resultText = '';
            let statusColor = '#10b981';
            if (balance > 0) {
                resultText = 'تستحق استرداد ' + formatMoney(balance, curr) + ' من أصدقائك ✅';
                statusColor = '#10b981';
            } else if (balance < 0) {
                resultText = 'عليك دفع ' + formatMoney(Math.abs(balance), curr) + ' لتسوية حسابك ⚠️';
                statusColor = '#ef4444';
            } else {
                resultText = 'حسابك مسوى بالكامل ومتوازن 0 ✅';
            }

            setPrimaryResult(formatMoney(perPersonShare, curr) + ' للشخص', 'حصة الفرد العادلة من الرحلة');
            showResultArea();

            setDetailStats([
                { label: 'حصة الفرد العادلة بالتساوي', value: formatMoney(perPersonShare, curr), color: '#3b82f6' },
                { label: 'المبلغ الذي دفعته أنت', value: formatMoney(paid, curr), color: '#10b981' },
                { label: 'حالة تسوية حسابك الشخصي', value: resultText, color: statusColor },
                { label: 'إجمالي مصاريف المجموعة', value: formatMoney(total, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي مصاريف الرحلة <strong>\${formatMoney(total, curr)}</strong> مقسمة بالتساوي على <strong>\${count} أشخاص</strong> تعطي حصة <strong>\${formatMoney(perPersonShare, curr)}</strong> لكل فرد. بما أنك دفعت <strong>\${formatMoney(paid, curr)}</strong>، فإن وضعك المالي: <strong>\${resultText}</strong>.</p>
            `);
        ",
        'points' => [
            'حصة الشخص الواحد = إجمالي المصاريف المشتركة ÷ عدد الأفراد.',
            'صافي التسوية = المبلغ المدفوع من الشخص - حصته المستحقة.',
            'إذا كان الناتج موجباً فالمستخدم يسترد الفرق من أصدقائه، وإذا كان سالباً فيجب عليه سداد المبلغ المتبقي.'
        ],
        'assumptions' => 'يفترض تقسيم التكاليف المشتركة بالتساوي دون بنود فردية خاصة.',
        'faqs' => [
            ['q' => 'كيف نتعامل مع المشتريات الشخصية المنفصلة أثناء السفر؟', 'a' => 'المشتريات الفردية كالهدايا التذكارية وملابس التسوق الشخصية يجب أن يدفعها صاحبها مباشرة ببطاقته ولا تضاف إلى الفواتير المشتركة للمجموعة.']
        ],
        'related' => ['travel-cost-calculator', 'restaurant-bill-split-calculator', 'rent-split-calculator']
    ],

    'fuel-by-distance-calculator' => [
        'title' => 'حاسبة الوقود حسب المسافة',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'tripDistanceKm', 'label' => 'المسافة المراد قطعها (بالكيلومتر)', 'type' => 'number', 'default' => '320', 'min' => '1', 'step' => '10'],
            ['id' => 'fuelConsumptionLiters100', 'label' => 'معدل استهلاك سيارتك (لتر لكل 100 كم)', 'type' => 'number', 'default' => '8.5', 'min' => '3', 'max' => '25', 'step' => '0.5'],
            ['id' => 'fuelUnitPrice', 'label' => 'سعر لتر الوقود', 'type' => 'number', 'default' => '2.18', 'min' => '0.1', 'step' => '0.05'],
        ],
        'calcJs' => "
            const km = Math.max(1, parseFloat(document.getElementById('tripDistanceKm').value) || 320);
            const rate = Math.max(3, parseFloat(document.getElementById('fuelConsumptionLiters100').value) || 8.5);
            const price = Math.max(0.1, parseFloat(document.getElementById('fuelUnitPrice').value) || 2.18);
            const curr = getSelectedCurrency();

            const litersNeeded = (km / 100) * rate;
            const tripCost = litersNeeded * price;
            const kmCost = tripCost / km;

            setPrimaryResult(litersNeeded.toFixed(1) + ' لتر بنزين (' + formatMoney(tripCost, curr) + ')', 'كمية وتكلفة الوقود المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الرحلة الإجمالية بالوقود', value: formatMoney(tripCost, curr), color: '#3b82f6' },
                { label: 'كمية الوقود المطلوبة', value: litersNeeded.toFixed(1) + ' لتر', color: '#10b981' },
                { label: 'تكلفة الكيلومتر الواحد', value: formatMoney(kmCost, curr), color: '#f59e0b' },
                { label: 'عدد الكيلومترات المقطوعة باللتر الواحد', value: (100 / rate).toFixed(1) + ' كم / لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>\${km} كم</strong>، تحتاج سيارتك إلى <strong>\${litersNeeded.toFixed(1)} لتر</strong> من الوقود بتكلفة <strong>\${formatMoney(tripCost, curr)}</strong>.</p>
            `);
        ",
        'points' => [
            'كمية الوقود (لتر) = (المسافة بالكم ÷ 100) × معدل الاستهلاك.',
            'التكلفة = كمية الوقود × سعر لتر البنزين.'
        ],
        'assumptions' => 'الاستهلاك محسوب على متوسط كفاءة محرك السيارة.',
        'faqs' => [
            ['q' => 'كيف أحسن كفاءة استهلاك الوقود في الرحلات الطويلة؟', 'a' => 'استخدم مثبت السرعة (Cruise Control) على الطرق السريعة المستوية، وتجنب التسارع والفرملة المفاجئة، وتأكد من صيانة البواجي وفلتر الهواء.']
        ],
        'related' => ['distance-fuel-calculator', 'monthly-gas-cost-calculator', 'car-consumption-calculator']
    ],

    'distance-fuel-calculator' => [
        'title' => 'حاسبة المسافة والوقود (كم تقطع السيارة بالتانكي؟)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'fuelTankLiters', 'label' => 'كمية الوقود المتوفرة أو سعة التانكي (باللتر)', 'type' => 'number', 'default' => '55', 'min' => '5', 'max' => '200', 'step' => '5'],
            ['id' => 'consumptionRateL100', 'label' => 'معدل استهلاك الوقود (لتر لكل 100 كم)', 'type' => 'number', 'default' => '8.0', 'min' => '3', 'max' => '25', 'step' => '0.5'],
            ['id' => 'reserveSafeMargin', 'label' => 'ترك وقود احتياطي في التانكي للطوارئ (لتر)', 'type' => 'number', 'default' => '5', 'min' => '0', 'max' => '20', 'step' => '1'],
        ],
        'calcJs' => "
            const tank = Math.max(5, parseFloat(document.getElementById('fuelTankLiters').value) || 55);
            const rate = Math.max(3, parseFloat(document.getElementById('consumptionRateL100').value) || 8.0);
            const reserve = Math.max(0, parseFloat(document.getElementById('reserveSafeMargin').value) || 5);

            const usableFuel = Math.max(1, tank - reserve);
            const totalDistance = (usableFuel / rate) * 100;
            const fullTankDistance = (tank / rate) * 100;
            const kmPerLiter = 100 / rate;

            setPrimaryResult(Math.round(totalDistance).toLocaleString() + ' كم', 'المسافة الآمنة التي تقطعها السيارة');
            showResultArea();

            setDetailStats([
                { label: 'المدى الآمن قبل إضاءة لمبة البنزين', value: Math.round(totalDistance) + ' كم', color: '#10b981' },
                { label: 'المدى الأقصى النظري حتى جفاف التانكي', value: Math.round(fullTankDistance) + ' كم', color: '#3b82f6' },
                { label: 'كفاءة اللتر الواحد', value: kmPerLiter.toFixed(1) + ' كم / لتر', color: '#f59e0b' },
                { label: 'كمية الوقود القابلة للاستخدام', value: usableFuel + ' لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بكمية وقود <strong>\${tank} لتر</strong> واحتياطي أمان <strong>\${reserve} لتر</strong>، تستطيع سيارتك قطع <strong>\${Math.round(totalDistance)} كم</strong> بأمان قبل الحاجة للوقوف عند محطة وقود.</p>
            `);
        ",
        'points' => [
            'المسافة المقطوعة (كم) = (كمية الوقود المتاحة باللتر ÷ معدل الاستهلاك) × 100.',
            'القيادة حتى جفاف التانكي تماماً يتلف طلمبة الوقود (طرمبة البنزين) لأنها تعتمد على غمرها بالبنزين لتبريدها.'
        ],
        'assumptions' => 'يفترض عدم وجود تسريب وقود وضغط إطارات سليم.',
        'faqs' => [
            ['q' => 'كم كيلومتر تمشي السيارة بعد إضاءة لمبة البنزين؟', 'a' => 'في معظم السيارات الحديثة، تحتوي لمبة البنزين على احتياطي يتراوح بين 7 إلى 10 لترات، وهو ما يكفي لقطع مسافة 50 إلى 80 كم تقريباً للوصول لأقرب محطة.']
        ],
        'related' => ['fuel-by-distance-calculator', 'car-consumption-calculator', 'monthly-gas-cost-calculator']
    ],

    'car-consumption-calculator' => [
        'title' => 'حاسبة استهلاك السيارة (لتر/100 كم و كم/لتر)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'distanceDrivenKm', 'label' => 'المسافة المقطوعة بالرحلة (كم)', 'type' => 'number', 'default' => '420', 'min' => '10', 'step' => '10'],
            ['id' => 'fuelUsedLiters', 'label' => 'كمية الوقود المستهلكة لإعادة ملء التانكي (باللتر)', 'type' => 'number', 'default' => '35', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const km = Math.max(10, parseFloat(document.getElementById('distanceDrivenKm').value) || 420);
            const liters = Math.max(1, parseFloat(document.getElementById('fuelUsedLiters').value) || 35);

            const litersPer100Km = (liters / km) * 100;
            const kmPerLiter = km / liters;

            let rating = 'اقتصادي وممتاز جداً  ممتاز ✅';
            let color = '#10b981';
            if (litersPer100Km > 8.5 && litersPer100Km <= 12) { rating = 'استهلاك متوسط طبيعي ⚠️'; color = '#3b82f6'; }
            if (litersPer100Km > 12) { rating = 'استهلاك مرتفع (سيارة شرهة للوقود) ❌'; color = '#ef4444'; }

            setPrimaryResult(litersPer100Km.toFixed(1) + ' لتر / 100 كم (' + kmPerLiter.toFixed(1) + ' كم / لتر)', 'معدل استهلاك السيارة الفعلي');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك القياسي (لتر لكل 100 كم)', value: litersPer100Km.toFixed(2) + ' L/100km', color: '#3b82f6' },
                { label: 'المسافة المقطوعة لكل لتر واحد', value: kmPerLiter.toFixed(2) + ' كم / لتر', color: '#10b981' },
                { label: 'تقييم كفاءة استهلاك الوقود', value: rating, color: color },
                { label: 'المسافة المقطوعة بالتجربة', value: km + ' كم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك سيارتك فعلياً <strong>\${litersPer100Km.toFixed(1)} لتر لكل 100 كم</strong>، أي أن كل لتر وقود يقطع مسافة <strong>\${kmPerLiter.toFixed(1)} كم</strong> - تقييم الأداء: <strong>\${rating}</strong>.</p>
            `);
        ",
        'points' => [
            'معدل الاستهلاك (لتر/100 كم) = (اللترات المستهلكة ÷ الكيلومترات المقطوعة) × 100.',
            'المعدل المقلوب (كم/لتر) = الكيلومترات المقطوعة ÷ اللترات المستهلكة.',
            'كلما قل رقم (لتر/100 كم) كان استهلاك السيارة أفضل وأكثر توفيراً.'
        ],
        'assumptions' => 'يفترض قياس دقيق بإعادة تعبئة التانكي بالكامل من نفس مضخة الوقود.',
        'faqs' => [
            ['q' => 'ما هو الاستهلاك المعتبر اقتصادياً للسيارات السيدان؟', 'a' => 'السيارات التي تستهلك أقل من 6.5 إلى 7.5 لتر لكل 100 كم (أو تقطع أكثر من 14 إلى 16 كم لكل لتر) تصنف كسيارات موفرة وممتازة لاستهلاك الوقود.']
        ],
        'related' => ['fuel-by-distance-calculator', 'distance-fuel-calculator', 'gas-vs-ev-calculator']
    ],

    'real-car-cost-calculator' => [
        'title' => 'حاسبة تكلفة السيارة الحقيقية (التكلفة الإجمالية للملكية TCO)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'carPurchasePrice', 'label' => 'سعر شراء السيارة', 'type' => 'number', 'default' => '90000', 'min' => '5000', 'step' => '5000'],
            ['id' => 'ownershipYears', 'label' => 'مدة الاحتفاظ بالسيارة قبل البيع (بالسنوات)', 'type' => 'select', 'options' => [
                '3' => '3 سنوات',
                '5' => '5 سنوات (المعيار الأكثر شيوعاً)',
                '7' => '7 سنوات',
                '10' => '10 سنوات'
            ], 'default' => '5'],
            ['id' => 'resaleValuePercent', 'label' => 'القيمة المتبقية التقديرية للسيارة عند بيعها (%)', 'type' => 'number', 'default' => '45', 'min' => '10', 'max' => '80', 'step' => '5'],
            ['id' => 'yearlyFuelCostInput', 'label' => 'تكلفة البنزين السنوية المتوقعة', 'type' => 'number', 'default' => '7200', 'min' => '1000', 'step' => '500'],
            ['id' => 'yearlyInsuranceAndMaint', 'label' => 'التأمين والصيانة الدورية والتراخيص سنوياً', 'type' => 'number', 'default' => '4500', 'min' => '500', 'step' => '500'],
        ],
        'calcJs' => "
            const price = Math.max(5000, parseFloat(document.getElementById('carPurchasePrice').value) || 90000);
            const years = parseInt(document.getElementById('ownershipYears').value) || 5;
            const resalePct = Math.max(10, Math.min(80, parseFloat(document.getElementById('resaleValuePercent').value) || 45)) / 100;
            const fuelYear = Math.max(1000, parseFloat(document.getElementById('yearlyFuelCostInput').value) || 7200);
            const maintYear = Math.max(500, parseFloat(document.getElementById('yearlyInsuranceAndMaint').value) || 4500);
            const curr = getSelectedCurrency();

            const resaleValue = price * resalePct;
            const totalDepreciation = price - resaleValue;
            const totalFuel = fuelYear * years;
            const totalMaint = maintYear * years;
            const totalCostOfOwnership = totalDepreciation + totalFuel + totalMaint;
            const monthlyTco = totalCostOfOwnership / (years * 12);

            setPrimaryResult(formatMoney(monthlyTco, curr) + ' شهرياً', 'التكلفة الحقيقية الكاملة لامتلاك السيارة (TCO)');
            showResultArea();

            setDetailStats([
                { label: 'انخفاض قيمة السيارة (Depreciation)', value: formatMoney(totalDepreciation, curr), color: '#ef4444' },
                { label: 'إجمالي تكاليف الوقود طوال ' + years + ' سنوات', value: formatMoney(totalFuel, curr), color: '#f59e0b' },
                { label: 'إجمالي الصيانة والتأمين والتراخيص', value: formatMoney(totalMaint, curr), color: '#3b82f6' },
                { label: 'القيمة المستردة عند بيع السيارة مستقبلاً', value: formatMoney(resaleValue, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>خلال <strong>\${years} سنوات</strong>، تكلفك هذه السيارة فعلياً <strong>\${formatMoney(totalCostOfOwnership, curr)}</strong> (بمعدل <strong>\${formatMoney(monthlyTco, curr)} شهرياً</strong>). انخفاض قيمة السيارة وحده يمثل <strong>\${formatMoney(totalDepreciation, curr)}</strong> وهي خسارة غير مرئية حتى لحظة البيع.</p>
            `);
        ",
        'points' => [
            'انخفاض القيمة السوقية (Depreciation) هو أكبر تكلفة خفية لامتلاك أي سيارة جديدة، حيث تفقد السيارة بين 15% إلى 25% من قيمتها في السنة الأولى وحدها.',
            'التكلفة الإجمالية للملكية (TCO) = هبوط القيمة + الوقود + التأمين + الصيانة + الفوائد.'
        ],
        'assumptions' => 'السيارة تحافظ على حوالي 40% إلى 50% من قيمتها الأصلية بعد 5 سنوات من الاستخدام العادي.',
        'faqs' => [
            ['q' => 'كيف أشتري سيارة بأقل خسارة في انخفاض القيمة؟', 'a' => 'شراء سيارة مستعملة نظيفة بعمر سنتين إلى 3 سنوات يجعل المالك الأول يتحمل قمة هبوط القيمة (Depreciation)، وتشتريها أنت بسعر منخفض وتبيعها لاحقاً بخسارة طفيفة جداً.']
        ],
        'related' => ['monthly-car-cost-calculator', 'buy-car-vs-transport-calculator', 'gas-vs-ev-calculator']
    ],

    'buy-car-vs-transport-calculator' => [
        'title' => 'حاسبة شراء سيارة أم المواصلات وتطبيقات النقل',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'uberRideCostAvg', 'label' => 'متوسط تكلفة المشوار الواحد بتطبيقات النقل (أوبر/كريم/تاكسي)', 'type' => 'number', 'default' => '35', 'min' => '5', 'step' => '5'],
            ['id' => 'ridesPerDayAvg', 'label' => 'متوسط عدد المشاوير اليومية', 'type' => 'number', 'default' => '2', 'min' => '1', 'max' => '10', 'step' => '1'],
            ['id' => 'carTotalMonthlyOwnership', 'label' => 'التكلفة الشهرية المتوقعة لشراء وامتلاك سيارة (قسط + وقود + صيانة)', 'type' => 'number', 'default' => '2200', 'min' => '300', 'step' => '100'],
        ],
        'calcJs' => "
            const rideCost = Math.max(5, parseFloat(document.getElementById('uberRideCostAvg').value) || 35);
            const ridesDay = Math.max(1, parseInt(document.getElementById('ridesPerDayAvg').value) || 2);
            const carMonthly = Math.max(300, parseFloat(document.getElementById('carTotalMonthlyOwnership').value) || 2200);
            const curr = getSelectedCurrency();

            const monthlyTransport = rideCost * ridesDay * 30;
            const diff = carMonthly - monthlyTransport;

            let winner = '';
            let statusColor = '#10b981';
            if (monthlyTransport < carMonthly) {
                winner = 'تطبيقات النقل والمواصلات أوفر بـ ' + formatMoney(Math.abs(diff), curr) + ' شهرياً 🚕';
                statusColor = '#10b981';
            } else {
                winner = 'امتلاك سيارة خاصة أوفر بـ ' + formatMoney(diff, curr) + ' شهرياً 🚗';
                statusColor = '#3b82f6';
            }

            setPrimaryResult(winner, 'القرار المالي الأوفر شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة تطبيقات النقل شهرياً', value: formatMoney(monthlyTransport, curr), color: '#3b82f6' },
                { label: 'تكلفة امتلاك السيارة شهرياً', value: formatMoney(carMonthly, curr), color: '#ef4444' },
                { label: 'الوفر السنوي للخيار الأفضل', value: formatMoney(Math.abs(diff) * 12, curr), color: '#10b981' },
                { label: 'عدد المشاوير الشهرية', value: (ridesDay * 30) + ' مشوار', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بمعدل <strong>\${ridesDay} مشاوير يومياً</strong>، تدفع لتطبيقات النقل <strong>\${formatMoney(monthlyTransport, curr)} شهرياً</strong>، مقارنة بـ <strong>\${formatMoney(carMonthly, curr)}</strong> لامتلاك سيارة. النتيجة: <strong>\${winner}</strong>.</p>
            `);
        ",
        'points' => [
            'تطبيقات النقل تعفيك من تكاليف التأمين، فحص المركبة، ركن السيارات، والصيانة، وتوفر عليك التوتر أثناء القيادة في الازدحام.',
            'السيارة الخاصة تمنح حرية حركة وراحة مطلقة وملاءمة أفضل للعائلات والتنقل المتكرر.'
        ],
        'assumptions' => 'المقارنة مالية بحتة دون احتساب الجوانب النفسية والراحة الشخصية.',
        'faqs' => [
            ['q' => 'متى يكون امتلاك سيارة خياراً حتمياً؟', 'a' => 'عند وجود أطفال ومسؤوليات مدرسية يومية، أو عندما يتطلب عملك التنقل بين عدة مواقع متباعدة يصعب تغطيتها بالتطبيقات دون تكلفة باهظة.']
        ],
        'related' => ['monthly-car-cost-calculator', 'real-car-cost-calculator', 'monthly-gas-cost-calculator']
    ],

    'gas-vs-ev-calculator' => [
        'title' => 'حاسبة سيارة بنزين مقابل سيارة كهربائية (EV)',
        'isFinancial' => true,
        'inputs' => [
            ['id' => 'yearlyKmCompare', 'label' => 'المسافة السنوية المقطوعة (كم)', 'type' => 'number', 'default' => '25000', 'min' => '5000', 'step' => '1000'],
            ['id' => 'gasCarL100', 'label' => 'استهلاك سيارة البنزين (لتر/100 كم)', 'type' => 'number', 'default' => '8.5', 'min' => '4', 'max' => '20', 'step' => '0.5'],
            ['id' => 'gasLiterCostComp', 'label' => 'سعر لتر البنزين', 'type' => 'number', 'default' => '2.18', 'min' => '0.1', 'step' => '0.05'],
            ['id' => 'evKwhPer100', 'label' => 'استهلاك السيارة الكهربائية (kWh لكل 100 كم) - المعتاد 16 إلى 20', 'type' => 'number', 'default' => '17.5', 'min' => '10', 'max' => '30', 'step' => '0.5'],
            ['id' => 'evHomeKwhCost', 'label' => 'سعر كيلوواط الكهرباء للشحن المنزلي (kWh)', 'type' => 'number', 'default' => '0.18', 'min' => '0.01', 'step' => '0.01'],
        ],
        'calcJs' => "
            const km = Math.max(5000, parseFloat(document.getElementById('yearlyKmCompare').value) || 25000);
            const gasRate = Math.max(4, parseFloat(document.getElementById('gasCarL100').value) || 8.5);
            const gasPrice = Math.max(0.1, parseFloat(document.getElementById('gasLiterCostComp').value) || 2.18);
            const evRate = Math.max(10, parseFloat(document.getElementById('evKwhPer100').value) || 17.5);
            const evPrice = Math.max(0.01, parseFloat(document.getElementById('evHomeKwhCost').value) || 0.18);
            const curr = getSelectedCurrency();

            // 1. تكلفة وقود البنزين سنوياً
            const annualGasLiters = (km / 100) * gasRate;
            const annualGasCost = annualGasLiters * gasPrice;
            const gasMaintAnnual = 2500; // غيار زيوت وبواجي وفلاتر

            // 2. تكلفة كهرباء شحن EV سنوياً
            const annualEvKwh = (km / 100) * evRate;
            const annualEvCost = annualEvKwh * evPrice;
            const evMaintAnnual = 800; // صيانة منخفضة جداً بدون زيوت ومكابس

            const totalGasYear = annualGasCost + gasMaintAnnual;
            const totalEvYear = annualEvCost + evMaintAnnual;
            const annualSavings = totalGasYear - totalEvYear;

            setPrimaryResult(formatMoney(annualSavings, curr) + ' وفر سنوي', 'الوفر السنوي لصالح السيارة الكهربائية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة وقود البنزين والصيانة سنوياً', value: formatMoney(totalGasYear, curr), color: '#ef4444' },
                { label: 'تكلفة شحن الكهرباء والصيانة سنوياً', value: formatMoney(totalEvYear, curr), color: '#10b981' },
                { label: 'نسبة توفير الطاقة مع السيارة الكهربائية', value: (((totalGasYear - totalEvYear) / totalGasYear) * 100).toFixed(0) + '% وفر', color: '#3b82f6' },
                { label: 'الوفر التراكمي على مدى 5 سنوات', value: formatMoney(annualSavings * 5, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>\${km.toLocaleString()} كم سنوياً</strong>، تدفع لسيارة البنزين <strong>\${formatMoney(totalGasYear, curr)}</strong> سنوياً مقابل <strong>\${formatMoney(totalEvYear, curr)}</strong> للسيارة الكهربائية، محققاً وفراً مذهلاً قدره <strong>\${formatMoney(annualSavings, curr)} سنوياً</strong> في مصاريف التشغيل والصيانة.</p>
            `);
        ",
        'points' => [
            'السيارات الكهربائية توفر ما بين 60% إلى 80% من تكلفة الوقود عند الشحن المنزلي الرخيص.',
            'محرك السيارة الكهربائية يحتوي على حوالي 20 قطعة متحركة فقط مقابل أكثر من 2000 قطعة في محرك الاحتراق الداخلي، مما يلغي تماماً مصاريف غيار الزيوت، الفلاتر، البواجي، وسيور التيمن.'
        ],
        'assumptions' => 'يفترض شحن منزلي بمعظم الوقت (الشواحن السريعة العامة تكون تكلفتها أعلى قليلاً).',
        'faqs' => [
            ['q' => 'ماذا عن تكلفة تغيير بطارية السيارة الكهربائية؟', 'a' => 'معظم بطاريات السيارات الكهربائية الحديثة تضمنها الشركات المصنعة لمدة 8 سنوات أو 160,000 كم، وتشير الدراسات الواقعية إلى أن البطاريات تفقد أقل من 10% إلى 15% من سعتها بعد قطع أكثر من 250,000 كم.']
        ],
        'related' => ['real-car-cost-calculator', 'car-consumption-calculator', 'monthly-gas-cost-calculator']
    ],
];

echo "Generating Group D: Auto & Travel Tools (11 tools)...\n";
foreach ($toolsD as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group D!\n";
