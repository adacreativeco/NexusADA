<div style="animation: fadeSlideIn 0.3s ease-out;">
    <div style="margin-bottom: 32px;">
        <h1 style="font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.02em; font-family: 'Geist', sans-serif;">Faturalarınız</h1>
        <p style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">Firmanız adına kesilen faturalar ve ödeme kayıtları.</p>
    </div>

    <div class="client-card" style="overflow-x: auto; padding: 0; border: 1px solid var(--border);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.01);">
                    <th style="padding: 16px 20px; text-align: left; font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Fatura Numarası</th>
                    <th style="padding: 16px 20px; text-align: left; font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Düzenlenme Tarihi</th>
                    <th style="padding: 16px 20px; text-align: left; font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Ödeme Durumu</th>
                    <th style="padding: 16px 20px; text-align: right; font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Fatura Tutarı</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr style="border-bottom: 1px solid var(--border); transition: var(--transition);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 20px; font-weight: 600; color: #fff; font-family: 'Geist Mono', monospace;">
                            {{ $invoice->income_number }}
                        </td>
                        <td style="padding: 16px 20px; color: var(--text-secondary);">
                            {{ $invoice->created_at->format('d.m.Y') }}
                        </td>
                        <td style="padding: 16px 20px;">
                            @php
                                $stColors = [
                                    'paid' => ['#10b981', 'rgba(16, 185, 129, 0.1)'], 
                                    'pending' => ['#f59e0b', 'rgba(245, 158, 11, 0.1)'], 
                                    'overdue' => ['#ef4444', 'rgba(239, 68, 68, 0.1)'], 
                                    'draft' => ['#9ca3af', 'rgba(156, 163, 175, 0.1)']
                                ];
                                $stLabels = [
                                    'paid' => 'Ödendi', 
                                    'pending' => 'Bekliyor', 
                                    'overdue' => 'Gecikmiş', 
                                    'draft' => 'Taslak'
                                ];
                                $currentSt = $stColors[$invoice->status] ?? ['#9ca3af', 'rgba(156, 163, 175, 0.1)'];
                            @endphp
                            <span style="font-size: 10px; padding: 4px 10px; border-radius: 6px; background: {{ $currentSt[1] }}; color: {{ $currentSt[0] }}; border: 1px solid {{ $currentSt[0] }}25; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $stLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </td>
                        <td style="padding: 16px 20px; text-align: right; font-weight: 700; color: var(--primary); font-size: 14px; font-family: 'Geist Mono', monospace;">
                            ₺{{ number_format($invoice->grand_total ?? 0, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: var(--text-secondary);">
                            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--border); margin-bottom: 12px; display: inline-block;">receipt_long</span>
                            <p style="font-weight: 600;">Henüz fatura kaydı bulunmuyor.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
