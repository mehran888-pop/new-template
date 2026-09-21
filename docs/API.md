# مرجع API و هوک‌ها (برای توسعه‌دهندگان)

## توابع کمکی قالب (neomorph)

```php
neomorph_option( $key, $default );   // خواندن تنظیمات قالب
neomorph_price( $amount );           // قالب‌بندی مبلغ + واحد پول
neomorph_panel_url( $view );         // آدرس پنل ('login'|'register')
neomorph_badge( $text, $type );      // بج نئومورف
neomorph_surface( $class, $tag );    // باز/بسته سطح نئو
```

## توابع کمکی هسته (nmc)

```php
nmc_setting( $key, $default );       // تنظیمات اتصال‌ها
nmc_price( $amount );
nmc_normalize_phone( $phone );       // → 09xxxxxxxxx
nmc_is_valid_phone( $phone );
nmc_user_phone( $user_id );
nmc_user_id_by_phone( $phone );
nmc_generate_token( $length );
nmc_get_meta( $post_id, $key );      // متای _nmc_*
nmc_update_meta( $post_id, $key, $value );
nmc_do_event( $event, $ctx );        // شلیک رویداد اتوماسیون
```

## کلاس‌های کلیدی

```php
\NeomorphCore\Finance\Invoice::create( $user_id, $items, $args );
\NeomorphCore\Finance\Invoice::payment_url( $invoice_id );
\NeomorphCore\Finance\Invoice::mark_paid( $invoice_id, $gateway, $ref );

\NeomorphCore\Loyalty\LoyaltyEngine::add_points( $user_id, $points, 'earn', $reason );
\NeomorphCore\Loyalty\LoyaltyEngine::balance( $user_id );
\NeomorphCore\Loyalty\LoyaltyEngine::tier_of( $user_id );

\NeomorphCore\Auth\OtpController::send_code( $phone );
\NeomorphCore\Auth\OtpController::verify( $phone, $code, 'login', $extra );

\NeomorphCore\Integrations\Notifier::send( 'sms|bale|telegram|admin|email', $message, $to );
\NeomorphCore\Recruitment\ApplicationController::score( $job_id, $text );
```

## رویدادهای اتوماسیون (nmc_event_{name})

| رویداد | context |
|--------|---------|
| `user_registered_otp` | user_id |
| `contact_created` / `contact_updated` | user_id |
| `invoice_created` / `invoice_sent` | post_id, user_id, amount |
| `invoice_paid` | post_id, user_id, amount, ref |
| `ticket_created` | post_id, user_id |
| `case_stage_changed` | post_id, stage, old_stage, user_id |
| `apply_received` | post_id, score, job_id |
| `points_changed` | user_id, points, balance |
| `loyalty_threshold` | user_id, balance |
| `consult_requested` | post_id, user_id |

عمومی: `do_action( 'nmc_event', $event_name, $ctx )`

## فیلترها

| فیلتر | کاربرد |
|-------|--------|
| `nmc_sms_provider` | جایگزینی سامانه پیامکی (challenge: شیء Provider) |
| `nmc_payment_gateways` | افزودن درگاه پرداخت دلخواه |
| `nmc_loyalty_tiers` / `nmc_loyalty_rewards` | سطوح و جوایز باشگاه |
| `nmc_crm_stages` | مراحل pipeline |
| `nmc_apply_steps` | مراحل ویزارد استخدام |
| `nmc_hiring_stages` | مراحل فرایند استخدام |
| `nmc_auto_video_threshold` | آستانه ورود خودکار به مصاحبه ویدیویی (پیش‌فرض ۷۰) |
| `nmc_notification_sent` (action) | لاگ/میرور همه اعلان‌های خروجی |

## REST / AJAX

| endpoint | پارامترها | خروجی |
|----------|-----------|-------|
| `admin-ajax.php?action=nmc_otp_send` | phone, nonce | {success, message} |
| `admin-ajax.php?action=nmc_otp_verify` | phone, code, mode, name, email | {success, redirect} |
| `admin-ajax.php?action=nmc_ticket_reply` | ticket_id, message | {success} |
| `admin-ajax.php?action=nmc_redeem_reward` | reward (index) | {success, balance} |

## نمونه: افزودن درگاه پرداخت سفارشی

```php
add_filter( 'nmc_payment_gateways', function ( $gateways ) {
    $gateways[] = new \MyPlugin\MyGateway(); // extends AbstractGateway
    return $gateways;
} );
```

## نمونه: ارسال پیامک با سامانه سفارشی

```php
add_filter( 'nmc_sms_provider', function () {
    return new \MyPlugin\SmsProvider(); // implements Provider
} );
```
