<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Work extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;

    protected $table = 'works';

    protected $guarded = [];

    protected $fillable = [
        'tenant_id',
        'client_id',
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'value',
        'currency',
        'started_at',
        'due_at',
        'completed_at',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'value' => 'decimal:2',
    ];

    /**
     * Relationship with Tenant (handled automatically by BelongsToTenant but good to declare explicitly if needed)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relationship with Client (Müşteri)
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship with Project (Proje)
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship with Creator (Oluşturan Kullanıcı)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Assignee (Atanan Kullanıcı)
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship with Tasks (Görevler)
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'work_id');
    }

    /**
     * Relationship with Events (Toplantılar/Etkinlikler)
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'work_id');
    }

    /**
     * Relationship with Proposals (Teklifler)
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'work_id');
    }

    /**
     * Relationship with Contracts (Sözleşmeler)
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'work_id');
    }

    /**
     * Polymorphic Relationship with Notes (Notlar)
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    /**
     * Polymorphic Relationship with Documents (Dosyalar)
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Polymorphic Relationship with Comments (Yorumlar)
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Relationship with Incomes (Gelirler)
     */
    public function incomes()
    {
        return $this->hasMany(Income::class, 'work_id');
    }

    /**
     * Relationship with Collections (Tahsilatlar)
     */
    public function collections()
    {
        return $this->hasMany(Collection::class, 'work_id');
    }
}
