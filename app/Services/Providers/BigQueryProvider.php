<?php

namespace App\Services\Providers;

use App\Enums\ServiceResponseCode;
use App\Exceptions\AppException;
use App\Exceptions\BvnProviderException;
use App\Contracts\BigQueryProviderContract;
use SchulzeFelix\BigQuery\Exceptions\InvalidConfiguration;

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
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Core\Exception\BadRequestException;

class BigQueryProvider implements BigQueryProviderContract
{
    /*
	|--------------------------------------------------------------------------
	| Variable Namespace
	|--------------------------------------------------------------------------
	*/
    protected \Google\Cloud\BigQuery\QueryResults|null $queryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function __construct()
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
        $result = [];

        foreach ($this->queryBuilder as $row) {
            array_push($result, $row);
        }

        return collect($result);
    }

    public function escapeBackSlashFromUrl(string $string): string
    {
        $string = str_replace(['\\', '/'], '', $string);
        $string = str_replace("/$string'([^']*?)'/", '', $string);
        return $string;
    }

    /*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function query(string $statement): object
    {
        $prepare_query = BigQuery::query(escapeBackSlashFromUrl($statement));

        try {
            $query = BigQuery::runQuery($prepare_query);
            $this->queryBuilder = $query;
            return $this;
        } catch (BadRequestException $exception) {
            throw new AppException(
                'BigQueryProvider@query',
                $exception->getMessage(),
                ServiceResponseCode::BIGQUERY_BAD_REQUEST_ERROR
            );
        } catch (ServiceException $exception) {
            throw new AppException(
                'BigQueryProvider@query',
                $exception->getMessage(),
                ServiceResponseCode::BIGQUERY_SERVICE_ERROR
            );
        } catch (NotFoundException $exception) {
            throw new AppException(
                'BigQueryProvider@query',
                'record not found'
            );
        }
    }
}
