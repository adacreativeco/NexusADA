<?php

namespace App\Livewire\Platform;

use Livewire\Component;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Comment;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class UsageStats extends Component
{
    public function render()
    {
        return view('livewire.platform.usage-stats', [
            'activity' => $this->getActivityMetrics(),
            'content' => $this->getContentMetrics(),
            'modules' => $this->getModuleMetrics(),
            'health' => $this->getHealthMetrics(),
            'storage' => $this->getStorageMetrics(),
        ])->layout('layouts.platform');
    }

    protected function getActivityMetrics()
    {
        $last30Days = now()->subDays(30);

        // Toplam login (Audit tablosundan event='login' olanlar)
        // Eğer login event'i loglanmıyorsa, User::whereNotNull('last_login_at') gibi bir fallback de düşünülebilir
        // Ama user'ın talebi doğrultusunda varsa gösteriyoruz.
        $totalLogins = DB::table('audits')->where('event', 'login')->count();

        // DAU (Daily Active Users) - Son 30 gün
        $dauData = DB::table('audits')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(distinct user_id) as count'))
            ->where('created_at', '>=', $last30Days)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // WAU (Weekly Active Users) - Son 7 gün
        $wau = DB::table('audits')
            ->where('created_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Kullanıcı başına ortalama günlük session (login/active days)
        $totalActiveDays = $dauData->sum('count');
        $distinctUsers = DB::table('audits')->distinct('user_id')->count('user_id');
        $avgSessionsPerUser = $distinctUsers > 0 ? round($totalActiveDays / ($distinctUsers * 30), 2) : 0;

        return [
            'total_logins' => $totalLogins,
            'dau_data' => $dauData,
            'wau' => $wau,
            'avg_sessions' => $avgSessionsPerUser,
        ];
    }

    protected function getContentMetrics()
    {
        $last7Days = now()->subDays(7);
        $last30Days = now()->subDays(30);

        return [
            'totals' => [
                'clients' => Client::count(),
                'projects' => Project::count(),
                'tasks' => Task::count(),
                'campaigns' => Campaign::count(),
                'events' => Event::count(),
            ],
            'recent' => [
                'projects' => Project::where('created_at', '>=', $last7Days)->count(),
                'tasks' => Task::where('created_at', '>=', $last7Days)->count(),
                'comments' => Comment::where('created_at', '>=', $last7Days)->count(),
                'files' => Document::where('created_at', '>=', $last7Days)->count(),
            ],
            'pdf' => [
                'total' => DB::table('audits')->where('url', 'like', '%/reports/%')->count(),
                'last_30_days' => DB::table('audits')->where('url', 'like', '%/reports/%')->where('created_at', '>=', $last30Days)->count(),
            ]
        ];
    }

    protected function getModuleMetrics()
    {
        // Hit event'lerini (Middleware tarafından oluşturulan) say
        $modules = DB::table('audits')
            ->select('url', DB::raw('count(*) as hits'))
            ->where('event', 'hit')
            ->groupBy('url')
            ->orderByDesc('hits')
            ->limit(10)
            ->get();

        return [
            'top_modules' => $modules,
            'unused_count' => 0,
        ];
    }

    protected function getHealthMetrics()
    {
        $last30Days = now()->subDays(30);

        // 500 hataları (Telescope entries type='exception' veya log tablosu)
        $errors500 = 0;
        if (Schema::hasTable('telescope_entries')) {
            $errors500 = DB::table('telescope_entries')
                ->where('type', 'exception')
                ->where('created_at', '>=', $last30Days->timestamp)
                ->count();
        }

        return [
            'errors_30_days' => $errors500,
            'avg_response_time' => 'N/A', // Middleware olmadan ölçülemez
        ];
    }

    protected function getStorageMetrics()
    {
        // DB Size (MySQL için)
        $dbSize = 0;
        try {
            $dbName = config('database.connections.mysql.database');
            $res = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $dbSize = $res[0]->size ?? 0;
        } catch (\Exception $e) {
            // Fallback for non-mysql
        }

        // File Storage Size (public disk)
        $fileSize = 0;
        $path = storage_path('app/public');
        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $fileSize += $file->getSize();
            }
        }
        $fileSize = $fileSize / 1024 / 1024; // MB

        return [
            'db_size_mb' => round($dbSize, 2),
            'file_size_mb' => round($fileSize, 2),
        ];
    }
}
