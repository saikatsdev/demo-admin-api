<?php

namespace App\Http\Controllers\Frontend\CMS;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CMS\FAQRepository;
use App\Http\Resources\Frontend\CMS\FAQResource;
use App\Http\Resources\Frontend\CMS\FAQCollection;

class FAQController extends BaseController
{
    protected $repository;

    public function __construct(FAQRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $faqs = $this->repository->index($request);

            $faqs = new FAQCollection($faqs);

            return $this->sendResponse($faqs, "FAQ list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $faq = $this->repository->show($id);

            $faq = new FAQResource($faq);

            return $this->sendResponse($faq, "Faq single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
