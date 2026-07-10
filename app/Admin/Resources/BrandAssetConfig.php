<?php
namespace App\Admin\Resources;
use App\Models\BrandAsset;

class BrandAssetConfig
{
    public static function config(): array
    {
        return [
            'model' => BrandAsset::class,
            'title' => 'Marka Varlıkları',
            'columns' => [
                ['key' => 'name', 'label' => 'Ad', 'searchable' => true, 'sortable' => true],
                ['key' => 'type', 'label' => 'Tip', 'badge' => true, 'badge_color' => 'info'],
                ['key' => 'version', 'label' => 'Versiyon'],
                ['key' => 'created_at', 'label' => 'Tarih', 'date' => true, 'sortable' => true],
            ],
            'sections' => [
                ['title' => 'Varlık Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Varlık Adı', 'required' => true],
                    ['key' => 'type', 'type' => 'select', 'label' => 'Tip', 'options' => [
                        'logo' => 'Logo', 'icon' => 'İkon', 'font' => 'Font', 'color_palette' => 'Renk Paleti',
                        'template' => 'Şablon', 'guideline' => 'Marka Kılavuzu',
                    ]],
                    ['key' => 'version', 'type' => 'text', 'label' => 'Versiyon', 'placeholder' => 'v1.0'],
                    ['key' => 'files', 'type' => 'file', 'label' => 'Dosya / Logo Yükle', 'span' => 2],
                    ['key' => 'notes', 'type' => 'textarea', 'label' => 'Notlar', 'span' => 'full'],
                ]],
            ],
        ];
    }
}
