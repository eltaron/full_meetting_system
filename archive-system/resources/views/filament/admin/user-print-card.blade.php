<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>طباعة حساب موظف - محافظة كفر الشيخ</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            /* لون خلفية خفيف جداً للمعاينة فقط */
            font-family: 'Cairo', sans-serif;
            display: flex;
            justify-content: center;
        }

        .a4-page {
            width: 210mm;
            height: 275mm;
            background: white;
            box-sizing: border-box;
            padding: 5mm 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 10px solid #1e3a8a;
            /* إطار أزرق ملكي خارجي */
            outline: 3px solid #fbbf24;
            /* إطار ذهبي داخلي */
            outline-offset: -7px;
        }

        /* تنسيقات الهيدر */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
        }

        .header .logo {
            height: 100px;
        }

        .header .title {
            text-align: left;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            color: #1e3a8a;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            color: #666;
        }

        .headline {
            background: #1e3a8a;
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 5px;
            font-size: 20px;
        }

        /* منطقة البيانات */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table td {
            padding: 20px 10px;
            font-size: 19px;
            border-bottom: 1px solid #eee;
        }

        .data-table td.label {
            font-weight: bold;
            color: #1e3a8a;
            width: 30%;
        }

        .login-box {
            background: #f8fafc;
            border: 2px dashed #1e3a8a;
            border-radius: 12px;
            padding: 0 0 25px;
            text-align: center;
            margin-bottom: 50px;
        }

        .login-box .title {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .login-box .value {
            font-size: 30px;
            font-weight: 900;
            color: #1e3a8a;
            font-family: monospace;
            letter-spacing: 1px;
        }

        .rules {
            font-size: 14px;
            color: #475569;
            margin-top: auto;
        }

        .rules h4 {
            color: #dc2626;
            border-bottom: 1px solid #fee2e2;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .footer .it-dept {
            font-weight: 900;
            color: #1e3a8a;
            font-size: 15px;
        }

        /* التحكم بالطباعة */
        .no-print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #fbbf24;
            color: #1e3a8a;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            z-index: 999;
        }

        @media print {
            body {
                background: white;
            }

            .a4-page {
                box-shadow: none;
                border: 15px solid #1e3a8a !important;
                margin: 0;
            }

            .no-print-btn {
                display: none;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <button class="no-print-btn" onclick="window.print()">🖨️ بدء الطباعة</button>

    <div class="a4-page">
        <div class="header">
            <div class="title" style="text-align: right">
                <h1>محافظة كفر الشيخ</h1>
                <h2>منظومة الأرشفة الموحدة - إدارة التحول الرقمي</h2>
            </div>
            <img src="{{ asset('logo.png') }}" class="logo">
        </div>

        <div class="headline">بيانات تسجيل الدخول الرسمية للمنظومة</div>

        <table class="data-table">
            <tr>
                <td class="label">اسم الموظف:</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">الإدارة المختصة:</td>
                <td>{{ $user->department->name ?? 'غير محدد' }}</td>
            </tr>
        </table>

        <div class="login-box">
            <p class="title">اسم المستخدم للدخول (Username)</p>
            <div class="value">{{ $user->username }}</div>
        </div>

        <div class="login-box">
            <p class="title">كلمة المرور الافتراضية (Password)</p>
            <div class="value" style="color: #dc2626;">12345678</div>
        </div>

        <div class="rules">
            <h4>إقرار مسؤولية هامة:</h4>
            <ul>
                <li>تستخدم هذه البيانات فقط بواسطة الموظف المذكور أعلاه.</li>
                <li>يجب تغيير كلمة المرور فور استلام البيانات لأول مرة.</li>
                <li>في حال نسيان البيانات، يرجى مراجعة إدارة التحول الرقمي بالديوان.</li>
            </ul>
        </div>

        <div class="footer">
            <p style="margin:0; font-size: 11px; color: #999;">تعتبر هذه البطاقة وثيقة سرية يتم التعامل بها داخل ديوان
                المحافظة</p>
            <p class="it-dept">إدارة التحول الرقمي - محافظة كفر الشيخ</p>
        </div>
    </div>

</body>

</html>
