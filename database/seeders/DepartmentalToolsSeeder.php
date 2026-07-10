<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;
use App\Models\PressContact;

class DepartmentalToolsSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            ['name' => 'Canva', 'url' => 'https://www.canva.com', 'icon' => 'heroicon-o-paint-brush', 'category' => 'Tasarım'],
            ['name' => 'Adobe Creative Cloud', 'url' => 'https://www.adobe.com', 'icon' => 'heroicon-o-swatch', 'category' => 'Tasarım'],
            ['name' => 'CapCut', 'url' => 'https://www.capcut.com', 'icon' => 'heroicon-o-video-camera', 'category' => 'Video'],
            ['name' => 'Meta Business Suite', 'url' => 'https://business.facebook.com', 'icon' => 'heroicon-o-users', 'category' => 'Sosyal Medya'],
            ['name' => 'LinkedIn Campaign Manager', 'url' => 'https://www.linkedin.com/ad-bundle/ads', 'icon' => 'heroicon-o-briefcase', 'category' => 'Pazarlama'],
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(['name' => $tool['name']], $tool);
        }

        PressContact::updateOrCreate(['email' => 'editor@medyahaber.com'], [
            'name' => 'Ahmet Yılmaz',
            'media_house' => 'Medya Haber Grubu',
            'category' => 'Gazeteci',
            'phone' => '0555 111 2233',
        ]);

        \App\Models\IncomingEmail::updateOrCreate(['subject' => 'Yeni Basın Bülteni Talebi'], [
            'from_address' => 'basin@ajans.com',
            'from_name' => 'Ajans İletişim',
            'body' => 'Merhaba, Mart ayı lansmanınız için bir basın bülteni hazırlamayı düşünüyoruz. Detayları paylaşabilir misiniz?',
            'status' => 'unread',
            'received_at' => now()->subHours(2),
        ]);

        \App\Models\IncomingEmail::updateOrCreate(['subject' => 'Röportaj Teklifi'], [
            'from_address' => 'editor@ekonomidergisi.com',
            'from_name' => 'Ekonomi Dergisi',
            'body' => 'CEO\'nuz ile sektörün geleceği üzerine bir röportaj yapmak istiyoruz.',
            'status' => 'read',
            'received_at' => now()->subDays(1),
        ]);
    }
}
