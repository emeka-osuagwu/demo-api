<?php

namespace App\Console\Commands;

/*
|--------------------------------------------------------------------------
| Package Namespace
|--------------------------------------------------------------------------
*/

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SchulzeFelix\BigQuery\BigQueryFacade as BigQuery;

/*
|--------------------------------------------------------------------------
| Exceptions Namespace
|--------------------------------------------------------------------------
*/
use Google\Cloud\Core\Exception\ConflictException;
use Google\Cloud\Core\Exception\BadRequestException;

class SofriBigQueryDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bigquery_db:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BigQuery Database Runner';
    protected $database_name;
    
    /*
	|--------------------------------------------------------------------------
	| Schema List
	|--------------------------------------------------------------------------
	*/
    protected $schema = [
        "products" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                ],
                [
                    'name' => 'value_type',
                    'type' => 'string',
                ],
                [
                    'name' => 'amount',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'image_url',
                    'type' => 'string',
                ],

                [
                    'name' => 'content',
                    'type' => 'string',
                ],
                [
                    'name' => 'description',
                    'type' => 'string'
                ],
                [
                    'name' => 'status',
                    'type' => 'string'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "transactions" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'player_id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'amount',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name'=> 'purchase',
                    'type'=> 'string'
                ],
                [
                    'name' => 'payment_channel',
                    'type' => 'string',
                ],
                [
                    'name' => 'transaction_reference',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'provider_transaction_reference',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'payment_method',
                    'type' => 'string'
                ],
                [
                    'name' => 'payment_method_id',
                    'type' => 'string'
                ],
                [
                    'name' => 'status',
                    'type' => 'string'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "puzzles" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'word',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                ],
                [
                    'name' => 'level_id',
                    'type' => 'string'
                ],
                [
                    'name' => 'level_number',
                    'type' => 'string'
                ],
                [
                    'name' => 'puzzle_level',
                    'type' => 'string'
                ],
                [
                    'name' => 'puzzle_sub_level',
                    'type' => 'string'
                ],
                [
                    'name' => 'status',
                    'type' => 'string'
                ],
                [
                    'name' => 'category_id',
                    'type' => 'string'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "users" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => "completed_puzzle_levels",
                    'type' => 'string',
                ],
                [
                    'name' => "image",
                    'type' => 'string',
                ],
                [
                    'name' => 'completed_puzzles',
                    'type' => 'string'
                ],
                [
                    'name' => 'full_name',
                    'type' => 'string'
                ],
                [
                    'name' => 'auth_id',
                    'type' => 'string'
                ],
                [
                    'name' => 'password',
                    'type' => 'string'
                ],
                [
                    'name' => 'role', // user, admin
                    'type' => 'string'
                ],
                [
                    'name' => 'authorization_token',
                    'type' => 'string'
                ],
                [
                    'name' => 'authorization_provider',
                    'type' => 'string'
                ],
                [
                    'name' => 'push_token',
                    'type' => 'string'
                ],
                [
                    'name' => 'juju',
                    'type' => 'string',
                ],
                [
                    'name' => 'begi',
                    'type' => 'string',
                ],
                [
                    'name' => 'cowries',
                    'type' => 'string',
                ],
                [
                    'name' => 'giraffing',
                    'type' => 'string',
                ],
                [
                    'name' => 'jara',
                    'type' => 'string',
                ],
                [
                    'name' => 'points',
                    'type' => 'string',
                ],
                [
                    'name' => 'player_id',
                    'type' => 'string',
                ],
                [
                    'name' => 'device_id',
                    'type' => 'string',
                ],
                [
                    'name' => 'level',
                    'type' => 'string',
                ],
                [
                    'name' => 'game_played',
                    'type' => 'string',
                ],
                [
                    'name' => 'game_won',
                    'type' => 'string',
                ],
                [
                    'name' => 'highest_score',
                    'type' => 'string',
                ],
                [
                    'name' => 'average_score',
                    'type' => 'string',
                ],
                [
                    'name' => 'totem',
                    'type' => 'string',
                ],
                [
                    'name' => 'longest_streak',
                    'type' => 'string',
                ],
                [
                    'name' => 'current_streak',
                    'type' => 'string',
                ],
                [
                    'name' => 'score',
                    'type' => 'string',
                ],
                [
                    'name' => 'padi_play_wins',
                    'type' => 'string',
                ],
                [
                    'name' => 'padi_play_losses',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "rewards" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'type',
                    'type' => 'string',
                ],
                [
                    'name' => 'category',
                    'type' => 'string',
                ],
                [
                    'name' => 'value',
                    'type' => 'string',
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "teams" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'team_name',
                    'type' => 'string',
                ],
                [
                    'name' => 'event_id',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "team_player_points" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'team_id',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'player_id',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'points',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "team_players" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'team_id',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'player_id',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'is_admin',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "point_donations" => [
            "fields" => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'points',
                    'type' => 'string',
                ],
                [
                    'name' => 'giver_player_id',
                    'type' => 'string',
                    'mode' => 'required'

                ],
                [
                    'name' => 'receiver_player_id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'transaction_id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "levels" => [
            "fields" =>[
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    "mode" => 'required'
                ],
                [
                    'name' => 'description',
                    'type' => 'string'
                ],
                [
                    'name' => 'level_number',
                    'type' => 'string',
                ],
                [
                    'name' => 'status',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "games" =>[
            "fields" =>[
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'player_1',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'player_2',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'completed',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'game_time',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'session_id',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'player_1_games',
                    'type' => 'string',
                ],
                [
                    'name' => 'winner',
                    'type' => 'string',
                ],
                [
                    'name' => 'game_mode', // padi_play, event, computer.
                    'type' => 'string',
                ],
                [
                    'name' => 'event_id', // if game mode is event.
                    'type' => 'string',
                ],
                [
                    'name' => 'player_2_games',
                    'type' => 'string',
                ],
                [
                    'name' => 'challenge_accepted',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'player_1_completed',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'player_2_completed',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
        "events" =>[
            "fields" =>[
                [
                    'name' => 'id',
                    'type' => 'string',
                    'mode' => 'required'
                ],
                [
                    'name' => 'title',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                ],
                [
                    'name' => 'status',
                    'type' => 'string', // pending | active | closed
                ],
                [
                    'name' => 'created_at',
                    'type' => 'string',
                    'mode' => 'required',
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'string',
                    'mode' => 'required',
                ]
            ]
        ],
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->database_name = env('BIG_QUERY_DATABASE');
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
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function runTableMigration()
    {
        $dataset = BigQuery::dataset($this->database_name);

        foreach ($this->schema as $key => $value) {
            try {
                $table = $dataset->createTable($key, ['schema' => $value]);
                systemLogger($key . ' Migration created on ' . $this->database_name);
            } catch (ConflictException $exception) {
                systemLogger($key . ' Migration exist ' . $this->database_name);
            } catch (BadRequestException $exception) {
                systemLogger("{$key} Migration failed {$this->database_name} with error: {$exception->getMessage()}");
            }
        }
    }
}
