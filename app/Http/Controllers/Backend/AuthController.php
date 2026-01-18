<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use App\Http\Requests\Backend\LoginRequest;
use App\Http\Resources\Backend\UserResource;

class AuthController extends BaseController
{
    public function __construct(protected AuthRepository $repository){}

    function login(LoginRequest $request)
    {
        try {
            $data = $this->repository->adminLogin($request);

            $data = [
                "user"  => new UserResource($data["user"]),
                "token" => $data["token"],
            ];

            return $this->sendResponse($data, "Login successfully done");

        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError('Something went wrong');
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->repository->logout($request);

            return $this->sendResponse(null, 'User logout successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError('Something went wrong');
        }
    }
}
