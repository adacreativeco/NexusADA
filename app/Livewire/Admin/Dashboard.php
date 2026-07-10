<?php

namespace App\Livewire\Admin;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\Activity;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $activeTimerId = null;
    public $selectedTaskIdForTimer = '';
    public string $dailyBriefingText = '';

    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function mount()
    {
        // Daily briefing is deferred to wire:init for instant initial page loading performance
    }

    public function loadDailyBriefing(bool $force = false)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $this->dailyBriefingText = \App\Services\DailyBriefingService::getBriefing($tenantId, $force);
    }

    public function completeTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['status' => 'done']);
            session()->flash('message', __('Görev başarıyla tamamlandı.'));

            \App\Services\ActivityService::logUser(
                __('Görev Tamamlandı'),
                __(':title görevi eylem merkezinden tamamlandı.', ['title' => $task->title]),
                $task
            );
        }
    }

    public function startTimer()
    {
        if (!$this->selectedTaskIdForTimer) {
            session()->flash('error', __('Lütfen bir görev seçin.'));
            return;
        }

        $task = Task::find($this->selectedTaskIdForTimer);
        if ($task) {
            // Stop any running timers for current user first
            $runningTimers = TimeLog::running()->where('user_id', Auth::id())->get();
            foreach ($runningTimers as $timer) {
                $timer->stop();
            }

            $log = TimeLog::create([
                'tenant_id' => Auth::user()->tenant_id,
                'user_id' => Auth::id(),
                'task_id' => $task->id,
                'project_id' => $task->project_id,
                'started_at' => now(),
            ]);

            \App\Services\ActivityService::logUser(
                __('Zamanlayıcı Başlatıldı'),
                __(':title görevi için zaman kaydı başlatıldı.', ['title' => $task->title]),
                $log
            );

            $this->selectedTaskIdForTimer = '';
            session()->flash('message', __('Zamanlayıcı başarıyla başlatıldı.'));
        }
    }

    public function stopTimer($timerId)
    {
        $timer = TimeLog::find($timerId);
        if ($timer) {
            $timer->stop();

            \App\Services\ActivityService::logUser(
                __('Zamanlayıcı Durduruldu'),
                __(':title görevi için zaman kaydı tamamlandı. Toplam süre: :duration.', [
                    'title' => $timer->task?->title ?? '-',
                    'duration' => $timer->duration_formatted
                ]),
                $timer
            );

            session()->flash('message', __('Zamanlayıcı durduruldu.'));
        }
    }

    public function approveProposal($proposalId)
    {
        $proposal = \App\Models\Proposal::find($proposalId);
        if ($proposal) {
            $proposal->approve(auth()->user(), __('Eylem merkezinden onaylandı.'));
            session()->flash('message', __('Teklif onaylandı.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function rejectProposal($proposalId)
    {
        $proposal = \App\Models\Proposal::find($proposalId);
        if ($proposal) {
            $proposal->reject(auth()->user(), __('Eylem merkezinden reddedildi.'));
            session()->flash('message', __('Teklif reddedildi.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function approveContract($contractId)
    {
        $contract = \App\Models\Contract::find($contractId);
        if ($contract) {
            $contract->approve(auth()->user(), __('Eylem merkezinden onaylandı.'));
            session()->flash('message', __('Sözleşme onaylandı.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function rejectContract($contractId)
    {
        $contract = \App\Models\Contract::find($contractId);
        if ($contract) {
            $contract->reject(auth()->user(), __('Eylem merkezinden reddedildi.'));
            session()->flash('message', __('Sözleşme reddedildi.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function approveExpense($expenseId)
    {
        $expense = \App\Models\Expense::find($expenseId);
        if ($expense) {
            $expense->approve(auth()->user(), __('Eylem merkezinden onaylandı.'));
            session()->flash('message', __('Gider onaylandı.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function rejectExpense($expenseId)
    {
        $expense = \App\Models\Expense::find($expenseId);
        if ($expense) {
            $expense->reject(auth()->user(), __('Eylem merkezinden reddedildi.'));
            session()->flash('message', __('Gider reddedildi.'));
            $this->dispatch('refreshDashboard');
        }
    }

    public function render()
    {
        // Overdue Tasks
        $overdueTasks = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Today's Events / Meetings
        $todayMeetings = Event::whereDate('start_date', today())
            ->orderBy('start_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->title,
                'time' => Carbon::parse($e->start_date)->format('H:i'),
                'notes' => $e->notes,
            ]);

        // Active user timer
        $activeTimer = TimeLog::running()
            ->where('user_id', Auth::id())
            ->with(['task', 'project'])
            ->first();

        // Tasks available to start a timer (assigned to user or active projects)
        $availableTasksForTimer = Task::whereIn('status', ['todo', 'in_progress'])
            ->limit(20)
            ->get();

        // Pending approvals list
        $pendingProposals = \App\Models\Proposal::with('client')
            ->where('status', 'pending_approval')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'type' => 'proposal',
                'title' => $p->title,
                'desc' => $p->proposal_number . ' · ' . ($p->client?->name ?? 'Müşterisiz'),
                'amount' => $p->grand_total,
            ]);

        $pendingContracts = \App\Models\Contract::with('client')
            ->where('status', 'pending_approval')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'type' => 'contract',
                'title' => $c->title,
                'desc' => $c->contract_number . ' · ' . ($c->client?->name ?? 'Müşterisiz'),
                'amount' => $c->value,
            ]);

        $pendingExpenses = \App\Models\Expense::where('status', 'pending_approval')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => 'expense',
                'title' => $e->title,
                'desc' => $e->expense_number . ' · ' . $e->vendor,
                'amount' => $e->grand_total,
            ]);

        $pendingApprovals = collect()
            ->merge($pendingProposals)
            ->merge($pendingContracts)
            ->merge($pendingExpenses)
            ->toArray();

        // Recommendations (live dashboard alerts)
        $recommendations = [];
        if ($overdueTasks->count() > 0) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => __('Gecikmiş Görevler'),
                'text' => __('Şu anda gecikmiş :count göreviniz bulunuyor. Bunları tamamlamanızı veya tarihlerini güncellemenizi öneririz.', ['count' => $overdueTasks->count()]),
            ];
        }
        if (!$activeTimer) {
            $recommendations[] = [
                'type' => 'info',
                'title' => __('Zaman Takibi'),
                'text' => __('Şu an aktif bir zamanlayıcınız yok. Çalışmalarınızı takip etmek için zamanlayıcıyı başlatabilirsiniz.'),
            ];
        }

        // Financial metrics
        $totalBalance = \App\Models\BankAccount::sum('balance');
        $thisMonthIncomes = \App\Models\Income::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total');
        $thisMonthExpenses = \App\Models\Expense::where('status', '!=', 'rejected_internal')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total');
        $burnRate = $thisMonthIncomes > 0 ? round(($thisMonthExpenses / $thisMonthIncomes) * 100, 1) : 0.0;

        // SVG Gauge stats
        $avgProfit = round(Project::avg('profitability_score') ?? 0);
        $thisMonthRevenue = Project::where('created_at', '>=', now()->startOfMonth())->sum('actual_revenue');
        $monthlyTarget = 50000; // Mock target, dynamically computed
        $targetProgress = $monthlyTarget > 0 ? min(round(($thisMonthRevenue / $monthlyTarget) * 100), 100) : 0;

        // Budget Health
        $budgetHealth = Project::whereNotNull('planned_hours')
            ->where('planned_hours', '>', 0)
            ->where('status', '!=', 'completed')
            ->get()
            ->map(fn($p) => [
                'name' => $p->title,
                'burn_rate' => round($p->budgetBurnRate()),
                'over_budget' => $p->isOverBudget(),
            ])
            ->sortByDesc('burn_rate')
            ->take(3)
            ->values()
            ->toArray();

        // Unified Activity Feed
        $recentActivity = Activity::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'user' => $a->user?->name ?? 'Sistem',
                'type' => $a->activity_type, // user, ai, automation, system
                'title' => $a->title,
                'desc' => $a->description,
                'time' => $a->created_at->diffForHumans(),
            ])
            ->toArray();

        return view('livewire.admin.dashboard', [
            'overdueTasks' => $overdueTasks,
            'todayMeetings' => $todayMeetings,
            'activeTimer' => $activeTimer,
            'availableTasksForTimer' => $availableTasksForTimer,
            'pendingApprovals' => $pendingApprovals,
            'recommendations' => $recommendations,
            'avgProfit' => $avgProfit,
            'targetProgress' => $targetProgress,
            'thisMonthRevenue' => $thisMonthRevenue,
            'budgetHealth' => $budgetHealth,
            'recentActivity' => $recentActivity,
            'totalBalance' => $totalBalance,
            'thisMonthIncomes' => $thisMonthIncomes,
            'thisMonthExpenses' => $thisMonthExpenses,
            'burnRate' => $burnRate,
        ])->layout('layouts.admin', [
            'title' => 'Home',
            'breadcrumb' => 'Home',
        ]);
    }
}
