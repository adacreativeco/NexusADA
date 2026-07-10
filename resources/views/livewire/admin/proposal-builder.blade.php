<div>
    {{-- Page Header --}}
    <div class="nx-page-header" style="margin-bottom: 24px;">
        <div>
            <h1 class="nx-page-title" style="font-size: 24px; font-weight: 700; color: var(--nx-text-primary);">{{ $proposalId ? 'Teklifi Düzenle' : 'Yeni Teklif Oluştur' }}</h1>
            <p class="nx-page-subtitle">Hizmet kalemleri ekleyin, vergileri hesaplayın ve onay sürecini başlatın</p>
        </div>
        <div>
            <a href="/admin/proposals" class="nx-btn nx-btn-secondary" style="font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                Teklifler Listesi
            </a>
        </div>
    </div>

    {{-- Main Form Grid --}}
    <form wire:submit.prevent style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        {{-- Left Panel: Metadata & Item lines --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            {{-- General Info Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-accent);">info</span>
                    Genel Bilgiler
                </h3>

                <div class="nx-form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="nx-form-group" style="grid-column: span 2;">
                        <label class="nx-label">Teklif Başlığı <span class="required">*</span></label>
                        <input type="text" wire:model="title" class="nx-input" placeholder="Örn: 2026 Kurumsal Web Tasarım Hizmeti">
                        @error('title') <span class="nx-error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-form-group">
                        <label class="nx-label">Müşteri <span class="required">*</span></label>
                        <select wire:model="clientId" class="nx-select">
                            <option value="">Seçin</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('clientId') <span class="nx-error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-form-group">
                        <label class="nx-label">İlişkili İş (Opsiyonel)</label>
                        <select wire:model="workId" class="nx-select">
                            <option value="">Seçin</option>
                            @foreach($works as $w)
                                <option value="{{ $w->id }}">{{ $w->title }} ({{ $w->client?->name ?? 'Müşterisiz' }})</option>
                            @endforeach
                        </select>
                        @error('workId') <span class="nx-error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-form-group">
                        <label class="nx-label">Teklif Numarası <span class="required">*</span></label>
                        <input type="text" wire:model="proposalNumber" class="nx-input" readonly style="background: var(--nx-bg-tertiary); cursor: not-allowed;">
                        @error('proposalNumber') <span class="nx-error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-form-group">
                        <label class="nx-label">Geçerlilik Son Tarih <span class="required">*</span></label>
                        <input type="date" wire:model="validUntil" class="nx-input">
                        @error('validUntil') <span class="nx-error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Line Items Table Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px;">
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); display: flex; align-items: center; gap: 6px; margin: 0;">
                        <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-accent);">list_alt</span>
                        Teklif Kalemleri
                    </h3>
                    <button type="button" wire:click="addItem" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                        Kalem Ekle
                    </button>
                </div>

                {{-- Table --}}
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--nx-border); text-align: left; color: var(--nx-text-secondary); font-weight: 600;">
                            <th style="padding: 10px 5px;">Hizmet/Ürün Açıklaması</th>
                            <th style="padding: 10px 5px; width: 80px;">Miktar</th>
                            <th style="padding: 10px 5px; width: 130px;">Birim Fiyat</th>
                            <th style="padding: 10px 5px; width: 100px;">KDV (%)</th>
                            <th style="padding: 10px 5px; width: 120px; text-align: right;">Toplam (KDV Dahil)</th>
                            <th style="padding: 10px 5px; width: 40px; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr style="border-bottom: 1px solid var(--nx-border);">
                                <td style="padding: 8px 5px;">
                                    <input type="text" wire:model.blur="items.{{ $index }}.description" class="nx-input" placeholder="Hizmet veya ürün detayını yazın..." style="font-size: 12px; padding: 6px 10px;">
                                    @error("items.{$index}.description") <span class="nx-error-text" style="font-size:10px;">Açıklama girilmelidir.</span> @enderror
                                </td>
                                <td style="padding: 8px 5px;">
                                    <input type="number" step="any" wire:model.live="items.{{ $index }}.quantity" class="nx-input" style="font-size: 12px; padding: 6px 10px; text-align: center;">
                                    @error("items.{$index}.quantity") <span class="nx-error-text" style="font-size:10px;">Hatalı</span> @enderror
                                </td>
                                <td style="padding: 8px 5px;">
                                    <input type="number" step="any" wire:model.live="items.{{ $index }}.unit_price" class="nx-input" style="font-size: 12px; padding: 6px 10px;">
                                    @error("items.{$index}.unit_price") <span class="nx-error-text" style="font-size:10px;">Hatalı</span> @enderror
                                </td>
                                <td style="padding: 8px 5px;">
                                    <select wire:model.live="items.{{ $index }}.vat_rate" class="nx-select" style="font-size: 12px; padding: 6px 10px;">
                                        <option value="0">%0 KDV</option>
                                        <option value="1">%1 KDV</option>
                                        <option value="10">%10 KDV</option>
                                        <option value="20">%20 KDV</option>
                                    </select>
                                </td>
                                <td style="padding: 8px 5px; text-align: right; font-weight: 600; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">
                                    @php
                                        $qty = (float)($item['quantity'] ?? 0);
                                        $price = (float)($item['unit_price'] ?? 0);
                                        $vat = (float)($item['vat_rate'] ?? 0);
                                        $tot = $qty * $price * (1 + ($vat/100));
                                    @endphp
                                    {{ number_format($tot, 2, ',', '.') }}
                                </td>
                                <td style="padding: 8px 5px; text-align: center;">
                                    <button type="button" wire:click="removeItem({{ $index }})" class="nx-btn-icon" style="color: #ef4444;" title="Kalemi Kaldır">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Notes & Terms --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-accent);">description</span>
                    Notlar & Koşullar
                </h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="nx-form-group">
                        <label class="nx-label">Teklif Notları (Müşteriye İletilecek)</label>
                        <textarea wire:model="notes" class="nx-textarea" rows="3" placeholder="Teklif içeriğine eklenecek özel notlar..."></textarea>
                    </div>
                    <div class="nx-form-group">
                        <label class="nx-label">Sözleşme / Hukuki Koşullar</label>
                        <textarea wire:model="terms" class="nx-textarea" rows="3" placeholder="Geçerlilik şartları, ödeme koşulları vb. standart maddeler..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel: Totals & Save Actions --}}
        <div style="display: flex; flex-direction: column; gap: 24px; position: sticky; top: 20px;">
            
            {{-- Summary Totals Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-accent);">payments</span>
                    Finansal Özet
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px; color: var(--nx-text-secondary);">
                    
                    <div class="nx-form-group" style="margin-bottom: 8px;">
                        <label class="nx-label">Para Birimi</label>
                        <select wire:model.live="currency" class="nx-select" style="font-size: 12px; padding: 6px 10px;">
                            <option value="TRY">TRY (₺)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                        </select>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--nx-border); padding-bottom: 6px;">
                        <span>Ara Toplam</span>
                        <span style="font-weight: 600; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">
                            {{ number_format($subtotal, 2, ',', '.') }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--nx-border); padding-bottom: 6px;">
                        <span>KDV Toplamı</span>
                        <span style="font-weight: 600; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">
                            {{ number_format($taxTotal, 2, ',', '.') }}
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 4px; border-bottom: 1px dashed var(--nx-border); padding-bottom: 6px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>İndirim Tutarı</span>
                            <span style="font-weight: 600; color: #ef4444; font-family: var(--nx-font-mono);">
                                -{{ number_format($discountTotal, 2, ',', '.') }}
                            </span>
                        </div>
                        <input type="number" step="any" wire:model.live="discountTotal" class="nx-input" style="font-size: 11px; padding: 4px 8px; text-align: right; width: 100px; align-self: flex-end;" placeholder="İndirim Girin">
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; color: var(--nx-text-primary); padding-top: 6px;">
                        <span>Genel Toplam</span>
                        <span style="color: var(--nx-accent); font-family: var(--nx-font-mono);">
                            @php
                                $currencySymbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                                $sym = $currencySymbols[$currency] ?? '₺';
                            @endphp
                            {{ $sym }}{{ number_format($grandTotal, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions Panel --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                @if($proposalId)
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <a href="/admin/proposals/{{ $proposalId }}/view" target="_blank" class="nx-btn nx-btn-secondary" style="display: inline-flex; justify-content: center; align-items: center; gap: 6px; text-decoration: none; border: 1px solid var(--nx-border);">
                            <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            Önizle
                        </a>
                        <a href="/admin/reports/proposal/{{ $proposalId }}?t={{ time() }}" target="_blank" class="nx-btn nx-btn-secondary" style="display: inline-flex; justify-content: center; align-items: center; gap: 6px; text-decoration: none; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">
                            <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                            PDF
                        </a>
                    </div>
                @endif
                <button type="button" wire:click="saveProposal('draft')" class="nx-btn nx-btn-secondary" style="width: 100%; display: inline-flex; justify-content: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                    Taslak Olarak Kaydet
                </button>
                <button type="button" wire:click="saveProposal('pending_approval')" class="nx-btn nx-btn-secondary" style="width: 100%; display: inline-flex; justify-content: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">send</span>
                    Onaya Gönder
                </button>
                @if(auth()->user()->hasAnyRole(['webmaster', 'admin', 'pm']) || auth()->user()->tenant_id === null)
                    <button type="button" wire:click="saveProposal('approved_internal')" class="nx-btn" style="width: 100%; display: inline-flex; justify-content: center; gap: 6px; background: linear-gradient(135deg, #059669, #10b981); color: white; border: none;">
                        <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                        Doğrudan Onayla
                    </button>
                    <button type="button" wire:click="saveProposal('sent')" class="nx-btn nx-btn-primary" style="width: 100%; display: inline-flex; justify-content: center; gap: 6px;">
                        <span class="material-symbols-outlined" style="font-size: 18px;">outgoing_mail</span>
                        Müşteriye Gönder (Yayınla)
                    </button>
                @endif
            </div>

        </div>
    </form>
</div>
