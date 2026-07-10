<?php

namespace App\Livewire\Platform;

use Livewire\Component;
use App\Models\PlatformSetting;

class Settings extends Component
{
    public string $platformName = 'ADA Co-OS';
    public string $supportEmail = '';
    public string $defaultCurrency = 'TRY';
    public int $defaultTrialDays = 14;
    public bool $registrationEnabled = true;

    // Desktop version management
    public string $desktopLatestVersion = '1.0.0';
    public string $desktopDownloadUrl = '';
    public string $desktopReleaseNotes = '';
    public bool $desktopUpdateMandatory = false;

    // Module Toggles
    public bool $cat_settings = true;
    public bool $mod_clients = true;
    public bool $mod_departments = true;

    public bool $cat_project = true;
    public bool $mod_projects = true;
    public bool $mod_tasks = true;
    public bool $mod_kanban = true;
    public bool $mod_timesheet = true;
    public bool $mod_gantt = true;

    public bool $cat_marketing = true;
    public bool $mod_campaigns = true;
    public bool $mod_content = true;
    public bool $mod_social = true;

    public bool $cat_media = true;
    public bool $mod_media = true;
    public bool $mod_events = true;
    public bool $mod_press = true;
    public bool $mod_calendar = true;

    public bool $cat_internal = true;
    public bool $mod_inbox = true;
    public bool $mod_tools = true;
    public bool $mod_brand = true;
    public bool $mod_emails = true;
    public bool $mod_email_acc = true;

    public bool $cat_system = true;
    public bool $mod_automations = true;
    public bool $mod_integrations = true;
    public bool $mod_proposal = true;
    public bool $mod_team = true;
    public bool $mod_2fa = true;
    public bool $mod_audit = true;

    public function mount()
    {
        $this->supportEmail = config('mail.from.address', 'admin@adacreative.co');

        // Load desktop settings from DB
        $this->desktopLatestVersion = PlatformSetting::get('desktop_latest_version', '1.0.0');
        $this->desktopDownloadUrl = PlatformSetting::get('desktop_download_url', '');
        $this->desktopReleaseNotes = PlatformSetting::get('desktop_release_notes', '');
        $this->desktopUpdateMandatory = (bool) PlatformSetting::get('desktop_update_mandatory', false);

        // Load module settings
        $fields = [
            'cat_settings', 'mod_clients', 'mod_departments',
            'cat_project', 'mod_projects', 'mod_tasks', 'mod_kanban', 'mod_timesheet', 'mod_gantt',
            'cat_marketing', 'mod_campaigns', 'mod_content', 'mod_social',
            'cat_media', 'mod_media', 'mod_events', 'mod_press', 'mod_calendar',
            'cat_internal', 'mod_inbox', 'mod_tools', 'mod_brand', 'mod_emails', 'mod_email_acc',
            'cat_system', 'mod_automations', 'mod_integrations', 'mod_proposal', 'mod_team', 'mod_2fa', 'mod_audit'
        ];
        foreach ($fields as $field) {
            $this->$field = (bool) PlatformSetting::get($field, true);
        }
    }

    public function save()
    {
        // Save desktop version settings
        PlatformSetting::set('desktop_latest_version', $this->desktopLatestVersion);
        PlatformSetting::set('desktop_download_url', $this->desktopDownloadUrl);
        PlatformSetting::set('desktop_release_notes', $this->desktopReleaseNotes);
        PlatformSetting::set('desktop_update_mandatory', $this->desktopUpdateMandatory ? '1' : '0');
        PlatformSetting::set('desktop_released_at', now()->toIso8601String());

        // Save module settings
        $fields = [
            'cat_settings', 'mod_clients', 'mod_departments',
            'cat_project', 'mod_projects', 'mod_tasks', 'mod_kanban', 'mod_timesheet', 'mod_gantt',
            'cat_marketing', 'mod_campaigns', 'mod_content', 'mod_social',
            'cat_media', 'mod_media', 'mod_events', 'mod_press', 'mod_calendar',
            'cat_internal', 'mod_inbox', 'mod_tools', 'mod_brand', 'mod_emails', 'mod_email_acc',
            'cat_system', 'mod_automations', 'mod_integrations', 'mod_proposal', 'mod_team', 'mod_2fa', 'mod_audit'
        ];
        foreach ($fields as $field) {
            PlatformSetting::set($field, $this->$field ? '1' : '0');
        }

        session()->flash('message', 'Platform ayarları kaydedildi.');
    }

    public function render()
    {
        return view('livewire.platform.settings')
            ->layout('layouts.platform', [
                'title' => 'Ayarlar',
                'breadcrumb' => 'Ayarlar',
            ]);
    }
}
