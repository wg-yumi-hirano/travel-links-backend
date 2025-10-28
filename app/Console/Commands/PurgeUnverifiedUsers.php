<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PurgeUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:purge-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '24時間以上未認証のユーザーを物理削除';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $purgeBeforeHours = config('project.command.purge_before_hours', 24);
        $expiredUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subHours($purgeBeforeHours))
            ->get();

        DB::transaction(function () use ($expiredUsers) {
            foreach ($expiredUsers as $user) {
                if ($user->sites()->exists()) {
                    $this->warn("[WARN] ユーザーID {$user->id} は関連レコードを保持しているため削除できません。");
                    continue;
                }

                $user->forceDelete();
            }
        });

        $this->info("[INFO] {$expiredUsers->count()} 件を削除しました。");

        return self::SUCCESS;
    }
}
