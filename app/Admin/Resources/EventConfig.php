<?php
namespace App\Admin\Resources;
use App\Models\Event;

class EventConfig
{
    public static function config(): array
    {
        return [
            'model' => Event::class,
            'title' => 'Etkinlikler',
            'columns' => [
                ['key' => 'title', 'label' => 'Etkinlik', 'searchable' => true, 'sortable' => true],
                ['key' => 'location', 'label' => 'Konum', 'searchable' => true],
                ['key' => 'start_date', 'label' => 'Başlangıç', 'date' => true, 'sortable' => true],
                ['key' => 'end_date', 'label' => 'Bitiş', 'date' => true],
            ],
            'sections' => [
                ['title' => 'Etkinlik Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'title', 'type' => 'text', 'label' => 'Etkinlik Adı', 'required' => true],
                    ['key' => 'location', 'type' => 'text', 'label' => 'Konum'],
                    ['key' => 'start_date', 'type' => 'date', 'label' => 'Başlangıç Tarihi', 'required' => true],
                    ['key' => 'end_date', 'type' => 'date', 'label' => 'Bitiş Tarihi'],
                    ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama', 'span' => 'full'],
                ]],
            ],
        ];
    }
}
