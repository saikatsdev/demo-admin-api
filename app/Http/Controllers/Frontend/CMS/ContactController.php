<?php

namespace App\Http\Controllers\Frontend\CMS;

use Exception;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Repositories\CMS\ContactRepository;
use App\Http\Requests\Frontend\ContactRequest;
use App\Http\Resources\Frontend\CMS\ContactResource;

class ContactController extends BaseController
{
    public function __construct(protected ContactRepository $repository) {}

    public function index(Request $request)
    {
        $contacts = $this->repository->index($request);

        return $this->sendResponse($contacts, "Contact List", 200);
    }

    public function store(ContactRequest $request)
    {
        try {
            $contact = $this->repository->store($request);

            $contact = new ContactResource($contact);

            return $this->sendResponse($contact, 'Contact created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
