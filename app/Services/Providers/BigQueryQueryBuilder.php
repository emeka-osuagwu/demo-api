<?php

namespace App\Services\Providers;

use App\Exceptions\AppException;
use App\Enums\ServiceResponseCode;

trait BigQueryQueryBuilder
{
    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    function generateBigQueryTableName(string $table): string
    {
        $project_id = env('GOOGLE_CLOUD_PROJECT_ID');
        $database_name = env('BIG_QUERY_DATABASE');
        $table_name = '';

        switch ($table) {
            case 'teams':
                $table_name = env('BIG_QUERY_DATABASE_TEAMS_TABLE');
                break;

            case 'users':
                $table_name = env('BIG_QUERY_DATABASE_USERS_TABLE');
                break;

            case 'puzzles':
                $table_name = env('BIG_QUERY_DATABASE_PUZZLES_TABLE');
                break;

            case 'rewards':
                $table_name = env('BIG_QUERY_DATABASE_REWARDS_TABLE');
                break;

            case 'products':
                $table_name = env('BIG_QUERY_DATABASE_PRODUCTS_TABLE');
                break;

            case 'transactions':
                $table_name = env('BIG_QUERY_DATABASE_TRANSACTIONS_TABLE');
                break;
            case 'levels':
                $table_name = env('BIG_QUERY_DATABASE_LEVELS_TABLE');
                break;
            case 'games':
                $table_name = env('BIG_QUERY_DATABASE_GAMES_TABLE');
                break;
            case 'events':
                $table_name = env('BIG_QUERY_DATABASE_EVENTS_TABLE');
                break;
            case 'team_players':
                $table_name = env('BIG_QUERY_DATABASE_TEAM_PLAYERS_TABLE');
                break;
            case 'team_player_points':
                $table_name = env('BIG_QUERY_DATABASE_TEAM_PLAYER_POINTS_TABLE');
                break;
            case 'point_donations':
                $table_name = env('BIG_QUERY_DATABASE_POINT_DONATIONS_TABLE');
                break;
            default:
                # code...
                break;
        }

        return "{$project_id}.{$database_name}.{$table_name}";
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    function generateInsertSql(array $columns, array $rows, string $table)
    {
        if (count($columns) < 1) {
            $log_info = extraErrorInfo(
                'BigQuery generateInsertSql',
                'Atleast one column is required',
                __FILE__,
                __LINE__,
            );

            throw new AppException($log_info);
        }

        if (count($rows) < 1) {

            $log_info = extraErrorInfo(
                'BigQuery generateInsertSql',
                'Atleast one column is required',
                __FILE__,
                __LINE__,
            );

            throw new AppException($log_info);
        }

        $values = [];

        foreach ($rows as $key => $row) {
            $row = array_values($row);

            if (($row_count = count($row)) !== ($col_count = count($columns))) {
                throw new QueryException("Expected ($col_count) values for row index [$key] but got ($row_count).");
            }

            array_walk($row, function (&$value) {
                // we add a quote to non integer values for
                // a valid sql statement.

                if (empty($value)) {
                    $value = 'null';
                }

                if (gettype($value) === "integer") {
                    $value = $value;
                }

                if (gettype($value) === "boolean") {
                    $value = (bool)$value;
                }

                if (gettype($value) === "string") {
                    $value = (string)"'$value'";
                }
            });

            // get the values for values of the row array
            // the output below may look like this:
            // "(1, 'Victor', 'another value')"
            $values[] = '(' . implode(', ', array_values($row)) . ')';
        }

        // when we have multiple values we should expect
        // a string output that may look like this:
        // "(1, 'Victor', 'column value'), (2, 'Emeka', 'column value')"
        $values = implode(', ', $values);

        $colums = '(' . implode(', ', $columns) . ')';

        return "INSERT INTO {$table} $colums VALUES {$values}";
    }

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    |
    */
    function generateFindSql(string $field, int | string $value, string $table)
    {
        return "SELECT * FROM {$table} WHERE {$field}={$value}";
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    function generateUpdateSql(string $raw_conditions, array $fields, string $table)
    {
        if (count($fields) < 1) {
            $log_info = extraErrorInfo('BigQuery generateUpdateSql', 'Atleast one field is required');
            throw new BigQueryException($log_info);
        }

        $updates = [];

        foreach ($fields as $column => $value) {

            if (gettype($value) === "string") {
                $fields[$column] = "'$value'";
            }

            if (gettype($value) === "boolean") {
                $fields[$column] = (boolval($value) ? 'true' : 'false');
            }

            $updates[] = "$column = $fields[$column]";
        }

        $raw_updates = implode(', ', $updates);

        return "UPDATE $table SET $raw_updates WHERE $raw_conditions";
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    function generateDeleteSql(string $raw_conditions, string $table)
    {
        return "DELETE FROM $table WHERE $raw_conditions";
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    function findInTableWhere(string $table_name, array $selector): string
    {
        if (empty($table_name)) {
            throw new AppException(
                'BigQueryQueryBuilder@findInTableWhere',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        foreach ($selector as $key => $value) {

            $isDate = (bool) strtotime($value);

            if (gettype($value) === "string") {
                !$isDate ? $selector[$key] = "'$value'" : '';
            }

            if (gettype($value) === "boolean") {
                $selector[$key] = (boolval($value) ? 'true' : 'false');
            }

            if ($isDate) {
                $date = \Carbon\Carbon::parse($selector[$key])->toDateString();
                $values[] = "DATE($key) = '$date'";
            }

            !$isDate ? $values[] =  "$key = $selector[$key]" : '';
        }

        $selector = implode(' AND ', $values);

        $table = $this->generateBigQueryTableName($table_name);
        return "SELECT * FROM {$table} WHERE $selector";
    }

    /*
	|--------------------------------------------------------------------------
	| Insert New Record In Table
	|--------------------------------------------------------------------------
	*/
    public function insertNewRecordInTable(string $table_name, array $payload): string
    {
        if (empty($table_name)) {
            throw new AppException(
                'BigQueryQueryBuilder@insertNewRecordInTable',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        $columns = array_keys($payload);
        return $this->generateInsertSql($columns, [$payload], $this->generateBigQueryTableName($table_name));
    }

    /*
	|--------------------------------------------------------------------------
	| find all in table
	|--------------------------------------------------------------------------
	*/
    public function findAllInTable(string $table_name): string
    {
        if (empty($table_name)) {
            throw new AppException(
                'BigQueryQueryBuilder@findAllInTable',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        $table = $this->generateBigQueryTableName($table_name);
        return "SELECT * FROM {$table}";
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function updateTableRecordWhereIn(string $table_name, string $raw_conditions, array $payload): string
    {
        if (empty($table_name)) {
            throw new AppException(
                'BigQueryQueryBuilder@updateTableRecordWhereIn',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        $table = $this->generateBigQueryTableName($table_name);
        return $this->generateUpdateSql($raw_conditions, $payload, $table);
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function deleteTableRecordWhereIn(string $table_name, string $raw_conditions): string
    {
        if (empty($table_name)) {
            throw new AppException(
                'BigQueryQueryBuilder@deleteTableRecordWhereIn',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        $table = $this->generateBigQueryTableName($table_name);
        return $this->generateDeleteSql($raw_conditions, $table);
    }

	/*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
	function findInTableWhereOr(string $table_name, array $selector): string
	{
        if(empty($table_name)){
            throw new AppException(
                'BigQueryQueryBuilder@findInTableWhere',
                'invalid query table name',
                ServiceResponseCode::BIGQUERY_QUERY_BUILDER_ERROR
            );
        }

        foreach ($selector as $key => $value) {

            $isDate = false;

            if (gettype($value) === "string") {
                !$isDate ? $selector[$key] = "'$value'" : '';
            }

            if (gettype($value) === "boolean") {
                $selector[$key] = (boolval($value) ? 'true' : 'false');
            }

            !$isDate ? $values[] =  "$key = $selector[$key]" : '';
        }

        $selector = implode(' OR ', $values);

        $table = $this->generateBigQueryTableName($table_name);
        return "SELECT * FROM {$table} WHERE $selector";
	}
}
