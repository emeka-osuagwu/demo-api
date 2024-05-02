<?php

namespace App\Http\Controllers\Products;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;


/*
|--------------------------------------------------------------------------
| Service Namespace
|--------------------------------------------------------------------------
*/
use App\Services\ProductService;
use App\Services\CacheService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\ProductResource;


class Products extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected CacheService $cacheService,
        protected ProductService $productService
    ){}


    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | find all products
        |--------------------------------------------------------------------------
        */
        // Need this, this should come from the cache
        $get_products_response = $this->productService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$get_products_response["is_successful"] && $get_products_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value){
            return $this->sendResponse([],ResponseCodeEnums::PRODUCT_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no product is found
        |--------------------------------------------------------------------------
        */
        if($get_products_response["is_successful"] && !count($get_products_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::PRODUCT_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | cache products
        |--------------------------------------------------------------------------
        */
        $save_product_response = $this->cacheService->saveRecord('products', $get_products_response["response"]);

        /*
        |--------------------------------------------------------------------------
        | check if products have been saved
        |--------------------------------------------------------------------------
        */
        if(!$save_product_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::PRODUCT_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(ProductResource::collection($get_products_response["response"]), ResponseCodeEnums::PRODUCT_REQUEST_SUCCESSFUL);

    }
}
