<?php

namespace App\Controllers\Admin;

class SettingsController extends \Controller {
    public function __construct() {
        parent::__construct();
    }

    /** GET /admin/settings */
    public function index(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $schema = $this->schema();
        $values = [];
        foreach ($schema as $group) {
            foreach ($group['fields'] as $key => $field) {
                $values[$key] = \Settings::get($key, $field['default'] ?? '');
            }
        }

        $this->view('admin.settings.index', [
            'title' => 'Site Settings',
            'page_title' => 'Site Settings',
            'schema' => $schema,
            'values' => $values,
        ], 'admin');
    }

    /** POST /admin/settings */
    public function save(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $this->verifyCsrf();

        $settings = new \App\Models\SettingsModel();
        $saved = 0;
        foreach ($this->schema() as $groupKey => $group) {
            foreach ($group['fields'] as $key => $field) {
                $type = $field['type'] ?? 'text';
                if ($type === 'checkbox') {
                    $value = $this->input($key) ? '1' : '0';
                } else {
                    $value = trim((string) $this->input($key, ''));
                    if ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->setFlash('error', $field['label'] . ' must be a valid email address.');
                        $this->redirect(BASE_URL . '/admin/settings');
                    }
                }
                $settings->set($key, $value, $groupKey);
                $saved++;
            }
        }

        \App\Models\SettingsModel::clearCache();
        \Audit::log('update', 'settings', 'Updated ' . $saved . ' site settings');
        $this->setFlash('success', 'Settings saved successfully.');
        $this->redirect(BASE_URL . '/admin/settings');
    }

    /**
     * Field schema keyed by setting group. Drives both render and save.
     */
    private function schema(): array {
        return [
            'general' => [
                'title' => 'General',
                'icon' => 'fa-cog',
                'fields' => [
                    'site_name' => ['label' => 'Church Name', 'default' => SITE_NAME],
                    'site_tagline' => ['label' => 'Tagline', 'default' => SITE_TAGLINE],
                    'church_address' => ['label' => 'Address', 'type' => 'textarea', 'default' => CHURCH_ADDRESS],
                    'church_phone' => ['label' => 'Phone', 'default' => CHURCH_PHONE],
                    'church_email' => ['label' => 'Email', 'type' => 'email', 'default' => CHURCH_EMAIL],
                    'social_facebook' => ['label' => 'Facebook URL'],
                    'social_instagram' => ['label' => 'Instagram URL'],
                    'social_youtube' => ['label' => 'YouTube URL'],
                ],
            ],
            'services' => [
                'title' => 'Service Times',
                'icon' => 'fa-clock',
                'fields' => [
                    'service_sunday_first' => ['label' => 'Sunday (First Service)', 'default' => SERVICE_SUNDAY_FIRST],
                    'service_sunday_second' => ['label' => 'Sunday (Second Service)', 'default' => SERVICE_SUNDAY_SECOND],
                    'service_wednesday' => ['label' => 'Wednesday', 'default' => SERVICE_WEDNESDAY],
                    'service_friday' => ['label' => 'Friday', 'default' => SERVICE_FRIDAY],
                ],
            ],
            'giving' => [
                'title' => 'Giving',
                'icon' => 'fa-hand-holding-heart',
                'fields' => [
                    'giving_enabled' => ['label' => 'Enable online giving', 'type' => 'checkbox', 'default' => '1'],
                    'paystack_public_key' => ['label' => 'Paystack Public Key', 'default' => PAYSTACK_PUBLIC_KEY],
                    'bank_name' => ['label' => 'Bank Name'],
                    'bank_account_name' => ['label' => 'Account Name'],
                    'bank_account_number' => ['label' => 'Account Number'],
                ],
            ],
            'livestream' => [
                'title' => 'Live Stream',
                'icon' => 'fa-video',
                'fields' => [
                    'livestream_url' => ['label' => 'YouTube Stream/Embed URL'],
                    'livestream_channel_id' => ['label' => 'YouTube Channel ID'],
                    'livestream_is_live' => ['label' => 'Stream is currently live', 'type' => 'checkbox', 'default' => '0'],
                    'livestream_offline_message' => ['label' => 'Offline Message', 'type' => 'textarea'],
                ],
            ],
            'email' => [
                'title' => 'Email (SMTP)',
                'icon' => 'fa-envelope',
                'fields' => [
                    'smtp_host' => ['label' => 'SMTP Host'],
                    'smtp_port' => ['label' => 'SMTP Port', 'default' => '587'],
                    'smtp_username' => ['label' => 'SMTP Username'],
                    'smtp_from_name' => ['label' => 'From Name', 'default' => MAIL_FROM_NAME],
                    'smtp_from_email' => ['label' => 'From Email', 'type' => 'email', 'default' => MAIL_FROM_EMAIL],
                ],
            ],
            'seo' => [
                'title' => 'SEO',
                'icon' => 'fa-magnifying-glass',
                'fields' => [
                    'seo_meta_title' => ['label' => 'Default Meta Title'],
                    'seo_meta_description' => ['label' => 'Default Meta Description', 'type' => 'textarea'],
                    'google_analytics_id' => ['label' => 'Google Analytics ID'],
                ],
            ],
            'maintenance' => [
                'title' => 'Maintenance',
                'icon' => 'fa-screwdriver-wrench',
                'fields' => [
                    'maintenance_mode' => ['label' => 'Take site offline (maintenance mode)', 'type' => 'checkbox', 'default' => '0'],
                    'maintenance_message' => ['label' => 'Maintenance Message', 'type' => 'textarea', 'default' => 'We are currently performing maintenance. Please check back soon.'],
                ],
            ],
        ];
    }
}
