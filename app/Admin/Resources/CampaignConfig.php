<?php
namespace App\Admin\Resources;
use App\Models\Campaign;
use App\Models\Department;

class CampaignConfig
{
    public static function config(): array
    {
        return [
            'model' => Campaign::class,
            'title' => 'Kampanyalar',
            'subtitle' => 'Pazarlama ve PR çalışmaları',
            'columns' => [
                ['key' => 'title', 'label' => 'Başlık', 'searchable' => true, 'sortable' => true],
                ['key' => 'goal', 'label' => 'Hedef', 'searchable' => true],
                ['key' => 'start_date', 'label' => 'Başlangıç', 'date' => true, 'sortable' => true],
                ['key' => 'end_date', 'label' => 'Bitiş', 'date' => true, 'sortable' => true],
                ['key' => 'budget', 'label' => 'Bütçe', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'active' => 'success', 'draft' => 'gray', 'completed' => 'info', 'cancelled' => 'danger',
                ]],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'active' => 'Aktif', 'draft' => 'Taslak', 'completed' => 'Tamamlandı', 'cancelled' => 'İptal',
                ]],
            ],
            'sections' => [
                ['title' => 'Kampanya Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'title', 'type' => 'text', 'label' => 'Başlık', 'required' => true],
                    ['key' => 'department_id', 'type' => 'belongs_to', 'label' => 'Departman', 'required' => true, 'model' => Department::class, 'display' => 'name'],
                    ['key' => 'goal', 'type' => 'text', 'label' => 'Hedef'],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'options' => [
                        'draft' => 'Taslak', 'active' => 'Aktif', 'completed' => 'Tamamlandı', 'cancelled' => 'İptal',
                    ]],
                    ['key' => 'start_date', 'type' => 'date', 'label' => 'Başlangıç Tarihi'],
                    ['key' => 'end_date', 'type' => 'date', 'label' => 'Bitiş Tarihi'],
                    ['key' => 'budget', 'type' => 'number', 'label' => 'Bütçe', 'prefix' => '₺', 'default' => 0],
                    ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama', 'span' => 'full'],
                ]],
            ],

            'row_actions' => [
                ['key' => 'pdf', 'label' => 'PDF', 'icon' => 'document-arrow-down', 'url' => '/admin/reports/campaign/{id}', 'target' => '_blank'],
            ],
        ];
    }
}
