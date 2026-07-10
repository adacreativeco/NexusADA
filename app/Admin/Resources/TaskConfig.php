<?php

namespace App\Admin\Resources;

class TaskConfig
{
    public static function config(): array
    {
        $users = \App\Models\User::orderBy('name')->pluck('name', 'id')->toArray();
        $projects = \App\Models\Project::orderBy('title')->pluck('title', 'id')->toArray();

        return [
            'label' => 'Görevler',
            'model' => \App\Models\Task::class,
            'searchable' => ['title', 'description'],
            'default_sort' => ['position', 'asc'],

            'columns' => [
                ['key' => 'title', 'label' => 'Başlık', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'sortable' => true,
                    'format_map' => ['todo' => 'Yapılacak', 'in_progress' => 'Devam', 'review' => 'İnceleme', 'done' => 'Tamamlandı'],
                    'badge_map' => ['todo' => 'gray', 'in_progress' => 'info', 'review' => 'warning', 'done' => 'success'],
                ],
                ['key' => 'priority', 'label' => 'Öncelik', 'sortable' => true,
                    'format_map' => ['low' => 'Düşük', 'medium' => 'Orta', 'high' => 'Yüksek', 'urgent' => 'Acil'],
                    'badge_map' => ['low' => 'gray', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'],
                ],
                ['key' => 'assignee.name', 'label' => 'Atanan'],
                ['key' => 'project.title', 'label' => 'Proje'],
                ['key' => 'due_date', 'label' => 'Son Tarih', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    '' => 'Tümü', 'todo' => 'Yapılacak', 'in_progress' => 'Devam', 'review' => 'İnceleme', 'done' => 'Tamamlandı',
                ]],
                ['key' => 'priority', 'label' => 'Öncelik', 'options' => [
                    '' => 'Tümü', 'low' => 'Düşük', 'medium' => 'Orta', 'high' => 'Yüksek', 'urgent' => 'Acil',
                ]],
            ],

            'with' => ['assignee', 'project'],

            'sections' => [
                [
                    'title' => 'Görev Bilgileri',
                    'fields' => [
                        ['key' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true, 'span' => 2],
                        ['key' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'span' => 2],
                        ['key' => 'status', 'label' => 'Durum', 'type' => 'select', 'default' => 'todo', 'options' => [
                            'todo' => 'Yapılacak', 'in_progress' => 'Devam Ediyor', 'review' => 'İnceleme', 'done' => 'Tamamlandı',
                        ]],
                        ['key' => 'priority', 'label' => 'Öncelik', 'type' => 'select', 'default' => 'medium', 'options' => [
                            'low' => 'Düşük', 'medium' => 'Orta', 'high' => 'Yüksek', 'urgent' => 'Acil',
                        ]],
                        ['key' => 'assigned_to', 'label' => 'Atanan Kişi', 'type' => 'select', 'options' => ['' => 'Seçiniz'] + $users],
                        ['key' => 'project_id', 'label' => 'Proje', 'type' => 'select', 'options' => ['' => 'Seçiniz'] + $projects],
                        ['key' => 'due_date', 'label' => 'Son Tarih', 'type' => 'date'],
                    ],
                ],
            ],

            'row_actions' => [],
        ];
    }
}
