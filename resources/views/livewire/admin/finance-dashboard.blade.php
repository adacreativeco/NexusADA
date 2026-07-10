<div>
    {{-- Action Shortcuts Header --}}
    <div class="nx-page-header" style="margin-bottom: 24px;">
        <div>
            <h1 class="nx-page-title" style="font-size: 24px; font-weight: 700; color: var(--nx-text-primary); display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 28px; color: var(--nx-accent);">account_balance_wallet</span>
                Finans Yönetim Merkezi
            </h1>
            <p class="nx-page-subtitle">Gelir, gider, banka hesapları ve kıymetli evrak takibi</p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="/admin/bank-accounts" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">account_balance</span>
                Bankalar
            </a>
            <a href="/admin/incomes" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">trending_up</span>
                Gelirler
            </a>
            <a href="/admin/expenses" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">trending_down</span>
                Giderler
            </a>
            <a href="/admin/collections" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">price_check</span>
                Tahsilatlar
            </a>
            <a href="/admin/financial-instruments" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">payments</span>
                Çek/Senet
            </a>
        </div>
    </div>

    {{-- Bento Summary Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        {{-- Total Cash --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 24px;">savings</span>
            </div>
            <div>
                <span style="font-size: 11px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Toplam Kasa / Banka</span>
                <h2 style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); margin: 4px 0 0 0; font-family: var(--nx-font-mono);">
                    ₺{{ number_format($totalBalance, 2, ',', '.') }}
                </h2>
            </div>
        </div>

        {{-- Monthly Incomes --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2); color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 24px;">add_chart</span>
            </div>
            <div>
                <span style="font-size: 11px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Bu Ay Faturalanan</span>
                <h2 style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); margin: 4px 0 0 0; font-family: var(--nx-font-mono);">
                    ₺{{ number_format($monthlyIncomes, 2, ',', '.') }}
                </h2>
            </div>
        </div>

        {{-- Monthly Collections --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 24px;">paid</span>
            </div>
            <div>
                <span style="font-size: 11px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Bu Ay Tahsil Edilen</span>
                <h2 style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); margin: 4px 0 0 0; font-family: var(--nx-font-mono);">
                    ₺{{ number_format($monthlyCollections, 2, ',', '.') }}
                </h2>
            </div>
        </div>

        {{-- Monthly Expenses --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 24px;">receipt_long</span>
            </div>
            <div>
                <span style="font-size: 11px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Bu Ay Giderler</span>
                <h2 style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); margin: 4px 0 0 0; font-family: var(--nx-font-mono);">
                    ₺{{ number_format($monthlyExpenses, 2, ',', '.') }}
                </h2>
            </div>
        </div>
    </div>

    {{-- Main Financial Analytics Grid --}}
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 20px; margin-bottom: 24px; align-items: start;">
        {{-- Bank Accounts list --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); margin: 0 0 16px 0; display: flex; align-items: center; gap: 6px;">
                <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-accent);">account_balance</span>
                Banka Hesapları & Kasalar
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
                @forelse($bankAccounts as $account)
                    <div style="border: 1px solid var(--nx-border); border-radius: 8px; padding: 14px; background: rgba(255,255,255,0.01);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $account->bank_name }}</span>
                            <span style="font-family: var(--nx-font-mono); font-size: 12px; font-weight: 700; color: #10b981;">
                                {{ number_format($account->balance, 2, ',', '.') }} {{ $account->currency }}
                            </span>
                        </div>
                        @if($account->iban)
                            <div style="font-family: var(--nx-font-mono); font-size: 11px; color: var(--nx-text-secondary); letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                                <span>{{ $account->iban }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $account->iban }}'); alert('IBAN kopyalandı!')" style="background: none; border: none; cursor: pointer; color: var(--nx-accent); padding: 0;" title="Kopyala">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">content_copy</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="grid-column: span 2; padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px;">
                        Kayıtlı banka hesabı bulunamadı.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Burn Rate Gauge --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center; height: 100%;">
            <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin: 0 0 16px 0;">
                Harcama Oranı (Burn Rate)
            </h3>
            
            @php
                $dashArray = 251.2;
                $clampedRate = min(100, $burnRate);
                $strokeOffset = $dashArray - ($dashArray * $clampedRate) / 100;
                $gaugeColor = '#10b981'; // Green
                if ($burnRate > 90) {
                    $gaugeColor = '#ef4444'; // Red
                } elseif ($burnRate > 50) {
                    $gaugeColor = '#f59e0b'; // Yellow
                }
            @endphp
            <div style="position: relative; width: 140px; height: 140px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <svg width="140" height="140" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                    <circle cx="50" cy="50" r="40" stroke="var(--nx-border)" stroke-width="8" fill="transparent" />
                    <circle cx="50" cy="50" r="40" stroke="{{ $gaugeColor }}" stroke-width="8" fill="transparent"
                            stroke-dasharray="{{ $dashArray }}" stroke-dashoffset="{{ $strokeOffset }}" stroke-linecap="round" />
                </svg>
                <div style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <span style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">
                        {{ $burnRate }}%
                    </span>
                    <span style="font-size: 9px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600;">Gider / Gelir</span>
                </div>
            </div>
            
            <p style="font-size: 11px; color: var(--nx-text-secondary); line-height: 1.4; margin: 0;">
                Bu ay elde edilen gelire oranla yapılan harcamaları gösterir. Düşük oran yüksek kârlılığa işaret eder.
            </p>
        </div>
    </div>

    {{-- Bottom Lists Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        {{-- Unpaid Incomes --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin: 0 0 16px 0; display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: #f59e0b;">hourglass_empty</span>
                    Bekleyen Tahsilatlar (Faturalar)
                </span>
                <a href="/admin/incomes" style="font-size: 11px; color: var(--nx-accent); text-decoration: none; font-weight: 600;">Tümü</a>
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($pendingCollections as $inc)
                    <div style="padding: 10px 12px; border: 1px solid var(--nx-border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $inc->title }}</div>
                            <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 2px;">
                                Fatura No: <strong>{{ $inc->income_number }}</strong> &bull; Müşteri: {{ $inc->client?->name ?? 'Genel' }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: var(--nx-font-mono); font-size: 12px; font-weight: 700; color: var(--nx-text-primary);">
                                ₺{{ number_format($inc->grand_total, 2, ',', '.') }}
                            </div>
                            <span style="font-size: 9px; padding: 1px 6px; border-radius: 4px; background: rgba(245,158,11,0.1); color: #f59e0b; text-transform: uppercase; font-weight: 700; margin-top: 4px; display: inline-block;">
                                {{ $inc->status === 'sent' ? 'Gönderildi' : 'Taslak' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 11px;">
                        Bekleyen tahsilat bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Financial Instruments --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin: 0 0 16px 0; display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: #3b82f6;">calendar_month</span>
                    Vadeli Çek & Senetler
                </span>
                <a href="/admin/financial-instruments" style="font-size: 11px; color: var(--nx-accent); text-decoration: none; font-weight: 600;">Tümü</a>
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($pendingInstruments as $inst)
                    <div style="padding: 10px 12px; border: 1px solid var(--nx-border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">
                                {{ $inst->type === 'check' ? '🎫 Çek' : '📄 Senet' }} ({{ $inst->instrument_number }})
                            </div>
                            <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 2px;">
                                Vade: <strong>{{ $inst->due_date->format('d.m.Y') }}</strong>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: var(--nx-font-mono); font-size: 12px; font-weight: 700; color: var(--nx-text-primary);">
                                ₺{{ number_format($inst->amount, 2, ',', '.') }}
                            </div>
                            <span style="font-size: 9px; padding: 1px 6px; border-radius: 4px; 
                                         background: {{ $inst->direction === 'inbound' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }}; 
                                         color: {{ $inst->direction === 'inbound' ? '#10b981' : '#ef4444' }}; 
                                         text-transform: uppercase; font-weight: 700; margin-top: 4px; display: inline-block;">
                                {{ $inst->direction === 'inbound' ? 'Alınan' : 'Verilen' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 11px;">
                        Vadesi bekleyen çek/senet bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin: 0 0 16px 0; display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: #ef4444;">receipt</span>
                    Son Harcamalar & Giderler
                </span>
                <a href="/admin/expenses" style="font-size: 11px; color: var(--nx-accent); text-decoration: none; font-weight: 600;">Tümü</a>
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($recentExpenses as $exp)
                    <div style="padding: 10px 12px; border: 1px solid var(--nx-border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $exp->title }}</div>
                            <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 2px;">
                                Fatura/Fis: <strong>{{ $exp->expense_number }}</strong> &bull; Kategori: {{ [
                                    'personnel' => 'Personel',
                                    'software' => 'Yazılım',
                                    'office' => 'Ofis',
                                    'marketing' => 'Pazarlama',
                                    'tax' => 'Vergi',
                                    'other' => 'Diğer',
                                ][$exp->category] ?? $exp->category }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: var(--nx-font-mono); font-size: 12px; font-weight: 700; color: var(--nx-text-primary);">
                                ₺{{ number_format($exp->grand_total, 2, ',', '.') }}
                            </div>
                            <span style="font-size: 9px; padding: 1px 6px; border-radius: 4px; 
                                         background: {{ $exp->status === 'paid' || $exp->status === 'approved_internal' ? 'rgba(16,185,129,0.1)' : ($exp->status === 'pending_approval' ? 'rgba(245,158,11,0.1)' : 'rgba(255,255,255,0.05)') }}; 
                                         color: {{ $exp->status === 'paid' || $exp->status === 'approved_internal' ? '#10b981' : ($exp->status === 'pending_approval' ? '#f59e0b' : 'var(--nx-text-secondary)') }}; 
                                         text-transform: uppercase; font-weight: 700; margin-top: 4px; display: inline-block;">
                                {{ [
                                    'draft' => 'Taslak',
                                    'pending_approval' => 'Onay Bekliyor',
                                    'approved_internal' => 'Onaylandı',
                                    'rejected_internal' => 'Reddedildi',
                                    'paid' => 'Ödendi',
                                ][$exp->status] ?? $exp->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 11px;">
                        Kayıtlı gider bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
