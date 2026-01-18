<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Models\Order\BlockUser;
use App\Exceptions\CustomException;
use Illuminate\Support\Str;

class BlockUserRepository
{
    public function __construct(protected BlockUser $model) {}

    public function index($request)
    {
        $paginateSize       = Helper::checkPaginateSize($request);
        $isBlock            = $request->input('is_block', null);
        $isPermanentBlock   = $request->input('is_permanent_block', null);
        $isPermanentUnblock = $request->input('is_permanent_unblock', null);

        $userBlocks = $this->model->with(["details"])
        ->when($isBlock, fn($query) => $query->where("is_block", $isBlock))
        ->when($isBlock === 0, fn($query) => $query->where("is_block", $isBlock))
        ->when($isPermanentBlock, fn($query) => $query->where("is_permanent_block", $isPermanentBlock))
        ->when($isPermanentBlock === 0, fn($query) => $query->where("is_permanent_block", $isPermanentBlock))
        ->when($isPermanentUnblock, fn($query) => $query->where("is_permanent_unblock", $isPermanentUnblock))
        ->when($isPermanentUnblock === 0, fn($query) => $query->where("is_permanent_unblock", $isPermanentUnblock))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $userBlocks;
    }

    public function show($id)
    {
        $blockUser = $this->model->with(["details"])->find($id);

        if (!$blockUser) {
            throw new CustomException("Block user not found");
        }

        return $blockUser;
    }

    public function store($request)
    {
        $blockUser = new $this->model;

        $blockUser->user_token           = Str::random(8);
        $blockUser->is_block             = $request->is_block;
        $blockUser->is_permanent_block   = $request->is_permanent_block;
        $blockUser->is_permanent_unblock = 0;

        $blockUser->save();

        $blockUser->details()->create([
            'block_user_id' => $blockUser->id,
            'phone_number'  => $request->phone_number,
            'ip_address'    => $request->ip_address ?? Helper::generateRandomIp(),
            'device_type'   => "Tablet",
        ]);

        return $blockUser;
    }

    public function update($request, $id)
    {
        $blockUser = $this->model->find($id);

        if (!$blockUser) {
            throw new CustomException("Block user not found");
        }

        $blockUser->is_block             = $request->is_block;
        $blockUser->is_permanent_block   = $request->is_permanent_block;
        $blockUser->is_permanent_unblock = $request->is_permanent_unblock;
        $blockUser->save();

        return $blockUser;
    }
    
    public function userBlock($request)
    {
        $blockUser = $this->model
        ->whereHas("details", fn($query) => $query->where("phone_number", $request->phone_number))
        ->first();

        if($blockUser){
            $blockUser->is_permanent_block = $request->is_permanent_block;
            $blockUser->is_permanent_unblock = 0;

            $blockUser->save();
        }

        return true;
    }
}
