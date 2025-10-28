<?php declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Console\Commands\PurgeUnverifiedUsers;
use App\Models\User;
use App\Models\Site;

class PurgeUnverifiedUsersTest extends TestCase
{
    use RefreshDatabase;

    public function testPositive()
    {
        $now = now();
        $purgeBeforeHours = config('project.command.purge_before_hours', 24);

        // 未認証かつ期限切れ → 削除対象
        $expiredUser = User::factory()
            ->unverified()
            ->create([
                'created_at' => $now->copy()->subHours($purgeBeforeHours)->subMinute(),
            ]);

        // 未認証かつ期限切れかつ関連データあり → 削除対象外
        $linkedUser = User::factory()
            ->unverified()
            ->create([
                'created_at' => $now->copy()->subHours($purgeBeforeHours)->subMinute(),
            ]);
        Site::factory()->create(['user_id' => $linkedUser->id]);

        // 認証済み → 削除対象外
        $verifiedUser = User::factory()->create([
            'created_at' => $now->copy()->subHours($purgeBeforeHours),
        ]);

        // 未認証だが期限内 → 削除対象外
        $pendingUser = User::factory()
            ->unverified()
            ->create([
                'created_at' => $now->copy()->subHours($purgeBeforeHours)->addMinute(),
            ]);

        // 実行
        $output = new BufferedOutput();
        $input = new ArrayInput([]);
        $command = new PurgeUnverifiedUsers();
        $command->setLaravel(app());
        $command->run($input, $output);
        dump($output->fetch());

        // 削除されたか確認
        $this->assertDatabaseMissing('users', ['id' => $expiredUser->id]);

        // 他のユーザーは残っているか確認
        $this->assertDatabaseHas('users', ['id' => $linkedUser->id]);
        $this->assertDatabaseHas('users', ['id' => $verifiedUser->id]);
        $this->assertDatabaseHas('users', ['id' => $pendingUser->id]);
    }
}