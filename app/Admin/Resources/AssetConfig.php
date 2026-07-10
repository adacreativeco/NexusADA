<?php

namespace App\Admin\Resources;

class AssetConfig
{
    public static function config(): array
    {
        $clients = \App\Models\Client::orderBy('name')->pluck('name', 'id')->toArray();

        return [
            'title' => __('Marka Varlıkları (Assets)'),
            'model' => \App\Models\Asset::class,
            'columns' => [
                [
                    'key' => 'name',
                    'label' => __('Varlık Adı'),
                    'sortable' => true,
                    'searchable' => true,
                ],
                [
                    'key' => 'type',
                    'label' => __('Tip'),
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => [
                        'logo' => 'primary',
                        'font' => 'info',
                        'color' => 'success',
                        'brandbook' => 'warning',
                        'template' => 'gray',
                        'prompt' => 'primary',
                        'file' => 'gray',
                    ],
                    'format_map' => [
                        'logo' => 'Logo Tasarımı',
                        'font' => 'Yazı Tipi / Font',
                        'color' => 'Renk Paleti (HEX)',
                        'brandbook' => 'Brand Book / Rehber',
                        'template' => 'Şablon / Taslak',
                        'prompt' => 'AI Prompt Şablonu',
                        'file' => 'Dosya / Kaynak',
                    ],
                ],
                [
                    'key' => 'client.name',
                    'label' => __('İlişkili Müşteri'),
                ],
                [
                    'key' => 'category',
                    'label' => __('Kategori'),
                    'sortable' => true,
                ],
                [
                    'key' => 'metadata',
                    'label' => __('Renkler / Detay'),
                    'color_chips' => true,
                ],
            ],
            'with' => ['client'],
            'sections' => [
                [
                    'title' => __('Varlık Detayları'),
                    'fields' => [
                        [
                            'key' => 'name',
                            'label' => __('Varlık Adı'),
                            'type' => 'text',
                            'required' => true,
                        ],
                        [
                            'key' => 'type',
                            'label' => __('Varlık Tipi'),
                            'type' => 'select',
                            'required' => true,
                            'options' => [
                                'logo' => 'Logo Tasarımı',
                                'font' => 'Yazı Tipi / Font',
                                'color' => 'Renk Paleti (HEX)',
                                'brandbook' => 'Brand Book / Rehber',
                                'template' => 'Şablon / Taslak',
                                'prompt' => 'AI Prompt Şablonu',
                                'file' => 'Dosya / Kaynak',
                            ],
                            'default' => 'logo',
                        ],
                        [
                            'key' => 'client_id',
                            'label' => __('İlişkili Müşteri'),
                            'type' => 'select',
                            'options' => ['' => 'Genel / Yok'] + $clients,
                        ],
                        [
                            'key' => 'category',
                            'label' => __('Kategori (örn: Kurumsal Kimlik, Sosyal Medya)'),
                            'type' => 'text',
                        ],
                        [
                            'key' => 'metadata',
                            'label' => __('Renk Değerleri (Renk tipi için virgülle ayırın, örn: #10b981, #3b82f6)'),
                            'type' => 'text',
                        ],
                        [
                            'key' => 'description',
                            'label' => __('Açıklama / Detaylar'),
                            'type' => 'textarea',
                            'span' => 2,
                        ],
                        [
                            'key' => 'file_path',
                            'label' => __('Dosya / Logo Yükle'),
                            'type' => 'file',
                            'span' => 2,
                        ],
                    ],
                ],
            ],
        ];
    }
}
