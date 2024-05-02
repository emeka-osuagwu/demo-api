<?php

namespace App\Http\Controllers\Products;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\ProductService;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\ProductResource;

class FindSingleProduct extends Controller
{
    use ResponseTrait;
    public function __construct(protected ProductService $productService)
    {
    }

    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | check if user exists
        |--------------------------------------------------------------------------
        */
        $find_product_response = $this->productService->findWhere(["id" => $request->product_id]);

        /*
        |--------------------------------------------------------------------------
        | if user request fails
        |--------------------------------------------------------------------------
        */
        if ($find_product_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$find_product_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | if user not found
        |--------------------------------------------------------------------------
        */
        if ($find_product_response["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && $find_product_response["is_successful"] && !count($find_product_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        return $this->sendResponse(ProductResource::make($find_product_response["response"]), ResponseCodeEnums::PRODUCT_REQUEST_SUCCESSFUL);
    }
}
