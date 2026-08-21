<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'route', 'method', 'ip_address', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getRecapLastDays($days = 3)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        return self::where('created_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get()
            ->toArray();
    }

    public static function cleanupOlderThan($days = 3): int
    {
        return self::where('created_at', '<', Carbon::now()->subDays($days))->delete();
    }
}
