<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Client extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;

    protected $guarded = [];

    /**
     * İlişki Zekası: Müşterinin projeleri
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Medya Analizleri: Müşteriden gelen/hakkındaki yansımalar
     */
    public function mediaInsights()
    {
        return $this->hasMany(MediaInsight::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function invoices()
    {
        return $this->hasMany(Income::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
