<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataExportService;

class KvkkController extends Controller
{
    public function export(Request $request, DataExportService $service)
    {
        $data = $service->exportUserData($request->user());
        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="kvkk_export.json"'
        ]);
    }

    public function anonymize(Request $request, DataExportService $service)
    {
        $user = $request->user();
        $service->anonymizeUser($user);
        
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Hesabınız KVKK kapsamında başarıyla anonimleştirildi.');
    }
}
