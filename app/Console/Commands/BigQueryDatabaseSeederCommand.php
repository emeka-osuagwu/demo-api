<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/*
|--------------------------------------------------------------------------
| Package Namespace
|--------------------------------------------------------------------------
*/
use Exception;
use App\Services\UserService;
use App\Services\TeamService;
use App\Services\GameService;
use App\Services\EventService;
use App\Services\LevelService;
use App\Services\RewardService;
use App\Services\PuzzlesService;
use App\Exceptions\AppException;
use App\Services\ProductService;
use App\Services\TransactionService;
use App\Services\TeamPlayerPointService;
use App\Services\TeamPlayerService;


class BigQueryDatabaseSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bigquery_db:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sofri bigQuery database seeder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected UserService $userService,
        protected TeamService $teamService,
        protected GameService $gameService,
        protected LevelService $levelService,
        protected EventService $eventService,
        protected RewardService $rewardService,
        protected PuzzlesService $puzzlesService,
        protected ProductService $productService,
        protected TeamPlayerService $teamPlayerService,
        protected TransactionService $transactionService,
        protected TeamPlayerPointService $teamPlayerPointService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLogger('Running schedules');
        $this->runTableMigration();
        systemLogger('Done schedules');
    }

    /*
    |--------------------------------------------------------------------------
    | Seed The migration
    |--------------------------------------------------------------------------
    */
    public function runTableMigration()
    {
        // $this->createUserSeeder();
        // $this->createTeamSeeder();
        // $this->createProductSeeder();
        // $this->createPuzzlesSeeder();
        // $this->createTransactionSeeder();
        $this->createEventLeaderboardSeeder();

        // $this->createGameSeeder(); // we dont need this
        // $this->createLevelSeeder(); // we dont need this
        // $this->createRewardSeeder(); // we dont need this
    }

    /*
    |--------------------------------------------------------------------------
    | User Seeder
    |--------------------------------------------------------------------------
    */
    public function createUserSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $users = [
            [
                "email" => "user_1@sample.com",
                "player_id" => "P11111",
                "authorization_token" => "P11111",
                "authorization_provider" => "google",
            ],
            [
                "email" => "user_2@gmail.com",
                "player_id" => "P22222",
                "authorization_token" => "P22222",
                "authorization_provider" => "google",
            ]
        ];

        /*
        |--------------------------------------------------------------------------
        | create user
        |--------------------------------------------------------------------------
        */
        foreach ($users as $payload) {
            try {
                $this->userService->create([
                    ...$payload,
                    "id" => generateUUID(),
                    'jara' => '0',
                    "begi" => '0',
                    'score' => '0',
                    'level' => '',
                    "juju" => '0',
                    'totem' => '500',
                    'points' => '0',
                    "cowries" => "0",
                    'game_won' => '0',
                    "password" => "0",
                    'device_id' => generateUUID(),
                    "full_name" => "able heart",
                    "giraffing" => '0',
                    "push_token" => "987654321",
                    "created_at" => now()->toDateTimeString(),
                    "updated_at" => now()->toDateTimeString(),
                    'game_played' => '0',
                    'highest_score' => '0',
                    'average_score' => '0',
                    'current_streak' => '0',
                    'longest_streak' => '0',
                    "padi_play_wins" => "0",
                    "padi_play_losses" => "0",
                    "completed_puzzles" => [],
                    "completed_puzzle_levels" => []
                ]);
                consoleLogger('user created successful');
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('user created failed');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Products Seeder
    |--------------------------------------------------------------------------
    */
    public function createProductSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            [
                'id' => generateUUID(),
                "name" => "smallie",
                'amount' => 1800,
                'status' => 'active',
                "content" => ["begi" => 1, "giraffing" => 1, "juju" => 1, "totem" => 1, "cowries" => 1200],
                "image_url" => "https://res.cloudinary.com/arm/image/upload/v1710188373/sabinus/product_image/smallie.png",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                "value_type" => 'Sure value',
                'description' => ""
            ],
            [
                'id' => generateUUID(),
                "name" =>  "oga",
                'amount' => 3200,
                'status' => 'active',
                "content" => ["begi" => 2, "giraffing" => 2, "juju" => 2, "totem" => 2, "cowries" => 2700],
                "image_url" => "https://res.cloudinary.com/arm/image/upload/v1710160350/sabinus/product_image/oga.png",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                "value_type" => 'popular jingo',
                'description' => ""
            ],
            [
                'id' => generateUUID(),
                "name" => "presido",
                'amount' => 10500,
                'status' => 'active',
                "content" => ["begi" => 8, "giraffing" => 8, "juju" => 8, "totem" => 8, "cowries" => 12000],
                "image_url" => "https://res.cloudinary.com/arm/image/upload/v1710188258/sabinus/product_image/presido.png",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                "value_type" => 'beta value',
                'description' => ""
            ],
            [
                'id' => generateUUID(),
                "name" => "chairman",
                'amount' => 19500,
                'status' => 'active',
                "content" => ["begi" => 16, "giraffing" => 16, "juju" => 16, "totem" => 16, "cowries" => 26000],
                "image_url" => "https://res.cloudinary.com/arm/image/upload/v1710160505/sabinus/product_image/chairman.png",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                "value_type" =>  'yakata value',
                'description' => ""
            ],
            [
                'id' => generateUUID(),
                "name" => "jagaban",
                'amount' => 50000,
                'status' => 'active',
                "content" => ["begi" => 55, "giraffing" => 55, "juju" => 55, "totem" => 55, "cowries" => 82000],
                "image_url" => "https://res.cloudinary.com/arm/image/upload/v1710160420/sabinus/product_image/jagaban.png",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                "value_type" =>  'gbosa value',
                'description' => ""
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | create product
        |--------------------------------------------------------------------------
        */
        foreach ($payload as $product) {
            try {
                $product = $this->productService->create($product);
                consoleLogger('user created successful');
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('user created failed');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Transaction Seeder
    |--------------------------------------------------------------------------
    */
    public function createTransactionSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            [
                "id" => generateUUID(),
                'amount' => '50',
                'status' => 'active',
                'purchase' => 'in_store_purchase',
                'player_id' => 'P11111',
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
                'payment_method' => 'card',
                'payment_channel' => 'card',
                'payment_method_id' => '132',
                'transaction_reference' => '32153',
                'provider_transaction_reference' => generateUUID()
            ],
            [
                "id" => generateUUID(),
                'amount' => '50',
                'status' => 'active',
                'purchase' => 'in_store_purchase',
                'player_id' => 'P11111',
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
                'payment_method' => 'card',
                'payment_channel' => 'card',
                'payment_method_id' => '123',
                'transaction_reference' => '32153',
                'provider_transaction_reference' => generateUUID(),
            ],
            [
                "id" => generateUUID(),
                'amount' => '50',
                'status' => 'active',
                'purchase' => 'in_store_purchase',
                'player_id' => 'P22222',
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
                'payment_method' => 'card',
                'payment_channel' => 'card',
                'payment_method_id' => '127',
                'transaction_reference' => '3215398',
                'provider_transaction_reference' => generateUUID(),
            ]

        ];

        /*
        |--------------------------------------------------------------------------
        | create transaction
        |--------------------------------------------------------------------------
        */
        foreach ($payload as $key => $value) {
            try {
                $transaction = $this->transactionService->create($value);
                consoleLogger('user created successful');
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('user created failed');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Level Seeder
    |--------------------------------------------------------------------------
    */
    public function createLevelSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            [
                'id' => generateUUID(),
                'name' => 'junior_sabinus',
                "status" => "active",
                "level_number" => "4",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'senior_sabinus',
                "status" => "active",
                "level_number" => "5",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'senior_sabinus',
                "status" => "active",
                "level_number" => "6",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'oga_pikin',
                "status" => "active",
                "level_number" => "7",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'correct_pikin',
                "status" => "active",
                "level_number" => "8",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'serious-persin',
                "status" => "active",
                "level_number" => "9",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ],
            [
                'id' => generateUUID(),
                'name' => 'area_persin',
                "status" => "active",
                "level_number" => "10",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
            ]
        ];

        /*
        |--------------------------------------------------------------------------
        | create level
        |--------------------------------------------------------------------------
        */
        try {
            foreach ($payload as $key => $value) {
                $level = $this->levelService->create($value);
            }
            consoleLogger('user created successful');
        } catch (AppException $exception) {
            report($exception);
            consoleLogger('user created failed');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Puzzles Seeder
    |--------------------------------------------------------------------------
    */
    public function createPuzzlesSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $levels = [
            [
                'id' => generateUUID(),
                'name' => 'johnny just come',
                "status" => "active",
                'description' => "JJC",
                "level_number" => "1",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'puzzles' => [
                    [
                        'id' => generateUUID(),
                        'word' => 'Abeg',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Abi',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'what',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Yamayama',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'ABU',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Acata',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Adire',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'not available',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Adonkia',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Afang',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'whats going on',
                        'level_number' => "4",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ansa',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Afta',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'plenty',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Agaracha',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'surplus',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Agbada',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'hex',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Agbepo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'stop smoking',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Agbero',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'alcohol',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ah-ah',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'weed',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'papa',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'father',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Aircon',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'mother',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ajasco',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'boy',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ajebota',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'play',
                        'level_number' => "10",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ajepako',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'his father warned him',
                        'level_number' => "10",
                    ],
                ]
            ],
            [
                'id' => generateUUID(),
                'name' => 'smallie',
                "status" => "active",
                'description' => "JJC",
                "level_number" => "2",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'puzzles' => [
                    [
                        'id' => generateUUID(),
                        'word' => 'ewee',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'watin',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'what',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'wahala',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'chai',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'ewee',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Yarnsh',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'not available',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'gbege',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Winchi',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'whats going on',
                        'level_number' => "4",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'palava',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'brekete',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'plenty',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Wa-zo-bia',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'surplus',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'juju',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'hex',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Wayo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'stop smoking',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'ogogoro',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'alcohol',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'ganja',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'weed',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'papa',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'father',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'mama',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'mother',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'aboy',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'boy',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'cruise',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'play',
                        'level_number' => "10",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Waya',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'his father warned him',
                        'level_number' => "10",
                    ],
                ]
            ],
            [
                'id' => generateUUID(),
                'name' => 'i dey count bridge',
                "status" => "active",
                'description' => "JJC",
                "level_number" => "3",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'puzzles' => [
                    [
                        'id' => generateUUID(),
                        'word' => 'Akara',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Amebo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'what',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Amugbo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Anoda',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Apkroko',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Apku',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'not available',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Apollo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Wado',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'whats going on',
                        'level_number' => "4",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Vibrate',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Aromental',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'plenty',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ashewo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'surplus',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'At-all',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'hex',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Awoof',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'stop smoking',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Yarnsh',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'alcohol',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Vess',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'weed',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bakassi',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'father',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Venue',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'mother',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bam',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'boy',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Banga',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'play',
                        'level_number' => "10",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Barawo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'his father warned him',
                        'level_number' => "10",
                    ],
                ]
            ],
            [
                'id' => generateUUID(),
                'name' => 'junior_sabinus',
                "status" => "active",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
                "level_number" => "4",
                'puzzles' => [
                    [
                        'id' => generateUUID(),
                        'word' => 'Basia',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Battalion',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'what',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'yabaleft',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Begbeg',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Hanlele',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Vamoose',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'not available',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bellefull',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bellesweet',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'whats going on',
                        'level_number' => "4",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Unilag',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'brekete',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'plenty',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Okrika',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'surplus',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Biforbifor',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'hex',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Uniben',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'stop smoking',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Ukodo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'alcohol',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Trousa',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'weed',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Tuffia',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'father',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bingo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'mother',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Truetrue',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'boy',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Traficate',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'play',
                        'level_number' => "10",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bleach',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'his father warned him',
                        'level_number' => "10",
                    ],
                ]
            ],
            [
                'id' => generateUUID(),
                'name' => 'senior_sabinus',
                "status" => "active",
                "level_number" => "5",
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'description' => "",
                'puzzles' => [
                    [
                        'id' => generateUUID(),
                        'word' => 'Tiro',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Blomblow',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'what',
                        'level_number' => "1",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bobbi',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bobo',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "2",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Tinigboko',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Throway',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'not available',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Boli',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "3",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bomboy',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'whats going on',
                        'level_number' => "4",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bone',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'problem',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Tey',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'plenty',
                        'level_number' => "5",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Borkotor',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'surplus',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Borku',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'hex',
                        'level_number' => "6",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Taya',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'stop smoking',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Borrowborrow',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'alcohol',
                        'level_number' => "7",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Bottompot',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'weed',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Tanda',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'father',
                        'level_number' => "8",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Branch',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'mother',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Break Kola',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'boy',
                        'level_number' => "9",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Brokkus',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'play',
                        'level_number' => "10",
                    ],
                    [
                        'id' => generateUUID(),
                        'word' => 'Broda',
                        "status" => "active",
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'description' => 'his father warned him',
                        'level_number' => "10",
                    ],
                ]
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | create puzzle
        |--------------------------------------------------------------------------
        */
        foreach ($levels as $key => $value) {
            $level = $this->levelService->create([
                'id' => $value['id'],
                'name' => $value['name'],
                "status" => $value['status'],
                'created_at' => $value['created_at'],
                'updated_at' => $value['updated_at'],
                'description' => $value['description'],
                "level_number" => $value['level_number'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | create puzzle
            |--------------------------------------------------------------------------
            */
            foreach ($value['puzzles'] as $key => $puzzle) {
                $puzzle['level_id'] = $level['response']['id'];
                $puzzlesService = $this->puzzlesService->create($puzzle);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reward Seeder
    |--------------------------------------------------------------------------
    */
    public function createRewardSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            'type' => 'bonus',
            'name' => 'bonus',
            'category' => 'jara',
            'id' => generateUUID(),
            'value' => 'view-bonus',
            'created_at' => 'nov-2023',
            'updated_at' => 'nov-2023',
        ];

        /*
        |--------------------------------------------------------------------------
        | create reward
        |--------------------------------------------------------------------------
        */
        try {
            $reward = $this->rewardService->create($payload);
            consoleLogger('reward created successful');
        } catch (AppException $exception) {
            report($exception);
            consoleLogger('user created failed');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Team Seeder
    |--------------------------------------------------------------------------
    */
    public function createTeamSeeder()
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            [
                'id' => generateUUID(),
                'owner_id' => generateUUID(),
                'event_id' => generateUUID(),
                'team_name' => 'Blue Team',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => generateUUID(),
                'owner_id' => generateUUID(),
                'event_id' => generateUUID(),
                'team_name' => 'Red Team',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]
        ];

        /*
        |--------------------------------------------------------------------------
        | create team
        |--------------------------------------------------------------------------
        */
        foreach ($payload as $value) {
            try {
                $team = $this->teamService->create($value);
                consoleLogger('team created successful');
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('user created failed');
            }
        }

        // create team player 
    }

    /*
    |--------------------------------------------------------------------------
    | Game Seeder
    |--------------------------------------------------------------------------
    */
    public function createGameSeeder()
    {
        $payload = [
            'id' => generateUUID(),
            "winner" => "9KVGVjVAZi",
            "player_1" => "7Om97w3obX",
            "player_2" => "9KVGVjVAZi",
            'completed' => true,
            "game_time" => 90,
            "session_id" => generateUUID(),
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            "player_1_games" => ["29f6e398-973b-46e3-b854-5aefab74e1f7"],
            "player_2_games" => ["b9ea6f00-f985-4f2c-860c-d0a03a835ee4", "f994303a-8a05-4cf5-92e7-aa1fe426646b"],
            "challenge_accepted" => true,
            "player_1_completed" => true,
            "player_2_completed" => true,
        ];

        /*
        |--------------------------------------------------------------------------
        | create game
        |--------------------------------------------------------------------------
        */
        try {
            $game = $this->gameService->create($payload);
            consoleLogger('game created successful');
        } catch (AppException $exception) {
            report($exception);
            consoleLogger('game created failed');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Event Leaderboard Seeder
    |--------------------------------------------------------------------------
    */
    public function createEventLeaderboardSeeder()
    {
        $events = [
            [
                'id' => generateUUID(),
                "title" => "Event 1", // Change to unique titles
                "status" => "active",
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
            ],
            [
                'id' => generateUUID(),
                "title" => "Event 2", // Change to unique titles
                "status" => "active",
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
            ],
        ];

        foreach ($events as $event) {
            try {
                $created_event = $this->eventService->create($event);
                $game = $this->createGame($created_event);
                $users = $this->createUsersAndPlayers($created_event);

                consoleLogger('Event created successfully');
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('Event creation failed');
            }
        }
    }

    private function createGame($event)
    {
        $game_data = [
            'id' => generateUUID(),
            "winner" => "9KVGVjVAZi",
            "player_1" => "7Om97w3obX",
            "player_2" => "9KVGVjVAZi",
            'completed' => true,
            "game_time" => 90,
            "event_id" => $event['response']['id'],
            "session_id" => generateUUID(),
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            "player_1_games" => ["29f6e398-973b-46e3-b854-5aefab74e1f7"],
            "player_2_games" => ["b9ea6f00-f985-4f2c-860c-d0a03a835ee4", "f994303a-8a05-4cf5-92e7-aa1fe426646b"],
            "challenge_accepted" => true,
            "player_1_completed" => true,
            "player_2_completed" => true,
        ];

        return $this->gameService->create($game_data);
    }

    private function createUsersAndPlayers($event)
    {
        $users = [
            [
                "email" => "user_1@sample.com",
                "auth_id" => "P11111",
                "role" => "user",
                "authorization_token" => "P11111",
                "authorization_provider" => "google",
            ],
            [
                "email" => "user_2@gmail.com",
                "auth_id" => "P22222",
                "role" => "user",
                "authorization_token" => "P22222",
                "authorization_provider" => "google",
            ]
        ];

        foreach ($users as $user) {
            try {
                $created_user = $this->createUser($user);
                $team = $this->createTeam($created_user, $event);
                $this->createPlayers($team);
            } catch (AppException $exception) {
                report($exception);
                consoleLogger('User creation failed');
            }
        }
    }

    private function createUser($userData)
    {
        $userData['id'] = generateUUID();
        $userData['jara'] = '0';
        $userData["begi"] = '0';
        $userData['score'] = (string) rand(1, 10);
        $userData['level'] = '';
        $userData["juju"] = '0';
        $userData['totem'] = '500';
        $userData['points'] = (string) rand(1, 10);
        $userData["cowries"] = "0";
        $userData['game_won'] = '0';
        $userData["password"] = "0";
        $userData['player_id'] = generateUUID();
        $userData['device_id'] = generateUUID();
        $userData["full_name"] = "able heart";
        $userData["giraffing"] = '0';
        $userData["push_token"] = "987654321";
        $userData["created_at"] = now()->toDateTimeString();
        $userData["updated_at"] = now()->toDateTimeString();
        $userData['game_played'] = '0';
        $userData['highest_score'] = '0';
        $userData['average_score'] = '0';
        $userData['current_streak'] = '0';
        $userData['longest_streak'] = '0';
        $userData["padi_play_wins"] = "0";
        $userData["padi_play_losses"] = "0";
        // $userData["completed_puzzles"] = "";
        // $userData["completed_puzzle_levels"] = "";

        return $this->userService->create($userData);
    }

    private function createTeam($user, $event)
    {
        $team_names = ['Blue Team', 'Red Team', 'Green Team', 'Yellow Team'];

        $teamData =[
            'id' => generateUUID(),
            'owner_id' => $user['response']['id'],
            'event_id' => $event['response']['id'],
            'team_name' => $team_names[array_rand($team_names)],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        return $this->teamService->create($teamData);
    }

    private function createPlayers($team)
    {
        $players_data = [
            [
                "email" => "player_1@sample.com", 
                "player_id" => generateUUID(),
                "role" => "user",
                "authorization_token" => "P11111", 
                "authorization_provider" => "google"
            ],
            [
                "email" => "player_2@gmail.com",
                "player_id" => generateUUID(),
                "role" => "user",
                "authorization_token" => "P22222",
                "authorization_provider" => "google"
            ],
            [
                "email" => "player_3@gmail.com",
                "player_id" => generateUUID(), 
                "role" => "user",
                "authorization_token" => "P22222", 
                "authorization_provider" => "google"
            ],
            [
                "email" => "player_4@gmail.com", 
                "player_id" => generateUUID(), 
                "role" => "user",
                "authorization_token" => generateUUID(), 
                "authorization_provider" => "google"
            ],
            [
                "email" => "player_5@gmail.com", 
                "player_id" => generateUUID(), 
                "role" => "user",
                "authorization_token" => "P22222", 
                "authorization_provider" => "google"
            ],
        ];

        foreach ($players_data as $player_data) {
            $player_data['id'] = generateUUID();
            $player_data['score'] = (string) rand(1, 10);
            $player_data['points'] = (string) rand(1, 10);
            $player_data['device_id'] = generateUUID();
            $player_data['created_at'] = now()->toDateTimeString();
            $player_data['updated_at'] = now()->toDateTimeString();

            $player = $this->userService->create($player_data);

            $team_player_data = [
                'id' => generateUUID(),
                'team_id' => $team['response']['id'],
                'player_id' => (string) $player['response']['player_id'],
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            $this->teamPlayerService->create($team_player_data);
        }
    }
}
