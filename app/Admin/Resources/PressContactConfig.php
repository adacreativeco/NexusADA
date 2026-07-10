<?php
namespace App\Admin\Resources;
use App\Models\PressContact;

class PressContactConfig
{
    public static function config(): array
    {
        return [
            'model' => PressContact::class,
            'title' => 'Basın İletişimi',
            'columns' => [
                ['key' => 'name', 'label' => 'Ad Soyad', 'searchable' => true, 'sortable' => true],
                ['key' => 'outlet', 'label' => 'Kuruluş', 'searchable' => true],
                ['key' => 'beat', 'label' => 'Alan', 'badge' => true, 'badge_color' => 'gray'],
                ['key' => 'email', 'label' => 'E-posta'],
                ['key' => 'phone', 'label' => 'Telefon'],
            ],
            'sections' => [
                ['title' => 'Basın İrtibat Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Ad Soyad', 'required' => true],
                    ['key' => 'outlet', 'type' => 'text', 'label' => 'Medya Kuruluşu', 'required' => true],
                    ['key' => 'beat', 'type' => 'text', 'label' => 'Alan / Beat', 'placeholder' => 'Teknoloji, Sağlık, Ekonomi...'],
                    ['key' => 'title', 'type' => 'text', 'label' => 'Unvan'],
                    ['key' => 'email', 'type' => 'text', 'label' => 'E-posta'],
                    ['key' => 'phone', 'type' => 'text', 'label' => 'Telefon'],
                    ['key' => 'notes', 'type' => 'textarea', 'label' => 'Notlar', 'span' => 'full'],
                ]],
            ],
        ];
    }
}
