<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use App\Http\Resources\Frontend\UserResource;
use App\Http\Requests\Frontend\RegisterRequest;
use App\Http\Requests\Frontend\ResendOtpRequest;
use App\Http\Requests\Frontend\RestPasswordRequest;
use App\Http\Requests\Frontend\UpdateProfileRequest;
use App\Http\Requests\Frontend\ChangePasswordRequest;
use App\Http\Requests\Frontend\VerificationOtpRequest;

class AuthController extends BaseController
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    function register(RegisterRequest $request)
    {
        try {
            $user = $this->repository->register($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'Register successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function verificationOtp(VerificationOtpRequest $request)
    {
        try {
            $user = $this->repository->verificationOtp($request);

            return $this->sendResponse($user, 'OTP verification successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function resetPasswordOtp(VerificationOtpRequest $request)
    {
        try {
            $user = $this->repository->resetPasswordOtp($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'OTP verification successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function resendOtp(ResendOtpRequest $request)
    {
        try {
            $user = $this->repository->resendOtp($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'OTP Re-send successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $this->repository->updateProfile($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User profile updated successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $this->repository->changePassword($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'Password change successfully done');
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function resetPassword(RestPasswordRequest $request)
    {
        try {
            $user = $this->repository->resetPassword($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'Password change successfully done');
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function login(Request $request)
    {
        try {
            $data = $this->repository->login($request);

            $data = [
                "token" => $data["token"],
                "user"  => new UserResource($data["user"])
            ];

            return $this->sendResponse($data, "Login successfully done");
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function userGet()
    {
        try {
            $user = $this->repository->userGet();

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User information');
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
