<?php
/**
 * مولد أدوات المجموعة E: الدراسة والتعليم (19 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsE = [
    'grade-percentage-calculator' => [
        'title' => 'حاسبة النسبة من العلامات والتقدير الأكاديمي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'studentScore', 'label' => 'العلامة / الدرجة التي حصلت عليها', 'type' => 'number', 'default' => '88', 'min' => '0', 'step' => '0.5'],
            ['id' => 'maxScore', 'label' => 'الدرجة الكلية العظمى للامتحان', 'type' => 'number', 'default' => '100', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const score = Math.max(0, parseFloat(document.getElementById('studentScore').value) || 0);
            const max = Math.max(1, parseFloat(document.getElementById('maxScore').value) || 100);

            const percentage = (score / max) * 100;
            let rating = 'راسب ❌';
            let gpa4 = 0;
            let letter = 'F';
            let color = '#ef4444';

            if (percentage >= 95) { rating = 'ممتاز مرتفع (A+) ⭐'; gpa4 = 4.0; letter = 'A+'; color = '#10b981'; }
            else if (percentage >= 90) { rating = 'ممتاز (A) 🌟'; gpa4 = 3.75; letter = 'A'; color = '#10b981'; }
            else if (percentage >= 85) { rating = 'جيد جداً مرتفع (B+)'; gpa4 = 3.5; letter = 'B+'; color = '#3b82f6'; }
            else if (percentage >= 80) { rating = 'جيد جداً (B)'; gpa4 = 3.0; letter = 'B'; color = '#3b82f6'; }
            else if (percentage >= 75) { rating = 'جيد مرتفع (C+)'; gpa4 = 2.5; letter = 'C+'; color = '#f59e0b'; }
            else if (percentage >= 70) { rating = 'جيد (C)'; gpa4 = 2.0; letter = 'C'; color = '#f59e0b'; }
            else if (percentage >= 65) { rating = 'مقبول مرتفع (D+)'; gpa4 = 1.5; letter = 'D+'; color = '#f59e0b'; }
            else if (percentage >= 60) { rating = 'مقبول (D)'; gpa4 = 1.0; letter = 'D'; color = '#f59e0b'; }

            setPrimaryResult(percentage.toFixed(2) + '% (' + letter + ')', 'النسبة المئوية والتقدير الأكاديمي');
            showResultArea();

            setDetailStats([
                { label: 'التقدير العام المعتمد', value: rating, color: color },
                { label: 'المعدل المكافئ من 4.0 (GPA)', value: gpa4.toFixed(2) + ' / 4.0', color: '#3b82f6' },
                { label: 'الدرجات المفقودة من الدرجة النهائية', value: (max - score).toFixed(1) + ' درجة', color: '#ef4444' },
                { label: 'الدرجة المدخلة', value: score + ' من ' + max, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>نسبتك المئوية هي <strong>\${percentage.toFixed(2)}%</strong> بتقدير <strong>\${rating}</strong> ومعدل <strong>\${gpa4.toFixed(2)} من 4.0</strong>.</p>
            `);
        ",
        'points' => [
            'النسبة المئوية = (درجة الطالب ÷ الدرجة العظمى) × 100.',
            'التقديرات المعتمدة مطابقة لمعايير الجامعات العربية والسلالم الأكاديمية الدولية.'
        ],
        'assumptions' => 'يفترض حد أدنى للنجاح 60% لمعظم الكليات الجامعية.',
        'faqs' => [
            ['q' => 'كيف أحول النسبة المئوية لمعدل من 4 أو 5؟', 'a' => 'استخدم حاسبة المعدل التراكمي GPA المخصصة أو جدول التحويل المعياري للأحرف والرموز.']
        ],
        'related' => ['passing-grade-calculator', 'target-gpa-calculator', 'cumulative-gpa-calculator']
    ],

    'passing-grade-calculator' => [
        'title' => 'حاسبة العلامة المطلوبة للنجاح في المادة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'courseworkScore', 'label' => 'مجموع درجات أعمال السنة والامتحان النصفي التي حصلت عليها', 'type' => 'number', 'default' => '28', 'min' => '0', 'max' => '100', 'step' => '0.5'],
            ['id' => 'courseworkMax', 'label' => 'الدرجة العظمى لأعمال السنة (المعتاد 40 إلى 50 درجة)', 'type' => 'number', 'default' => '40', 'min' => '10', 'max' => '100', 'step' => '5'],
            ['id' => 'passingScoreNeeded', 'label' => 'درجة النجاح الكلية المشروطة في المادة (المعتاد 50 أو 60)', 'type' => 'number', 'default' => '60', 'min' => '40', 'max' => '100', 'step' => '5'],
            ['id' => 'finalExamMax', 'label' => 'الدرجة العظمى للامتحان النهائي (Final Exam)', 'type' => 'number', 'default' => '60', 'min' => '10', 'max' => '100', 'step' => '5'],
        ],
        'calcJs' => "
            const current = Math.max(0, parseFloat(document.getElementById('courseworkScore').value) || 0);
            const cwMax = Math.max(10, parseFloat(document.getElementById('courseworkMax').value) || 40);
            const passReq = Math.max(40, parseFloat(document.getElementById('passingScoreNeeded').value) || 60);
            const finalMax = Math.max(10, parseFloat(document.getElementById('finalExamMax').value) || 60);

            const neededFinal = Math.max(0, passReq - current);
            const neededPercentageOfFinal = (neededFinal / finalMax) * 100;

            let status = 'فرصة ممتازة وسهلة التحقيق ✅';
            let color = '#10b981';
            if (neededFinal > finalMax) { status = 'للأسف يستحيل النجاح حتى بالدرجة النهائية الكاملة ❌'; color = '#ef4444'; }
            else if (neededPercentageOfFinal > 75) { status = 'صعبة وتحتاج لمذاكرة مركزة جداً ⚠️'; color = '#f59e0b'; }

            setPrimaryResult(neededFinal.toFixed(1) + ' من ' + finalMax + ' (' + neededPercentageOfFinal.toFixed(1) + '%)', 'الدرجة المطلوبة في الفاينل للنجاح');
            showResultArea();

            setDetailStats([
                { label: 'العلامة التي تضمن النجاح بالضبط', value: neededFinal.toFixed(1) + ' درجة', color: '#3b82f6' },
                { label: 'نسبة الإنجاز المطلوبة من ورقة الفاينل', value: neededPercentageOfFinal.toFixed(1) + '%', color: '#10b981' },
                { label: 'تقييم فرصة النجاح', value: status, color: color },
                { label: 'مجموع درجاتك الحالي', value: current + ' من ' + cwMax, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج للحصول على <strong>\${neededFinal.toFixed(1)} درجة من أصل \${finalMax}</strong> في الامتحان النهائي (أي إجابة صحيحة بنسبة <strong>\${neededPercentageOfFinal.toFixed(1)}%</strong> من أسئلة الامتحان) للوصول لدرجة النجاح \${passReq}.</p>
            `);
        ",
        'points' => [
            'الدرجة المطلوبة في النهائي = درجة النجاح المطلوبة - مجموع درجات أعمال السنة المحققة.',
            'انتبه إلى اشتراط بعض الجامعات حصول الطالب على نسبة معينة في الامتحان النهائي كشرط نجاح مستقل (مثلاً 30% من ورقة الفاينل).'
        ],
        'assumptions' => 'يفترض عدم وجود شرط رسوب منفصل للورقة الامتحانية النهائية.',
        'faqs' => [
            ['q' => 'ماذا لو كانت درجتي الحالية أعلى من درجة النجاح بالفعل؟', 'a' => 'إذا كانت درجات أعمالك تجاوزت 60 درجة مسبقاً، فأنت ناجح في المادة رسمياً، ولكن حضور الامتحان النهائي يظل إلزامياً للحصول على تقدير مرتفع ولمنع الحرمان.']
        ],
        'related' => ['final-grade-calculator', 'target-gpa-calculator', 'grade-percentage-calculator']
    ],

    'target-gpa-calculator' => [
        'title' => 'حاسبة العلامة المطلوبة للوصول لمعدل معين (GPA)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'currentCgpa', 'label' => 'المعدل التراكمي الحالي (CGPA)', 'type' => 'number', 'default' => '3.10', 'min' => '0', 'max' => '5.0', 'step' => '0.01'],
            ['id' => 'completedHours', 'label' => 'عدد الساعات المكتسبة السابقة المنجزة', 'type' => 'number', 'default' => '60', 'min' => '1', 'max' => '250', 'step' => '1'],
            ['id' => 'targetCgpa', 'label' => 'المعدل التراكمي المستهدف المطلوب تحقيقه', 'type' => 'number', 'default' => '3.40', 'min' => '1', 'max' => '5.0', 'step' => '0.01'],
            ['id' => 'nextSemesterHours', 'label' => 'عدد الساعات المسجلة في الفصل القادم', 'type' => 'number', 'default' => '15', 'min' => '3', 'max' => '25', 'step' => '1'],
            ['id' => 'gpaScaleType', 'label' => 'نظام المعدل بالجامعة', 'type' => 'select', 'options' => [
                '4' => 'نظام 4.0 نقاط',
                '5' => 'نظام 5.0 نقاط (النظام السعودي المعتمد)'
            ], 'default' => '4'],
        ],
        'calcJs' => "
            const currentGpa = Math.max(0, parseFloat(document.getElementById('currentCgpa').value) || 3.10);
            const prevHours = Math.max(1, parseFloat(document.getElementById('completedHours').value) || 60);
            const targetGpa = Math.max(1, parseFloat(document.getElementById('targetCgpa').value) || 3.40);
            const nextHours = Math.max(3, parseFloat(document.getElementById('nextSemesterHours').value) || 15);
            const scale = parseFloat(document.getElementById('gpaScaleType').value) || 4;

            const totalHoursAfter = prevHours + nextHours;
            const targetTotalPoints = targetGpa * totalHoursAfter;
            const currentTotalPoints = currentGpa * prevHours;
            const neededSemesterPoints = targetTotalPoints - currentTotalPoints;
            const requiredSemesterGpa = neededSemesterPoints / nextHours;

            let status = 'هدف ممكن ومتاح بالاجتهاد ✅';
            let color = '#10b981';
            if (requiredSemesterGpa > scale) {
                status = 'مستحيل في فصل واحد! تحتاج لفصول إضافية لرفع المعدل ❌';
                color = '#ef4444';
            } else if (requiredSemesterGpa >= (scale * 0.9)) {
                status = 'يتطلب معدل امتياز مرتفع A+ في جميع مواد الفصل 🌟';
                color = '#f59e0b';
            }

            setPrimaryResult(requiredSemesterGpa > scale ? 'غير ممكن في فصل واحد' : requiredSemesterGpa.toFixed(2) + ' / ' + scale, 'المعدل الفصلي المطلوب في الفصل القادم');
            showResultArea();

            setDetailStats([
                { label: 'المعدل الفصلي المطلوب بالضبط', value: requiredSemesterGpa.toFixed(2) + ' من ' + scale, color: '#3b82f6' },
                { label: 'إمكانية التحقيق في هذا الفصل', value: status, color: color },
                { label: 'إجمالي الساعات الكلية بعد الفصل', value: totalHoursAfter + ' ساعة', color: '#8b5cf6' },
                { label: 'النقاط التراكمية الإضافية المطلوبة', value: neededSemesterPoints.toFixed(1) + ' نقطة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لرفع معدلك التراكمي من <strong>\${currentGpa}</strong> إلى <strong>\${targetGpa}</strong> خلال <strong>\${nextHours} ساعة</strong>، يجب أن تحقق معدلاً فصلياً لا يقل عن <strong>\${requiredSemesterGpa.toFixed(2)} من \${scale}</strong>.</p>
            `);
        ",
        'points' => [
            'المعدل الفصلي المطلوب = ((المعدل المستهدف × إجمالي الساعات الجديدة) - (المعدل الحالي × الساعات السابقة)) ÷ ساعات الفصل القادم.',
            'كلما زادت الساعات المنجزة مسبقاً، كلما تطلب رفع المعدل مجهوداً أكبر وأصبح تحركه أبطأ.'
        ],
        'assumptions' => 'يفترض عدم حذف أو رسوب في أي من مواد الفصل المسجلة.',
        'faqs' => [
            ['q' => 'ما العمل إذا كان المعدل المطلوب أكبر من الحد الأقصى (مثلاً أكبر من 4.0)؟', 'a' => 'هذا يعني أن عدد الساعات المسجلة في الفصل غير كافٍ لرفع المعدل لهذه الدرجة، والحل هو تقسيم الهدف على فصلين أو ثلاثة فصول دراسية قادمة.']
        ],
        'related' => ['cumulative-gpa-calculator', 'next-semester-gpa-calculator', 'grade-percentage-calculator']
    ],

    'final-grade-calculator' => [
        'title' => 'حاسبة المعدل النهائي للمادة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'homeworkScore', 'label' => 'الواجبات والمشاريع (الدرجة المحققة / الوزن)', 'type' => 'number', 'default' => '18', 'min' => '0', 'max' => '30', 'step' => '1'],
            ['id' => 'homeworkWeight', 'label' => 'وزن الواجبات في المادة (%)', 'type' => 'number', 'default' => '20', 'min' => '5', 'max' => '50', 'step' => '5'],
            ['id' => 'midtermScore', 'label' => 'درجة الامتحان النصفي (Midterm)', 'type' => 'number', 'default' => '26', 'min' => '0', 'max' => '40', 'step' => '1'],
            ['id' => 'midtermWeight', 'label' => 'وزن الامتحان النصفي (%)', 'type' => 'number', 'default' => '30', 'min' => '10', 'max' => '50', 'step' => '5'],
            ['id' => 'finalScore', 'label' => 'درجة الامتحان النهائي المتوقعة أو الفعلية', 'type' => 'number', 'default' => '45', 'min' => '0', 'max' => '60', 'step' => '1'],
            ['id' => 'finalWeight', 'label' => 'وزن الامتحان النهائي (%)', 'type' => 'number', 'default' => '50', 'min' => '20', 'max' => '70', 'step' => '5'],
        ],
        'calcJs' => "
            const hwScore = Math.max(0, parseFloat(document.getElementById('homeworkScore').value) || 0);
            const hwWeight = Math.max(5, parseFloat(document.getElementById('homeworkWeight').value) || 20);
            const midScore = Math.max(0, parseFloat(document.getElementById('midtermScore').value) || 0);
            const midWeight = Math.max(10, parseFloat(document.getElementById('midtermWeight').value) || 30);
            const finScore = Math.max(0, parseFloat(document.getElementById('finalScore').value) || 0);
            const finWeight = Math.max(20, parseFloat(document.getElementById('finalWeight').value) || 50);

            // حساب النسبة الموزونة
            const totalScore = (hwScore) + (midScore) + (finScore);
            const totalWeights = hwWeight + midWeight + finWeight;

            let letterGrade = 'F';
            let statusColor = '#ef4444';
            if (totalScore >= 90) { letterGrade = 'A (ممتاز)'; statusColor = '#10b981'; }
            else if (totalScore >= 80) { letterGrade = 'B (جيد جداً)'; statusColor = '#3b82f6'; }
            else if (totalScore >= 70) { letterGrade = 'C (جيد)'; statusColor = '#f59e0b'; }
            else if (totalScore >= 60) { letterGrade = 'D (مقبول)'; statusColor = '#f59e0b'; }

            setPrimaryResult(totalScore.toFixed(1) + ' من 100 (' + letterGrade + ')', 'المعدل النهائي الإجمالي للمادة');
            showResultArea();

            setDetailStats([
                { label: 'التقدير الحرفي المستحق', value: letterGrade, color: statusColor },
                { label: 'مجموع الأوزان الموزونة', value: totalWeights + '%', color: '#3b82f6' },
                { label: 'نقاط أعمال السنة والنصفي', value: (hwScore + midScore).toFixed(1) + ' درجة', color: '#10b981' },
                { label: 'نقاط الامتحان النهائي', value: finScore.toFixed(1) + ' درجة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مجموعك النهائي في هذه المادة هو <strong>\${totalScore.toFixed(1)} من 100</strong> بتقدير <strong>\${letterGrade}</strong>.</p>
            `);
        ",
        'points' => [
            'الدرجة الموزونة = (درجة الواجبات × وزنها) + (درجة النصفي × وزنه) + (درجة الفاينل × وزنه).',
            'التأكد من أن مجموع الأوزان يساوي 100% لضمان صحة النتيجة النهائية.'
        ],
        'assumptions' => 'يفترض توزيع درجات قياسي بإجمالي 100 درجة للمقرر.',
        'faqs' => [
            ['q' => 'كيف تؤثر درجات الواجبات على التقدير العام؟', 'a' => 'الواجبات والمشاريع تمثل عادة درجات مضمونة وسهلة التحصيل تضمن للطالب النجاح حتى لو واجه صعوبة في الامتحان النهائي.']
        ],
        'related' => ['passing-grade-calculator', 'target-gpa-calculator', 'grade-percentage-calculator']
    ],

    'cumulative-gpa-calculator' => [
        'title' => 'حاسبة المعدل التراكمي الشاملة (GPA Calculator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'previousGpa', 'label' => 'المعدل التراكمي السابق (اتركه 0 إذا كان الفصل الأول)', 'type' => 'number', 'default' => '3.25', 'min' => '0', 'max' => '5.0', 'step' => '0.01'],
            ['id' => 'previousHours', 'label' => 'عدد الساعات المكتسبة السابقة', 'type' => 'number', 'default' => '45', 'min' => '0', 'max' => '250', 'step' => '1'],
            ['id' => 'currentSemGpa', 'label' => 'المعدل الفصلي للفصل الحالي', 'type' => 'number', 'default' => '3.80', 'min' => '0', 'max' => '5.0', 'step' => '0.01'],
            ['id' => 'currentSemHours', 'label' => 'عدد ساعات الفصل الحالي', 'type' => 'number', 'default' => '15', 'min' => '1', 'max' => '25', 'step' => '1'],
            ['id' => 'gpaMaxSystem', 'label' => 'النظام المعتمد لمعدل جامعتك', 'type' => 'select', 'options' => [
                '4' => 'من 4.0 نقاط',
                '5' => 'من 5.0 نقاط'
            ], 'default' => '4'],
        ],
        'calcJs' => "
            const prevGpa = Math.max(0, parseFloat(document.getElementById('previousGpa').value) || 0);
            const prevHours = Math.max(0, parseFloat(document.getElementById('previousHours').value) || 0);
            const semGpa = Math.max(0, parseFloat(document.getElementById('currentSemGpa').value) || 3.80);
            const semHours = Math.max(1, parseFloat(document.getElementById('currentSemHours').value) || 15);
            const maxScale = parseFloat(document.getElementById('gpaMaxSystem').value) || 4;

            const totalHours = prevHours + semHours;
            const prevPoints = prevGpa * prevHours;
            const semPoints = semGpa * semHours;
            const newCgpa = (prevPoints + semPoints) / totalHours;
            const gpaDiff = newCgpa - prevGpa;

            let changeText = 'ثابت';
            let changeColor = '#3b82f6';
            if (gpaDiff > 0) { changeText = 'ارتفاع بمقدار +' + gpaDiff.toFixed(2) + ' 📈'; changeColor = '#10b981'; }
            if (gpaDiff < 0) { changeText = 'انخفاض بمقدار ' + gpaDiff.toFixed(2) + ' 📉'; changeColor = '#ef4444'; }

            setPrimaryResult(newCgpa.toFixed(2) + ' من ' + maxScale, 'المعدل التراكمي الجديد (New CGPA)');
            showResultArea();

            setDetailStats([
                { label: 'المعدل التراكمي الجديد', value: newCgpa.toFixed(2) + ' / ' + maxScale, color: '#10b981' },
                { label: 'حركة وتغير المعدل', value: changeText, color: changeColor },
                { label: 'إجمالي الساعات المكتسبة الكلية', value: totalHours + ' ساعة معتمدة', color: '#3b82f6' },
                { label: 'النسبة المئوية التقديرية المكافئة', value: ((newCgpa / maxScale) * 100).toFixed(1) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بعد إنجاز <strong>\${semHours} ساعات</strong> بمعدل فصلي <strong>\${semGpa}</strong>، أصبح معدلك التراكمي <strong>\${newCgpa.toFixed(2)} من \${maxScale}</strong> بإجمالي <strong>\${totalHours} ساعة مكتسبة</strong> (\${changeText}).</p>
            `);
        ",
        'points' => [
            'المعدل التراكمي الجديد = (مجموع النقاط السابقة + نقاط الفصل الحالي) ÷ إجمالي الساعات.',
            'نقاط الفصل = المعدل الفصلي × عدد ساعات الفصل.'
        ],
        'assumptions' => 'يفترض عدم إعادة مواد سابقة كانت محسوبة مسبقاً في الساعات.',
        'faqs' => [
            ['q' => 'كيف تؤثر المادة المعادة على المعدل التراكمي؟', 'a' => 'عند إعادة مادة، يُحذف التقدير القديم من احتساب المعدل في معظم الجامعات ويحل محله التقدير الجديد، مما يعطي قفزة إيجابية وسريعة للمعدل التراكمي.']
        ],
        'related' => ['target-gpa-calculator', 'next-semester-gpa-calculator', 'grade-percentage-calculator']
    ],

    'daily-study-hours-calculator' => [
        'title' => 'حاسبة ساعات الدراسة اليومية وجدول الامتحانات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'coursesCount', 'label' => 'عدد المواد أو المقررات المسجلة', 'type' => 'number', 'default' => '5', 'min' => '1', 'max' => '12', 'step' => '1'],
            ['id' => 'daysUntilExams', 'label' => 'الأيام المتبقية حتى بدء الامتحانات', 'type' => 'number', 'default' => '21', 'min' => '1', 'max' => '120', 'step' => '1'],
            ['id' => 'materialDifficulty', 'label' => 'متوسط صعوبة المقررات', 'type' => 'select', 'options' => [
                'light' => 'مواد نظرية خفيفة وقصيرة (حوالي 25 ساعة لكل مادة)',
                'medium' => 'مواد متوسطة ومتوازنة (حوالي 40 ساعة لكل مادة)',
                'heavy' => 'مواد علمية/طبية/هندسية دسمة (حوالي 65 ساعة لكل مادة)'
            ], 'default' => 'medium'],
            ['id' => 'breakDaysCount', 'label' => 'أيام راحة تخصصها للطوارئ والترفيه', 'type' => 'number', 'default' => '2', 'min' => '0', 'max' => '15', 'step' => '1'],
        ],
        'calcJs' => "
            const courses = Math.max(1, parseInt(document.getElementById('coursesCount').value) || 5);
            const totalDays = Math.max(1, parseInt(document.getElementById('daysUntilExams').value) || 21);
            const diff = document.getElementById('materialDifficulty').value;
            const breakDays = Math.max(0, parseInt(document.getElementById('breakDaysCount').value) || 2);

            let hoursPerCourse = 40;
            if (diff === 'light') hoursPerCourse = 25;
            if (diff === 'heavy') hoursPerCourse = 65;

            const effectiveDays = Math.max(1, totalDays - breakDays);
            const totalRequiredHours = courses * hoursPerCourse;
            const dailyHours = totalRequiredHours / effectiveDays;

            let status = 'جدول متوازن ومريح جداً ✅';
            let color = '#10b981';
            if (dailyHours > 8) { status = 'مكثف وشاق جداً! ابدأ فوراً وقلل أيام الراحة ⚠️'; color = '#ef4444'; }
            else if (dailyHours > 5) { status = 'متوسط ويحتاج التزاماً وانضباطاً يومياً 🎯'; color = '#f59e0b'; }

            setPrimaryResult(dailyHours.toFixed(1) + ' ساعة دراسة يومياً', 'الخطة اليومية الموصى بها');
            showResultArea();

            setDetailStats([
                { label: 'ساعات الدراسة الصافية يومياً', value: dailyHours.toFixed(1) + ' ساعة / يوم', color: '#3b82f6' },
                { label: 'إجمالي الساعات المطلوبة للمنهج كاملاً', value: totalRequiredHours + ' ساعة', color: '#10b981' },
                { label: 'أيام الدراسة الفعلية', value: effectiveDays + ' يوماً', color: '#f59e0b' },
                { label: 'تقييم ضغط الجدول', value: status, color: color }
            ]);

            setResultContent(`
                <p>لدراسة <strong>\${courses} مواد</strong> خلال <strong>\${effectiveDays} يوماً دراسياً</strong>، تحتاج إلى <strong>\${dailyHours.toFixed(1)} ساعة مذاكرة يومياً</strong> بمعدل جلسات بومودورو مقسمة بانتظام.</p>
            `);
        ",
        'points' => [
            'ساعات الدراسة اليومية = إجمالي الساعات التقديرية للمواد ÷ أيام الدراسة الفعلية.',
            'تطبيق تقنية بومودورو (25 دقيقة تركيز + 5 دقائق راحة) يزيد من قدرة الاستيعاب ويمنع الإجهاد الذهني.'
        ],
        'assumptions' => 'يفترض التركيز والابتعاد عن المشتتات والهواتف أثناء ساعات المذاكرة.',
        'faqs' => [
            ['q' => 'هل الأفضل دراسة مادة واحدة في اليوم أم عدة مواد؟', 'a' => 'الدراسات المعرفية تفضل التبديل بين مادتين مختلفتين يومياً (Interleaving Practice) مثل مادة حسابية ومادة نظرية لتنشيط فصوص الدماغ المختلفة ومنع الملل.']
        ],
        'related' => ['syllabus-finish-time-calculator', 'exam-countdown-calculator', 'revision-plan-calculator']
    ],

    'syllabus-finish-time-calculator' => [
        'title' => 'حاسبة وقت إنهاء المنهج الدراسي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalLessonsLeft', 'label' => 'عدد الدروس أو الفصول المتبقية لإنهاء المنهج', 'type' => 'number', 'default' => '24', 'min' => '1', 'step' => '1'],
            ['id' => 'lessonsPerDaySpeed', 'label' => 'عدد الدروس التي تستطيع إنجازها يومياً', 'type' => 'number', 'default' => '2', 'min' => '0.5', 'max' => '10', 'step' => '0.5'],
            ['id' => 'revisionDaysBuffer', 'label' => 'أيام إضافية مخصصة للمراجعة الشاملة وحل الامتحانات', 'type' => 'number', 'default' => '4', 'min' => '0', 'max' => '20', 'step' => '1'],
        ],
        'calcJs' => "
            const lessons = Math.max(1, parseFloat(document.getElementById('totalLessonsLeft').value) || 24);
            const speed = Math.max(0.5, parseFloat(document.getElementById('lessonsPerDaySpeed').value) || 2);
            const revision = Math.max(0, parseInt(document.getElementById('revisionDaysBuffer').value) || 4);

            const studyDays = Math.ceil(lessons / speed);
            const totalDaysWithRevision = studyDays + revision;

            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + totalDaysWithRevision);
            const finishDateStr = targetDate.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

            setPrimaryResult(totalDaysWithRevision + ' يوماً (' + finishDateStr + ')', 'الموعد المتوقع لختم المنهج والمراجعة');
            showResultArea();

            setDetailStats([
                { label: 'أيام دراسة المنهج الجديد', value: studyDays + ' يوماً', color: '#3b82f6' },
                { label: 'أيام المراجعة وحل النماذج', value: revision + ' أيام', color: '#10b981' },
                { label: 'معدل الدروس المنجزة أسبوعياً', value: (speed * 7).toFixed(1) + ' درس', color: '#f59e0b' },
                { label: 'عدد الدروس المتبقية', value: lessons + ' درساً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بمعدل إنجاز <strong>\${speed} دروس يومياً</strong>، ستختم المنهج خلال <strong>\${studyDays} يوماً</strong>، ومع إضافة <strong>\${revision} أيام للمراجعة</strong>، تكون جاهزاً تماماً بحلول <strong>\${finishDateStr}</strong>.</p>
            `);
        ",
        'points' => [
            'مدة الإنهاء = (عدد الدروس المتبقية ÷ معدل الإنجاز اليومي) + أيام المراجعة الاحتياطية.',
            'تخصيص أيام منفصلة لحل نماذج السنوات السابقة يرفع درجة الطالب بما لا يقل عن 15% إلى 20%.'
        ],
        'assumptions' => 'يفترض التزاماً يومياً ثابتاً بالمعدل المكتوب.',
        'faqs' => [
            ['q' => 'كيف ألتزم بإنهاء الدروس المقررة يومياً؟', 'a' => 'حدد وقت بداية المذاكرة بدقة (مثلاً السادسة صباحاً أو الرابعة عصراً) واعتبره موعداً مقدساً غير قابل للتأجيل، وتخلص من الهاتف خارج غرفة المذاكرة.']
        ],
        'related' => ['daily-study-hours-calculator', 'curriculum-progress-calculator', 'exam-countdown-calculator']
    ],

    'exam-countdown-calculator' => [
        'title' => 'حاسبة الأيام المتبقية للامتحان (العد التنازلي)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'examDateInput', 'label' => 'تاريخ بدء الامتحانات', 'type' => 'text', 'default' => '2026-06-15', 'placeholder' => 'YYYY-MM-DD'],
            ['id' => 'studyHoursPerDayAvail', 'label' => 'ساعات المذاكرة المتاحة لك يومياً', 'type' => 'number', 'default' => '5', 'min' => '1', 'max' => '16', 'step' => '1'],
        ],
        'calcJs' => "
            const dateStr = document.getElementById('examDateInput').value;
            const hoursDay = Math.max(1, parseFloat(document.getElementById('studyHoursPerDayAvail').value) || 5);

            const examDate = new Date(dateStr);
            const now = new Date();
            const diffTime = examDate - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (isNaN(diffDays) || diffDays < 0) {
                alert('يرجى إدخال تاريخ مستقبلي صحيح للامتحان بالصيغة YYYY-MM-DD');
                return;
            }

            const totalStudyHoursRemaining = diffDays * hoursDay;
            const weeksRemaining = (diffDays / 7).toFixed(1);

            setPrimaryResult(diffDays + ' يوماً متبقية (' + weeksRemaining + ' أسبوع)', 'العد التنازلي للامتحان');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ساعات المذاكرة المتاحة', value: totalStudyHoursRemaining + ' ساعة', color: '#10b981' },
                { label: 'عدد الأسابيع المتبقية', value: weeksRemaining + ' أسبوع', color: '#3b82f6' },
                { label: 'ساعات اليوم المعتمدة', value: hoursDay + ' ساعات / يوم', color: '#f59e0b' },
                { label: 'تاريخ الامتحان المحدد', value: examDate.toLocaleDateString('ar-EG'), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متبقي أمامك <strong>\${diffDays} يوماً</strong> حتى الامتحان. إذا خصصت <strong>\${hoursDay} ساعات يومياً</strong>، فسيكون لديك <strong>\${totalStudyHoursRemaining} ساعة مذاكرة صافية</strong> كافية جداً لتحقيق التفوق بالانضباط والتركيز.</p>
            `);
        ",
        'points' => [
            'العد التنازلي يساعد في كسر التسويف وتحويل الوقت المتبقي إلى ساعات عمل ملموسة وواضحة.',
            'حساب الساعات المتاحة يمنحك تصورا واقعيا لما يمكنك إنجازه ويقلل من التوتر النفسي.'
        ],
        'assumptions' => 'يفترض الحساب التاريخ الحالي لليوم مقارنة بتاريخ الاختبار.',
        'faqs' => [
            ['q' => 'كيف أتعامل مع قلق وتوتر اقتراب موعد الامتحانات؟', 'a' => 'التوتر ينتج من المجهول، وعندما تقسم المنهج إلى مهام يومية صغيرة ومكتوبة تشعر بالسيطرة على الموقف، بالإضافة للنوم الجيد لمدة 7 ساعات ليلاً.']
        ],
        'related' => ['daily-study-hours-calculator', 'revision-plan-calculator', 'syllabus-finish-time-calculator']
    ],

    'revision-plan-calculator' => [
        'title' => 'حاسبة خطة المراجعة الذكية (التكرار المتباعد Spaced Repetition)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'studyDateInput', 'label' => 'تاريخ اليوم الذي درست فيه الدرس لأول مرة', 'type' => 'text', 'default' => date('Y-m-d'), 'placeholder' => 'YYYY-MM-DD'],
            ['id' => 'topicName', 'label' => 'اسم الدرس أو الفصل', 'type' => 'text', 'default' => 'الفصل الأول - مقدمة المادة', 'placeholder' => 'اسم الموضوع'],
        ],
        'calcJs' => "
            const dateStr = document.getElementById('studyDateInput').value;
            const topic = document.getElementById('topicName').value || 'الموضوع';

            const baseDate = new Date(dateStr);
            if (isNaN(baseDate.getTime())) {
                alert('يرجى إدخال تاريخ صحيح بالصيغة YYYY-MM-DD');
                return;
            }

            const intervals = [
                { name: 'المراجعة 1 (تثبيت فوري)', days: 1, desc: 'مراجعة سريعة لمدة 10 دقائق لتثبيت الذاكرة الأولية' },
                { name: 'المراجعة 2 (كسر منحنى النسيان)', days: 3, desc: 'استرجاع نشط وحل 3 أسئلة على الدرس' },
                { name: 'المراجعة 3 (نقل للذاكرة طويلة المدى)', days: 7, desc: 'مراجعة خريطة المفاهيم والنقاط الرئيسية' },
                { name: 'المراجعة 4 (ترسيخ عميق)', days: 14, desc: 'حل أسئلة امتحانات سابقة بدون النظر للكتاب' },
                { name: 'المراجعة 5 (تثبيت دائم)', days: 30, desc: 'استرجاع شامل قبل الامتحان النهائي' }
            ];

            let scheduleHtml = '<div style=\"display:flex;flex-direction:column;gap:0.75rem;margin-top:1rem\">';
            intervals.forEach((item, idx) => {
                const revDate = new Date(baseDate);
                revDate.setDate(revDate.getDate() + item.days);
                const dateFormatted = revDate.toLocaleDateString('ar-EG', { weekday: 'short', month: 'short', day: 'numeric' });
                scheduleHtml += `
                    <div style=\"background:var(--bg-glass);padding:0.75rem 1rem;border-radius:var(--radius-md);border-right:4px solid var(--primary)\">
                        <div style=\"display:flex;justify-content:space-between;align-items:center;font-weight:600\">
                            <span>\${item.name} (+ \${item.days} أيام)</span>
                            <span style=\"color:var(--text-accent-light)\">\${dateFormatted}</span>
                        </div>
                        <div style=\"font-size:0.85rem;color:var(--text-secondary);margin-top:0.25rem\">\${item.desc}</div>
                    </div>
                `;
            });
            scheduleHtml += '</div>';

            setPrimaryResult('جدول 5 مراجعات ذكية مبني على منحنى النسيان (Ebbinghaus)', 'جدول المراجعة المتباعدة');
            showResultArea();

            setDetailStats([
                { label: 'نسبة الحفظ المتوقعة بعد 30 يوماً', value: '92% استرجاع ممتاز ⭐', color: '#10b981' },
                { label: 'وقت المراجعة الواحدة المقترح', value: '10 إلى 15 دقيقة فقط', color: '#3b82f6' },
                { label: 'الموضوع المراد مراجعته', value: topic, color: '#f59e0b' },
                { label: 'الأساس العلمي المعتمد', value: 'منحنى النسيان لإبنجهاوس', color: '#8b5cf6' }
            ]);

            setResultContent(scheduleHtml);
        ",
        'points' => [
            'أثبت العالم إبنجهاوس أن الإنسان ينسى حوالي 70% مما تعلمه بعد 24 ساعة فقط إذا لم يراجعه.',
            'المراجعة على فترات متصاعدة (يوم 1، يوم 3، يوم 7، يوم 14، يوم 30) تعيد قوة الذاكرة إلى 100% بأقل مجهود زمني ممكن.'
        ],
        'assumptions' => 'يفترض استخدام الاسترجاع النشط (Active Recall) بدلاً من مجرد إعادة القراءة السلبية.',
        'faqs' => [
            ['q' => 'ما هو الاسترجاع النشط (Active Recall)؟', 'a' => 'هو محاولة تذكر المعلومات واختبار نفسك من الذاكرة وإغلاق الكتاب، بدلاً من إعادة قراءة النص المظلل بالألوان، وهو الأسلوب الأقوى عالمياً لتثبيت المعلومات.']
        ],
        'related' => ['memorization-time-calculator', 'daily-study-hours-calculator', 'exam-countdown-calculator']
    ],

    'daily-pages-calculator' => [
        'title' => 'حاسبة عدد الصفحات اليومية للمذاكرة والقراءة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalPagesBook', 'label' => 'إجمالي عدد صفحات الكتاب أو المذكرة', 'type' => 'number', 'default' => '240', 'min' => '5', 'step' => '10'],
            ['id' => 'readingDaysAvailable', 'label' => 'عدد الأيام المتاحة للقراءة أو المذاكرة', 'type' => 'number', 'default' => '15', 'min' => '1', 'max' => '365', 'step' => '1'],
            ['id' => 'minutesPerPage', 'label' => 'متوسط الوقت المستغرق لقراءة واستيعاب الصفحة (بالدقائق)', 'type' => 'number', 'default' => '4', 'min' => '1', 'max' => '30', 'step' => '1'],
        ],
        'calcJs' => "
            const pages = Math.max(5, parseFloat(document.getElementById('totalPagesBook').value) || 240);
            const days = Math.max(1, parseInt(document.getElementById('readingDaysAvailable').value) || 15);
            const minPage = Math.max(1, parseFloat(document.getElementById('minutesPerPage').value) || 4);

            const pagesPerDay = Math.ceil(pages / days);
            const dailyMinutes = pagesPerDay * minPage;
            const dailyHours = (dailyMinutes / 60);

            setPrimaryResult(pagesPerDay + ' صفحة يومياً', 'الورد اليومي المطلوب من الصفحات');
            showResultArea();

            setDetailStats([
                { label: 'الوقت اليومي المطلوب للقراءة', value: Math.round(dailyMinutes) + ' دقيقة (' + dailyHours.toFixed(1) + ' ساعة)', color: '#3b82f6' },
                { label: 'إجمالي صفحات الكتاب', value: pages + ' صفحة', color: '#10b981' },
                { label: 'الصفحات المقروءة في الأسبوع', value: (pagesPerDay * 7) + ' صفحة', color: '#f59e0b' },
                { label: 'المدة الإجمالية لإنهاء الكتاب', value: days + ' يوماً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لإنهاء كتاب من <strong>\${pages} صفحة</strong> خلال <strong>\${days} يوماً</strong>، تحتاج لقراءة <strong>\${pagesPerDay} صفحة يومياً</strong>، وتستغرق منك حوالي <strong>\${Math.round(dailyMinutes)} دقيقة يومياً</strong>.</p>
            `);
        ",
        'points' => [
            'الصفحات اليومية = إجمالي صفحات الكتاب ÷ عدد الأيام المتاحة.',
            'قراءة 15 إلى 20 صفحة يومياً بانتظام تمكنك من إنهاء كتابين كاملين شهرياً (أكثر من 24 كتاباً في السنة).'
        ],
        'assumptions' => 'يفترض استيعاب وفهم النصوص وليس مجرد التصفح السريع.',
        'faqs' => [
            ['q' => 'كيف أزيد سرعتي في قراءة الصفحات دون الإخلال بالفهم؟', 'a' => 'تجنب التراجع لقراءة الكلمات السابقة، واستخدم إصبعك أو قلماً كدليل بصري لتحريك عينيك بسلاسة، وتخلص من نطق الكلمات داخلياً في عقلك (Subvocalization).']
        ],
        'related' => ['reading-time-calculator', 'daily-lectures-calculator', 'syllabus-finish-time-calculator']
    ],

    'daily-lectures-calculator' => [
        'title' => 'حاسبة عدد المحاضرات اليومية لمشاهدة الكورسات',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalLecturesCount', 'label' => 'عدد المحاضرات أو الفيديوهات المتبقية في الدورة', 'type' => 'number', 'default' => '36', 'min' => '1', 'step' => '1'],
            ['id' => 'avgLectureMinutes', 'label' => 'متوسط مدة المحاضرة الواحدة (بالدقائق)', 'type' => 'number', 'default' => '45', 'min' => '5', 'step' => '5'],
            ['id' => 'daysToFinishCourse', 'label' => 'عدد الأيام المحددة لإنهاء الكورس', 'type' => 'number', 'default' => '12', 'min' => '1', 'max' => '180', 'step' => '1'],
            ['id' => 'playbackSpeed', 'label' => 'سرعة تشغيل الفيديو المفضلة', 'type' => 'select', 'options' => [
                '1.0' => 'السرعة العادية (1.0x)',
                '1.25' => 'تسريع خفيف مريح (1.25x - توفير 20% وقت)',
                '1.5' => 'تسريع متوسط (1.5x - توفير 33% وقت)',
                '1.75' => 'تسريع فائق (1.75x)',
                '2.0' => 'سرعة مضاعفة (2.0x - توفير 50% وقت)'
            ], 'default' => '1.25'],
        ],
        'calcJs' => "
            const count = Math.max(1, parseInt(document.getElementById('totalLecturesCount').value) || 36);
            const mins = Math.max(5, parseFloat(document.getElementById('avgLectureMinutes').value) || 45);
            const days = Math.max(1, parseInt(document.getElementById('daysToFinishCourse').value) || 12);
            const speed = parseFloat(document.getElementById('playbackSpeed').value) || 1.25;

            const lecturesPerDay = count / days;
            const actualLectureDuration = mins / speed;
            const dailyWatchingMinutes = lecturesPerDay * actualLectureDuration;
            const dailyHours = dailyWatchingMinutes / 60;
            const totalHoursSaved = ((count * mins) - (count * actualLectureDuration)) / 60;

            setPrimaryResult(lecturesPerDay.toFixed(1) + ' محاضرة يومياً (' + dailyHours.toFixed(1) + ' ساعة/يوم)', 'المعدل اليومي المطلوب لمشاهدة المحاضرات');
            showResultArea();

            setDetailStats([
                { label: 'المحاضرات المطلوبة في اليوم', value: lecturesPerDay.toFixed(1) + ' محاضرة', color: '#3b82f6' },
                { label: 'الوقت اليومي بالسرعة المختارة', value: Math.round(dailyWatchingMinutes) + ' دقيقة / يوم', color: '#10b981' },
                { label: 'ساعات الوقت الموفرة بفضل التسريع', value: totalHoursSaved.toFixed(1) + ' ساعة وفر', color: '#f59e0b' },
                { label: 'مدة المحاضرة الفعلية بعد التسريع', value: actualLectureDuration.toFixed(0) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لإنهاء <strong>\${count} محاضرة</strong> خلال <strong>\${days} يوماً</strong> بسرعة <strong>\${speed}x</strong>، تحتاج لمشاهدة <strong>\${lecturesPerDay.toFixed(1)} محاضرة يومياً</strong>، وتستغرق منك <strong>\${dailyHours.toFixed(1)} ساعة</strong> يومياً مع توفير <strong>\${totalHoursSaved.toFixed(1)} ساعة</strong> من وقتك الكلي.</p>
            `);
        ",
        'points' => [
            'المدة الفعلية بعد التسريع = مدة الفيديو الأصلية ÷ سرعة التشغيل.',
            'الاستماع بسرعة 1.25x إلى 1.5x يحافظ على وضوح مخارج الحروف مع تدريب العقل على المعالجة الذهنية السريعة وتوفير ثلث الوقت.'
        ],
        'assumptions' => 'يفترض تدوين الملاحظات بالتوازي أثناء الاستماع.',
        'faqs' => [
            ['q' => 'هل التسريع يقلل من الاستيعاب؟', 'a' => 'تثبت الدراسات أن سرعة 1.25x لا تؤثر مطلقاً على نسبة الاستيعاب لمعظم الطلاب، بل قد تزيد من التركيز لأنها تمنع العقل من السرحان وتشتت الانتباه المصاحب للحديث البطيء.']
        ],
        'related' => ['daily-pages-calculator', 'daily-study-hours-calculator', 'syllabus-finish-time-calculator']
    ],

    'curriculum-progress-calculator' => [
        'title' => 'حاسبة نسبة إنجاز المنهج الدراسي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'completedUnits', 'label' => 'عدد الدروس / الفصول التي تم الانتهاء منها ودراستها', 'type' => 'number', 'default' => '18', 'min' => '0', 'step' => '1'],
            ['id' => 'totalUnitsCurriculum', 'label' => 'إجمالي عدد دروس أو فصول المنهج بالكامل', 'type' => 'number', 'default' => '30', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const done = Math.max(0, parseFloat(document.getElementById('completedUnits').value) || 0);
            const total = Math.max(1, parseFloat(document.getElementById('totalUnitsCurriculum').value) || 30);

            const percent = Math.min(100, (done / total) * 100);
            const remaining = Math.max(0, total - done);

            let status = 'بداية مشجعة، استمر!';
            let color = '#3b82f6';
            if (percent >= 100) { status = 'تم إنجاز المنهج كاملاً! مبروك 🏆'; color = '#10b981'; }
            else if (percent >= 75) { status = 'على وشك الختام، خط النهاية قريب جداً 🎯'; color = '#10b981'; }
            else if (percent >= 50) { status = 'تجاوزت نصف الطريق بنجاح ⚡'; color = '#f59e0b'; }

            setPrimaryResult(percent.toFixed(1) + '% إنجاز المنهج', 'نسبة تقدمك في المنهج');
            showResultArea();

            setDetailStats([
                { label: 'النسبة المكتملة', value: percent.toFixed(1) + '%', color: color },
                { label: 'الدروس أو الفصول المتبقية', value: remaining + ' درساً', color: '#ef4444' },
                { label: 'الدروس المنجزة بنجاح', value: done + ' درساً', color: '#10b981' },
                { label: 'تقييم مرحلة الإنجاز', value: status, color: color }
            ]);

            setResultContent(`
                <div style=\"background:rgba(255,255,255,0.05);border-radius:10px;padding:4px;margin:1rem 0\">
                    <div style=\"width:\${percent}%;height:18px;background:linear-gradient(90deg, #6c63ff, #00d4ff);border-radius:8px;transition:width 0.5s ease\"></div>
                </div>
                <p>أنجزت <strong>\${done} من أصل \${total} درساً</strong> بنسبة <strong>\${percent.toFixed(1)}%</strong>، ومتبقي لك <strong>\${remaining} درساً</strong> لإتمام المقرر بالكامل - \${status}.</p>
            `);
        ",
        'points' => [
            'نسبة الإنجاز = (الدروس المنتهية ÷ إجمالي الدروس) × 100.',
            'متابعة شريط التقدم المرئي تحفز إفراز الدوبامين وتعزز الحافز النفسي للاستمرار في الإنجاز.'
        ],
        'assumptions' => 'يفترض تساوي الوزن النسبي بين الدروس.',
        'faqs' => [
            ['q' => 'كيف أتعامل مع الشعور بالإحباط في منتصف المنهج؟', 'a' => 'احتفل بالإنجاز الذي حققته بالفعل (نصف الكوب الممتلئ)، وقسم الدروس المتبقية إلى حزم أسبوعية صغيرة لتحقيق انتصارات سريعة تعيد لك الحماس.']
        ],
        'related' => ['remaining-study-time-calculator', 'syllabus-finish-time-calculator', 'daily-study-hours-calculator']
    ],

    'remaining-study-time-calculator' => [
        'title' => 'حاسبة وقت الدراسة المتبقي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalSubjectEstimatedHours', 'label' => 'إجمالي الساعات المقدرة للمادة بالكامل', 'type' => 'number', 'default' => '50', 'min' => '1', 'step' => '5'],
            ['id' => 'hoursAlreadyStudied', 'label' => 'عدد الساعات التي درستها بالفعل حتى الآن', 'type' => 'number', 'default' => '18', 'min' => '0', 'step' => '1'],
            ['id' => 'dailyPaceHours', 'label' => 'معدل دراستك اليومي المعتاد (ساعات / يوم)', 'type' => 'number', 'default' => '4', 'min' => '0.5', 'max' => '16', 'step' => '0.5'],
        ],
        'calcJs' => "
            const total = Math.max(1, parseFloat(document.getElementById('totalSubjectEstimatedHours').value) || 50);
            const studied = Math.max(0, parseFloat(document.getElementById('hoursAlreadyStudied').value) || 18);
            const pace = Math.max(0.5, parseFloat(document.getElementById('dailyPaceHours').value) || 4);

            const remainingHours = Math.max(0, total - studied);
            const daysNeeded = Math.ceil(remainingHours / pace);
            const completionRate = Math.min(100, (studied / total) * 100);

            setPrimaryResult(remainingHours.toFixed(1) + ' ساعة متبقية (' + daysNeeded + ' أيام)', 'الوقت الصافي المتبقي لإنهاء المادة');
            showResultArea();

            setDetailStats([
                { label: 'الساعات المتبقية للمذاكرة', value: remainingHours.toFixed(1) + ' ساعة', color: '#ef4444' },
                { label: 'الأيام المطلوبة بالمعدل الحالي', value: daysNeeded + ' يوماً', color: '#f59e0b' },
                { label: 'نسبة الإنجاز الزمني للمقرر', value: completionRate.toFixed(1) + '%', color: '#10b981' },
                { label: 'الساعات المنجزة حتى الآن', value: studied + ' ساعة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>متبقي لك <strong>\${remainingHours.toFixed(1)} ساعة مذاكرة</strong>. بمعدل <strong>\${pace} ساعات يومياً</strong>، تحتاج إلى <strong>\${daysNeeded} أيام</strong> للانتهاء من المادة تماماً.</p>
            `);
        ",
        'points' => [
            'الساعات المتبقية = إجمالي الساعات المقدرة للمادة - الساعات المدروسة.',
            'الأيام المطلوبة = الساعات المتبقية ÷ معدل الساعات اليومي.'
        ],
        'assumptions' => 'يفترض التزاماً بالسرعة اليومية المدخلة.',
        'faqs' => [
            ['q' => 'كيف أقدر ساعات المادة إذا كنت لا أعرفها؟', 'a' => 'القاعدة الجامعية المعتادة: كل ساعة معتمدة في الجامعة تتطلب ساعتين إلى 3 ساعات مذاكرة مستقلة أسبوعياً (مادة 3 ساعات تتطلب حوالي 40 إلى 50 ساعة مذاكرة خلال الفصل).']
        ],
        'related' => ['curriculum-progress-calculator', 'daily-study-hours-calculator', 'syllabus-finish-time-calculator']
    ],

    'memorization-time-calculator' => [
        'title' => 'حاسبة وقت الحفظ المتوقع (القرآن والمحتوى الأكاديمي)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'pagesToMemorize', 'label' => 'عدد الصفحات أو الأوجه المراد حفظها', 'type' => 'number', 'default' => '30', 'min' => '1', 'step' => '1'],
            ['id' => 'minutesPerPageMem', 'label' => 'الوقت المستغرق لحفظ الصفحة الواحدة (بالدقائق) - المعتاد 30 إلى 60 دقيقة', 'type' => 'number', 'default' => '40', 'min' => '5', 'step' => '5'],
            ['id' => 'dailyMemHours', 'label' => 'الوقت المخصص للحفظ يومياً (ساعات)', 'type' => 'number', 'default' => '1.5', 'min' => '0.25', 'max' => '10', 'step' => '0.25'],
            ['id' => 'reviewRatioMem', 'label' => 'نسبة وقت المراجعة والتثبيت (%)- الموصى به 30%', 'type' => 'number', 'default' => '30', 'min' => '10', 'max' => '60', 'step' => '5'],
        ],
        'calcJs' => "
            const pages = Math.max(1, parseInt(document.getElementById('pagesToMemorize').value) || 30);
            const minPage = Math.max(5, parseFloat(document.getElementById('minutesPerPageMem').value) || 40);
            const dailyHours = Math.max(0.25, parseFloat(document.getElementById('dailyMemHours').value) || 1.5);
            const revRate = Math.max(10, parseFloat(document.getElementById('reviewRatioMem').value) || 30) / 100;

            const pureMemMinutes = pages * minPage;
            const totalMinutesWithReview = pureMemMinutes * (1 + revRate);
            const totalHours = totalMinutesWithReview / 60;
            const daysNeeded = Math.ceil(totalHours / dailyHours);

            const pagesPerDay = (pages / daysNeeded);

            setPrimaryResult(daysNeeded + ' يوماً (' + totalHours.toFixed(1) + ' ساعة عمل)', 'المدة المتوقعة لإتمام الحفظ والتثبيت');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الساعات شاملة التثبيت', value: totalHours.toFixed(1) + ' ساعة', color: '#3b82f6' },
                { label: 'معدل الحفظ اليومي المطلوب', value: pagesPerDay.toFixed(1) + ' صفحة / يوم', color: '#10b981' },
                { label: 'وقت الحفظ الجديد الصافي', value: (pureMemMinutes / 60).toFixed(1) + ' ساعة', color: '#f59e0b' },
                { label: 'وقت المراجعة والربط المخصص', value: ((pureMemMinutes * revRate) / 60).toFixed(1) + ' ساعة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لحفظ وتثبيت <strong>\${pages} صفحة</strong> بمعدل <strong>\${dailyHours} ساعة يومياً</strong>، تحتاج إلى <strong>\${daysNeeded} يوماً</strong> (بمعدل حفظ <strong>\${pagesPerDay.toFixed(1)} صفحة يومياً</strong>) مع تخصيص 30% من الوقت للربط والمراجعة التراكمية.</p>
            `);
        ",
        'points' => [
            'الحفظ المتين يتطلب دائماً تخصيص ثلث الوقت على الأقل لمراجعة المحفوظ القديم (التكرار التراكمي).',
            'الحفظ بعد صلاة الفجر في الصباح الباكر هو الأكثر ثباتاً وسرعة بسبب صفاء الذهن وقلة المشتتات.'
        ],
        'assumptions' => 'يفترض التلاوة الصحيحة قبل البدء بالحفظ لتفادي حفظ الأخطاء.',
        'faqs' => [
            ['q' => 'ما هي أفضل طريقة لتثبيت المحفوظ؟', 'a' => 'التسميع على زميل أو شيخ، وتكرار الصفحة من الذاكرة غيباً 10 إلى 15 مرة على مدار اليوم، والصلاة بما تم حفظه في قيام الليل والصلوات اليومية.']
        ],
        'related' => ['revision-plan-calculator', 'daily-pages-calculator', 'daily-study-hours-calculator']
    ],

    'grades-to-percentage-converter' => [
        'title' => 'حاسبة تحويل الدرجات إلى نسبة مئوية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'inputGradeVal', 'label' => 'الدرجة أو المعدل الحالي', 'type' => 'number', 'default' => '3.45', 'min' => '0', 'step' => '0.01'],
            ['id' => 'sourceSystemScale', 'label' => 'النظام المصدر للعلامة', 'type' => 'select', 'options' => [
                'gpa4' => 'معدل جامعي من 4.0 نقاط',
                'gpa5' => 'معدل جامعي من 5.0 نقاط (جامعات المملكة)',
                'scale20' => 'نظام من 20 درجة (دول المغرب العربي وفرنسا)',
                'scale10' => 'نظام من 10 درجات',
                'custom' => 'نظام مخصص بنهاية عظمى محددة'
            ], 'default' => 'gpa4'],
            ['id' => 'customMaxScale', 'label' => 'الدرجة العظمى (إذا اخترت مخصص)', 'type' => 'number', 'default' => '100', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const val = Math.max(0, parseFloat(document.getElementById('inputGradeVal').value) || 0);
            const sys = document.getElementById('sourceSystemScale').value;
            const customMax = Math.max(1, parseFloat(document.getElementById('customMaxScale').value) || 100);

            let maxVal = 4.0;
            let percent = 0;

            if (sys === 'gpa4') {
                maxVal = 4.0;
                // معادلة WES القياسية لتحويل GPA 4 إلى نسبة تقريبية
                percent = (val / 4.0) * 100;
            } else if (sys === 'gpa5') {
                maxVal = 5.0;
                // النظام السعودي: النسبة = (المعدل / 5) * 100 أو المعادلة الرسمية
                percent = (val / 5.0) * 100;
            } else if (sys === 'scale20') {
                maxVal = 20.0;
                percent = (val / 20.0) * 100;
            } else if (sys === 'scale10') {
                maxVal = 10.0;
                percent = (val / 10.0) * 100;
            } else {
                maxVal = customMax;
                percent = (val / customMax) * 100;
            }

            percent = Math.min(100, Math.max(0, percent));

            setPrimaryResult(percent.toFixed(2) + '%', 'النسبة المئوية المكافئة');
            showResultArea();

            setDetailStats([
                { label: 'النسبة المئوية الصافية', value: percent.toFixed(2) + '%', color: '#10b981' },
                { label: 'المعدل المكافئ من 4.0', value: ((percent / 100) * 4.0).toFixed(2), color: '#3b82f6' },
                { label: 'المعدل المكافئ من 5.0', value: ((percent / 100) * 5.0).toFixed(2), color: '#8b5cf6' },
                { label: 'النظام المصدر المعتمد', value: val + ' من ' + maxVal, color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>العلامة <strong>\${val} من \${maxVal}</strong> تعادل بالضبط <strong>\${percent.toFixed(2)}%</strong> كنسبة مئوية عامة للمقارنة والتقديم على المنح والجامعات الدولية.</p>
            `);
        ",
        'points' => [
            'النسبة المئوية المكافئة = (العلامة المحققة ÷ الحد الأقصى للنظام) × 100.',
            'تستخدم هذه المعادلة لتسهيل معادلة الشهادات والتقديم على الوظائف والمنح الدراسية بالخارج.'
        ],
        'assumptions' => 'يفترض تحويلاً خطياً متناسباً (Linear Conversion).',
        'faqs' => [
            ['q' => 'هل تختلف معادلة التحويل لبعض برامج الابتعاث؟', 'a' => 'نعم؛ بعض الجامعات الغربية تستخدم جداول مطابقة غير خطية مبنية على التوزيع التكراري للدرجات (Percentile Rank)، ولكن التحويل الخطي هو المعيار المعتمد عموماً ما لم يُنص على خلاف ذلك.']
        ],
        'related' => ['grade-percentage-calculator', 'cumulative-gpa-calculator', 'grade-difference-calculator']
    ],

    'grade-difference-calculator' => [
        'title' => 'حاسبة الفرق بين علامتين ونسبة التطور',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'previousScoreInput', 'label' => 'العلامة السابقة (الامتحان الأول أو الفصل الماضي)', 'type' => 'number', 'default' => '72', 'min' => '0', 'step' => '0.5'],
            ['id' => 'currentScoreInput', 'label' => 'العلامة الحالية (الامتحان الثاني أو الفصل الحالي)', 'type' => 'number', 'default' => '86', 'min' => '0', 'step' => '0.5'],
            ['id' => 'examTotalPoints', 'label' => 'الدرجة العظمى للامتحان', 'type' => 'number', 'default' => '100', 'min' => '1', 'step' => '1'],
        ],
        'calcJs' => "
            const prev = Math.max(0, parseFloat(document.getElementById('previousScoreInput').value) || 0);
            const curr = Math.max(0, parseFloat(document.getElementById('currentScoreInput').value) || 0);
            const max = Math.max(1, parseFloat(document.getElementById('examTotalPoints').value) || 100);

            const pointsDiff = curr - prev;
            const percentDiffOnExam = ((curr - prev) / max) * 100;
            const growthRate = prev > 0 ? ((curr - prev) / prev) * 100 : 0;

            let status = 'تحسن وتطور ممتاز ملحوظ 📈';
            let color = '#10b981';
            if (pointsDiff < 0) { status = 'تراجع في الدرجات 📉'; color = '#ef4444'; }
            if (pointsDiff === 0) { status = 'ثبات تام في المستوى ⚖️'; color = '#3b82f6'; }

            setPrimaryResult((pointsDiff >= 0 ? '+' : '') + pointsDiff.toFixed(1) + ' درجة (' + (growthRate >= 0 ? '+' : '') + growthRate.toFixed(1) + '%)', 'فارق الدرجات ونسبة التغير');
            showResultArea();

            setDetailStats([
                { label: 'فارق الدرجات النقطي', value: (pointsDiff >= 0 ? '+' : '') + pointsDiff.toFixed(1) + ' درجة', color: color },
                { label: 'نسبة التطور مقارنة بالسابقة', value: (growthRate >= 0 ? '+' : '') + growthRate.toFixed(1) + '%', color: color },
                { label: 'الفارق المئوي من مجموع الامتحان', value: (percentDiffOnExam >= 0 ? '+' : '') + percentDiffOnExam.toFixed(1) + '%', color: '#3b82f6' },
                { label: 'تقييم الحركة الأكاديمية', value: status, color: color }
            ]);

            setResultContent(`
                <p>تغيرت درجتك من <strong>\${prev}</strong> إلى <strong>\${curr}</strong> بفارق <strong>\${pointsDiff >= 0 ? '+' : ''}\${pointsDiff.toFixed(1)} درجة</strong>، بنسبة تطور <strong>\${growthRate.toFixed(1)}%</strong> - \${status}.</p>
            `);
        ",
        'points' => [
            'فارق الدرجات = الدرجة الحالية - الدرجة السابقة.',
            'نسبة التطور = ((الدرجة الحالية - السابقة) ÷ السابقة) × 100.'
        ],
        'assumptions' => 'يفترض أن الامتحانين من نفس الدرجة العظمى والمستوى التقييمي.',
        'faqs' => [
            ['q' => 'كيف أحلل سبب تراجع الدرجات في مادة معينة؟', 'a' => 'راجع ورقة الإجابة لتحديد نوع الأخطاء: هل هي ناتجة عن سوء فهم المفاهيم، أم عدم حفظ القوانين، أم التسرع في الحسابات وقراءة الأسئلة.']
        ],
        'related' => ['grade-percentage-calculator', 'target-gpa-calculator', 'grades-to-percentage-converter']
    ],

    'grade-distribution-calculator' => [
        'title' => 'حاسبة توزيع العلامات التكتيكي للوصول لمعدل معين',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'targetSemesterGpaTactical', 'label' => 'المعدل الفصلي المستهدف (GPA من 4.0)', 'type' => 'number', 'default' => '3.50', 'min' => '2.0', 'max' => '4.0', 'step' => '0.05'],
            ['id' => 'totalSubjectsCount', 'label' => 'عدد مواد الفصل الدراسي (بافتراض 3 ساعات لكل مادة)', 'type' => 'number', 'default' => '5', 'min' => '2', 'max' => '8', 'step' => '1'],
        ],
        'calcJs' => "
            const target = Math.max(2.0, Math.min(4.0, parseFloat(document.getElementById('targetSemesterGpaTactical').value) || 3.50));
            const count = Math.max(2, parseInt(document.getElementById('totalSubjectsCount').value) || 5);

            // توزيع الدرجات المقترح:
            // إذا كان الهدف 3.5: يحتاج مثلاً 3 مواد A (4.0) و 2 مواد B (3.0) -> (12 + 6)/5 = 3.6
            const totalPointsNeeded = target * count;
            let aCount = 0;
            let bCount = 0;
            let cCount = 0;

            for (let a = count; a >= 0; a--) {
                for (let b = count - a; b >= 0; b--) {
                    let c = count - a - b;
                    let pts = (a * 4.0) + (b * 3.0) + (c * 2.0);
                    if (pts >= totalPointsNeeded) {
                        aCount = a;
                        bCount = b;
                        cCount = c;
                    }
                }
            }

            const achievedGpa = ((aCount * 4.0) + (bCount * 3.0) + (cCount * 2.0)) / count;

            setPrimaryResult(aCount + ' مواد (A) + ' + bCount + ' مواد (B)' + (cCount > 0 ? ' + ' + cCount + ' مواد (C)' : ''), 'التوزيع التكتيكي المقترح لدرجات المواد');
            showResultArea();

            setDetailStats([
                { label: 'عدد المواد بتقدير ممتاز A (4.0)', value: aCount + ' مواد', color: '#10b981' },
                { label: 'عدد المواد بتقدير جيد جداً B (3.0)', value: bCount + ' مواد', color: '#3b82f6' },
                { label: 'عدد المواد بتقدير جيد C (2.0)', value: cCount + ' مواد', color: '#f59e0b' },
                { label: 'المعدل الناتج المتوقع', value: achievedGpa.toFixed(2) + ' / 4.0', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتحقيق معدل <strong>\${target}</strong> عبر <strong>\${count} مواد</strong>، يمكنك توزيع مجهودك بذكاء بالحصول على <strong>\${aCount} مواد بتقدير A</strong> في المواد السهلة و <strong>\${bCount} مواد بتقدير B</strong> في المواد الصعبة، لتحقق معدلاً نهائياً <strong>\${achievedGpa.toFixed(2)}</strong>.</p>
            `);
        ",
        'points' => [
            'التوزيع التكتيكي يساعدك على توجيه طاقتك الذهنية إلى المواد ذات الأثر الأكبر أو المواد السهلة لضمان تقدير A فيها.',
            'الحصول على تقدير B في مادة معقدة لا يمنعك من تحقيق معدل امتياز إذا وازنته بتقديرات A في بقية المواد.'
        ],
        'assumptions' => 'يفترض تساوي الساعات المعتمدة للمواد (3 ساعات لكل مقرر).',
        'faqs' => [
            ['q' => 'كيف أختار المواد التي أركز عليها لرفع المعدل؟', 'a' => 'ركز على المواد ذات الساعات المعتمدة الأعلى (مثلاً 4 ساعات) ومقررات المتطلبات العامة السهلة لرفع النقاط التراكمية بأقل مخاطرة.']
        ],
        'related' => ['target-gpa-calculator', 'cumulative-gpa-calculator', 'next-semester-gpa-calculator']
    ],

    'next-semester-gpa-calculator' => [
        'title' => 'حاسبة المعدل المطلوب في الفصل القادم (رفع الإنذار الأكاديمي)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'currentWarningGpa', 'label' => 'المعدل التراكمي الحالي (تحت الإنذار)', 'type' => 'number', 'default' => '1.85', 'min' => '0', 'max' => '4.0', 'step' => '0.01'],
            ['id' => 'clearedHoursWarning', 'label' => 'الساعات المنجزة حتى الآن', 'type' => 'number', 'default' => '32', 'min' => '1', 'max' => '200', 'step' => '1'],
            ['id' => 'requiredClearGpa', 'label' => 'الحد الأدنى لرفع الإنذار في جامعتك (المعتاد 2.00 من 4.0)', 'type' => 'number', 'default' => '2.00', 'min' => '1.5', 'max' => '3.0', 'step' => '0.05'],
            ['id' => 'registeredHoursNext', 'label' => 'عدد الساعات التي ستسجلها في الفصل القادم', 'type' => 'number', 'default' => '14', 'min' => '3', 'max' => '22', 'step' => '1'],
        ],
        'calcJs' => "
            const current = Math.max(0, parseFloat(document.getElementById('currentWarningGpa').value) || 1.85);
            const pastHours = Math.max(1, parseFloat(document.getElementById('clearedHoursWarning').value) || 32);
            const target = Math.max(1.5, parseFloat(document.getElementById('requiredClearGpa').value) || 2.00);
            const nextHours = Math.max(3, parseFloat(document.getElementById('registeredHoursNext').value) || 14);

            const totalHours = pastHours + nextHours;
            const requiredPoints = target * totalHours;
            const currentPoints = current * pastHours;
            const neededSemesterPoints = requiredPoints - currentPoints;
            const neededGpa = neededSemesterPoints / nextHours;

            let possible = true;
            let status = 'ممكن تماماً ويمكنك رفع الإنذار بسهولة بإذن الله ✅';
            let color = '#10b981';

            if (neededGpa > 4.0) {
                possible = false;
                status = 'غير كافٍ في فصل واحد! تحتاج لتسجيل ساعات أكثر أو إعادة مواد سابقة ⚠️';
                color = '#ef4444';
            } else if (neededGpa > 3.0) {
                status = 'يحتاج لمعدل جيد جداً إلى ممتاز (B+ أو أعلى) 🎯';
                color = '#f59e0b';
            }

            setPrimaryResult(neededGpa > 4.0 ? 'غير ممكن بفصل واحد' : neededGpa.toFixed(2) + ' / 4.0', 'المعدل الفصلي المطلوب لرفع الإنذار');
            showResultArea();

            setDetailStats([
                { label: 'المعدل الفصلي المطلوب في الفصل القادم', value: neededGpa.toFixed(2) + ' من 4.0', color: '#3b82f6' },
                { label: 'حالة إمكانية رفع الإنذار', value: status, color: color },
                { label: 'التقدير المكافئ المطلوب في المواد', value: neededGpa <= 2.3 ? 'تقدير C+ في كل مادة' : (neededGpa <= 3.0 ? 'تقدير B في كل مادة' : 'تقدير A/B+'), color: '#8b5cf6' },
                { label: 'الساعات التراكمية بعد إنهاء الفصل', value: totalHours + ' ساعة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لرفع معدلك التراكمي إلى <strong>\${target.toFixed(2)}</strong> والتخلص من الإنذار الأكاديمي، تحتاج لتحقيق معدل فصلي لا يقل عن <strong>\${neededGpa.toFixed(2)} من 4.0</strong> في الساعات المسجلة (\${nextHours} ساعة).</p>
            `);
        ",
        'points' => [
            'الإنذار الأكاديمي يُرفع رسمياً بمجرد وصول المعدل التراكمي إلى 2.00 من 4.00 (أو 2.75 من 5.00) في معظم اللوائح الجامعية.',
            'إعادة دراسة المواد التي رسبت فيها سابقاً (F) أو حصلت فيها على (D) هي أسرع وسيلة قانونية لرفع المعدل لأنها تمحو النقاط السلبية القديمة.'
        ],
        'assumptions' => 'يفترض عدم الحصول على أي إنذار إضافي أثناء الفصل.',
        'faqs' => [
            ['q' => 'ماذا يحدث إذا لم أرفع الإنذار في الفصل القادم؟', 'a' => 'تمنح معظم الجامعات فرصة إنذار ثانٍ أو ثالث قبل اتخاذ قرار طي القيد أو الفصل الأكاديمي، ويمكن للطالب تقديم التماس للجنة الشؤون الأكاديمية لتمديد فرصة استثنائية.']
        ],
        'related' => ['target-gpa-calculator', 'cumulative-gpa-calculator', 'graduation-hours-calculator']
    ],

    'graduation-hours-calculator' => [
        'title' => 'حاسبة الساعات المطلوبة للتخرج والخطة الدراسية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'totalDegreeHours', 'label' => 'إجمالي الساعات المعتمدة المطلوبة للتخرج بالخطة', 'type' => 'number', 'default' => '132', 'min' => '90', 'max' => '220', 'step' => '1'],
            ['id' => 'passedHoursSoFar', 'label' => 'الساعات المجتازة بنجاح حتى الآن', 'type' => 'number', 'default' => '78', 'min' => '0', 'step' => '1'],
            ['id' => 'currentEnrolledHours', 'label' => 'الساعات المسجلة في الفصل الحالي (قيد الدراسة)', 'type' => 'number', 'default' => '15', 'min' => '0', 'max' => '25', 'step' => '1'],
            ['id' => 'avgHoursPerSemester', 'label' => 'متوسط الساعات التي تخطط لتسجيلها في كل فصل قادم', 'type' => 'number', 'default' => '15', 'min' => '9', 'max' => '22', 'step' => '1'],
            ['id' => 'summerSemestersPlanned', 'label' => 'هل تخطط لدراسة فصول صيفية؟', 'type' => 'select', 'options' => [
                'none' => 'لا، فصول اعتيادية فقط (خريف وربيع)',
                'one_summer' => 'نعم، فصل صيفي واحد (حوالي 6 إلى 9 ساعات)',
                'two_summers' => 'نعم، فصلين صيفيين'
            ], 'default' => 'none'],
        ],
        'calcJs' => "
            const total = Math.max(90, parseInt(document.getElementById('totalDegreeHours').value) || 132);
            const passed = Math.max(0, parseInt(document.getElementById('passedHoursSoFar').value) || 78);
            const enrolled = Math.max(0, parseInt(document.getElementById('currentEnrolledHours').value) || 15);
            const pace = Math.max(9, parseInt(document.getElementById('avgHoursPerSemester').value) || 15);
            const summer = document.getElementById('summerSemestersPlanned').value;

            const remainingAfterCurrent = Math.max(0, total - (passed + enrolled));
            let summerHoursDeduct = 0;
            if (summer === 'one_summer') summerHoursDeduct = 8;
            if (summer === 'two_summers') summerHoursDeduct = 16;

            const hoursForRegularSemesters = Math.max(0, remainingAfterCurrent - summerHoursDeduct);
            const semestersLeft = Math.ceil(hoursForRegularSemesters / pace);
            const yearsLeft = (semestersLeft / 2).toFixed(1);
            const completionPercent = ((passed + enrolled) / total) * 100;

            setPrimaryResult(semestersLeft + ' فصول دراسية متبقية (' + remainingAfterCurrent + ' ساعة)', 'المدة المتبقية للتخرج');
            showResultArea();

            setDetailStats([
                { label: 'الساعات المتبقية بعد الفصل الحالي', value: remainingAfterCurrent + ' ساعة معتمدة', color: '#ef4444' },
                { label: 'نسبة إنجاز الخطة الدراسية', value: completionPercent.toFixed(1) + '%', color: '#10b981' },
                { label: 'السنوات الدراسية المتبقية تقريباً', value: yearsLeft + ' سنة', color: '#3b82f6' },
                { label: 'إجمالي الساعات المنجزة والمسجلة', value: (passed + enrolled) + ' من ' + total, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متبقي لك <strong>\${remainingAfterCurrent} ساعة معتمدة</strong> بعد اجتياز الفصل الحالي. بمعدل <strong>\${pace} ساعة لكل فصل</strong>، ستتخرج بإذن الله خلال <strong>\${semestersLeft} فصول دراسية</strong> (حوالي <strong>\${yearsLeft} سنة</strong>).</p>
            `);
        ",
        'points' => [
            'الساعات المتبقية = ساعات الخطة الكلية - (الساعات المجتازة + الساعات المسجلة حالياً).',
            'استغلال الفصول الصيفية لتسجيل 6 إلى 9 ساعات يختصر فصلاً دراسياً كاملاً ويسرع التخرج بنصف سنة.'
        ],
        'assumptions' => 'يفترض النجاح في جميع المقررات المسجلة دون رسوب أو تأجيل.',
        'faqs' => [
            ['q' => 'كم أقصى عدد ساعات يسمح للطالب بتسجيله في فصل التخرج؟', 'a' => 'تسمح معظم اللوائح الجامعية للطالب الخريج بزيادة العبء الدراسي (Overload) ليصل إلى 21 أو 24 ساعة معتمدة في فصل تخرجه الأخير لتفادي تأخير التخرج فصلاً إضافياً.']
        ],
        'related' => ['cumulative-gpa-calculator', 'target-gpa-calculator', 'daily-study-hours-calculator']
    ],
];

echo "Generating Group E: Education & Study Tools (19 tools)...\n";
foreach ($toolsE as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group E!\n";
