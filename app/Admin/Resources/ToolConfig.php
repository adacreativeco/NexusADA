<?php
namespace App\Admin\Resources;
use App\Models\Tool;

class ToolConfig
{
    public static function config(): array
    {
        return [
            'model' => Tool::class,
            'title' => 'Araçlar',
            'columns' => [
                ['key' => 'name', 'label' => 'Araç', 'searchable' => true, 'sortable' => true],
                ['key' => 'category', 'label' => 'Kategori', 'badge' => true, 'badge_color' => 'info', 'searchable' => true],
                ['key' => 'url', 'label' => 'URL'],
                ['key' => 'cost', 'label' => 'Maliyet', 'money' => true, 'prefix' => '₺', 'sortable' => true],
            ],
            'sections' => [
                ['title' => 'Araç Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Araç Adı', 'required' => true],
                    ['key' => 'category', 'type' => 'select', 'label' => 'Kategori', 'options' => [
                        'design' => 'Tasarım', 'marketing' => 'Pazarlama', 'analytics' => 'Analitik',
                        'project_management' => 'Proje Yönetimi', 'communication' => 'İletişim', 'other' => 'Diğer',
                    ]],
                    ['key' => 'url', 'type' => 'text', 'label' => 'URL', 'placeholder' => 'https://...'],
                    ['key' => 'cost', 'type' => 'number', 'label' => 'Aylık Maliyet', 'prefix' => '₺'],
                    ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama', 'span' => 'full'],
                ]],
            ],
        ];
    }
}
