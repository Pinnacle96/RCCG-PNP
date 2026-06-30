<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('This command must be run from the command line.');
}

$settings = new \App\Models\SettingsModel();

$defaults = [
    'general' => [
        'site_name' => SITE_NAME,
        'site_tagline' => SITE_TAGLINE,
        'church_address' => CHURCH_ADDRESS,
        'church_phone' => CHURCH_PHONE,
        'church_email' => CHURCH_EMAIL,
    ],
    'services' => [
        'service_sunday_first' => SERVICE_SUNDAY_FIRST,
        'service_sunday_second' => SERVICE_SUNDAY_SECOND,
        'service_wednesday' => SERVICE_WEDNESDAY,
        'service_friday' => SERVICE_FRIDAY,
    ],
    'giving' => [
        'giving_enabled' => '1',
        'paystack_public_key' => PAYSTACK_PUBLIC_KEY,
        'bank_name' => 'Zenith Bank',
        'bank_account_name' => 'RCCG PRINCE AND PRINCESS ESA-OKE',
        'bank_account_number' => '1016193636',
    ],
    'integrations' => [
        'recaptcha_site_key' => RECAPTCHA_SITE_KEY,
    ],
];

foreach ($defaults as $group => $items) {
    foreach ($items as $key => $value) {
        if ($settings->get($key) === null) {
            $settings->set($key, $value, $group);
            fwrite(STDOUT, 'Seeded setting: ' . $key . PHP_EOL);
        }
    }
}

fwrite(STDOUT, 'Settings seed complete.' . PHP_EOL);
