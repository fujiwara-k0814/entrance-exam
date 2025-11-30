<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\UserEvaluation;

class UserReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    //ユーザー評価　レビュー平均
    public function test_user_review_average()
    {
        $user = User::find(1);
        UserEvaluation::create([
            'targeter_id' => $user->id,
            'evaluator_id' => 2,
            'score' => 4,
        ]);
        UserEvaluation::create([
            'targeter_id' => $user->id,
            'evaluator_id' => 3,
            'score' => 3,
        ]);

        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        //goodレビュー評価のクラス名をカウント
        $response->assertSee('<span class="review good">', false);
        $html = $response->getContent();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query("//*[contains(@class, 'review good')]");
        $this->assertCount(4, $nodes);
    }
}
