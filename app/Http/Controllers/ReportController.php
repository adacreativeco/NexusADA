<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Helper to retrieve logo path/url and read SVG raw data for DomPDF compliance.
     */
    private function getLogoData($tenantId): array
    {
        // 1. Try to find the logo in BrandAsset (Marka Varlıkları) first
        $brandLogo = \App\Models\BrandAsset::where('tenant_id', $tenantId)
            ->where('type', 'logo')
            ->latest()
            ->first();

        $logoUrl = null;
        if ($brandLogo && is_array($brandLogo->files) && !empty($brandLogo->files)) {
            $logoUrl = $brandLogo->files[0] ?? null;
        }

        // 2. Fall back to Asset (Varlıklar) table if not found in BrandAsset
        if (!$logoUrl) {
            $logoAsset = \App\Models\Asset::where('tenant_id', $tenantId)
                ->where('type', 'logo')
                ->latest()
                ->first();
            $logoUrl = $logoAsset ? $logoAsset->file_path : null;
        }

        $logoRaw = null;

        if ($logoUrl && str_ends_with(strtolower($logoUrl), '.svg')) {
            $relativePath = str_replace(url('/'), '', $logoUrl);
            $localPath = public_path($relativePath);
            if (!file_exists($localPath)) {
                $cleanPath = str_replace('/storage/', '', $relativePath);
                $localPath = storage_path('app/public/' . $cleanPath);
            }
            if (file_exists($localPath)) {
                $raw = file_get_contents($localPath);
                $logoRaw = preg_replace('/<\?xml.*\?>/i', '', $raw);
                // Ensure SVG has inline style constraints
                if (stripos($logoRaw, 'width=') === false) {
                    $logoRaw = str_ireplace('<svg', '<svg width="200px" height="40px"', $logoRaw);
                } else {
                    // Force replace width/height to look decent in header
                    $logoRaw = preg_replace('/width="[^"]+"/', 'width="200px"', $logoRaw);
                    $logoRaw = preg_replace('/height="[^"]+"/', 'height="40px"', $logoRaw);
                }
            }
        }

        return [
            'logoUrl' => $logoUrl,
            'logoRaw' => $logoRaw,
        ];
    }

    /**
     * Proje PDF Raporu
     */
    public function projectPdf(Project $project)
    {
        try {
            $project->load(['client', 'tenant']);
            $logo = $this->getLogoData($project->tenant_id);

            $pdf = Pdf::loadView('reports.report-project', [
                'project' => $project,
                'tenant' => $project->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'generatedAt' => now()->format('d.m.Y H:i'),
                'generatedBy' => auth()->user()->name ?? 'Sistem',
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("proje-rapor-{$project->id}.pdf");
        } catch (\Exception $e) {
            \Log::error("PDF Generation Error (Project {$project->id}): " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, "PDF oluşturulamadı: " . $e->getMessage());
        }
    }

    /**
     * Müşteri Özet Raporu
     */
    public function clientPdf(Client $client)
    {
        try {
            $client->load(['projects', 'tenant']);
            $logo = $this->getLogoData($client->tenant_id);

            $totalRevenue = $client->projects->sum('actual_revenue');
            $totalBudget = $client->projects->sum('budget');
            $avgProfitability = $client->projects->avg('profitability_score');

            $pdf = Pdf::loadView('reports.report-client', [
                'client' => $client,
                'tenant' => $client->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'totalRevenue' => $totalRevenue,
                'totalBudget' => $totalBudget,
                'avgProfitability' => round($avgProfitability ?? 0),
                'generatedAt' => now()->format('d.m.Y H:i'),
                'generatedBy' => auth()->user()->name ?? 'Sistem',
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("musteri-rapor-{$client->id}.pdf");
        } catch (\Exception $e) {
            \Log::error("PDF Generation Error (Client {$client->id}): " . $e->getMessage());
            abort(500, "PDF oluşturulamadı.");
        }
    }

    /**
     * Kampanya Raporu
     */
    public function campaignPdf(Campaign $campaign)
    {
        try {
            $campaign->load(['department', 'contentItems', 'tenant']);
            $logo = $this->getLogoData($campaign->tenant_id);

            $pdf = Pdf::loadView('reports.report-campaign', [
                'campaign' => $campaign,
                'tenant' => $campaign->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'generatedAt' => now()->format('d.m.Y H:i'),
                'generatedBy' => auth()->user()->name ?? 'Sistem',
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("kampanya-rapor-{$campaign->id}.pdf");
        } catch (\Exception $e) {
            \Log::error("PDF Generation Error (Campaign {$campaign->id}): " . $e->getMessage());
            abort(500, "PDF oluşturulamadı.");
        }
    }

    /**
     * Teklif PDF Raporu
     */
    public function proposalPdf(\App\Models\Proposal $proposal)
    {
        try {
            $proposal->load(['client', 'work', 'creator', 'tenant']);
            $logo = $this->getLogoData($proposal->tenant_id);

            $pdf = Pdf::loadView('reports.proposal-pdf', [
                'proposal' => $proposal,
                'tenant' => $proposal->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'generatedAt' => now()->format('d.m.Y H:i'),
                'generatedBy' => auth()->user()->name ?? 'Sistem',
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("teklif-{$proposal->proposal_number}.pdf");
        } catch (\Exception $e) {
            \Log::error("PDF Generation Error (Proposal {$proposal->id}): " . $e->getMessage());
            abort(500, "PDF oluşturulamadı: " . $e->getMessage());
        }
    }

    /**
     * Teklif HTML Önizleme / Görüntüleme
     */
    public function proposalView(\App\Models\Proposal $proposal)
    {
        try {
            $proposal->load(['client', 'work', 'creator', 'tenant']);
            $logo = $this->getLogoData($proposal->tenant_id);

            return view('reports.proposal-view', [
                'proposal' => $proposal,
                'tenant' => $proposal->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'generatedAt' => now()->format('d.m.Y H:i'),
            ]);
        } catch (\Exception $e) {
            \Log::error("Proposal View Error (Proposal {$proposal->id}): " . $e->getMessage());
            abort(500, "Teklif görüntülenemedi: " . $e->getMessage());
        }
    }

    /**
     * Sözleşme PDF Raporu
     */
    public function contractPdf(\App\Models\Contract $contract)
    {
        try {
            $contract->load(['client', 'work', 'creator', 'tenant']);
            $logo = $this->getLogoData($contract->tenant_id);

            $pdf = Pdf::loadView('reports.contract-pdf', [
                'contract' => $contract,
                'tenant' => $contract->tenant,
                'logoUrl' => $logo['logoUrl'],
                'logoRaw' => $logo['logoRaw'],
                'generatedAt' => now()->format('d.m.Y H:i'),
                'generatedBy' => auth()->user()->name ?? 'Sistem',
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("sozlesme-{$contract->contract_number}.pdf");
        } catch (\Exception $e) {
            \Log::error("PDF Generation Error (Contract {$contract->id}): " . $e->getMessage());
            abort(500, "PDF oluşturulamadı: " . $e->getMessage());
        }
    }
}
