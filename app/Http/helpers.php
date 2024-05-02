<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

// require_once __DIR__.'/Helpers/MathHelpers.php';
// require_once __DIR__.'/Helpers/LoanHelpers.php';
// require_once __DIR__.'/Helpers/DateHelpers.php';
require_once __DIR__ . '/Helpers/LoggerHelpers.php';
require_once __DIR__ . '/Helpers/AuthHelpers.php';

function generatePlayerId(): string
{
    $id = Str::random(10);

    return $id;
}

function generateUUID($size = null)
{
    if ($size) {
        return Str::limit(Str::uuid(), $size, '');
    }
    return (string) Str::uuid();
}

function convertKoboToNaira(int $data = 0): int
{
    return $data / 100;
}

function generateToken(): string
{
    /*
    |--------------------------------------------------------------------------
    |  Generate a random 6-digit number between 100000 and 999999
    |--------------------------------------------------------------------------
    */
    $random_string = mt_rand(100000, 999999);

    /*
    |--------------------------------------------------------------------------
    |  Convert the number to a string and pad it with leading zeros if necessary
    |--------------------------------------------------------------------------
    */
    $token = str_pad((string) $random_string, 6, '0', STR_PAD_LEFT);
    return $token;
}

function escapeBackSlashFromUrl(string $string)
{
    return str_ireplace('\/', '-', $string);
}

function generateLoanPaymentFrequency(int $tenure): int
{
    return ($tenure > 30) ? ($tenure / 30) : 2;
}

function generatePaymentInstallment(int $tenure): int
{
    return ($tenure <= 30) ? 1 : 2;
}

function generateInterestRate($interest, $duration)
{
    return ($duration > 30) ? ($duration / 30) : 2;
}

function removeFirstCharacter(string $string): string
{
    $cleanedString = substr($string, 1);
    return $cleanedString;
}

function camelCaseToSnakeCase(string $string): string
{
    // Use a regular expression to match camelCase and replace with snake_case
    $snakeCaseString = preg_replace_callback('/([a-z])([A-Z])/', function ($matches) {
        return $matches[1] . '_' . strtolower($matches[2]);
    }, $string);

    return strtolower($snakeCaseString);
}

function measurementDate($date, string $measurement)
{
    switch ($measurement) {
        case 'days':
            return Carbon::parse($date)->diffInDays(now());
            break;

        case 'years':
            return Carbon::parse($date)->diffInYears(Carbon::now());
            break;

        case 'months':
            return Carbon::parse($date)->diffInMonths(Carbon::now());
            break;

        case 'minutes':
            return Carbon::parse($date)->diffInRealMinutes(Carbon::now());
            break;

        case 'hours':
            return Carbon::parse($date)->diffInHours(Carbon::now());
            break;

        case 'seconds':
            return Carbon::parse($date)->diffInSeconds(Carbon::now());
            break;

        default:
            # code...
            break;
    }
}

function getLoanProductType(string $product_name)
{

    $type = "";

    if ($product_name == "Nano Loan") {
        $type = "nano_loan_product_code";
    }

    if ($product_name == "Pay Day Loan") {
        $type = "payday_loan_product_code";
    }

    if ($product_name == "Civic Connect") {
        $type = "civic_loan_product_code";
    }

    return $type;
}

function calculateInterest(int $amount, int $repayment_amount)
{
    return ($repayment_amount - $amount) / $amount * 100;
}

/*
|--------------------------------------------------------------------------
| add comment
|--------------------------------------------------------------------------
*/
function generateOtp(): string
{
    if (env('APP_ENV') == "production") {
        return (string) implode('', array_map(fn() => mt_rand(0, 9), range(1, 5)));
    }

    return 111 . now()->format('i');
}

/*
|--------------------------------------------------------------------------
| add comment
|--------------------------------------------------------------------------
*/
function getFirebaseCreds()
{
    return base_path() . '/' . env('GOOGLE_CLOUD_APPLICATION_CREDENTIALS');
}

/*
|--------------------------------------------------------------------------
| add comment
|--------------------------------------------------------------------------
*/
function removePrefix($string, $prefix)
{
    if (strpos($string, $prefix) === 0) {
        return substr($string, strlen($prefix));
    }
    return $string;
}

/*
|--------------------------------------------------------------------------
| filter out null values
|--------------------------------------------------------------------------
*/
function filterNullValues(array $payload): array
{
    return array_filter($payload, fn($value) => $value !== null);
}

/*
|--------------------------------------------------------------------------
| sort leaderboard
|--------------------------------------------------------------------------
*/
function sortLeaderBoardByScore(array $leaderboard): array
{
    usort($leaderboard, function ($a, $b) {
        $score_comparison = (int) $b['score'] - (int) $a['score'];

        if ($score_comparison == 0) {
            return (((int) $b['highest_score']) ?? 0) - (((int) $a['highest_score']) ?? 0);
        }

        return $score_comparison;
    });

    return $leaderboard;
}

/*
|--------------------------------------------------------------------------
| add comments
|--------------------------------------------------------------------------
*/
function shuffleArray($array) {
    $currentIndex = count($array);
    while ($currentIndex !== 0) {
        $randomIndex = rand(0, $currentIndex - 1);
        $currentIndex--;
        // Swap elements
        $temp = $array[$currentIndex];
        $array[$currentIndex] = $array[$randomIndex];
        $array[$randomIndex] = $temp;
    }
    return $array;
}

/*
|--------------------------------------------------------------------------
| add comments
|--------------------------------------------------------------------------
*/
function combineArrays($easy, $hard) {
    return shuffleArray([
        ...$hard,
        ...$easy
    ]);
}

/*
|--------------------------------------------------------------------------
| add comments
|--------------------------------------------------------------------------
*/
function convert_values_to_string(array $payload, array $keys) {
    foreach ($keys as $key) {
        if (isset($payload[$key])) {
            $payload[$key] = (string) $payload[$key];
        }
    }

    return $payload;
}