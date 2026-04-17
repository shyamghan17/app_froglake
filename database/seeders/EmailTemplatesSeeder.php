<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\User;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('type','superadmin')->first();

        $emailTemplate = [
            'New User',
            'Sales Invoice',
            'Sales Invoice Return',
            'Purchase Invoice',
            'Purchase Invoice Return',
            'Helpdesk Ticket',
            'Helpdesk Ticket Reply',
        ];

        $defaultTemplate = [
            'New User' => [
                'subject' => 'Login Detail',
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "App Url": "app_url",
                    "Name": "name",
                    "Email": "email",
                    "Password": "password"
                  }',
                  'lang' => [
                    'ar' => '<p>مرحبا،&nbsp;<br>مرحبا بك في {app_name}.</p><p><b>البريد الإلكتروني </b>: {email}<br><b>كلمه السر</b> : {password}</p><p>{app_url}</p><p>شكر،<br>{company_name}</p><p>{app_name}</p>',
                    'da' => '<p>Hej,&nbsp;<br>Velkommen til {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Adgangskode</b> : {password}</p><p>{app_url}</p><p>Tak,<br>{company_name}</p><p>{app_name}</p>',
                    'de' => '<p>Hallo,&nbsp;<br>Willkommen zu {app_name}.</p><p><b>Email </b>: {email}<br><b>Passwort</b> : {password}</p><p>{app_url}</p><p>Vielen Dank,<br>{company_name}</p><p>{app_name}</p>',
                    'en' => '<p>Hello,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Email </strong>: {email}<br /><strong>Password</strong> : {password}</p>
                    <p>{app_url}</p>
                    <p>Thanks,<br />{company_name}</p><p>{app_name}</p>',
                    'es' => '<p>Hola,&nbsp;<br>Bienvenido a {app_name}.</p><p><b>Correo electrónico </b>: {email}<br><b>Contraseña</b> : {password}</p><p>{app_url}</p><p>Gracias,<br>{company_name}</p><p>{app_name}</p>',
                    'fr' => '<p>Bonjour,&nbsp;<br>Bienvenue à {app_name}.</p><p><b>Email </b>: {email}<br><b>Mot de passe</b> : {password}</p><p>{app_url}</p><p>Merci,<br>{company_name}</p><p>{app_name}</p>',
                    'it' => "<p>Ciao,&nbsp;<br>Benvenuto a {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Parola d'ordine</b> : {password}</p><p>{app_url}</p><p>Grazie,<br>{company_name}</p><p>{app_name}</p>",
                    'ja' => '<p>こんにちは、&nbsp;<br>へようこそ {app_name}.</p><p><b>Eメール </b>: {email}<br><b>パスワード</b> : {password}</p><p>{app_url}</p><p>おかげで、<br>{company_name}</p><p>{app_name}</p>',
                    'nl' => '<p>Hallo,&nbsp;<br>Welkom bij {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Wachtwoord</b> : {password}</p><p>{app_url}</p><p>Bedankt,<br>{company_name}</p><p>{app_name}</p>',
                    'pl' => '<p>Witaj,&nbsp;<br>Witamy w {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Hasło</b> : {password}</p><p>{app_url}</p><p>Dzięki,<br>{company_name}</p><p>{app_name}</p>',
                    'pt' => '<p>Ol&aacute;, Bem-vindo a {app_name}.</p>
                    <p>E-mail: {email}</p>
                    <p>Senha: {password}</p>
                    <p>{app_url}</p>
                    <p>&nbsp;</p>
                    <p>Obrigado,</p>
                    <p>{app_name}</p>',
                    'pt-BR' => '<p>Ol&aacute;, Bem-vindo a {app_name}.</p>
                    <p>E-mail: {email}</p>
                    <p>Senha: {password}</p>
                    <p>{app_url}</p>
                    <p>&nbsp;</p>
                    <p>Obrigado,</p>
                    <p>{app_name}</p>',
                    'ru' => '<p>Привет,&nbsp;<br>Добро пожаловать в {app_name}.</p><p><b>Электронная почта </b>: {email}<br><b>Пароль</b> : {password}</p><p>{app_url}</p><p>Спасибо,<br>{company_name}</p><p>{app_name}</p>',
                    'he' => '<p>שלום,<br />ברוך הבא אל {app_name}</p><p><strong>אימייל </strong>: {email}<br /><strong>סיסמה</strong> : {password}</p><p>{app_url}</p><p>תודה,<br />{company_name}</p><p>{app_name}</p>',
                    'tr' => '<p>Merhaba,<br />{app_name} uygulamasına hoş geldiniz</p><p><strong>E-posta </strong>: {email}<br /><strong>Şifre</strong> : {password}</p><p>{app_url}</p><p>Teşekkürler,<br />{company_name}</p><p>{app_name}</p>',
                    'zh' => '<p>您好，<br />欢迎使用 {app_name}</p><p><strong>邮箱 </strong>: {email}<br /><strong>密码</strong> : {password}</p><p>{app_url}</p><p>谢谢,<br />{company_name}</p><p>{app_name}</p>',
                ],
            ],
            'Sales Invoice' => [
                'subject' => 'Sales Invoice Created',
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "App Url": "app_url",
                    "Invoice Number": "invoice_number",
                    "Customer Name": "sales_customer_name",
                    "Warehouse Name": "warehouse_name",
                    "Total Amount ": "total_amount",
                    "Discount Amount" : "discount_amount"
                  }',
                  'lang' => [
                    'ar' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">مرحبًا، {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">مرحبًا بك في {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">نأمل أن تصلك هذه الرسالة وأنت بخير! يرجى العثور على تفاصيل فاتورتك أدناه.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>رقم الفاتورة:</strong> {invoice_number}<br>
                    <strong>المستودع:</strong> {warehouse_name}<br>
                    <strong>المبلغ الإجمالي:</strong> {total_amount}<br>
                    <strong>مبلغ الخصم:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    لا تتردد في التواصل معنا إذا كان لديك أي أسئلة.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    شكرًا لك،,<br>
                    مع أطيب التحيات،<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'da' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hej, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Velkommen til {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Jeg håber, denne e-mail finder dig vel. Nedenfor finder du detaljerne for din faktura.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Fakturanummer:</strong> {invoice_number}<br>
                    <strong>Lager:</strong> {warehouse_name}<br>
                    <strong>Samlet beløb:</strong> {total_amount}<br>
                    <strong>Rabatbeløb:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Du er velkommen til at kontakte os, hvis du har spørgsmål.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Tak,<br>
                    Med venlig hilsen,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'de' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hallo, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Willkommen bei {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Ich hoffe, diese E-Mail erreicht Sie wohlbehalten. Nachfolgend finden Sie die Details Ihrer Rechnung.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Rechnungsnummer:</strong> {invoice_number}<br>
                    <strong>Lager:</strong> {warehouse_name}<br>
                    <strong>Gesamtbetrag:</strong> {total_amount}<br>
                    <strong>Rabattbetrag:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Bitte zögern Sie nicht, uns bei Fragen zu kontaktieren.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Vielen Dank,<br>
                    Mit freundlichen Grüßen,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                   'en' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hi, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Welcome to {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hope this email finds you well! Please find the details of your invoice below.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Invoice Number:</strong> {invoice_number}<br>
                    <strong>Warehouse:</strong> {warehouse_name}<br>
                    <strong>Total Amount:</strong> {total_amount}<br>
                    <strong>Discount Amount:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Feel free to reach out if you have any questions.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Thank You,<br>
                    Regards,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',
                    
                    'es' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hola, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Bienvenido a {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Esperamos que este correo le encuentre bien. A continuación encontrará los detalles de su factura.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Número de factura:</strong> {invoice_number}<br>
                    <strong>Almacén:</strong> {warehouse_name}<br>
                    <strong>Importe total:</strong> {total_amount}<br>
                    <strong>Importe del descuento:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    No dude en contactarnos si tiene alguna pregunta.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Gracias,<br>
                    Saludos,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'fr' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Bonjour, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Bienvenue chez {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Nous espérons que cet e-mail vous trouve bien. Veuillez trouver ci-dessous les détails de votre facture.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Numéro de facture:</strong> {invoice_number}<br>
                    <strong>Entrepôt:</strong> {warehouse_name}<br>
                    <strong>Montant total:</strong> {total_amount}<br>
                    <strong>Montant de la remise:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Nhésitez pas à nous contacter si vous avez des questions.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Merci,<br>
                    Cordialement,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'he' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">שלום, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">ברוכים הבאים ל {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">מקווים שהאימייל הזה מוצא אותך בטוב. להלן פרטי החשבונית שלך..</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>מספר חשבונית:</strong> {invoice_number}<br>
                    <strong>מחסן:</strong> {warehouse_name}<br>
                    <strong>סכום כולל:</strong> {total_amount}<br>
                    <strong>סכום הנחה:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    אל תהסס לפנות אלינו אם יש לך שאלות.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    תודה,<br>
                    בברכה,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'it' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Ciao, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Benvenuto in {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Speriamo che questa email la trovi bene. Di seguito trova i dettagli della sua fattura.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Numero fattura:</strong> {invoice_number}<br>
                    <strong>Magazzino:</strong> {warehouse_name}<br>
                    <strong>Importo totale:</strong> {total_amount}<br>
                    <strong>Importo dello sconto:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Non esiti a contattarci per qualsiasi domanda.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Grazie,<br>
                    Cordiali saluti,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                   'ja' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">こんにちは、{sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">{app_name}へようこそ</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">お元気でお過ごしのことと思います。以下に請求書の詳細をご確認ください。</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>請求書番号:</strong> {invoice_number}<br>
                    <strong>倉庫:</strong> {warehouse_name}<br>
                    <strong>合計金額:</strong> {total_amount}<br>
                    <strong>割引金額:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    ご不明な点がございましたら、お気軽にお問い合わせください。
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    ありがとうございます。<br>
                    よろしくお願いいたします。<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'nl' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Hallo, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Welkom bij {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Ik hoop dat deze e-mail u goed bereikt. Hieronder vindt u de details van uw factuur.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Factuurnummer:</strong> {invoice_number}<br>
                    <strong>Magazijn:</strong> {warehouse_name}<br>
                    <strong>Totaalbedrag:</strong> {total_amount}<br>
                    <strong>Kortingsbedrag:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Neem gerust contact met ons op als u vragen heeft.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Dank u,<br>
                    Met vriendelijke groet,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'pl' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Cześć, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Witamy w {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Mamy nadzieję, że ten e-mail zastaje Cię w dobrym zdrowiu. Poniżej znajdziesz szczegóły swojej faktury.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Numer faktury:</strong> {invoice_number}<br>
                    <strong>Magazyn:</strong> {warehouse_name}<br>
                    <strong>Łączna kwota:</strong> {total_amount}<br>
                    <strong>Kwota rabatu:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    W razie pytań prosimy o kontakt.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Dziękujemy,<br>
                    Z poważaniem,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',


                    'ru' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Здравствуйте, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Добро пожаловать в {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Надеемся, что это письмо находит вас в хорошем состоянии. Ниже приведены детали вашего счета.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Номер счета:</strong> {invoice_number}<br>
                    <strong>Склад:</strong> {warehouse_name}<br>
                    <strong>Общая сумма:</strong> {total_amount}<br>
                    <strong>Сумма скидки:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Пожалуйста, свяжитесь с нами, если у вас возникнут вопросы.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Спасибо,<br>
                    С уважением,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',


                    'pt' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Olá, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Bem-vindo a {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Esperamos que este e-mail o encontre bem. Abaixo estão os detalhes da sua fatura.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Número da fatura:</strong> {invoice_number}<br>
                    <strong>Armazém:</strong> {warehouse_name}<br>
                    <strong>Valor total:</strong> {total_amount}<br>
                    <strong>Valor do desconto:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Sinta-se à vontade para entrar em contacto se tiver alguma dúvida.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Obrigado,<br>
                    Com os melhores cumprimentos,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'pt-BR' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Olá, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Bem-vindo à {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Esperamos que este e-mail o encontre bem. Abaixo estão os detalhes da sua fatura.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Número da fatura:</strong> {invoice_number}<br>
                    <strong>Armazém:</strong> {warehouse_name}<br>
                    <strong>Valor total:</strong> {total_amount}<br>
                    <strong>Valor do desconto:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Sinta-se à vontade para entrar em contato se tiver alguma dúvida.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Obrigado,<br>
                    Atenciosamente,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'tr' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Merhaba, {sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">{app_name}\'e hoş geldiniz</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">Umarız bu e-posta sizi iyi bulur. Aşağıda faturanızın detaylarını bulabilirsiniz.</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>Fatura Numarası:</strong> {invoice_number}<br>
                    <strong>Depo:</strong> {warehouse_name}<br>
                    <strong>Toplam Tutar:</strong> {total_amount}<br>
                    <strong>İndirim Tutarı:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    Teşekkür ederiz,<br>
                    Saygılarımızla,<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',

                    'zh' => '<p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">您好，{sales_customer_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">欢迎来到 {app_name}</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">希望此邮件能让您一切安好。以下是您的发票详情。</span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    <strong>发票编号:</strong> {invoice_number}<br>
                    <strong>仓库:</strong> {warehouse_name}<br>
                    <strong>总金额:</strong> {total_amount}<br>
                    <strong>折扣金额:</strong> {discount_amount}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    如果您有任何问题，请随时与我们联系。
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    谢谢，<br>
                    此致敬礼，<br>
                    {company_name}
                    </span></p>

                    <p><span style="color: #1d1c1d; font-family: Slack-Lato, Slack-Fractions, appleLogo, sans-serif; font-size: 15px; background-color: #f8f8f8;">
                    {app_url}
                    </span></p>',
                ],
            ],
            'Sales Invoice Return' => [
                'subject' => 'Invoice Return',
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "App Url": "app_url",
                    "Return Number": "return_number",
                    "Return Date": "return_date",
                    "Customer Name": "sales_customer_name",
                    "Warehouse Name": "warehouse_name",
                    "Reason": "reason",
                    "Total Amount": "total_amount"
                  }',
                'lang' => [
                    'ar' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    مرحبا <strong>{sales_customer_name}</strong>،
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    نود إعلامك بأنه تم <strong>إرجاع فاتورة المبيعات</strong> بنجاح في <strong>{app_name}</strong>.
                    يرجى الاطلاع على تفاصيل الإرجاع أدناه.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">الحقل</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">رقم الإرجاع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">تاريخ الإرجاع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">اسم العميل</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">المستودع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">السبب</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">المبلغ الإجمالي</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    إذا كان لديك أي أسئلة بخصوص هذا الإرجاع، فلا تتردد في التواصل مع <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    شكرًا لاستخدامك <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    مع التحية،<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'da' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hej <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Vi vil gerne informere dig om, at en <strong>Retur af salgsfaktura</strong> er blevet behandlet med succes i <strong>{app_name}</strong>.
                    Se venligst returdetaljerne nedenfor.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Felt</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detaljer</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Returnummer</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Returdato</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Kundenavn</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Lager</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Årsag</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Samlet beløb</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Hvis du har spørgsmål vedrørende denne retur, er du velkommen til at kontakte <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Tak fordi du bruger <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Med venlig hilsen,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'de' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wir möchten Sie darüber informieren, dass eine <strong>Rückgabe einer Verkaufsrechnung</strong> erfolgreich in <strong>{app_name}</strong> verarbeitet wurde.
                    Die Rückgabedetails finden Sie unten.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Feld</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Rücksendenummer</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Rücksendedatum</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Kundenname</td><td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Lager</td><td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Grund</td><td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Gesamtbetrag</td><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td></tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Wenn Sie Fragen zu dieser Rückgabe haben, wenden Sie sich bitte an <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Vielen Dank für die Nutzung von <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Mit freundlichen Grüßen,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'en' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hello <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    We would like to inform you that a <strong>Sales Invoice Return</strong> has been successfully processed in <strong>{app_name}</strong>.  
                    Please find the return details below.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Field</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Return Number</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Return Date</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Customer Name</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Warehouse</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Reason</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Total Amount</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    If you have any questions regarding this return, please feel free to contact <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Thank you for using <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Regards,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'es' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hola <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nos gustaría informarle que una <strong>Devolución de factura de venta</strong> ha sido procesada con éxito en <strong>{app_name}</strong>.
                    A continuación encontrará los detalles de la devolución.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalles</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número de devolución</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Fecha de devolución</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nombre del cliente</td><td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Almacén</td><td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td><td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Importe total</td><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td></tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Si tiene alguna pregunta sobre esta devolución, no dude en contactar con <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Gracias por usar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Saludos,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'fr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Bonjour <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nous souhaitons vous informer qu’un <strong>Retour de facture de vente</strong> a été traité avec succès dans <strong>{app_name}</strong>.
                    Veuillez trouver les détails du retour ci-dessous.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Champ</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Détails</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numéro de retour</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Date de retour</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nom du client</td><td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Entrepôt</td><td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Raison</td><td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Montant total</td><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td></tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Si vous avez des questions concernant ce retour, n’hésitez pas à contacter <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Merci d’utiliser <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Cordialement,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'he' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    שלום <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    ברצוננו להודיע לך כי <strong>החזרת חשבונית מכירה</strong> עובדה בהצלחה ב־<strong>{app_name}</strong>.
                    להלן פרטי ההחזרה.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">שדה</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">פרטים</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">מספר החזרה</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">תאריך החזרה</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">שם הלקוח</td><td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">מחסן</td><td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">סיבה</td><td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">סכום כולל</td><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td></tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    אם יש לך שאלות לגבי החזרה זו, אנא פנה אל <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    תודה על השימוש ב־<strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    בברכה,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'it' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Ciao <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Desideriamo informarti che un <strong>Reso fattura di vendita</strong> è stato elaborato con successo in <strong>{app_name}</strong>.
                    Di seguito trovi i dettagli del reso.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numero reso</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data reso</td><td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome cliente</td><td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazzino</td><td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td></tr>
                    <tr><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td><td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td></tr>
                    <tr style="background-color:#fafafa;"><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Importo totale</td><td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td></tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Se hai domande su questo reso, contatta <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Grazie per aver utilizzato <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Cordiali saluti,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'ja' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    こんにちは <strong>{sales_customer_name}</strong> 様、
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> にて <strong>販売請求書返品</strong> が正常に処理されたことをお知らせいたします。  
                    返品の詳細は以下をご確認ください。
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">項目</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">詳細</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">返品番号</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">返品日</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">顧客名</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">倉庫</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">理由</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">合計金額</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    返品についてご質問がございましたら、<strong>{company_name}</strong> までお気軽にお問い合わせください。
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    <strong>{app_name}</strong> をご利用いただきありがとうございます。
                    </p>

                    <p style="font-size:14px;color:#666;">
                    敬具<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'nl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    We willen u informeren dat een <strong>Verkoopfactuur Retour</strong> succesvol is verwerkt in <strong>{app_name}</strong>.  
                    Hieronder vindt u de retourgegevens.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Veld</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Retournummer</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Retourdatum</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Klantnaam</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazijn</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Reden</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Totaalbedrag</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Als u vragen heeft over deze retourzending, neem dan gerust contact op met <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Bedankt voor het gebruik van <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Met vriendelijke groet,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'pl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Witaj <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Informujemy, że <strong>Zwrot faktury sprzedaży</strong> został pomyślnie przetworzony w <strong>{app_name}</strong>.  
                    Szczegóły zwrotu znajdują się poniżej.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Pole</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Szczegóły</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numer zwrotu</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data zwrotu</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nazwa klienta</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazyn</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Powód</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Łączna kwota</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    W przypadku pytań dotyczących tego zwrotu prosimy o kontakt z <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Dziękujemy za korzystanie z <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Pozdrawiamy,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'ru' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Здравствуйте, <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Сообщаем вам, что <strong>возврат счета продажи</strong> был успешно обработан в <strong>{app_name}</strong>.  
                    Пожалуйста, ознакомьтесь с деталями возврата ниже.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Поле</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Детали</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Номер возврата</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Дата возврата</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Имя клиента</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Склад</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Причина</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Общая сумма</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Если у вас есть вопросы по поводу этого возврата, пожалуйста свяжитесь с <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Спасибо за использование <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    С уважением,<br>
                    <strong>{company_name}</strong>
                    </p>',

                   'pt' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma <strong>Devolução de Fatura de Venda</strong> foi processada com sucesso em <strong>{app_name}</strong>.  
                    Por favor, veja os detalhes da devolução abaixo.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalhes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número da Devolução</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data da Devolução</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome do Cliente</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Armazém</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Valor Total</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Se tiver alguma dúvida sobre esta devolução, por favor contacte <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Obrigado por utilizar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Com os melhores cumprimentos,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'pt-BR' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma <strong>Devolução de Fatura de Venda</strong> foi processada com sucesso em <strong>{app_name}</strong>.  
                    Por favor, veja os detalhes da devolução abaixo.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalhes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número da Devolução</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data da Devolução</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome do Cliente</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Armazém</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Valor Total</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Se você tiver alguma dúvida sobre esta devolução, entre em contato com <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Obrigado por usar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Atenciosamente,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    
                    'tr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Merhaba <strong>{sales_customer_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> içerisinde bir <strong>Satış Faturası İadesi</strong> başarıyla işlendiğini bildirmek isteriz.  
                    İade detaylarını aşağıda bulabilirsiniz.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Alan</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detaylar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">İade Numarası</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">İade Tarihi</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Müşteri Adı</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Depo</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Sebep</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Toplam Tutar</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Bu iade hakkında herhangi bir sorunuz varsa lütfen <strong>{company_name}</strong> ile iletişime geçin.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    <strong>{app_name}</strong> kullandığınız için teşekkür ederiz.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Saygılarımızla,<br>
                    <strong>{company_name}</strong>
                    </p>',
                    
                    'zh' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    您好 <strong>{sales_customer_name}</strong>，
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    我们想通知您，<strong>销售发票退货</strong> 已在 <strong>{app_name}</strong> 中成功处理。  
                    请查看以下退货详情。
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">字段</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">详情</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">退货编号</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">退货日期</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">客户名称</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{sales_customer_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">仓库</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">原因</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">总金额</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    如果您对本次退货有任何疑问，请联系 <strong>{company_name}</strong>。
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    感谢您使用 <strong>{app_name}</strong>。
                    </p>

                    <p style="font-size:14px;color:#666;">
                    此致敬礼，<br>
                    <strong>{company_name}</strong>
                    </p>',

                ],
            ],
            'Purchase Invoice' => [
                'subject' => 'Purchase Invoice Created',
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "App Url": "app_url",
                    "Invoice Number": "invoice_number",
                    "Vandor Name": "purchase_vendor_name",
                    "Warehouse Name": "warehouse_name",
                    "Total Amount": "total_amount"
                    "Discount Amount": "discount_amount",
                  }',
                'lang' => [
                    'ar' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    مرحبًا <strong>{purchase_vendor_name}</strong>،

                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    نود إعلامك بأنه تم إنشاء فاتورة شراء في <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>تفاصيل الفاتورة:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>رقم الفاتورة:</strong> {invoice_number}</li>
                    <li><strong>اسم المورد:</strong> {purchase_vendor_name}</li>
                    <li><strong>اسم المستودع:</strong> {warehouse_name}</li>
                    <li><strong>قيمة الخصم:</strong> {discount_amount}</li>
                    <li><strong>المبلغ الإجمالي:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    إذا كانت لديك أي أسئلة، فلا تتردد في التواصل مع <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    شكرًا لك،<br>
                    <strong>{company_name}</strong>
                    </p>',

                   'da' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hej <strong>{purchase_vendor_name}</strong>,

                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Vi vil gerne informere dig om, at der er blevet oprettet en købsfaktura i <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Fakturadetaljer:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Fakturanummer:</strong> {invoice_number}</li>
                    <li><strong>Leverandørnavn:</strong> {purchase_vendor_name}</li>
                    <li><strong>Lagerets navn:</strong> {warehouse_name}</li>
                    <li><strong>Rabatbeløb:</strong> {discount_amount}</li>
                    <li><strong>Samlet beløb:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Hvis du har spørgsmål, er du velkommen til at kontakte <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Tak,<br>
                    <strong>{company_name}</strong>
                    </p>',



                    'de' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{purchase_vendor_name}</strong>,

                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wir möchten Sie darüber informieren, dass in <strong>{app_name}</strong> eine Einkaufsrechnung erstellt wurde.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Rechnungsdetails:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Rechnungsnummer:</strong> {invoice_number}</li>
                    <li><strong>Lieferantenname:</strong> {purchase_vendor_name}</li>
                    <li><strong>Lagername:</strong> {warehouse_name}</li>
                    <li><strong>Rabattbetrag:</strong> {discount_amount}</li>
                    <li><strong>Gesamtbetrag:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wenn Sie Fragen haben, können Sie sich gerne an <strong>{company_name}</strong> wenden.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Vielen Dank,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'en' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hello <strong>{purchase_vendor_name}</strong>,

                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    We would like to inform you that a purchase invoice has been generated in <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Invoice Details:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Invoice Number:</strong> {invoice_number}</li>
                    <li><strong>Vendor Name:</strong> {purchase_vendor_name}</li>
                    <li><strong>Warehouse Name:</strong> {warehouse_name}</li>
                    <li><strong>Discount Amount:</strong> {discount_amount}</li>
                    <li><strong>Total Amount:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    If you have any questions, please feel free to contact <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Thank you,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'es' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hola <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nos gustaría informarle que se ha generado una factura de compra en <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Detalles de la factura:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Número de factura:</strong> {invoice_number}</li>
                    <li><strong>Nombre del proveedor:</strong> {purchase_vendor_name}</li>
                    <li><strong>Nombre del almacén:</strong> {warehouse_name}</li>
                    <li><strong>Monto de descuento:</strong> {discount_amount}</li>
                    <li><strong>Monto total:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Si tiene alguna pregunta, no dude en contactar con <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Gracias,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'fr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Bonjour <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nous souhaitons vous informer qu\'une facture d\'achat a été générée dans <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Détails de la facture :</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Numéro de facture :</strong> {invoice_number}</li>
                    <li><strong>Nom du fournisseur :</strong> {purchase_vendor_name}</li>
                    <li><strong>Nom de l\'entrepôt :</strong> {warehouse_name}</li>
                    <li><strong>Montant de la remise :</strong> {discount_amount}</li>
                    <li><strong>Montant total :</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Si vous avez des questions, n\'hésitez pas à contacter <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Merci,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'he' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    שלום <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    אנו רוצים להודיע לך כי נוצרה חשבונית רכישה ב־<strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>פרטי החשבונית:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>מספר חשבונית:</strong> {invoice_number}</li>
                    <li><strong>שם הספק:</strong> {purchase_vendor_name}</li>
                    <li><strong>שם המחסן:</strong> {warehouse_name}</li>
                    <li><strong>סכום הנחה:</strong> {discount_amount}</li>
                    <li><strong>סכום כולל:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    אם יש לך שאלות, אל תהסס ליצור קשר עם <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    תודה,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'it' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Ciao <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Desideriamo informarti che è stata generata una fattura di acquisto in <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Dettagli della fattura:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Numero fattura:</strong> {invoice_number}</li>
                    <li><strong>Nome del fornitore:</strong> {purchase_vendor_name}</li>
                    <li><strong>Nome del magazzino:</strong> {warehouse_name}</li>
                    <li><strong>Importo sconto:</strong> {discount_amount}</li>
                    <li><strong>Importo totale:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Se hai domande, non esitare a contattare <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Grazie,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'ja' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    こんにちは <strong>{purchase_vendor_name}</strong> 様,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> にて仕入請求書が作成されましたのでお知らせいたします。
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>請求書の詳細:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>請求書番号:</strong> {invoice_number}</li>
                    <li><strong>仕入先名:</strong> {purchase_vendor_name}</li>
                    <li><strong>倉庫名:</strong> {warehouse_name}</li>
                    <li><strong>割引額:</strong> {discount_amount}</li>
                    <li><strong>合計金額:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    ご不明な点がございましたら、<strong>{company_name}</strong> までお問い合わせください。
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    ありがとうございます。<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'nl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wij willen u informeren dat er een inkoopfactuur is gegenereerd in <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Factuurdetails:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Factuurnummer:</strong> {invoice_number}</li>
                    <li><strong>Leveranciersnaam:</strong> {purchase_vendor_name}</li>
                    <li><strong>Magazijnnaam:</strong> {warehouse_name}</li>
                    <li><strong>Kortingsbedrag:</strong> {discount_amount}</li>
                    <li><strong>Totaalbedrag:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Als u vragen heeft, neem dan gerust contact op met <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Bedankt,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'pl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Witaj <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Chcielibyśmy poinformować, że w <strong>{app_name}</strong> została wygenerowana faktura zakupu.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Szczegóły faktury:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Numer faktury:</strong> {invoice_number}</li>
                    <li><strong>Nazwa dostawcy:</strong> {purchase_vendor_name}</li>
                    <li><strong>Nazwa magazynu:</strong> {warehouse_name}</li>
                    <li><strong>Kwota rabatu:</strong> {discount_amount}</li>
                    <li><strong>Kwota całkowita:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Jeśli masz pytania, skontaktuj się z <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Dziękujemy,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'pt' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma fatura de compra foi gerada em <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Detalhes da fatura:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Número da fatura:</strong> {invoice_number}</li>
                    <li><strong>Nome do fornecedor:</strong> {purchase_vendor_name}</li>
                    <li><strong>Nome do armazém:</strong> {warehouse_name}</li>
                    <li><strong>Valor do desconto:</strong> {discount_amount}</li>
                    <li><strong>Valor total:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Se tiver alguma dúvida, entre em contato com <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Obrigado,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'pt-BR' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma fatura de compra foi gerada no <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Detalhes da fatura:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Número da fatura:</strong> {invoice_number}</li>
                    <li><strong>Nome do fornecedor:</strong> {purchase_vendor_name}</li>
                    <li><strong>Nome do armazém:</strong> {warehouse_name}</li>
                    <li><strong>Valor do desconto:</strong> {discount_amount}</li>
                    <li><strong>Valor total:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Se tiver alguma dúvida, entre em contato com <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Obrigado,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'ru' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Здравствуйте <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Сообщаем вам, что в <strong>{app_name}</strong> был создан счет на покупку.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Детали счета:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Номер счета:</strong> {invoice_number}</li>
                    <li><strong>Имя поставщика:</strong> {purchase_vendor_name}</li>
                    <li><strong>Название склада:</strong> {warehouse_name}</li>
                    <li><strong>Сумма скидки:</strong> {discount_amount}</li>
                    <li><strong>Общая сумма:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Если у вас есть вопросы, свяжитесь с <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Спасибо,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'tr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Merhaba <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> içinde bir satın alma faturası oluşturulduğunu bildirmek isteriz.
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>Fatura Detayları:</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>Fatura Numarası:</strong> {invoice_number}</li>
                    <li><strong>Tedarikçi Adı:</strong> {purchase_vendor_name}</li>
                    <li><strong>Depo Adı:</strong> {warehouse_name}</li>
                    <li><strong>İndirim Tutarı:</strong> {discount_amount}</li>
                    <li><strong>Toplam Tutar:</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Herhangi bir sorunuz varsa <strong>{company_name}</strong> ile iletişime geçebilirsiniz.
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    Teşekkürler,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'zh' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    您好 <strong>{purchase_vendor_name}</strong>，
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    我们想通知您，在 <strong>{app_name}</strong> 中已生成一张采购发票。
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>发票详情：</strong>
                    </p>

                    <ul style="font-size:15px;color:#333;line-height:1.8;padding-left:20px;">
                    <li><strong>发票编号：</strong> {invoice_number}</li>
                    <li><strong>供应商名称：</strong> {purchase_vendor_name}</li>
                    <li><strong>仓库名称：</strong> {warehouse_name}</li>
                    <li><strong>折扣金额：</strong> {discount_amount}</li>
                    <li><strong>总金额：</strong> {total_amount}</li>
                    </ul>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    如果您有任何问题，请联系 <strong>{company_name}</strong>。
                    </p>

                    <p style="font-size:15px;color:#333;margin-top:15px;">
                    谢谢，<br>
                    <strong>{company_name}</strong>
                    </p>',
                ],
            ],
            'Purchase Invoice Return' => [
                'subject' => 'Invoice Return',
                'variables' => '{
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "App Url": "app_url",
                    "Return Number": "return_number",
                    "Return Date": "return_date",
                    "Vendor Name": "purchase_vendor_name",
                    "Warehouse Name": "warehouse_name",
                    "Reason": "reason",
                    "Total Amount": "total_amount"
                  }',
                  'lang' => [
                    'ar' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    مرحبًا <strong>{purchase_vendor_name}</strong>،
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    نود إعلامك بأنه تم بنجاح معالجة <strong>إرجاع فاتورة المبيعات</strong> في <strong>{app_name}</strong>.
                    يرجى الاطلاع على تفاصيل الإرجاع أدناه.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">الحقل</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">رقم الإرجاع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">تاريخ الإرجاع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">اسم المورد</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">المستودع</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">السبب</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">المبلغ الإجمالي</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    إذا كان لديك أي استفسار بخصوص هذا الإرجاع، فلا تتردد في التواصل مع <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    شكرًا لاستخدامك <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    مع التحية،<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'da' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hej <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Vi vil gerne informere dig om, at en <strong>Salgsfaktura-retur</strong> er blevet behandlet med succes i <strong>{app_name}</strong>.
                    Se venligst returdetaljerne nedenfor.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Felt</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detaljer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Returnummer</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Returdato</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Leverandørnavn</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Lager</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Årsag</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Samlet beløb</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Hvis du har spørgsmål vedrørende denne retur, er du velkommen til at kontakte <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Tak fordi du bruger <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Med venlig hilsen,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'de' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wir möchten Sie darüber informieren, dass eine <strong>Verkaufsrechnungsrückgabe</strong> erfolgreich in <strong>{app_name}</strong> verarbeitet wurde.
                    Bitte finden Sie unten die Rückgabedetails.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Feld</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Rückgabenummer</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Rückgabedatum</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Lieferantenname</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Lager</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Grund</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Gesamtbetrag</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Wenn Sie Fragen zu dieser Rückgabe haben, können Sie sich gerne an <strong>{company_name}</strong> wenden.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Vielen Dank, dass Sie <strong>{app_name}</strong> verwenden.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Mit freundlichen Grüßen,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'en' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hello <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    We would like to inform you that a <strong>Sales Invoice Return</strong> has been successfully processed in <strong>{app_name}</strong>.
                    Please find the return details below.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Field</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Return Number</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Return Date</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Vendor Name</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Warehouse</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Reason</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Total Amount</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    If you have any questions regarding this return, please feel free to contact <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Thank you for using <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Regards,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'es' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hola <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nos gustaría informarle que una <strong>Devolución de Factura de Venta</strong> ha sido procesada con éxito en <strong>{app_name}</strong>.
                    Por favor, encuentre los detalles de la devolución a continuación.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número de devolución</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Fecha de devolución</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nombre del proveedor</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Almacén</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Razón</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Monto total</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Si tiene alguna pregunta sobre esta devolución, no dude en ponerse en contacto con <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Gracias por usar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Saludos,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'fr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Bonjour <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Nous souhaitons vous informer qu\'un <strong>Retour de facture de vente</strong> a été traité avec succès dans <strong>{app_name}</strong>.
                    Veuillez trouver les détails du retour ci-dessous.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Champ</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numéro de retour</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Date de retour</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nom du fournisseur</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Entrepôt</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Raison</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Montant total</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Si vous avez des questions concernant ce retour, veuillez contacter <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Merci d\'utiliser <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Cordialement,<br>
                    <strong>{company_name}</strong>
                    </p>',


                   'it' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Ciao <strong>{purchase_vendor_name}</strong>,
                    </p>
                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Ti informiamo che un <strong>Reso della fattura di vendita</strong> è stato elaborato con successo in <strong>{app_name}</strong>.
                    </p>
                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                    <thead>
                    <tr style="background-color:#f5f5f5;">
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                    <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numero di reso</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data di reso</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome fornitore</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazzino</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                    </tr>
                    <tr>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                    </tr>
                    <tr style="background-color:#fafafa;">
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Importo totale</td>
                    <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                    </tr>
                    </tbody>
                    </table>
                    <p style="font-size:15px;color:#333;margin-top:18px;">
                    Se hai domande contatta <strong>{company_name}</strong>.
                    </p>
                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Grazie per utilizzare <strong>{app_name}</strong>.
                    </p>
                    <p style="font-size:14px;color:#666;">
                    Cordiali saluti,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'ja' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    こんにちは <strong>{purchase_vendor_name}</strong> 様、
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> にて <strong>販売請求書返品</strong> が正常に処理されたことをお知らせいたします。
                    以下に返品の詳細をご確認ください。
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">項目</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">詳細</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">返品番号</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">返品日</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">仕入先名</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">倉庫</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">理由</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">合計金額</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    この返品に関してご不明な点がございましたら、<strong>{company_name}</strong> までお気軽にお問い合わせください。
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    <strong>{app_name}</strong> をご利用いただきありがとうございます。
                    </p>

                    <p style="font-size:14px;color:#666;">
                    よろしくお願いいたします。<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'nl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Wij willen u informeren dat een <strong>Verkoopfactuur Retour</strong> succesvol is verwerkt in <strong>{app_name}</strong>.
                    Bekijk hieronder de retourdetails.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Veld</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Retournummer</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Retourdatum</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Leveranciersnaam</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazijn</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Reden</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Totaalbedrag</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Als u vragen heeft over deze retour, neem dan gerust contact op met <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Bedankt voor het gebruik van <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Met vriendelijke groet,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'pl' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Witaj <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Chcielibyśmy poinformować, że <strong>Zwrot Faktury Sprzedaży</strong> został pomyślnie przetworzony w <strong>{app_name}</strong>.
                    Poniżej znajdują się szczegóły zwrotu.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Pole</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Szczegóły</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Numer zwrotu</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data zwrotu</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nazwa dostawcy</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Magazyn</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Powód</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Łączna kwota</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Jeśli masz jakiekolwiek pytania dotyczące tego zwrotu, skontaktuj się z <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Dziękujemy za korzystanie z <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Z poważaniem,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'ru' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Здравствуйте, <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Сообщаем вам, что <strong>возврат счета продажи</strong> был успешно обработан в <strong>{app_name}</strong>.
                    Пожалуйста, ознакомьтесь с деталями возврата ниже.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Поле</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Детали</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Номер возврата</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Дата возврата</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Имя поставщика</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Склад</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Причина</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Общая сумма</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Если у вас есть вопросы по этому возврату, пожалуйста, свяжитесь с <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Спасибо за использование <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    С уважением,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'pt' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma <strong>Devolução de Fatura de Venda</strong> foi processada com sucesso no <strong>{app_name}</strong>.
                    Por favor, veja os detalhes da devolução abaixo.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número da Devolução</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data da Devolução</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome do Fornecedor</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Armazém</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Valor Total</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Se tiver alguma dúvida sobre esta devolução, não hesite em contactar <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Obrigado por usar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Atenciosamente,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'pt-BR' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    Gostaríamos de informar que uma <strong>Devolução de Fatura de Venda</strong> foi processada com sucesso no <strong>{app_name}</strong>.
                    Por favor, veja os detalhes da devolução abaixo.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Campo</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Número da Devolução</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Data da Devolução</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Nome do Fornecedor</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Armazém</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Motivo</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Valor Total</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Se tiver alguma dúvida sobre esta devolução, não hesite em contactar <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    Obrigado por usar <strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Atenciosamente,<br>
                    <strong>{company_name}</strong>
                    </p>',


                    'he' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    שלום <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    ברצוננו להודיע לך כי <strong>החזרת חשבונית מכירה</strong> עובדה בהצלחה ב-<strong>{app_name}</strong>.
                    אנא עיין בפרטי ההחזרה להלן.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">שדה</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">פרטים</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">מספר החזרה</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">תאריך החזרה</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">שם הספק</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">מחסן</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">סיבה</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">סכום כולל</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    אם יש לך שאלות בנוגע להחזרה זו, אנא צור קשר עם <strong>{company_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    תודה על השימוש ב-<strong>{app_name}</strong>.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    בברכה,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'tr' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Merhaba <strong>{purchase_vendor_name}</strong>,
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    <strong>{app_name}</strong> içinde bir <strong>Satış Faturası İadesi</strong> başarıyla işlendiğini size bildirmek isteriz.
                    Lütfen aşağıdaki iade detaylarını inceleyin.
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Alan</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">Detaylar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">İade Numarası</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">İade Tarihi</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Tedarikçi Adı</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Depo</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Sebep</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">Toplam Tutar</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    Bu iade ile ilgili herhangi bir sorunuz varsa, lütfen <strong>{company_name}</strong> ile iletişime geçmekten çekinmeyin.
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    <strong>{app_name}</strong> kullandığınız için teşekkür ederiz.
                    </p>

                    <p style="font-size:14px;color:#666;">
                    Saygılarımızla,<br>
                    <strong>{company_name}</strong>
                    </p>',

                    'zh' => '<p style="font-size:15px;color:#333;margin-bottom:10px;">
                    您好 <strong>{purchase_vendor_name}</strong>，
                    </p>

                    <p style="font-size:15px;color:#333;line-height:1.6;">
                    我们想通知您，在 <strong>{app_name}</strong> 中一笔<strong>销售发票退货</strong>已成功处理。
                    请查看以下退货详情。
                    </p>

                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin-top:18px;font-size:14px;color:#333;">
                        <thead>
                            <tr style="background-color:#f5f5f5;">
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">字段</th>
                                <th align="left" style="padding:10px;border:1px solid #e5e5e5;">详情</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">退货编号</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_number}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">退货日期</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{return_date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">供应商名称</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{purchase_vendor_name}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">仓库</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{warehouse_name}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">原因</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;">{reason}</td>
                            </tr>
                            <tr style="background-color:#fafafa;">
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">总金额</td>
                                <td style="padding:10px;border:1px solid #e5e5e5;font-weight:600;">{total_amount}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="font-size:15px;color:#333;margin-top:18px;line-height:1.6;">
                    如果您对此退货有任何疑问，请随时联系 <strong>{company_name}</strong>。
                    </p>

                    <p style="font-size:14px;color:#666;margin-top:20px;">
                    感谢您使用 <strong>{app_name}</strong>。
                    </p>

                    <p style="font-size:14px;color:#666;">
                    此致敬礼，<br>
                    <strong>{company_name}</strong>
                    </p>',
                ],
            ],
            'Helpdesk Ticket' =>
            [
                'subject' => 'Helpdesk Ticket Created',
                'variables' => '{
                        "App Name": "app_name",
                        "Company Name" : "company_name",
                        "App Url": "app_url",
                        "Ticket Name": "ticket_name",
                        "Ticket ID": "ticket_id",
                        "Ticket URL" : "ticket_url",
                        "Ticket Description": "ticket_description",
                        "Ticket Category": "ticket_category",
                        "Ticket Priority": "ticket_priority",
                    }',
                    'lang' => [
                        'ar' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        مرحبًا ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        مرحبًا بك في <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        تم إنشاء تذكرة الدعم الخاصة بك بنجاح. سيقوم فريق الدعم لدينا بمراجعتها والرد عليك قريبًا.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>معرّف التذكرة :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>الوصف :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        يمكنك متابعة تقدم التذكرة الخاصة بك في أي وقت باستخدام الزر أدناه.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        عرض التذكرة
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        أو افتح التطبيق من هنا: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        شكرًا لتواصلك معنا. سيقوم فريقنا بمساعدتك في أقرب وقت ممكن.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        مع أطيب التحيات,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',


                        'da' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Hej ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Velkommen til <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Din supportticket er blevet oprettet med succes. Vores supportteam vil gennemgå den og vende tilbage til dig snart.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Ticket ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Beskrivelse :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Du kan følge status på din ticket når som helst ved at bruge knappen nedenfor.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Se Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Eller åbn applikationen her: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Tak fordi du kontaktede os. Vores team vil hjælpe dig så hurtigt som muligt.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Med venlig hilsen,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        
                        'de' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Hallo ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Willkommen bei <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Ihr Support-Ticket wurde erfolgreich erstellt. Unser Support-Team wird es prüfen und sich bald bei Ihnen melden.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Ticket ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Beschreibung :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Sie können den Fortschritt Ihres Tickets jederzeit über die untenstehende Schaltfläche verfolgen.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Ticket anzeigen
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Oder öffnen Sie die Anwendung hier: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Vielen Dank, dass Sie uns kontaktiert haben. Unser Team wird Ihnen so schnell wie möglich helfen.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Mit freundlichen Grüßen,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'en' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Hello ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Welcome to <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Your support ticket has been successfully created. Our support team will review it and get back to you shortly.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Ticket ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Description :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        You can track the progress of your ticket anytime using the button below.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        View Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Or open the application here: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Thank you for contacting us. Our team will assist you as soon as possible.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Best Regards,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'es' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Hola ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Bienvenido a <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Tu ticket de soporte se ha creado correctamente. Nuestro equipo de soporte lo revisará y se pondrá en contacto contigo pronto.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID del Ticket :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Descripción :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Puedes seguir el progreso de tu ticket en cualquier momento usando el botón a continuación.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Ver Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        O abre la aplicación aquí: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Gracias por contactarnos. Nuestro equipo te ayudará lo antes posible.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Saludos cordiales,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',


                        'fr' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Bonjour ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Bienvenue sur <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Votre ticket de support a été créé avec succès. Notre équipe de support va lexaminer et vous répondra bientôt.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID du Ticket :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Description :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Vous pouvez suivre lavancement de votre ticket à tout moment en utilisant le bouton ci-dessous.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Voir le Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Ou ouvrez lapplication ici : 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Merci de nous avoir contactés. Notre équipe vous assistera dès que possible.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Cordialement,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',


                        'it' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Ciao ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Benvenuto su <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Il tuo ticket di supporto è stato creato con successo. Il nostro team di supporto lo esaminerà e ti risponderà a breve.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID Ticket :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Descrizione :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Puoi monitorare lo stato del tuo ticket in qualsiasi momento utilizzando il pulsante qui sotto.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Visualizza Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Oppure apri lapplicazione qui: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Grazie per averci contattato. Il nostro team ti assisterà il prima possibile.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Cordiali saluti,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'ja' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        こんにちは  さん,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        <strong>{app_name}</strong> へようこそ 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        サポートチケットが正常に作成されました。サポートチームが内容を確認し、まもなくご連絡いたします。
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>チケットID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>説明 :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        以下のボタンを使用して、いつでもチケットの進捗状況を確認できます。
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        チケットを見る
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        またはこちらからアプリケーションを開いてください: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        お問い合わせいただきありがとうございます。サポートチームができるだけ早く対応いたします。
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        よろしくお願いいたします,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'nl' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Hallo ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Welkom bij <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Je supportticket is succesvol aangemaakt. Ons supportteam zal het bekijken en binnenkort contact met je opnemen.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Ticket ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Beschrijving :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Je kunt de voortgang van je ticket op elk moment volgen via de onderstaande knop.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Bekijk Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Of open de applicatie hier: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Bedankt dat je contact met ons hebt opgenomen. Ons team helpt je zo snel mogelijk.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Met vriendelijke groet,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'pl' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Witaj ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Witamy w <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Twoje zgłoszenie do pomocy technicznej zostało pomyślnie utworzone. Nasz zespół wsparcia wkrótce je przeanalizuje i skontaktuje się z Tobą.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID Zgłoszenia :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Opis :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Możesz w każdej chwili śledzić status swojego zgłoszenia, korzystając z poniższego przycisku.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Zobacz Zgłoszenie
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Lub otwórz aplikację tutaj: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Dziękujemy za kontakt z nami. Nasz zespół pomoże Ci tak szybko, jak to możliwe.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Pozdrawiamy,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'ru' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Здравствуйте ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Добро пожаловать в <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Ваш запрос в службу поддержки был успешно создан. Наша команда поддержки рассмотрит его и скоро свяжется с вами.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID Тикета :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Описание :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Вы можете отслеживать статус вашего тикета в любое время с помощью кнопки ниже.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Просмотреть Тикет
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Или откройте приложение здесь: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Спасибо, что связались с нами. Наша команда поможет вам как можно скорее.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        С уважением,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'pt' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Olá ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Bem-vindo ao <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Seu ticket de suporte foi criado com sucesso. Nossa equipe de suporte irá analisá-lo e retornará em breve.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID do Ticket :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Descrição :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Você pode acompanhar o progresso do seu ticket a qualquer momento usando o botão abaixo.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Ver Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Ou abra a aplicação aqui: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Obrigado por entrar em contato conosco. Nossa equipe irá ajudá-lo o mais rápido possível.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Atenciosamente,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'tr'=>'<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Merhaba ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        <strong>{app_name}</strong> uygulamasına hoş geldiniz 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Destek talebiniz başarıyla oluşturuldu. Destek ekibimiz talebinizi inceleyecek ve kısa süre içinde size geri dönüş yapacaktır.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Talep ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Açıklama :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Aşağıdaki butonu kullanarak talebinizin durumunu istediğiniz zaman takip edebilirsiniz.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Talebi Görüntüle
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Veya uygulamayı buradan açın: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Bizimle iletişime geçtiğiniz için teşekkür ederiz. Ekibimiz size mümkün olan en kısa sürede yardımcı olacaktır.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Saygılarımızla,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'pt-BR' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Olá ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Bem-vindo ao <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Seu ticket de suporte foi criado com sucesso. Nossa equipe de suporte irá analisá-lo e retornará em breve.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>ID do Ticket :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Descrição :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Você pode acompanhar o progresso do seu ticket a qualquer momento usando o botão abaixo.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Ver Ticket
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Ou abra a aplicação aqui: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Obrigado por entrar em contato conosco. Nossa equipe irá ajudá-lo o mais rápido possível.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Atenciosamente,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'he' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        שלום ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        ברוכים הבאים ל־<strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        כרטיס התמיכה שלך נוצר בהצלחה. צוות התמיכה שלנו יבדוק אותו ויחזור אליך בקרוב.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>מזהה כרטיס :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>תיאור :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        תוכל לעקוב אחר התקדמות הכרטיס שלך בכל עת באמצעות הכפתור למטה.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        הצג כרטיס
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        או פתח את האפליקציה כאן: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        תודה שפנית אלינו. הצוות שלנו יסייע לך בהקדם האפשרי.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        בברכה,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'tr' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        Merhaba ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        <strong>{app_name}</strong> uygulamasına hoş geldiniz 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Destek talebiniz başarıyla oluşturuldu. Destek ekibimiz talebinizi inceleyecek ve kısa süre içinde size geri dönüş yapacaktır.
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>Talep ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>Açıklama :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        Aşağıdaki butonu kullanarak talebinizin durumunu istediğiniz zaman takip edebilirsiniz.
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        Talebi Görüntüle
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        Veya uygulamayı buradan açın: 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        Bizimle iletişime geçtiğiniz için teşekkür ederiz. Ekibimiz size mümkün olan en kısa sürede yardımcı olacaktır.
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        Saygılarımızla,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',

                        'zh' => '<div style="font-family: Arial, Helvetica, sans-serif; background:#f7f8fc; padding:25px;">

                        <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;padding:25px;border:1px solid #e6e8f0;">

                        <p style="font-size:18px;color:#333;margin-bottom:15px;">
                        您好 ,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        欢迎使用 <strong>{app_name}</strong> 🎉
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        您的支持工单已成功创建。我们的支持团队将会查看您的请求并尽快与您联系。
                        </p>

                        <div style="background:#f1f3ff;border-left:4px solid #6676ef;padding:15px;margin:20px 0;border-radius:5px;">
                        <p style="margin:0;font-size:14px;color:#333;">
                        <strong>工单 ID :</strong> {ticket_id}
                        </p>

                        <p style="margin-top:8px;font-size:14px;color:#333;">
                        <strong>描述 :</strong> {ticket_description}
                        </p>
                        </div>

                        <p style="font-size:15px;color:#555;line-height:1.7;">
                        您可以随时使用下面的按钮查看您的工单状态。
                        </p>

                        <p style="text-align:center;margin:25px 0;">
                        <a href="{ticket_url}" target="_blank"
                        style="background:#6676ef;color:#ffffff;padding:12px 22px;text-decoration:none;border-radius:6px;font-size:15px;font-weight:bold;">
                        查看工单
                        </a>
                        </p>

                        <p style="font-size:14px;color:#666;">
                        或在这里打开应用： 
                        <a href="{app_url}" style="color:#6676ef;text-decoration:none;">{app_url}</a>
                        </p>

                        <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                        <p style="font-size:14px;color:#777;">
                        感谢您联系我们。我们的团队会尽快为您提供帮助。
                        </p>

                        <p style="font-size:14px;color:#333;margin-top:15px;">
                        此致敬礼,<br>
                        <strong>{company_name}</strong><br>
                        {app_name}
                        </p>

                        </div>
                        </div>',
                    ],
            ],
            'Helpdesk Ticket Reply' => [
                'subject' => 'Helpdesk Ticket Reply',
                'variables' => '{
                        "App Name" : "app_name",
                        "Company Name" : "company_name",
                        "App Url": "app_url",
                        "Ticket Name" : "ticket_name",
                        "Ticket Id" : "ticket_id",
                        "Ticket URL" : "ticket_url",
                        "Ticket Description" : "ticket_description"
                    }',
                'lang' => [
                    'ar' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • إشعار تذكرة الدعم
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    مرحباً،
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    تم استلام طلب الدعم الخاص بك بنجاح من قبل <strong>{company_name}</strong>.  
                    سيقوم فريقنا بمراجعة المشكلة والرد عليك في أقرب وقت ممكن.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>رقم التذكرة :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>عنوان التذكرة :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>الوصف :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    يمكنك متابعة التحديثات أو الرد على هذه التذكرة في أي وقت باستخدام الزر أدناه.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    عرض التذكرة
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    إذا كنت بحاجة إلى مزيد من المساعدة، فلا تتردد في التواصل من خلال التطبيق.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',

                    'da' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Support Ticket Notifikation
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hej,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Din supportanmodning er blevet modtaget af <strong>{company_name}</strong>.  
                    Vores team vil gennemgå problemet og vende tilbage til dig så hurtigt som muligt.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket Titel :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Beskrivelse :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Du kan følge opdateringer eller svare på denne ticket når som helst via knappen nedenfor.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Se Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Hvis du har brug for yderligere hjælp, er du velkommen til at kontakte os via applikationen.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',

                    'de' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Support Ticket Benachrichtigung
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Ihre Support-Anfrage wurde erfolgreich von <strong>{company_name}</strong> empfangen.  
                    Unser Team wird das Problem prüfen und sich so schnell wie möglich bei Ihnen melden.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket Titel :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Beschreibung :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Sie können Updates verfolgen oder jederzeit über die Schaltfläche unten auf dieses Ticket antworten.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Ticket anzeigen
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Wenn Sie weitere Unterstützung benötigen, können Sie uns jederzeit über die Anwendung kontaktieren.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    
                    'en' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Support Ticket Notification
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hello,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Your support request has been successfully received by <strong>{company_name}</strong>.  
                    Our team will review the issue and respond as soon as possible.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket Title :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Description :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    You can track updates or reply to this ticket anytime using the button below.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    View Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    If you need further assistance, feel free to reach out through the application.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',

                    'es' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Notificación de Ticket de Soporte
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hola,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Su solicitud de soporte ha sido recibida con éxito por <strong>{company_name}</strong>.  
                    Nuestro equipo revisará el problema y le responderá lo antes posible.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID del Ticket :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Título del Ticket :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Descripción :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Puede seguir las actualizaciones o responder a este ticket en cualquier momento usando el botón de abajo.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Ver Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Si necesita más ayuda, no dude en comunicarse a través de la aplicación.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'fr' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Notification de Ticket de Support
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Bonjour,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Votre demande de support a été reçue avec succès par <strong>{company_name}</strong>.  
                    Notre équipe examinera le problème et vous répondra dès que possible.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID du Ticket :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Titre du Ticket :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Description :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Vous pouvez suivre les mises à jour ou répondre à ce ticket à tout moment en utilisant le bouton ci-dessous.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Voir le Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Si vous avez besoin d\une assistance supplémentaire, nhésitez pas à nous contacter via lapplication.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'it' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Notifica Ticket di Supporto
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Ciao,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    La tua richiesta di supporto è stata ricevuta con successo da <strong>{company_name}</strong>.  
                    Il nostro team esaminerà il problema e ti risponderà il prima possibile.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID Ticket :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Titolo Ticket :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Descrizione :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Puoi monitorare gli aggiornamenti o rispondere a questo ticket in qualsiasi momento utilizzando il pulsante qui sotto.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Visualizza Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Se hai bisogno di ulteriore assistenza, non esitare a contattarci tramite l\applicazione.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'ja' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • サポートチケット通知
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    こんにちは、
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    あなたのサポートリクエストは <strong>{company_name}</strong> に正常に送信されました。  
                    サポートチームが問題を確認し、できるだけ早くご連絡いたします。
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>チケットID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>チケットタイトル :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>説明 :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    以下のボタンを使用して、いつでもチケットの更新状況を確認したり返信することができます。
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    チケットを見る
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    さらにサポートが必要な場合は、アプリケーションからお気軽にお問い合わせください。
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'nl' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Support Ticket Meldingsbericht
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Hallo,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Uw supportverzoek is succesvol ontvangen door <strong>{company_name}</strong>.  
                    Ons team zal het probleem bekijken en zo snel mogelijk contact met u opnemen.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Ticket Titel :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Beschrijving :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    U kunt updates volgen of op elk moment op dit ticket reageren via de onderstaande knop.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Bekijk Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Als u verdere hulp nodig heeft, neem dan gerust contact met ons op via de applicatie.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'pl' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Powiadomienie o Zgłoszeniu Wsparcia
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Witaj,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Twoje zgłoszenie wsparcia zostało pomyślnie otrzymane przez <strong>{company_name}</strong>.  
                    Nasz zespół przeanalizuje problem i odpowie tak szybko, jak to możliwe.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID zgłoszenia :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Tytuł zgłoszenia :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Opis :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Możesz śledzić aktualizacje lub odpowiedzieć na to zgłoszenie w dowolnym momencie, korzystając z poniższego przycisku.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Zobacz zgłoszenie
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Jeśli potrzebujesz dodatkowej pomocy, skontaktuj się z nami za pośrednictwem aplikacji.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'ru' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Уведомление о Тикете Поддержки
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Здравствуйте,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Ваш запрос в службу поддержки был успешно получен компанией <strong>{company_name}</strong>.  
                    Наша команда рассмотрит проблему и ответит вам как можно скорее.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID тикета :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Название тикета :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Описание :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Вы можете отслеживать обновления или ответить на этот тикет в любое время, используя кнопку ниже.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Просмотреть тикет
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Если вам нужна дополнительная помощь, пожалуйста, свяжитесь с нами через приложение.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'pt' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Notificação de Ticket de Suporte
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Sua solicitação de suporte foi recebida com sucesso por <strong>{company_name}</strong>.  
                    Nossa equipe analisará o problema e responderá o mais rápido possível.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID do Ticket :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Título do Ticket :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Descrição :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Você pode acompanhar as atualizações ou responder a este ticket a qualquer momento usando o botão abaixo.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Ver Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Se precisar de mais assistência, sinta-se à vontade para entrar em contato através do aplicativo.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'pt-BR' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Notificação de Ticket de Suporte
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Olá,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Sua solicitação de suporte foi recebida com sucesso por <strong>{company_name}</strong>.  
                    Nossa equipe analisará o problema e responderá o mais rápido possível.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>ID do Ticket :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Título do Ticket :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Descrição :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Você pode acompanhar as atualizações ou responder a este ticket a qualquer momento usando o botão abaixo.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Ver Ticket
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Se precisar de mais assistência, sinta-se à vontade para entrar em contato através do aplicativo.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'he' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • התראה על כרטיס תמיכה
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    שלום,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    בקשת התמיכה שלך התקבלה בהצלחה על ידי <strong>{company_name}</strong>.  
                    הצוות שלנו יבדוק את הבעיה ויחזור אליך בהקדם האפשרי.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>מזהה כרטיס :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>כותרת הכרטיס :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>תיאור :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    תוכל לעקוב אחר עדכונים או להשיב לכרטיס זה בכל עת באמצעות הכפתור למטה.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    צפה בכרטיס
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    אם אתה זקוק לעזרה נוספת, אל תהסס לפנות אלינו דרך האפליקציה.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'tr' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • Destek Talebi Bildirimi
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    Merhaba,
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    Destek talebiniz <strong>{company_name}</strong> tarafından başarıyla alınmıştır.  
                    Ekibimiz sorunu inceleyecek ve en kısa sürede size geri dönüş yapacaktır.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Talep ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Talep Başlığı :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>Açıklama :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    Aşağıdaki düğmeyi kullanarak bu talebin güncellemelerini takip edebilir veya istediğiniz zaman yanıt verebilirsiniz.
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    Talebi Görüntüle
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    Daha fazla yardıma ihtiyacınız olursa uygulama üzerinden bizimle iletişime geçebilirsiniz.
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                    'zh' => '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;padding:30px;">

                    <div style="max-width:620px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

                    <div style="background:#4f46e5;color:#ffffff;padding:18px 25px;font-size:18px;font-weight:bold;">
                    {app_name} • 支持工单通知
                    </div>

                    <div style="padding:25px;">

                    <p style="font-size:15px;color:#333;margin-bottom:10px;">
                    您好，
                    </p>

                    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">
                    您的支持请求已成功被 <strong>{company_name}</strong> 接收。  
                    我们的团队将会检查该问题，并尽快与您联系。
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:20px;">

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>工单 ID :</strong> {ticket_id}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>工单标题 :</strong> {ticket_name}
                    </p>

                    <p style="margin:6px 0;font-size:14px;color:#444;">
                    <strong>描述 :</strong> {ticket_description}
                    </p>

                    </div>

                    <p style="font-size:14px;color:#555;margin-bottom:15px;">
                    您可以通过下面的按钮随时查看更新或回复该工单。
                    </p>

                    <div style="text-align:center;margin:25px 0;">
                    <a href="{ticket_url}" style="background:#4f46e5;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;">
                    查看工单
                    </a>
                    </div>

                    <p style="font-size:14px;color:#555;line-height:1.6;">
                    如果您需要进一步的帮助，请随时通过应用程序与我们联系。
                    </p>

                    </div>

                    <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:15px 25px;font-size:13px;color:#777;text-align:center;">
                    {company_name} • <a href="{app_url}" style="color:#4f46e5;text-decoration:none;">{app_name}</a>
                    </div>

                    </div>

                    </div>',
                ],
            ],
        ];

        foreach($emailTemplate as $eTemp)
        {
            $table = EmailTemplate::where('name',$eTemp)->where('module_name','general')->exists();
            if(!$table)
            {
                $emailtemplate=  EmailTemplate::create(
                    [
                        'name' => $eTemp,
                        'from' =>  !empty(env('APP_NAME')) ? env('APP_NAME') : 'WorkDo Dash',
                        'module_name' => 'general',
                        'created_by' => $admin->id,
                        'creator_id' => $admin->id,
                        ]
                    );
                    foreach($defaultTemplate[$eTemp]['lang'] as $lang => $content)
                    {
                        EmailTemplateLang::create(
                            [
                                'parent_id' => $emailtemplate->id,
                                'lang' => $lang,
                                'subject' => $defaultTemplate[$eTemp]['subject'],
                                'variables' => $defaultTemplate[$eTemp]['variables'],
                                'content' => $content,
                            ]
                        );
                    }
            }
        }

    }
}
