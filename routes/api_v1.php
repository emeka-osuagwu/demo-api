<?php

use App\Http\Controllers\Admin\AdminLogin;
use App\Http\Controllers\Admin\AdminRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Transactions
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Register;

/*
|--------------------------------------------------------------------------
| Transactions
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Transactions\FetchByPlayerId;
use App\Http\Controllers\Transactions\FetchTransactions;
use App\Http\Controllers\Transactions\FetchTransactionByReference;

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\User\Profile;
use App\Http\Controllers\User\UpdateUser;
use App\Http\Controllers\User\UserPuzzles;
use App\Http\Controllers\User\GetUserByPlayerId;
use App\Http\Controllers\User\UpdateUserCompletedPuzzles;

/*
|--------------------------------------------------------------------------
| Puzzles
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Puzzles\Puzzles;
use App\Http\Controllers\Puzzles\DeletePuzzle;
use App\Http\Controllers\Puzzles\PuzzleCreate;
use App\Http\Controllers\Puzzles\BulkUploadPuzzles;

/*
|--------------------------------------------------------------------------
| Teams
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Teams\CreateTeam;
use App\Http\Controllers\Teams\GetAllTeams;
use App\Http\Controllers\Teams\GetTeamById;
use App\Http\Controllers\Teams\GetTeamByName;

/*
|--------------------------------------------------------------------------
| Team Players
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\TeamPlayers\InvitePlayer;
use App\Http\Controllers\TeamPlayers\AcceptPlayer;
use App\Http\Controllers\TeamPlayers\RemoveTeamPlayer;
use App\Http\Controllers\TeamPlayers\GetAllPlayersRequests;
use App\Http\Controllers\TeamPlayers\TeamAcceptPlayerRequest;
use App\Http\Controllers\TeamPlayers\PlayerRequestToJoinTeam;

/*
|--------------------------------------------------------------------------
| Team Player Points
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\TeamPlayerPoints\TeamPlayerPointCreate;
use App\Http\Controllers\TeamPlayerPoints\DonateTeamPlayerPoints;

/*
|--------------------------------------------------------------------------
| Events
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Events\AllEvent;
use App\Http\Controllers\Events\UpdateEvent;
use App\Http\Controllers\Events\CreateEvent;
use App\Http\Controllers\Events\GetEventById;
use App\Http\Controllers\Events\GetActiveEvents;
use App\Http\Controllers\Events\SoloLeaderBoard;
use App\Http\Controllers\Events\EventLeaderBoard;

/*
|--------------------------------------------------------------------------
| Levels
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Levels\FetchAllLevels;

/*
|--------------------------------------------------------------------------
| Games
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Games\Games;
use App\Http\Controllers\Games\GameStart;
use App\Http\Controllers\Games\GameUpdate;
use App\Http\Controllers\Games\AcceptInvite;

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Products\Payment;
use App\Http\Controllers\Products\Products;
use App\Http\Controllers\Products\FindSingleProduct;

/*
|--------------------------------------------------------------------------
| Webhooks
|--------------------------------------------------------------------------
*/
// use App\Http\Controllers\V1\Hooks\WebhookHandler;

Route::middleware(['throttle:api'])->group(function () {

    Route::group(['prefix' => '/v1'], function () {
        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/'], function () {
            Route::post('login', Login::class);
            Route::post('register', Register::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/'], function () {
            Route::post('admin.login', AdminLogin::class);
            Route::post('admin.register', AdminRegister::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/'], function () {
            Route::get('transactions', FetchTransactions::class);
            Route::get('transactions.fetchByPlayerId/{player_id}', FetchByPlayerId::class);
            Route::get('transactions.fetchTransactionByReference/{reference}', FetchTransactionByReference::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/'], function () {
            Route::get('levels', FetchAllLevels::class);
        });

        /*
        |--------------------------------------------------------------------------
        | Product API - cleared
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/', 'middleware' => ['app_auth']], function () {
            Route::get('products', Products::class);
            Route::post('product.payment', Payment::class);
            Route::post("product.getProductById", FindSingleProduct::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/', 'middleware' => ['app_auth']], function () {
            Route::get("user.puzzle", UserPuzzles::class);
            Route::get('user.profile', Profile::class);
            Route::post("user.update", UpdateUser::class);
            Route::post("user.updateCompletedPuzzles", UpdateUserCompletedPuzzles::class);
            Route::get("user.getUserByPlayerId/{player_id}", GetUserByPlayerId::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/'], function () {
            Route::get("puzzle", Puzzles::class);
            Route::post("puzzle.create", PuzzleCreate::class)->middleware('admin_auth');
            Route::post("puzzle.delete/{id}", DeletePuzzle::class)->middleware('admin_auth');
            Route::post("puzzle.bulkUpoload", BulkUploadPuzzles::class)->middleware('admin_auth');
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/', 'middleware' => ['app_auth']], function () {
            Route::get("teams", GetAllTeams::class);
            Route::post('team.create', CreateTeam::class);
            Route::post('team.acceptInvite', AcceptPlayer::class);
            Route::post('team.invitePlayer', InvitePlayer::class);
            Route::post('team.removePlayer', RemoveTeamPlayer::class);
            Route::get('team.getTeamById/{team_id}', GetTeamById::class);
            
            Route::post('team.joinRequest', PlayerRequestToJoinTeam::class);
            Route::get('team.playersRequests', GetAllPlayersRequests::class);
            Route::get('team.getTeamByName/{team_name}', GetTeamByName::class);
            Route::post('team.acceptPlayerRequest/{player_id}', TeamAcceptPlayerRequest::class);
        });

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/', 'middleware' => ['app_auth']], function () {
            Route::post('user.donate_point', DonateTeamPlayerPoints::class);
            Route::post('team.create_team_player_points', TeamPlayerPointCreate::class);
        });

        /*
        |--------------------------------------------------------------------------
        | EVENT API
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => '/', 'middleware' => ['app_auth']], function () {// Need fix -> make sure this has a admin only middleware
            Route::get('events', AllEvent::class);
            Route::post('event.create', CreateEvent::class);
            Route::post('event.update', UpdateEvent::class);
            Route::get('event.active_event', GetActiveEvents::class);
            Route::get('event.getById/{event_id}', GetEventById::class);
        });

        /*
        |--------------------------------------------------------------------------
        | games
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix'=> '/', 'middleware' => ['app_auth']], function () {
            Route::get('games', Games::class);
            Route::post("game.start", GameStart::class);
            Route::post('game.update', GameUpdate::class);
            Route::post('game.acceptInvite', AcceptInvite::class);
        });
        
        /*
        |--------------------------------------------------------------------------
        | Leaderboard
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix'=> '/'], function () {
            Route::get('leaderboard.solo', SoloLeaderBoard::class);
            Route::post('leaderboard.event', EventLeaderBoard::class);
        });
    });
});
