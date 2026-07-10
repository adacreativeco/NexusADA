<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class NexusTable extends Component
{
    use WithPagination, WithFileUploads;

    public string $resource = '';
    public string $search = '';
    public string $sortField = '';
    public string $sortDirection = 'asc';
    public array $activeFilters = [];
    public int $perPage = 15;
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Delete confirmation state
    public ?int $confirmingDeleteId = null;

    // Editor state
    public bool $editorOpen = false;
    public ?int $editingId = null;
    public array $editorData = [];

    // Notes state
    public string $newNote = '';
    public array $recordNotes = [];

    // Documents state
    public $uploadFile = null;
    public string $docCategory = 'other';
    public array $recordDocuments = [];
    public array $formFiles = [];

    // Time tracking state
    public array $recordTimeLogs = [];
    public int $manualMinutes = 0;
    public string $manualTimeDescription = '';

    // Comments state
    public array $recordComments = [];
    public string $newComment = '';
    public ?int $replyingTo = null;

    // Tab state for slide-over
    public string $activeTab = 'info';

    // Interaction state (for clients)
    public array $recordInteractions = [];
    public array $recordActivities = [];
    public array $recordRelations = [];
    public string $aiResult = '';
    public bool $aiLoading = false;
    public array $interactionForm = [
        'type' => 'call',
        'subject' => '',
        'content' => '',
        'interaction_date' => '',
        'duration_minutes' => '',
        'outcome' => '',
        'follow_up_date' => '',
    ];

    public array $quickData = [];

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    // ── Resource Config Registry ────────────────────────────────
    protected function getConfigs(): array
    {
        return [
            'clients' => \App\Admin\Resources\ClientConfig::class,
            'works' => \App\Admin\Resources\WorkConfig::class,
            'proposals' => \App\Admin\Resources\ProposalConfig::class,
            'contracts' => \App\Admin\Resources\ContractConfig::class,
            'projects' => \App\Admin\Resources\ProjectConfig::class,
            'campaigns' => \App\Admin\Resources\CampaignConfig::class,
            'content-items' => \App\Admin\Resources\ContentItemConfig::class,
            'media-insights' => \App\Admin\Resources\MediaInsightConfig::class,
            'brand-assets' => \App\Admin\Resources\BrandAssetConfig::class,
            'departments' => \App\Admin\Resources\DepartmentConfig::class,
            'events' => \App\Admin\Resources\EventConfig::class,
            'emails' => \App\Admin\Resources\IncomingEmailConfig::class,
            'press-contacts' => \App\Admin\Resources\PressContactConfig::class,
            'tools' => \App\Admin\Resources\ToolConfig::class,
            'tasks' => \App\Admin\Resources\TaskConfig::class,
            'social-posts' => \App\Admin\Resources\SocialPostConfig::class,
            'incomes' => \App\Admin\Resources\IncomeConfig::class,
            'expenses' => \App\Admin\Resources\ExpenseConfig::class,
            'collections' => \App\Admin\Resources\CollectionConfig::class,
            'financial-instruments' => \App\Admin\Resources\FinancialInstrumentConfig::class,
            'bank-accounts' => \App\Admin\Resources\BankAccountConfig::class,
            'email-templates' => \App\Admin\Resources\EmailTemplateConfig::class,
            'automations' => \App\Admin\Resources\AutomationRuleConfig::class,
            'integrations' => \App\Admin\Resources\IntegrationSettingConfig::class,
            'ai-memories' => \App\Admin\Resources\AIMemoryConfig::class,
            'workflows' => \App\Admin\Resources\WorkflowConfig::class,
            'assets' => \App\Admin\Resources\AssetConfig::class,
            'knowledge-articles' => \App\Admin\Resources\KnowledgeArticleConfig::class,
        ];
    }

    public function mount(string $resource)
    {
        $this->resource = $resource;
        $config = $this->getConfig();

        // Default sort
        if (!$this->sortField && !empty($config['columns'])) {
            $sortable = collect($config['columns'])->firstWhere('sortable', true);
            $this->sortField = $sortable['key'] ?? $config['columns'][0]['key'];
        }
    }

    public function getConfig(): array
    {
        $configs = $this->getConfigs();
        $configClass = $configs[$this->resource] ?? null;

        if (!$configClass || !class_exists($configClass)) {
            abort(404, "Resource '{$this->resource}' bulunamadı.");
        }

        return $configClass::config();
    }

    // ── Sorting ─────────────────────────────────────────────────
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // ── Search ──────────────────────────────────────────────────
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ── Selection ───────────────────────────────────────────────
    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;
        $this->updatedSelectAll($this->selectAll);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->buildQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds($value)
    {
        $currentPageIds = $this->buildQuery()
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        if (count($currentPageIds) > 0 && count(array_intersect($currentPageIds, $this->selectedIds)) === count($currentPageIds)) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    // ── Bulk Delete ─────────────────────────────────────────────
    public function bulkDelete()
    {
        $config = $this->getConfig();
        $model = $config['model'];
        $count = count($this->selectedIds);

        $model::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;

        $this->dispatch('notify', type: 'success', message: $count . ' kayıt silindi.');
    }

    // ── Single Delete (with in-app confirmation) ────────────────
    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteRecord(?int $id = null)
    {
        $deleteId = $id ?? $this->confirmingDeleteId;
        if (!$deleteId) return;

        $config = $this->getConfig();
        $config['model']::findOrFail($deleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('notify', type: 'success', message: 'Kayıt başarıyla silindi.');
    }

    // ── CSV Export ───────────────────────────────────────────────
    public function exportCsv()
    {
        $config = $this->getConfig();
        $columns = $config['columns'];
        $records = $this->buildQuery()->get();
        $filename = $this->resource . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($columns, $records) {
            $handle = fopen('php://output', 'w');
            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            $headers = array_map(fn($col) => $col['label'] ?? $col['key'], $columns);
            fputcsv($handle, $headers, ';');

            // Rows
            foreach ($records as $record) {
                $row = [];
                foreach ($columns as $col) {
                    $value = data_get($record, $col['key']);
                    if ($col['money'] ?? false) {
                        $row[] = number_format($value ?? 0, 2, ',', '.');
                    } elseif ($col['date'] ?? false) {
                        $row[] = $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : '';
                    } elseif (isset($col['format_map']) && isset($col['format_map'][$value])) {
                        $row[] = $col['format_map'][$value];
                    } else {
                        $row[] = $value ?? '';
                    }
                }
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── Custom Actions ──────────────────────────────────────────
    public function executeAction(string $actionKey, int $recordId)
    {
        if ($actionKey === 'start_workflow') {
            $work = \App\Models\Work::findOrFail($recordId);
            $tenantId = $work->tenant_id;

            // Fetch or create default workflow
            $workflow = \App\Models\Workflow::where('tenant_id', $tenantId)->where('is_active', true)->first();
            if (!$workflow) {
                $workflow = \App\Models\Workflow::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Varsayılan Süreç Akışı',
                    'description' => 'Otomatik oluşturulmuş ilk süreç akış adımları.',
                    'steps' => [
                        ['label' => 'Müşteri İrtibatı Kuruldu', 'role' => 'pm', 'action' => 'create_task'],
                        ['label' => 'Yapay Zeka Durum Değerlendirmesi', 'role' => 'pm', 'action' => 'ai_analyze'],
                        ['label' => 'Teklif Hazırlanması', 'role' => 'designer', 'action' => 'create_proposal'],
                    ],
                    'is_active' => true,
                ]);
            }

            \App\Services\WorkflowEngine::start($work, $workflow);

            $this->dispatch('notify', type: 'success', message: 'İş akışı başarıyla başlatıldı ve ilk adım tetiklendi.');
            return;
        }

        $config = $this->getConfig();
        $actions = $config['row_actions'] ?? [];

        foreach ($actions as $action) {
            if (($action['key'] ?? '') === $actionKey && isset($action['handler'])) {
                $model = $config['model']::findOrFail($recordId);
                call_user_func($action['handler'], $model);
                return;
            }
        }
    }

    // ── Editor (Slide-Over) ─────────────────────────────────────
    public function openEditor(?int $id = null)
    {
        $this->editingId = $id;
        $this->activeTab = 'info';
        $config = $this->getConfig();

        if ($id) {
            $record = $config['model']::findOrFail($id);
            $this->editorData = $record->toArray();
            $this->loadNotes($record);
            $this->loadDocuments($record);
            $this->loadInteractions($record);
            $this->loadTimeLogs($record);
            $this->loadComments($record);
            $this->loadActivities($record);
            $this->loadRelations($record);
            $this->aiResult = '';
            $this->aiLoading = false;
        } else {
            $this->editorData = [];
            $this->recordNotes = [];
            $this->recordDocuments = [];
            $this->recordInteractions = [];
            $this->recordActivities = [];
            $this->recordRelations = [];
            $this->aiResult = '';
            $this->aiLoading = false;
            // Set defaults from config
            foreach ($config['sections'] ?? [] as $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    if (isset($field['default'])) {
                        $this->editorData[$field['key']] = $field['default'];
                    }
                }
            }
        }

        $this->editorOpen = true;
    }

    public function closeEditor()
    {
        $this->editorOpen = false;
        $this->editingId = null;
        $this->editorData = [];
        $this->newNote = '';
        $this->recordNotes = [];
        $this->recordDocuments = [];
        $this->recordTimeLogs = [];
        $this->recordRelations = [];
        $this->aiResult = '';
        $this->aiLoading = false;
        $this->manualMinutes = 0;
        $this->manualTimeDescription = '';
        $this->recordComments = [];
        $this->newComment = '';
        $this->replyingTo = null;
        $this->uploadFile = null;
        $this->docCategory = 'other';
        $this->activeTab = 'info';
        $this->formFiles = [];
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    // ── Notes ────────────────────────────────────────────────────
    protected function loadNotes($record)
    {
        if (method_exists($record, 'notes')) {
            $this->recordNotes = $record->notes()
                ->with('user')
                ->latest()
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'content' => $n->content,
                    'user' => $n->user?->name ?? 'Sistem',
                    'time' => $n->created_at->diffForHumans(),
                ])
                ->toArray();
        } else {
            $this->recordNotes = [];
        }
    }

    public function addNote()
    {
        if (empty(trim($this->newNote)) || !$this->editingId) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        if (method_exists($record, 'notes')) {
            $record->notes()->create([
                'content' => trim($this->newNote),
                'user_id' => auth()->id(),
            ]);
        }

        $this->newNote = '';
        $this->loadNotes($record);
    }

    public function deleteNote(int $noteId)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];
        
        $note = \App\Models\Note::find($noteId);
        if ($note && $note->noteable_id === $this->editingId && $note->noteable_type === $modelType) {
            $note->delete();
        }

        if ($this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::findOrFail($this->editingId);
            $this->loadNotes($record);
        }
    }

    // ── Documents ───────────────────────────────────────────────
    protected function loadDocuments($record)
    {
        if (method_exists($record, 'documents')) {
            $this->recordDocuments = $record->documents()
                ->with('uploader')
                ->latest()
                ->get()
                ->map(fn($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'size' => $d->human_size,
                    'category' => $d->category,
                    'uploader' => $d->uploader?->name ?? 'Sistem',
                    'time' => $d->created_at->diffForHumans(),
                    'mime' => $d->mime_type,
                ])
                ->toArray();
        } else {
            $this->recordDocuments = [];
        }
    }

    public function uploadDocument()
    {
        if (!$this->uploadFile || !$this->editingId) return;

        $this->validate([
            'uploadFile' => 'file|max:10240|mimes:pdf,docx,xlsx,jpg,jpeg,png,svg,pptx,txt,csv',
        ]);

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        if (method_exists($record, 'documents')) {
            $path = $this->uploadFile->store('documents', 'local');
            $record->documents()->create([
                'name' => $this->uploadFile->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $this->uploadFile->getMimeType(),
                'size' => $this->uploadFile->getSize(),
                'category' => $this->docCategory,
                'uploaded_by' => auth()->id(),
            ]);
        }

        $this->uploadFile = null;
        $this->docCategory = 'other';
        $this->loadDocuments($record);
    }

    public function deleteDocument(int $docId)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];

        $doc = \App\Models\Document::find($docId);
        if ($doc && $doc->documentable_id === $this->editingId && $doc->documentable_type === $modelType) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->path);
            $doc->delete();
        }

        if ($this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::findOrFail($this->editingId);
            $this->loadDocuments($record);
        }
    }

    public function downloadDocument(int $docId)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];

        $doc = \App\Models\Document::findOrFail($docId);
        
        if ($doc->documentable_id !== $this->editingId || $doc->documentable_type !== $modelType) {
            abort(403, 'Bu belgeye erişim yetkiniz yok.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($doc->path, $doc->name);
    }

    // ── Interactions (Clients only) ─────────────────────────────
    protected function loadInteractions($record)
    {
        if (method_exists($record, 'interactions')) {
            $this->recordInteractions = $record->interactions()
                ->with('user')
                ->orderBy('interaction_date', 'desc')
                ->get()
                ->map(fn($i) => [
                    'id' => $i->id,
                    'type' => $i->type,
                    'subject' => $i->subject,
                    'content' => $i->content,
                    'date' => $i->interaction_date->format('d.m.Y H:i'),
                    'duration' => $i->duration_minutes,
                    'outcome' => $i->outcome,
                    'follow_up' => $i->follow_up_date?->format('d.m.Y'),
                    'user' => $i->user?->name ?? 'Sistem',
                ])
                ->toArray();
        } else {
            $this->recordInteractions = [];
        }
    }

    public function addInteraction()
    {
        if (!$this->editingId || empty($this->interactionForm['subject'])) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        if (method_exists($record, 'interactions')) {
            $data = $this->interactionForm;
            $data['user_id'] = auth()->id();
            $data['interaction_date'] = $data['interaction_date'] ?: now();

            foreach (['duration_minutes', 'outcome', 'follow_up_date'] as $f) {
                if (empty($data[$f])) $data[$f] = null;
            }

            $record->interactions()->create($data);
        }

        $this->interactionForm = [
            'type' => 'call', 'subject' => '', 'content' => '',
            'interaction_date' => '', 'duration_minutes' => '', 'outcome' => '', 'follow_up_date' => '',
        ];
        $this->loadInteractions($record);
    }

    public function deleteInteraction(int $id)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];

        $interaction = \App\Models\Interaction::find($id);
        if ($interaction && $interaction->interactable_id === $this->editingId && $interaction->interactable_type === $modelType) {
            $interaction->delete();
        }
        if ($this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::findOrFail($this->editingId);
            $this->loadInteractions($record);
        }
    }

    // ── Activities (Clients mini-timeline) ──────────────────────
    protected function loadActivities($record)
    {
        if ($this->resource === 'clients') {
            try {
                $projectIds = \App\Models\Project::where('client_id', $record->id)->pluck('id')->toArray();
                $workIds = \App\Models\Work::where('client_id', $record->id)->pluck('id')->toArray();
                $proposalIds = \App\Models\Proposal::where('client_id', $record->id)->pluck('id')->toArray();

                $this->recordActivities = \App\Models\Activity::query()
                    ->where(function ($q) use ($record, $projectIds, $workIds, $proposalIds) {
                        $q->where(fn($q) => $q->where('model_type', \App\Models\Client::class)->where('model_id', $record->id))
                          ->orWhere(fn($q) => $q->where('model_type', \App\Models\Project::class)->whereIn('model_id', $projectIds))
                          ->orWhere(fn($q) => $q->where('model_type', \App\Models\Work::class)->whereIn('model_id', $workIds))
                          ->orWhere(fn($q) => $q->where('model_type', \App\Models\Proposal::class)->whereIn('model_id', $proposalIds));
                    })
                    ->with('user')
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(fn($a) => [
                        'title' => $a->title,
                        'description' => $a->description,
                        'user' => $a->user?->name ?? 'Sistem',
                        'time' => $a->created_at->diffForHumans(),
                        'type' => $a->activity_type,
                    ])
                    ->toArray();
            } catch (\Exception $e) {
                \Log::warning("Failed loading client mini-timeline activities: " . $e->getMessage());
                $this->recordActivities = [];
            }
        } else {
            $this->recordActivities = [];
        }
    }

    // ── Time Tracking ───────────────────────────────────────────
    protected function loadTimeLogs($record)
    {
        if (method_exists($record, 'timeLogs')) {
            $this->recordTimeLogs = $record->timeLogs()
                ->with('user')
                ->orderBy('started_at', 'desc')
                ->limit(20)
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'user' => $t->user?->name ?? 'Sistem',
                    'description' => $t->description,
                    'started_at' => $t->started_at->format('d.m.Y H:i'),
                    'duration' => $t->duration_formatted,
                    'is_running' => $t->isRunning(),
                    'is_manual' => $t->is_manual,
                    'billable' => $t->billable,
                ])
                ->toArray();
        } else {
            $this->recordTimeLogs = [];
        }
    }

    public function startTimer()
    {
        if (!$this->editingId) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        // Stop any running timer for this user
        \App\Models\TimeLog::running()
            ->where('user_id', auth()->id())
            ->each(fn($log) => $log->stop());

        $data = [
            'user_id' => auth()->id(),
            'started_at' => now(),
        ];

        // Assign task_id or project_id based on model type
        if ($record instanceof \App\Models\Task) {
            $data['task_id'] = $record->id;
            $data['project_id'] = $record->project_id;
        } elseif ($record instanceof \App\Models\Project) {
            $data['project_id'] = $record->id;
        }

        \App\Models\TimeLog::create($data);
        $this->loadTimeLogs($record);
    }

    public function stopTimer()
    {
        if (!$this->editingId) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        $timer = \App\Models\TimeLog::running()
            ->where('user_id', auth()->id());

        if ($record instanceof \App\Models\Task) {
            $timer = $timer->where('task_id', $record->id);
        } elseif ($record instanceof \App\Models\Project) {
            $timer = $timer->where('project_id', $record->id);
        }

        $timer = $timer->first();
        if ($timer) {
            $timer->stop();
        }

        $this->loadTimeLogs($record);
    }

    public function addManualTime()
    {
        if (!$this->editingId || $this->manualMinutes <= 0) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        $data = [
            'user_id' => auth()->id(),
            'description' => $this->manualTimeDescription ?: null,
            'started_at' => now()->subMinutes($this->manualMinutes),
            'stopped_at' => now(),
            'duration_minutes' => $this->manualMinutes,
            'is_manual' => true,
        ];

        if ($record instanceof \App\Models\Task) {
            $data['task_id'] = $record->id;
            $data['project_id'] = $record->project_id;
        } elseif ($record instanceof \App\Models\Project) {
            $data['project_id'] = $record->id;
        }

        \App\Models\TimeLog::create($data);

        $this->manualMinutes = 0;
        $this->manualTimeDescription = '';
        $this->loadTimeLogs($record);
    }

    public function deleteTimeLog(int $id)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];

        $log = \App\Models\TimeLog::find($id);
        if ($log) {
            $isProject = $modelType === \App\Models\Project::class && $log->project_id === $this->editingId;
            $isTask = $modelType === \App\Models\Task::class && $log->task_id === $this->editingId;
            
            if ($isProject || $isTask) {
                $log->delete();
            }
        }
        if ($this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::findOrFail($this->editingId);
            $this->loadTimeLogs($record);
        }
    }

    // ── Comments ──────────────────────────────────────────────
    public function loadComments($record = null)
    {
        // When called from wire:poll, $record is null — resolve from editingId
        if (!$record && $this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::find($this->editingId);
        }

        if (!$record || !method_exists($record, 'comments')) {
            $this->recordComments = [];
            return;
        }

        $this->recordComments = $record->comments()
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'user' => $c->user?->name ?? 'Sistem',
                'user_initial' => substr($c->user?->name ?? 'S', 0, 1),
                'content' => $c->content,
                'time' => $c->created_at->diffForHumans(),
                'attachment_url' => $c->attachment_url,
                'attachment_name' => $c->attachment_name,
                'attachment_mime' => $c->attachment_mime,
                'replies' => $c->replies->map(fn($r) => [
                    'id' => $r->id,
                    'user' => $r->user?->name ?? 'Sistem',
                    'user_initial' => substr($r->user?->name ?? 'S', 0, 1),
                    'content' => $r->content,
                    'time' => $r->created_at->diffForHumans(),
                ])->toArray(),
            ])
            ->toArray();
    }

    public function addComment()
    {
        if (!$this->editingId || empty(trim($this->newComment))) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        $comment = new \App\Models\Comment([
            'user_id' => auth()->id(),
            'content' => $this->newComment,
            'parent_id' => $this->replyingTo,
        ]);

        // Parse mentions
        $mentions = $comment->parseMentions();
        if (!empty($mentions)) {
            $comment->mentions = $mentions;
            // Create notifications for mentioned users
            foreach ($mentions as $userId) {
                \App\Models\AppNotification::create([
                    'user_id' => $userId,
                    'title' => auth()->user()->name . ' sizi etiketledi',
                    'body' => \Illuminate\Support\Str::limit($this->newComment, 80),
                    'type' => 'mention',
                    'url' => request()->url(),
                ]);
            }
        }

        $record->comments()->save($comment);

        $this->newComment = '';
        $this->replyingTo = null;
        $this->loadComments($record);
    }

    public function setReplyingTo(?int $commentId)
    {
        $this->replyingTo = $commentId;
    }

    public function deleteComment(int $id)
    {
        $config = $this->getConfig();
        $modelType = $config['model'];

        $comment = \App\Models\Comment::find($id);
        if ($comment && $comment->commentable_id === $this->editingId && $comment->commentable_type === $modelType) {
            $comment->delete();
        }
        if ($this->editingId) {
            $config = $this->getConfig();
            $record = $config['model']::findOrFail($this->editingId);
            $this->loadComments($record);
        }
    }

    public function saveEditor()
    {
        $config = $this->getConfig();

        // Build validation rules from config
        $rules = [];
        foreach ($config['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $fieldRules = [];
                if (($field['type'] ?? 'text') === 'file') {
                    if (($field['required'] ?? false) && empty($this->editorData[$field['key']])) {
                        $rules['formFiles.' . $field['key']] = 'required|file|max:10240';
                    } else {
                        $rules['formFiles.' . $field['key']] = 'nullable|file|max:10240';
                    }
                } else {
                    if ($field['required'] ?? false) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }
                    if (($field['type'] ?? 'text') === 'number') {
                        $fieldRules[] = 'numeric';
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    $rules['editorData.' . $field['key']] = implode('|', $fieldRules);
                }
            }
        }

        $validated = $this->validate($rules);
        
        // Handle file uploads defined in sections/fields
        foreach ($config['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (($field['type'] ?? 'text') === 'file' && isset($this->formFiles[$field['key']])) {
                    $file = $this->formFiles[$field['key']];
                    $path = $file->store('assets', 'public');
                    $this->editorData[$field['key']] = asset('storage/' . $path);
                }
            }
        }

        $safeData = $validated['editorData'] ?? [];

        // DB'ye null gitmesini (strict mode hatalarını) önlemek veya null izinli alanları boşaltabilmek için:
        // Yaratma işleminde (CREATE) null/boş stringleri çıkartıyoruz (orijinal sistem davranışı)
        $data = collect($safeData)->filter(function ($value, $key) {
            return $value !== null && $value !== '';
        })->toArray();

        // Include newly uploaded file URLs in the create/update payload
        foreach ($config['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (($field['type'] ?? 'text') === 'file' && isset($this->editorData[$field['key']])) {
                    $data[$field['key']] = $this->editorData[$field['key']];
                    $safeData[$field['key']] = $this->editorData[$field['key']];
                }
            }
        }

        $modelClass = $config['model'];

        if ($this->editingId) {
            $record = $modelClass::findOrFail($this->editingId);
            $record->fill($safeData);
            $record->save();
            $this->dispatch('notify', type: 'success', message: 'Kayıt güncellendi.');
        } else {
            $modelClass::create($data);
            $this->dispatch('notify', type: 'success', message: 'Yeni kayıt oluşturuldu.');
        }

        $this->closeEditor();
    }

    // ── Query Builder ───────────────────────────────────────────
    protected function buildQuery()
    {
        $config = $this->getConfig();
        $query = $config['model']::query();

        // Search
        if ($this->search) {
            $searchable = collect($config['columns'])
                ->where('searchable', true)
                ->pluck('key')
                ->toArray();

            if (!empty($searchable)) {
                $query->where(function ($q) use ($searchable) {
                    foreach ($searchable as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            }
        }

        // Filters
        foreach ($this->activeFilters as $key => $value) {
            if ($value !== '' && $value !== null) {
                $query->where($key, $value);
            }
        }

        // Sort
        if ($this->sortField) {
            if (str_contains($this->sortField, '.')) {
                [$relation, $column] = explode('.', $this->sortField);
                $model = $query->getModel();
                if (method_exists($model, $relation)) {
                    $relatedTable = $model->$relation()->getRelated()->getTable();
                    $foreignKey = $model->$relation()->getForeignKeyName();
                    $ownerKey = $model->$relation()->getOwnerKeyName();
                    
                    $query->join($relatedTable, $model->getTable() . '.' . $foreignKey, '=', $relatedTable . '.' . $ownerKey)
                        ->orderBy($relatedTable . '.' . $column, $this->sortDirection)
                        ->select($model->getTable() . '.*');
                } else {
                    $query->orderBy($this->sortField, $this->sortDirection);
                }
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }

        return $query;
    }

    // ── Quick Entry ───────────────────────────────────────────────
    public function quickSave()
    {
        $config = $this->getConfig();
        $title = trim($this->quickData['title'] ?? $this->quickData['name'] ?? '');

        if (empty($title)) {
            $this->dispatch('notify', type: 'danger', message: 'Lütfen gerekli alanları doldurun.');
            return;
        }

        if ($this->resource === 'tasks') {
            $record = $config['model']::create([
                'tenant_id' => auth()->user()->tenant_id,
                'title' => $title,
                'priority' => $this->quickData['priority'] ?? 'medium',
                'project_id' => !empty($this->quickData['project_id']) ? $this->quickData['project_id'] : null,
                'status' => 'todo',
            ]);
        } elseif ($this->resource === 'clients') {
            $record = $config['model']::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name' => $title,
                'industry' => trim($this->quickData['industry'] ?? ''),
                'strategic_importance' => 3,
            ]);
        } elseif ($this->resource === 'works') {
            $record = $config['model']::create([
                'tenant_id' => auth()->user()->tenant_id,
                'title' => $title,
                'client_id' => !empty($this->quickData['client_id']) ? $this->quickData['client_id'] : null,
                'value' => floatval($this->quickData['value'] ?? 0),
                'currency' => 'TRY',
                'status' => 'lead',
                'priority' => 'medium',
                'created_by' => auth()->id(),
            ]);
        } else {
            $this->openEditor();
            return;
        }

        $this->quickData = [];
        $this->dispatch('notify', type: 'success', message: 'Kayıt başarıyla oluşturuldu.');
    }

    protected function loadRelations($record)
    {
        $class = get_class($record);
        $id = $record->id;

        $this->recordRelations = \App\Models\EntityRelation::where(function ($q) use ($class, $id) {
                $q->where('source_type', $class)->where('source_id', $id);
            })
            ->orWhere(function ($q) use ($class, $id) {
                $q->where('target_type', $class)->where('target_id', $id);
            })
            ->latest()
            ->get()
            ->map(function ($rel) use ($class, $id) {
                $isSource = $rel->source_type === $class && $rel->source_id === $id;
                
                $relatedType = $isSource ? $rel->target_type : $rel->source_type;
                $relatedId = $isSource ? $rel->target_id : $rel->source_id;

                if (!class_exists($relatedType)) return null;
                $relatedModel = $relatedType::find($relatedId);
                if (!$relatedModel) return null;

                $typeBaseName = class_basename($relatedModel);
                $translatedType = [
                    'Client' => 'Müşteri',
                    'Work' => 'İş Süreci',
                    'Proposal' => 'Teklif',
                    'Contract' => 'Sözleşme',
                    'Task' => 'Görev',
                    'Project' => 'Proje',
                    'Income' => 'Gelir/Fatura',
                    'Collection' => 'Tahsilat',
                ][$typeBaseName] ?? $typeBaseName;

                $relatedName = $relatedModel->name ?? $relatedModel->title ?? $relatedModel->proposal_number ?? $relatedModel->contract_number ?? $relatedModel->income_number ?? $relatedModel->expense_number ?? "#{$relatedModel->id}";

                $translatedRelation = [
                    'created_for' => 'için oluşturuldu',
                    'associated_with' => 'ile ilişkili',
                    'has_proposal' => 'teklifine sahip',
                    'received_proposal' => 'teklifini aldı',
                    'has_contract' => 'sözleşmesine sahip',
                    'signed_contract' => 'sözleşmesini imzaladı',
                    'has_income' => 'faturasına sahip',
                    'billed_to' => 'adına fatura kesildi',
                    'collected_for' => 'tahsilatı yapıldı',
                    'paid_by' => 'tarafından ödendi',
                ][$rel->relation_type] ?? $rel->relation_type;

                return [
                    'id' => $rel->id,
                    'type' => $translatedType,
                    'name' => $relatedName,
                    'relation' => $translatedRelation,
                    'time' => $rel->created_at->diffForHumans(),
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function runAiAction(string $actionType)
    {
        if (!$this->editingId) return;

        $this->aiLoading = true;
        $this->aiResult = '';

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        $prompt = "Aşağıdaki kaydı detaylı olarak analiz et:\n" . json_encode($this->editorData, JSON_UNESCAPED_UNICODE);
        $systemPrompt = " Sen ADA Co-OS akıllı iş motorusun.";

        try {
            $this->aiResult = \App\Services\AIService::ask($prompt, $systemPrompt, $actionType);
        } catch (\Exception $e) {
            $this->aiResult = 'Hata oluştu: ' . $e->getMessage();
        }

        $this->aiLoading = false;
    }

    public function saveAiResultAsNote()
    {
        if (empty($this->aiResult) || !$this->editingId) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        if (method_exists($record, 'notes')) {
            $record->notes()->create([
                'content' => "🤖 YAPAY ZEKA ANALİZİ:\n\n" . $this->aiResult,
                'user_id' => auth()->id(),
            ]);
            $this->loadNotes($record);
            $this->dispatch('notify', type: 'success', message: 'Yapay zekâ analizi başarıyla süreç notu olarak kaydedildi.');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Bu kayıt tipi için not ekleme desteklenmiyor.');
        }
    }

    public function publishAiResultToFeed()
    {
        if (empty($this->aiResult) || !$this->editingId) return;

        $config = $this->getConfig();
        $record = $config['model']::findOrFail($this->editingId);

        $cleanText = strip_tags(str_replace(['###', '*', '`'], '', $this->aiResult));
        $desc = \Illuminate\Support\Str::limit($cleanText, 250);

        \App\Services\ActivityService::logAI(
            'AI Analiz Raporu',
            "Yapay zekâ bu kayıt için analiz raporu hazırladı: " . $desc,
            $record
        );

        $this->dispatch('notify', type: 'success', message: 'Analiz raporu başarıyla aktivite akışına gönderildi.');
    }

    public function getStats()
    {
        switch ($this->resource) {
            case 'clients':
                return [
                    [
                        'label' => 'Toplam Firma',
                        'value' => \App\Models\Client::count(),
                        'icon' => 'group',
                        'color' => '#3b82f6',
                        'desc' => 'Aktif kurumsal müşteri sayısı'
                    ],
                    [
                        'label' => 'Stratejik Ortaklar',
                        'value' => \App\Models\Client::where('strategic_importance', '>=', 4)->count(),
                        'icon' => 'star',
                        'color' => '#f59e0b',
                        'desc' => 'Kritik seviyedeki ortaklıklar'
                    ],
                    [
                        'label' => 'Toplam Proje',
                        'value' => \App\Models\Project::count(),
                        'icon' => 'folder',
                        'color' => '#10b981',
                        'desc' => 'Müşterilere bağlı yürütülen projeler'
                    ],
                    [
                        'label' => 'Son Etkileşim',
                        'value' => \App\Models\Interaction::latest()->first()?->interaction_date?->diffForHumans() ?? 'Temas yok',
                        'icon' => 'chat_bubble',
                        'color' => '#8b5cf6',
                        'desc' => 'Son kurulan müşteri iletişimi'
                    ]
                ];
            case 'works':
                return [
                    [
                        'label' => 'Açık İş Fırsatları',
                        'value' => \App\Models\Work::whereIn('status', ['lead', 'proposal', 'started', 'in_progress'])->count(),
                        'icon' => 'work',
                        'color' => '#3b82f6',
                        'desc' => 'Aktif süreçte olan işler'
                    ],
                    [
                        'label' => 'Pipeline Finansal Değeri',
                        'value' => '₺' . number_format(\App\Models\Work::whereIn('status', ['lead', 'proposal', 'started', 'in_progress'])->sum('value'), 0, ',', '.'),
                        'icon' => 'payments',
                        'color' => '#10b981',
                        'desc' => 'Açık süreçlerin toplam bütçesi'
                    ],
                    [
                        'label' => 'Tamamlanan İşler',
                        'value' => \App\Models\Work::where('status', 'completed')->count(),
                        'icon' => 'task_alt',
                        'color' => '#8b5cf6',
                        'desc' => 'Başarıyla kapatılmış süreçler'
                    ],
                    [
                        'label' => 'Kritik Öncelikliler',
                        'value' => \App\Models\Work::where('priority', 'critical')->count(),
                        'icon' => 'warning',
                        'color' => '#ef4444',
                        'desc' => 'Müdahale gerektiren süreçler'
                    ]
                ];
            case 'tasks':
                return [
                    [
                        'label' => 'Bekleyen Görevler',
                        'value' => \App\Models\Task::where('status', 'todo')->count(),
                        'icon' => 'assignment_late',
                        'color' => '#3b82f6',
                        'desc' => 'Henüz başlanmamış olanlar'
                    ],
                    [
                        'label' => 'Yürütülen Süreçler',
                        'value' => \App\Models\Task::where('status', 'in_progress')->count(),
                        'icon' => 'pending',
                        'color' => '#f59e0b',
                        'desc' => 'Şu an aktif olarak çalışılan'
                    ],
                    [
                        'label' => 'Tamamlananlar',
                        'value' => \App\Models\Task::where('status', 'done')->count(),
                        'icon' => 'task_alt',
                        'color' => '#10b981',
                        'desc' => 'Kapatılmış olan iş paketleri'
                    ],
                    [
                        'label' => 'Gecikmiş Eylemler',
                        'value' => \App\Models\Task::where('status', '!=', 'done')->where('due_date', '<', now())->count(),
                        'icon' => 'error',
                        'color' => '#ef4444',
                        'desc' => 'Vadesi geçmiş olan görevler'
                    ]
                ];
            case 'proposals':
                return [
                    [
                        'label' => 'Taslak Teklifler',
                        'value' => \App\Models\Proposal::where('status', 'draft')->count(),
                        'icon' => 'edit_document',
                        'color' => '#6b7280',
                        'desc' => 'Hazırlanan teklif taslakları'
                    ],
                    [
                        'label' => 'Onay Bekleyenler',
                        'value' => \App\Models\Proposal::where('status', 'pending_approval')->count(),
                        'icon' => 'hourglass_empty',
                        'color' => '#f59e0b',
                        'desc' => 'Onaya sunulmuş teklifler'
                    ],
                    [
                        'label' => 'Kabul Edilenler',
                        'value' => \App\Models\Proposal::where('status', 'accepted')->count(),
                        'icon' => 'check_circle',
                        'color' => '#10b981',
                        'desc' => 'İmzalanan/kabul edilenler'
                    ],
                    [
                        'label' => 'Kabul Edilen Hacim',
                        'value' => '₺' . number_format(\App\Models\Proposal::where('status', 'accepted')->sum('grand_total'), 0, ',', '.'),
                        'icon' => 'payments',
                        'color' => '#3b82f6',
                        'desc' => 'Kazanılan tekliflerin toplamı'
                    ]
                ];
            case 'contracts':
                return [
                    [
                        'label' => 'Aktif Sözleşmeler',
                        'value' => \App\Models\Contract::where('status', 'active')->count(),
                        'icon' => 'verified_user',
                        'color' => '#10b981',
                        'desc' => 'Yürürlükteki yasal bağlayıcılar'
                    ],
                    [
                        'label' => 'İmza Bekleyenler',
                        'value' => \App\Models\Contract::where('status', 'pending_approval')->count(),
                        'icon' => 'draw',
                        'color' => '#f59e0b',
                        'desc' => 'Müşteri imzası bekleyen'
                    ],
                    [
                        'label' => 'Sözleşme Toplam Değeri',
                        'value' => '₺' . number_format(\App\Models\Contract::where('status', 'active')->sum('value'), 0, ',', '.'),
                        'icon' => 'monetization_on',
                        'color' => '#3b82f6',
                        'desc' => 'Aktif sözleşme hacmi'
                    ],
                    [
                        'label' => 'Kritik Süre (30 Gün)',
                        'value' => \App\Models\Contract::where('status', 'active')->where('end_date', '<=', now()->addDays(30))->count(),
                        'icon' => 'alarm_on',
                        'color' => '#ef4444',
                        'desc' => 'Bitişi 30 günden az kalanlar'
                    ]
                ];
            case 'projects':
                return [
                    [
                        'label' => 'Aktif Projeler',
                        'value' => \App\Models\Project::count(),
                        'icon' => 'folder_open',
                        'color' => '#3b82f6',
                        'desc' => 'Üzerinde çalışılan projeler'
                    ],
                    [
                        'label' => 'Planlanan Toplam Bütçe',
                        'value' => '₺' . number_format(\App\Models\Project::sum('budget'), 0, ',', '.'),
                        'icon' => 'currency_lira',
                        'color' => '#10b981',
                        'desc' => 'Projelere atanan toplam bütçe'
                    ],
                    [
                        'label' => 'Gerçekleşen Faturalama',
                        'value' => '₺' . number_format(\App\Models\Project::sum('actual_revenue'), 0, ',', '.'),
                        'icon' => 'account_balance',
                        'color' => '#8b5cf6',
                        'desc' => 'Faturalandırılmış reel bütçe'
                    ],
                    [
                        'label' => 'Planlanan İş Gücü',
                        'value' => number_format(\App\Models\Project::sum('planned_hours'), 0) . ' Saat',
                        'icon' => 'schedule',
                        'color' => '#f59e0b',
                        'desc' => 'Tahsis edilen toplam efor'
                    ]
                ];
            default:
                try {
                    $config = $this->getConfig();
                    $count = $config['model']::count();
                } catch (\Exception $e) {
                    $count = 0;
                }
                return [
                    [
                        'label' => 'Toplam Kayıt',
                        'value' => $count,
                        'icon' => 'analytics',
                        'color' => '#3b82f6',
                        'desc' => 'Bu modüle kayıtlı toplam veri'
                    ]
                ];
        }
    }

    // ── Render ───────────────────────────────────────────────────
    public function render()
    {
        $config = $this->getConfig();
        $records = $this->buildQuery()->paginate($this->perPage);

        return view('livewire.admin.nexus-table', [
            'config' => $config,
            'records' => $records,
        ])->layout('layouts.admin', [
            'title' => $config['title'] ?? 'Resource',
            'breadcrumb' => $config['title'] ?? 'Resource',
        ]);
    }
}
