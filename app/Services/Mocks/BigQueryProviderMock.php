<?php

namespace App\Services\Mocks;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exceptions\AppException;
use App\Contracts\BigQueryProviderContract;

use App\Enums\Mocks;

/*
|--------------------------------------------------------------------------
| Package Namespace
|--------------------------------------------------------------------------
*/
use SchulzeFelix\BigQuery\BigQueryFacade as BigQuery;

/*
|--------------------------------------------------------------------------
| Exceptions Namespace
|--------------------------------------------------------------------------
*/
use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Core\Exception\BadRequestException;

/*
|--------------------------------------------------------------------------
| Mock Handles Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Mocks\Handles\handleUserQuery;
use App\Services\Mocks\Handles\handleTeamsQuery;
use App\Services\Mocks\Handles\handlePuzzlesQuery;
use App\Services\Mocks\Handles\handleTeamPlayersQuery;
use App\Services\Mocks\Handles\handleTransactionsQuery;
use App\Services\Mocks\Handles\handleTeamPlayerPointsQuery;

class BigQueryProviderMock implements BigQueryProviderContract
{
    /*
	|--------------------------------------------------------------------------
	| Variable Namespace
	|--------------------------------------------------------------------------
	*/
    protected $queryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function __construct(
        protected HandleUserQuery $handleUserQuery,
        protected HandleTeamsQuery $handleTeamsQuery,
        protected HandlePuzzlesQuery $handlePuzzlesQuery,
        protected HandleTeamPlayersQuery $handleTeamPlayersQuery,
        protected HandleTransactionsQuery $handleTransactionsQuery,
        protected HandleTeamPlayerPointsQuery $handleTeamPlayerPointsQuery,
    )
    {
        $this->queryBuilder = null;
    }

    /*
	|--------------------------------------------------------------------------
	| toJson
	|--------------------------------------------------------------------------
	| This functions converts the bigquery result to json
	*/
    public function toJson(): object | array
    {
        return collect($this->queryBuilder);
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function query(string $statement): object
    {
        $statement = escapeBackSlashFromUrl($statement);

		/*
		|--------------------------------------------------------------------------
		| Users Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'users')){
			return $this->handleUserQuery->mocks($statement);
		}


		/*
		|--------------------------------------------------------------------------
		| Puzzles Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'puzzles')){
			return $this->handlePuzzlesQuery->mocks($statement);
		}

        /*
		|--------------------------------------------------------------------------
		| Teams Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'teams')){
			return $this->handleTeamsQuery->mocks($statement);
		}

        /*
		|--------------------------------------------------------------------------
		| Team Players Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'team_players')){
			return $this->handleTeamPlayersQuery->mocks($statement);
		}

        /*
		|--------------------------------------------------------------------------
		| Team Player Points Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'team_player_points')){
			return $this->handleTeamPlayerPointsQuery->mocks($statement);
		}

        /*
		|--------------------------------------------------------------------------
		| Team Player Points Mock
		|--------------------------------------------------------------------------
		*/
		if(Str::contains($statement, 'transactions')){
			return $this->handleTransactionsQuery->mocks($statement);
		}

        throw new AppException('Bigquery', 'error from bigquery mock');
    }
}
