<?php
namespace App\Admin\Resources;
use App\Models\Department;

class DepartmentConfig
{
    public static function config(): array
    {
        return [
            'model' => Department::class,
            'title' => 'Departmanlar',
            'columns' => [
                ['key' => 'name', 'label' => 'Departman', 'searchable' => true, 'sortable' => true],
                ['key' => 'description', 'label' => 'Açıklama', 'searchable' => true],
                ['key' => 'created_at', 'label' => 'Tarih', 'date' => true, 'sortable' => true],
            ],
            'sections' => [
                ['title' => 'Departman Bilgileri', 'columns' => 1, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Departman Adı', 'required' => true],
                    ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama'],
                ]],
            ],
        ];
    }
}
