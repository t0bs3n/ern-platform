<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Conference;
use App\Models\News;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\ApplicationSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\MemberSeeder;
use Database\Seeders\NoticeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['ern.seed_password' => 'a-local-test-password']);
    }

    public function test_the_seeder_runs_on_an_empty_database(): void
    {
        $this->assertDatabaseCount('users', 0);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(28, User::count());
    }

    public function test_there_is_one_test_user_per_role_with_the_address_pattern(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (Role::cases() as $role) {
            $user = User::where('email', $role->value.'@example.test')->first();

            $this->assertNotNull($user, "No test user for {$role->value}");
            $this->assertTrue($user->isApproved());
            $this->assertTrue($user->hasRole($role));
            $this->assertTrue($user->hasRole(Role::Member));
            $this->assertSame('Test', $user->first_name);
        }

        $this->assertTrue(Gate::forUser(User::where('email', 'database_lead@example.test')->first())->allows('manage-database'));
        $this->assertFalse(Gate::forUser(User::where('email', 'member@example.test')->first())->allows('open-admin-area'));
    }

    public function test_test_users_sign_in_with_the_local_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/members/sign-in', ['email' => 'editor@example.test', 'password' => 'a-local-test-password'])
            ->assertRedirect('/members');
        $this->assertAuthenticated();
    }

    public function test_twenty_invented_members_start_with_the_mockup_people(): void
    {
        $this->seed(MemberSeeder::class);

        $this->assertSame(20, User::count());
        $maja = User::where('email', 'maja.lindqvist@example.test')->first();
        $this->assertSame('Nordic University (example)', $maja->institution);
        $this->assertTrue($maja->hasRole(Role::Admin));
        $this->assertSame(
            ['Maja Lindqvist', 'Rafael Monteiro', 'Ji-ho Baek', 'Amara Okafor', 'Lukas Brandt', 'Priya Raman', 'Aiko Tanabe', 'Elif Demir'],
            User::orderBy('id')->limit(8)->get()->map->full_name->all()
        );

        // The roles of the mockup's rights editor (ROLE0); everyone else is a plain member unless given a role.
        $expected = [
            'maja.lindqvist@example.test' => ['admin', 'member'],
            'amara.okafor@example.test' => ['database_lead', 'member', 'reviewer'],
            'lukas.brandt@example.test' => ['conference_team', 'final_editor', 'member'],
            'aiko.tanabe@example.test' => ['member', 'reviewer'],
            'rafael.monteiro@example.test' => ['member'],
        ];
        foreach ($expected as $email => $roles) {
            $held = User::where('email', $email)->first()->roles->map(fn ($r) => $r->role->value)->sort()->values()->all();
            $this->assertSame($roles, $held, $email);
        }
        $this->assertGreaterThanOrEqual(10, User::distinct()->count('country'));
        $this->assertGreaterThanOrEqual(5, User::distinct()->count('career_stage'));
        $this->assertTrue(User::where('listed_in_directory', false)->exists());
    }

    public function test_every_institution_carries_example_and_every_address_is_a_test_address(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (User::all() as $user) {
            $this->assertStringEndsWith('(example)', $user->institution, $user->email);
            $this->assertStringEndsWith('@example.test', $user->email);
            $this->assertTrue(in_array($user->career_stage, User::CAREER_STAGES, true), $user->email);
        }
    }

    public function test_seeding_twice_creates_no_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(28, User::count());
    }

    public function test_the_seeder_refuses_in_production(): void
    {
        $this->app['env'] = 'production';

        // Even with --force, which skips the framework's own question in production.
        try {
            $this->artisan('db:seed', ['--force' => true]);
            $this->fail('The seeder ran in production.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('never in production or on staging', $e->getMessage());
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_a_single_seeder_refuses_in_production_too(): void
    {
        $this->app['env'] = 'production';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('never in production or on staging');
        $this->app->make(MemberSeeder::class)->setContainer($this->app)->__invoke();
    }

    /** @return array<string, array{class-string}> */
    public static function inventedDataSeeders(): array
    {
        return ['applications' => [ApplicationSeeder::class], 'events' => [EventSeeder::class], 'notices' => [NoticeSeeder::class]];
    }

    #[DataProvider('inventedDataSeeders')]
    public function test_each_seeder_with_invented_data_refuses_on_staging_and_live(string $seeder): void
    {
        foreach (['staging', 'production'] as $environment) {
            $this->app['env'] = $environment;
            try {
                $this->app->make($seeder)->setContainer($this->app)->__invoke();
                $this->fail($seeder.' ran on '.$environment);
            } catch (RuntimeException $e) {
                $this->assertStringContainsString('never in production or on staging', $e->getMessage());
            }
        }

        foreach (['membership_applications', 'events', 'notices'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_the_real_site_content_may_be_loaded_on_the_server_and_twice(): void
    {
        $this->app['env'] = 'production';

        $this->app->make(ContentSeeder::class)->setContainer($this->app)->__invoke();
        $counts = [Page::count(), News::count(), Conference::count()];
        $this->app->make(ContentSeeder::class)->setContainer($this->app)->__invoke();

        $this->assertGreaterThan(0, $counts[0]);
        $this->assertSame($counts, [Page::count(), News::count(), Conference::count()]);
    }

    public function test_the_seeder_refuses_on_staging(): void
    {
        $this->app['env'] = 'staging';

        try {
            $this->artisan('db:seed', ['--force' => true]);
            $this->fail('The seeder ran on staging.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('never in production or on staging', $e->getMessage());
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_the_seeder_refuses_without_a_local_password(): void
    {
        config(['ern.seed_password' => '']);

        $this->expectException(RuntimeException::class);
        $this->seed(MemberSeeder::class);
    }
}
