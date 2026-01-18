<?php

namespace App\Repositories\CMS;

use App\Helpers\Helper;
use App\Models\CMS\Contact;
use App\Exceptions\CustomException;

class ContactRepository
{
    public function __construct(protected Contact $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $contacts = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->paginate($paginateSize);

        return $contacts;
    }

    public function store($request)
    {
        $contact = new $this->model();

        $contact->name        = $request->name;
        $contact->phone       = $request->phone;
        $contact->email       = $request->email;
        $contact->description = $request->description;
        $contact->save();

        return $contact;
    }

    public function show($id)
    {
        $contact = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);
        if (!$contact) {
            throw new CustomException("Contact not found");
        }

        return $contact;
    }

    public function update($request, $id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            throw new CustomException("Contact Not found");
        }

        $contact->name        = $request->name;
        $contact->phone       = $request->phone;
        $contact->email       = $request->email;
        $contact->description = $request->description;
        $contact->save();

        return $contact;
    }

    public function delete($id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            throw new CustomException('Contact not found');
        }

        return $contact->forceDelete();
    }
}
