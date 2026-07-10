<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Campaign;
use App\Models\Department;
use App\Models\Work;
use App\Models\Income;
use App\Models\Expense;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $tenantId = auth()->user()->tenant_id ?? 1;
        
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];
        $isShort = strlen($q) < 4;
        $isSqlite = config('database.default') === 'sqlite';
        $isTest = app()->runningUnitTests();

        // Helper search query builder using FULLTEXT with LIKE fallback
        $searchQuery = function ($modelClass, $searchField, $fulltextIndex, $queryStr) use ($isShort, $tenantId, $isSqlite, $isTest) {
            $query = $modelClass::where('tenant_id', $tenantId);
            if ($isShort || $isSqlite || $isTest) {
                return $query->where($searchField, 'like', "%{$queryStr}%");
            } else {
                return $query->whereRaw("MATCH({$searchField}) AGAINST(? IN BOOLEAN MODE)", ["*{$queryStr}*"]);
            }
        };

        // 1. Clients
        $clients = $searchQuery(Client::class, 'name', 'fulltext_client_name', $q)
            ->limit(5)->get();
        foreach ($clients as $client) {
            $results[] = [
                'label' => $client->name,
                'category' => 'Müşteri',
                'url' => route('admin.resource.index', ['resource' => 'clients']) . '?id=' . $client->id,
                'icon' => 'group'
            ];
        }

        // 2. Works (İşler)
        $works = $searchQuery(Work::class, 'title', 'fulltext_work_title', $q)
            ->limit(5)->get();
        foreach ($works as $work) {
            $results[] = [
                'label' => $work->title,
                'category' => 'İş Süreci',
                'url' => route('admin.resource.index', ['resource' => 'works']) . '?id=' . $work->id,
                'icon' => 'folder_open'
            ];
        }

        // 3. Projects
        $projects = Project::where('tenant_id', $tenantId)
            ->where('title', 'like', "%{$q}%")
            ->limit(5)->get();
        foreach ($projects as $project) {
            $results[] = [
                'label' => $project->title,
                'category' => 'Proje',
                'url' => route('admin.resource.index', ['resource' => 'projects']) . '?id=' . $project->id,
                'icon' => 'folder'
            ];
        }

        // 4. Tasks
        $tasks = Task::where('tenant_id', $tenantId);
        if ($isShort || $isSqlite || $isTest) {
            $tasks = $tasks->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            });
        } else {
            $tasks = $tasks->whereRaw("MATCH(title, description) AGAINST(? IN BOOLEAN MODE)", ["*{$q}*"]);
        }
        $tasks = $tasks->limit(5)->get();
        foreach ($tasks as $task) {
            $results[] = [
                'label' => $task->title,
                'category' => 'Görev',
                'url' => route('admin.resource.index', ['resource' => 'tasks']) . '?id=' . $task->id,
                'icon' => 'task_alt'
            ];
        }

        // 5. Incomes (Gelirler)
        $incomes = Income::where('tenant_id', $tenantId)
            ->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('income_number', 'like', "%{$q}%");
            })
            ->limit(5)->get();
        foreach ($incomes as $inc) {
            $results[] = [
                'label' => $inc->income_number . ' · ' . $inc->title,
                'category' => 'Gelir / Fatura',
                'url' => route('admin.resource.index', ['resource' => 'incomes']) . '?id=' . $inc->id,
                'icon' => 'trending_up'
            ];
        }

        // 6. Expenses (Giderler)
        $expenses = Expense::where('tenant_id', $tenantId)
            ->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('expense_number', 'like', "%{$q}%")
                    ->orWhere('vendor', 'like', "%{$q}%");
            })
            ->limit(5)->get();
        foreach ($expenses as $exp) {
            $results[] = [
                'label' => $exp->expense_number . ' · ' . $exp->vendor . ' (' . $exp->title . ')',
                'category' => 'Gider / Ödeme',
                'url' => route('admin.resource.index', ['resource' => 'expenses']) . '?id=' . $exp->id,
                'icon' => 'trending_down'
            ];
        }

        // 7. Campaigns
        $campaigns = Campaign::where('tenant_id', $tenantId)
            ->where('title', 'like', "%{$q}%")
            ->limit(5)->get();
        foreach ($campaigns as $campaign) {
            $results[] = [
                'label' => $campaign->title,
                'category' => 'Kampanya',
                'url' => route('admin.resource.index', ['resource' => 'campaigns']) . '?id=' . $campaign->id,
                'icon' => 'campaign'
            ];
        }

        // 8. Departments
        $departments = Department::where('tenant_id', $tenantId)
            ->where('name', 'like', "%{$q}%")
            ->limit(5)->get();
        foreach ($departments as $dept) {
            $results[] = [
                'label' => $dept->name,
                'category' => 'Departman',
                'url' => route('admin.resource.index', ['resource' => 'departments']) . '?id=' . $dept->id,
                'icon' => 'corporate_fare'
            ];
        }

        return response()->json($results);
    }
}
