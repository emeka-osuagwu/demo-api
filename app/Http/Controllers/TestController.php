<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\Games\DeleteRedisGamesJob;
use App\Contracts\PushNotificationContract;
use App\Jobs\Games\SaveRedisGamesJob;
use App\Jobs\Games\CalculateGameResultJob;


use Kreait\Firebase\Factory;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\CardContract;
use App\Services\Providers\FirestoreProvider;

// use App\Services\ConfigService;
// use App\Contracts\CoreBankingContract;

class TestController extends Controller
{
    public function __construct
    (
        public CardContract $cardContract,
        protected SaveRedisGamesJob $saveRedisGamesJob,
        protected FirestoreProvider $firestoreProvider,
        public PushNotificationContract $pushNotification,
        // protected ConfigService $configService,
        // protected CoreBankingContract $coreBankingService

    ){}

    function index(Request $request) {
        return $this->firestoreFindWhereTest();
        return $this->firestoreDeleteTest();
        // return $this->firestoreUpdateTest();
        // return $this->firestoreReadTest();
        return $this->firestoreCreateTest();
        // return $this->notification();
        // return $this->deleteRedisGames();
        return $this->saveRedisGamesJobs();
        // return $this->calculateGameResult($request);
    }

    public function card(){
        return $this->cardContract->getProviderName();
    }
    public function notification(){
        // $auth_user_id = request()->auth_user["payload"]['id'];
        // $token = $this->cacheService->findWhere("push_tokens:{$auth_user_id}");
        $Quadri_push_token = 'f4kzGhIfTpCP3vAwulqiD1:APA91bGiQ3pKv_on_x02VVOiRG7ZnxmWFAcghJqGSwKF4rhLli0YiP52ztAlzkKMLzzQLNV9rbnms1DavFwF7-8Rnv2YWQ0aevet52iT4dES2CYk9QwsaILo5zEZq1-fJFEnuo8KuNYh';

        try {
            $notification_remark = "Schedule is working time -> " . now()->format('Y-m-d H:i:s');
            $this->pushNotification
            ->setType('account_upgrade_notification')
            ->setBody($notification_remark)
            ->setIcon('stock_ticker_update')
            ->setTokens([$Quadri_push_token])
            ->setTitle('System Check')
            ->setPayload([])
            ->sendNotification();
        } catch (\Exception $exception) {
            report($exception);
        }
        // return $this->pushNotification->setTokens($Quadri_push_token)->sendNotification();
        // ->setPayload()
        // ->setTokens()
        // ->setType()
        // ->setBody()
        // ->setIcon()
        // ->setSound()
        // ->setTitle()
        // ->setBadge()
        // ->setChannelId()
    }

    public function deleteRedisGames(){
        DeleteRedisGamesJob::dispatch();
    }
  
    public function firestoreCreateTest(){
        return $this->firestoreProvider->create("test", ["test" => "test"]);
    }
  
    public function firestoreUpdateTest(){
        return $this->firestoreProvider->updateRecord("test", "f658bd4e9e0b47bebe15", ["test" => "Hey"]);
    }
  
    public function firestoreReadTest(){
        return $this->firestoreProvider->readRecord("test", "f658bd4e9e0b47bebe15");
    }
    
    public function firestoreDeleteTest(){
        return $this->firestoreProvider->delete("test", "39e053a1e2414b3d988a");
    }
  
    public function firestoreFindWhereTest(){
        return $this->firestoreProvider->findWhere("test", "test", "Hey");
    }


    public function saveRedisGamesJobs(){

        $factory = (new Factory)->withServiceAccount(getFirebaseCreds());

        return $messaging = $factory->createFirestore();

        return $otherDatabase = $factory
            ->withFirestoreDatabase('config')
            ->createFirestore()
            ->database();

        $thirdDatabase = $factory
            ->withFirestoreDatabase('third-database')
            ->createFirestore()
            ->database();


        SaveRedisGamesJob::dispatch();
    }

    public function calculateGameResult(Request $request){
        $session_id = "184ce2d3-3f9d-4d0d-80b4-71e83d2ce06c";
        $push_token = ["a", "b"];

        CalculateGameResultJob::dispatch(session_id: $session_id, token: $push_token);
    }
}
