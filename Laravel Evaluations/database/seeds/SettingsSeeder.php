<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{

    public function run()
    {
        Setting::create(['key' => 'audio_file_path', 'value' => 'sample.mp3']);
        Setting::create(['key' => 'welcome', 'value' => 'Welcome']);
        Setting::create(['key' => 'welcome_audio', 'value' => 'Welcome audio']);
        Setting::create(['key' => 'instructions', 'value' => 'instructions']);
        Setting::create(['key' => 'audio_instruction', 'value' => 'audio_instructions']);
        Setting::create(['key' => 'language_audit_manager_email', 'value' => 'eucom@support.ro']);
        Setting::create(['key' => 'eucom_email', 'value' => 'eucom@support.ro']);
        Setting::create(['key' => 'test_task_canceled_title', 'value' => '']);
        Setting::create(['key' => 'test_task_canceled_text', 'value' => '']);
        Setting::create(['key' => 'test_task_expired_title', 'value' => '']);
        Setting::create(['key' => 'test_task_expired_text', 'value' => '']);
        Setting::create(['key' => 'thank_you_text', 'value' => 'thank_you_text']);
        Setting::create(['key' => 'start_test_button', 'value' => 'Start']);
    }
}