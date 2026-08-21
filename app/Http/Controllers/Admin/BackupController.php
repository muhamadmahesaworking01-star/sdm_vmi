<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use ZipArchive;
class BackupController extends Controller {
    public function download() {
        $dir = storage_path('app/private'); if (! is_dir($dir)) mkdir($dir, 0750, true);
        $file = $dir.'/backup-sdm-'.now()->format('Ymd-His').'.zip'; $zip = new ZipArchive; $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach (['users','employees','announcements','specializations','employee_documents','activity_logs'] as $table) { try { $zip->addFromString($table.'.json', json_encode(DB::table($table)->get(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); } catch (\Throwable) {} }
        $zip->addFromString('manifest.json', json_encode(['created_at'=>now()->toIso8601String(),'format'=>'json-table-backup-v1'], JSON_PRETTY_PRINT)); $zip->close();
        return response()->download($file)->deleteFileAfterSend(true);
    }
}
