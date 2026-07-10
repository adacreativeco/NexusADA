<div style="animation: fadeSlideIn 0.3s ease-out;">
    {{-- Toast/Alert Messages --}}
    @if(session()->has('message'))
        <div style="margin-bottom: 24px; padding: 16px 20px; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25); border-radius: 12px; color: #34d399; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
                {{ session('message') }}
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>
    @endif

    <div style="margin-bottom: 32px;">
        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.08em; display: inline-block; padding: 4px 10px; background: rgba(16,185,129,0.1); border-radius: 6px; border: 1px solid rgba(16,185,129,0.15);">B2B MÜŞTERİ PORTALI</span>
        <h1 style="font-size: 32px; font-weight: 800; color: #fff; margin-top: 8px; letter-spacing: -0.03em; font-family: 'Geist', sans-serif;">Hoş geldiniz, {{ $clientUser->name }}</h1>
        <p style="font-size: 14px; color: var(--text-secondary); margin-top: 6px;">{{ $client->name }} firmasına ait güncel proje durumları ve finansal süreçler.</p>
    </div>

    {{-- Stats Bento Grid --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
        {{-- Card 1: Active Projects --}}
        <div class="client-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(20, 27, 46, 0.6) 0%, rgba(16, 185, 129, 0.05) 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 28px; background: rgba(16,185,129,0.1); padding: 8px; border-radius: 10px;">folder_open</span>
                <span style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">Canlı Süreçler</span>
            </div>
            <div style="margin-top: auto;">
                <div style="font-size: 36px; font-weight: 800; color: #fff; line-height: 1; font-family: 'Geist Mono', monospace;">{{ $activeProjects }}</div>
                <div style="font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Aktif Proje</div>
            </div>
        </div>
        
        {{-- Card 2: Pending Approvals --}}
        <div class="client-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(20, 27, 46, 0.6) 0%, rgba(59, 130, 246, 0.05) 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="material-symbols-outlined" style="color: #3b82f6; font-size: 28px; background: rgba(59, 130, 246, 0.1); padding: 8px; border-radius: 10px;">draw</span>
                <span style="font-size: 10px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em;">Bekleyen İmza</span>
            </div>
            <div style="margin-top: auto;">
                <div style="font-size: 36px; font-weight: 800; color: #fff; line-height: 1; font-family: 'Geist Mono', monospace;">{{ $pendingProposals->count() + $pendingContracts->count() }}</div>
                <div style="font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Onay Bekleyen Belge</div>
            </div>
        </div>

        {{-- Card 3: Invoices --}}
        <div class="client-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(20, 27, 46, 0.6) 0%, rgba(245, 158, 11, 0.05) 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="material-symbols-outlined" style="color: #f59e0b; font-size: 28px; background: rgba(245, 158, 11, 0.1); padding: 8px; border-radius: 10px;">receipt_long</span>
                <span style="font-size: 10px; font-weight: 700; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em;">Faturalandırma</span>
            </div>
            <div style="margin-top: auto;">
                <div style="font-size: 36px; font-weight: 800; color: #fff; line-height: 1; font-family: 'Geist Mono', monospace;">{{ $recentInvoices->count() }}</div>
                <div style="font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Son Fatura Bildirimi</div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start;">
        
        {{-- Left: Onay Bekleyen Belgeler --}}
        <div class="client-card">
            <h2 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-family: 'Geist', sans-serif;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 22px;">edit_document</span>
                İmza ve Onay Bekleyen Belgeler
            </h2>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                @php $hasDocuments = false; @endphp
                
                @foreach($pendingProposals as $prop)
                    @php $hasDocuments = true; @endphp
                    <div style="padding: 16px 20px; border: 1px solid var(--border); background: rgba(255,255,255,0.01); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; transition: var(--transition);" onmouseover="this.style.borderColor='rgba(16,185,129,0.3)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div>
                            <span style="font-size: 9px; padding: 2px 6px; background: rgba(59,130,246,0.1); color: #3b82f6; border-radius: 4px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 6px;">FİYAT TEKLİFİ</span>
                            <div style="font-size: 14px; font-weight: 600; color: #fff;">{{ $prop->title }}</div>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                                Toplam Tutar: <strong style="color: #fff; font-family: 'Geist Mono', monospace;">₺{{ number_format($prop->grand_total, 2, ',', '.') }}</strong>
                            </div>
                        </div>
                        <button type="button" wire:click="approveProposal({{ $prop->id }})" class="nx-btn nx-btn-primary" style="padding: 10px 18px; font-size: 12px;">
                            <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                            Teklifi Onayla
                        </button>
                    </div>
                @endforeach

                @foreach($pendingContracts as $contract)
                    @php $hasDocuments = true; @endphp
                    <div style="padding: 16px 20px; border: 1px solid var(--border); background: rgba(255,255,255,0.01); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; transition: var(--transition);" onmouseover="this.style.borderColor='rgba(16,185,129,0.3)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div>
                            <span style="font-size: 9px; padding: 2px 6px; background: rgba(16,185,129,0.1); color: var(--primary); border-radius: 4px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 6px;">B2B SÖZLEŞME</span>
                            <div style="font-size: 14px; font-weight: 600; color: #fff;">{{ $contract->title }}</div>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                                Sözleşme Kodu: <strong style="color: #fff; font-family: 'Geist Mono', monospace;">{{ $contract->contract_number }}</strong>
                            </div>
                        </div>
                        <button type="button" wire:click="approveContract({{ $contract->id }})" class="nx-btn nx-btn-primary" style="padding: 10px 18px; font-size: 12px; background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25);">
                            <span class="material-symbols-outlined" style="font-size: 16px;">draw</span>
                            Sözleşmeyi İmzala
                        </button>
                    </div>
                @endforeach

                @if(!$hasDocuments)
                    <div style="padding: 40px 20px; text-align: center; color: var(--text-secondary); font-size: 13px;">
                        <span class="material-symbols-outlined" style="font-size: 40px; color: var(--border); margin-bottom: 12px; display: inline-block; padding: 12px; background: rgba(255,255,255,0.02); border-radius: 50%;">verified</span>
                        <p style="font-weight: 600; color: #fff;">Tüm belgeler onaylı!</p>
                        <p style="margin-top: 4px; font-size: 12px; color: var(--text-muted);">Şu anda onayınızı veya imzanızı bekleyen aktif bir belge bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Son Faturalar --}}
        <div class="client-card">
            <h2 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-family: 'Geist', sans-serif;">
                <span class="material-symbols-outlined" style="color: #f59e0b; font-size: 22px;">receipt</span>
                Son Faturalar
            </h2>
            
            <div style="display: flex; flex-direction: column; gap: 4px;">
                @forelse($recentInvoices as $invoice)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0; {{ !$loop->last ? 'border-bottom: 1px solid var(--border);' : '' }}">
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #fff; font-family: 'Geist Mono', monospace;">{{ $invoice->income_number }}</div>
                            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 4px;">{{ $invoice->created_at->format('d.m.Y') }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 700; color: var(--primary); font-family: 'Geist Mono', monospace;">
                                ₺{{ number_format($invoice->grand_total ?? 0, 0, ',', '.') }}
                            </div>
                            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #10b981; margin-top: 4px; display: inline-block;">
                                ÖDENDİ
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px 0; text-align: center; color: var(--text-muted); font-size: 12px;">
                        Henüz fatura kaydı bulunmuyor.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
