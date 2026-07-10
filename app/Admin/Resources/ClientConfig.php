<?php

namespace App\Admin\Resources;

use App\Models\Client;

class ClientConfig
{
    public static function config(): array
    {
        return [
            'model' => Client::class,
            'title' => 'Müşteriler',
            'subtitle' => 'İlişki zekası ve stratejik müşteri yönetimi',
            'icon' => 'user-group',
            'has_interactions' => true,

            // ── Table Columns ───────────────────────────────────
            'columns' => [
                ['key' => 'name', 'label' => 'Firma', 'searchable' => true, 'sortable' => true, 'avatar' => true, 'sub_key' => 'industry'],
                ['key' => 'industry', 'label' => 'Sektör', 'searchable' => true, 'badge' => true, 'badge_color' => 'gray'],
                ['key' => 'decision_style', 'label' => 'Karar Tarzı', 'format_map' => [
                    'hizli' => 'Hızlı', 'analitik' => 'Analitik', 'konsensus' => 'Konsensüs', 'hiyerarsik' => 'Hiyerarşik',
                ]],
                ['key' => 'risk_level', 'label' => 'Risk', 'badge' => true, 'badge_colors' => [
                    'dusuk' => 'success', 'orta' => 'warning', 'yuksek' => 'danger',
                ], 'format_map' => [
                    'dusuk' => 'Düşük', 'orta' => 'Orta', 'yuksek' => 'Yüksek',
                ]],
                ['key' => 'strategic_importance', 'label' => 'Önem', 'numeric' => true, 'sortable' => true],
            ],

            // ── Filters ─────────────────────────────────────────
            'filters' => [
                ['key' => 'risk_level', 'label' => 'Risk Seviyesi', 'options' => [
                    'dusuk' => 'Düşük', 'orta' => 'Orta', 'yuksek' => 'Yüksek',
                ]],
            ],

            // ── Form Sections ───────────────────────────────────
            'sections' => [
                [
                    'title' => 'Kurumsal Kimlik',
                    'description' => 'Müşterinin temel tanımlayıcı bilgileri',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'name', 'type' => 'text', 'label' => 'Müşteri/Firma Adı', 'required' => true, 'max' => 255],
                        ['key' => 'industry', 'type' => 'text', 'label' => 'Sektör/Endüstri', 'max' => 255],
                        ['key' => 'email', 'type' => 'text', 'label' => 'E-posta Adresi', 'max' => 255],
                        ['key' => 'phone', 'type' => 'text', 'label' => 'Telefon Numarası', 'max' => 255],
                        ['key' => 'address', 'type' => 'textarea', 'label' => 'Açık Adres', 'span' => 'full', 'rows' => 2],
                    ],
                ],
                [
                    'title' => 'İlişki Zekası (Context)',
                    'description' => 'AI ve Strateji için bağlamsal veriler',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'decision_style', 'type' => 'select', 'label' => 'Karar Alma Tarzı', 'options' => [
                            'hizli' => 'Hızlı ve Çevik',
                            'analitik' => 'Analitik ve Detaycı',
                            'konsensus' => 'Konsensüs Odaklı',
                            'hiyerarsik' => 'Hiyerarşik / Üst Yönetim Odaklı',
                        ]],
                        ['key' => 'risk_level', 'type' => 'select', 'label' => 'Risk Seviyesi', 'options' => [
                            'dusuk' => 'Düşük (Güvenli Liman)',
                            'orta' => 'Orta (Dengeli)',
                            'yuksek' => 'Yüksek (İnovatif/Riskli)',
                        ]],
                        ['key' => 'presentation_language', 'type' => 'select', 'label' => 'Sunum/İletişim Dili', 'options' => [
                            'teknik' => 'Teknik ve Operasyonel',
                            'vizyoner' => 'Vizyoner ve Stratejik',
                            'finansal' => 'Finansal ve Verimlilik Odaklı',
                            'prestij' => 'Prestij ve Referans Odaklı',
                        ]],
                        ['key' => 'strategic_importance', 'type' => 'select', 'label' => 'Stratejik Önem', 'required' => true, 'default' => 3, 'options' => [
                            1 => 'Düşük',
                            2 => 'Normal',
                            3 => 'Önemli',
                            4 => 'Kritik',
                            5 => 'Vazgeçilmez (Partner)',
                        ]],
                        ['key' => 'behavioral_notes', 'type' => 'textarea', 'label' => 'Davranışsal Notlar', 'span' => 'full',
                         'placeholder' => 'Örn: Sürekli revizyon ister, fiyat hassasiyeti yüksektir...'],
                    ],
                ],
            ],

            'row_actions' => [
                ['key' => 'pdf', 'label' => 'PDF', 'icon' => 'document-arrow-down', 'url' => '/admin/reports/client/{id}', 'target' => '_blank'],
            ],
        ];
    }
}
