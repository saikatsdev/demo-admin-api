<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Events\SMSNotification;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Backend\UserResource;

class AuthRepository
{
    public function __construct(protected User $model) {}

    public function register($request)
    {
        $user = new $this->model();

        $verificationOtp = Helper::getRandomNumber();

        $user->username         = $request->username;
        $user->email            = $request->email;
        $user->phone_number     = $request->phone_number;
        $user->status           = StatusEnum::ACTIVE;
        $user->is_verified      = true;
        $user->verification_otp = $verificationOtp;
        $user->user_category_id = 1;
        $user->password         = Hash::make($request->password);
        $user->save();

        try {
            SMSNotification::dispatch($request->phone_number, "Your phone number validation code is $verificationOtp");
        } catch (\Exception $exception) {
            info("Your sms gateway not configure " . $exception->getMessage());
        }

        return $user;
    }

    public function verificationOtp($request)
    {
        $user = User::where("phone_number", $request->phone_number)->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        if ($user->verification_otp == $request->verification_otp) {
            $user->is_verified = true;
            $user->save();

            $token = $user->createToken("auth_token")->plainTextToken;

            $data = [
                "user"  => new UserResource($user),
                "token" => $token,
            ];

            return $data;
        } else {
            throw new CustomException("Your provided OTP is invalid");
        }
    }

    public function resetPasswordOtp($request)
    {
        $user = User::where("phone_number", $request->phone_number)->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        if ($user->verification_otp == $request->verification_otp) {
            $user->is_verified = true;
            $user->save();

            return $user;
        }

        throw new CustomException("Your provided OTP is invalid");
    }

    function resendOtp($request)
    {
        $user = User::where("phone_number", $request->phone_number)->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        $verificationOtp = Helper::getRandomNumber();

        $user->verification_otp = $verificationOtp;
        $user->save();

        try {
            SMSNotification::dispatch($request->phone_number, "Your phone number validation code is $verificationOtp");
        } catch (\Exception $exception) {
            info("Your sms gateway not configure " . $exception->getMessage());
        }


        return $user;
    }

    public function updateProfile($request)
    {
        $user = Auth::user();

        $user->username       = $request->username;
        $user->email          = $request->email;
        $user->home_address   = $request->home_address;
        $user->office_address = $request->office_address;
        $user->dob            = $request->dob;

        // Upload image one
        if ($request->image) {
            $user->img_path = Helper::uploadFile($request->image, $user->uploadPath, $request->height, $request->width, $user->img_path);
        }

        $user->save();

        return $user;
    }

    public function login($request)
    {
        $user = $this->model->with(["roles", "roles.permissions"])
            ->where("phone_number", $request->phone_number)
            ->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        if (!$user->is_verified) {
            throw new CustomException("You are not verified");
        }

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken("auth_token")->plainTextToken;

            $data = [
                "user"  => $user,
                "token" => $token
            ];

            return $data;
        }

        throw new CustomException("User credential dosen't match");
    }

    public function adminLogin($request)
    {
        $user = $this->model->with(["roles", "roles.permissions"])
            ->where("phone_number", $request->phone_number)
            ->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        if (!$user->is_verified) {
            throw new CustomException("You are not verified");
        }

        // Check staff
        if (Helper::setting("otp_required_when_staff_login") && !in_array($user->roles?->first()?->id, [1, 2])) {
            if (!$user->staff_login_otp) {
                $user->staff_login_otp = Helper::getRandomNumber();
                $user->save();
            }

            if (!$request->staff_login_otp) {
                throw new CustomException("Please provided your login otp");
            }

            if ($request->staff_login_otp != $user->staff_login_otp) {
                throw new CustomException("Your provided otp is invalid");
            }
        }

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken("auth_token")->plainTextToken;
            $user->login_at        = now();
            $user->logout_at       = null;
            $user->staff_login_otp = null;
            $user->save();

            $data = [
                "user"  => $user,
                "token" => $token
            ];

            return $data;
        }

        throw new CustomException("User credential dosen't match");
    }

    public function userGet()
    {
        return Auth::user();
    }

    public function changePassword($request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw new CustomException("Current password does not match.");
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $user;
    }

    public function resetPassword($request)
    {
        $user = User::where("phone_number", $request->phone_number)->first();

        if (!$user) {
            throw new CustomException("User not found");
        }

        if ($user->verification_otp != $request->verification_otp) {
            throw new CustomException("OTP does not match");
        }

        // Check user is active
        $this->checkUserIsActive($user);

        $user->password = Hash::make($request->password);
        $user->save();

        return $user;
    }

    public function logout($request)
    {
        $user = $request->user();

        $user->login_at  = null;
        $user->logout_at = now();
        $user->save();

        return $user->tokens()->delete();
    }

    private function checkUserIsActive($user)
    {
        if ($user->status === StatusEnum::INACTIVE) {
            $contactNumber = Helper::setting("phone_number");
            throw new CustomException("You are currently inactive please contact with $contactNumber");
        }
    }
}
