<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use Carbon\Carbon;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\CampaignRepository;
use App\Http\Resources\Frontend\Product\CampaignResource;
use App\Http\Resources\Frontend\Product\CampaignCollection;
use App\Http\Resources\Frontend\Product\CampaignProductResource;

class CampaignController extends BaseController
{
    public $repository;

    public function __construct(CampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    function index(Request $request)
    {
        $now = Carbon::now();

        try {
            $request->merge(["now" => $now, "status" => StatusEnum::ACTIVE]);

            $campaigns = $this->repository->index($request);

            $campaigns = new CampaignCollection($campaigns);

            return $this->sendResponse($campaigns, "Campaign products", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($slug)
    {
        try {
            $campaign = $this->repository->show($slug, StatusEnum::ACTIVE);

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse($campaign, "Campaign product single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError(__($exception->getMessage()));
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    // Get campaign product price
    function campaignProductDetail($campaignId, $productId)
    {
        try {
            $campaignProductDetails = $this->repository->campaignProductDetail($campaignId, $productId);

            $campaignProductDetails = new CampaignProductResource($campaignProductDetails);

            return $this->sendResponse($campaignProductDetails, 'Campaign Product Details', 200);
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    // Get campaign product price
    function campaignProductPrice(Request $request)
    {
        try {
            $campaignProductPrice = $this->repository->campaignProductPrice($request);

            return $this->sendResponse($campaignProductPrice, 'Variation product price', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
