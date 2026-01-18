<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Enums\DurationTypeEnum;
use App\Models\Product\Gallery;
use App\Models\Order\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Drivers\Gd\Driver;

class Helper
{
    public static function checkPaginateSize($request)
    {
        $paginateSize        = $request->paginate_size;
        $maxPaginateSize     = config('crud.paginate_size.max');
        $defaultPaginateSize = config('crud.paginate_size.default');
        $paginateSize        = $paginateSize ?? $defaultPaginateSize;
        $paginateSize        = $paginateSize > $maxPaginateSize ? $maxPaginateSize : $paginateSize;
        
        return $paginateSize;
    }

    public static function getRandomNumber($startNumber = 1111, $endNumber = 9999)
    {
        return rand($startNumber, $endNumber);
    }

    public static function generateRandomString($length = 16, $model, $column, $prefix = '')
    {
        do {
            $randomString = strtoupper($prefix . Str::random($length));
            $exists       = $model::where($column, $randomString)->exists() ?? false;
        } while ($exists);

        return $randomString;
    }

    public static function generateInvoiceNumber($modelClass, $column = 'invoice_number', $padLength = 4)
    {
        $prefix = self::setting('invoice_number');

        if (!$prefix) {
            throw new \Exception('Invoice prefix not found in settings');
        }

        $lastInvoice = $modelClass::where($column, 'like', $prefix . '%')
            ->orderByRaw("CAST(REPLACE($column, ?, '') AS UNSIGNED) DESC", [$prefix])
            ->lockForUpdate()
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) str_replace($prefix, '', $lastInvoice->$column);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
    }

    public static function uploadFile($file, $uploadPath, $height = 450, $width = 450, $key = '')
    {
        if ($file) {
            if (is_string($file)) {
                return $file;
            }

            if (!$height || !$width) {
                $height = 450;
                $width  = 450;
            }

            $fileNameWithExtension = $file->getClientOriginalName();
            $fileName              = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $fileName              = str_replace([' ', '(', ')'], '', $fileName);

            $fileName   = $fileName . $key . "_" . time() . '.webp';
            $filePath   = $uploadPath . "/" . $fileName;
            $uploadPath = $file->move(public_path($uploadPath), $fileName);

            $manager = new ImageManager(new Driver());

            $image = $manager->read($uploadPath);

            $image->resize((int) $width, (int) $height);

            $image->save($uploadPath);

            Gallery::firstOrCreate(
                ['img_path' => $filePath],
                ['img_path' => $filePath]
            );

            return $filePath;
        }
    }

    public static function uploadImage($file, $uploadPath, $height = 450, $width = 450, $oldFilePath = null, $key = '')
    {
        if ($file) {
            // Delete old file
            if ($oldFilePath) {
                $filePath = public_path($oldFilePath);

                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            $fileNameWithExtension = $file->getClientOriginalName();
            $fileName              = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $fileName              = str_replace([' ', '(', ')'], '', $fileName);

            $fileName   = $fileName . $key . "_" . time() . '.webp';
            $filePath   = $uploadPath . "/" . $fileName;
            $uploadPath = $file->move(public_path($uploadPath), $fileName);

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read($uploadPath);

            // resize by width and height
            $image->resize((int) $width, (int) $height);

            $image->save($uploadPath);

            Gallery::firstOrCreate(
                ['img_path' => $filePath],
                ['img_path' => $filePath]
            );

            return $filePath;
        }
    }

    public static function getFilePath($filePath)
    {
        if ($filePath) {
            if (File::exists(public_path($filePath))) {
                $imagePath =  asset($filePath);
            } else {
                $imagePath = asset('uploads/default.png');
            }
        } else {
            $imagePath = asset('uploads/default.png');
        }

        return $imagePath;
    }

    public static function deleteFile($filePath)
    {
        if ($filePath) {
            $filePath = public_path($filePath);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    public static function updateEnvVariable(array $data)
    {
        if (count($data)) {
            $envPath    = base_path('.env');
            $envContent = file_get_contents($envPath);

            foreach ($data as $key => $value) {
                $pattern = "/^{$key}=(.*)$/m";
                $replacement = "{$key}=\"{$value}\"";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envPath, $envContent);

            Artisan::call('optimize:clear');
        }

        return true;
    }

    public static function setting($key)
    {
        $setting = Setting::where("key", $key)->first();

        return $setting ? $setting->value : false;
    }

    public static function timeHumanFormat($time)
    {
        return $time ? Carbon::parse($time)->format('h:i:s A') : null;
    }

    public static function timeFormat($time)
    {
        return $time ? Carbon::parse($time)->format('H:i:s') : null;
    }

    public static function dateFormat($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }

    public static function dateTimeFormat($dateTime)
    {
        return $dateTime ? Carbon::parse($dateTime)->format('Y-m-d H:i:s') : null;
    }

    public static function startOfDate($date)
    {
        return $date ? Carbon::parse($date)->startOfDay() : null;
    }

    public static function endOfDate($date)
    {
        return $date ? Carbon::parse($date)->endOfDay() : null;
    }

    public static function diffForHuman($datetime)
    {
        return $datetime ? Carbon::parse($datetime)->diffForHumans() : null;
    }

    public static function getStartAndEndTime($duration, $durationType)
    {
        $endTime   = Carbon::now();

        if ($durationType === DurationTypeEnum::MINUTES) {
            $startTime = Carbon::now()->subMinutes($duration);
        } elseif ($durationType === DurationTypeEnum::HOURS) {
            $startTime = Carbon::now()->subHours($duration);
        } elseif ($durationType === DurationTypeEnum::DAYS) {
            $startTime = Carbon::now()->subDays($duration);
        } else {
            $startTime = Carbon::now();
        }

        return [$startTime, $endTime];
    }

    public static function createTransaction($orderId, $pgPaymentId = null, $pgTrxId = null, $pSendFromNumber)
    {
        $transaction = new Transaction();

        $transaction->order_id                 = $orderId;
        $transaction->payment_id               = $pgPaymentId;
        $transaction->payment_gateway_trx_id   = $pgTrxId;
        $transaction->payment_send_from_number = $pSendFromNumber;
        $transaction->save();

        return $transaction;
    }

    public static function isAdminOrTeamLead(): bool
    {
        $roleId = Auth::user()?->roles?->first()?->id;

        return $roleId && in_array($roleId, [1, 2]);
    }

    public static function isAdmin(): bool
    {
        $roleId = Auth::user()->roles->first()?->id;

        return $roleId && $roleId === 1;
    }

    public static function isTeamLead(): bool
    {
        $roleId = Auth::user()->roles->first()?->id;

        return $roleId && $roleId === 2;
    }

    public static function isModuleActive(string $module): bool
    {
        $path = base_path('modules_statuses.json');

        if (!File::exists($path)) {
            return false;
        }

        $modules = json_decode(File::get($path), true);

        return !empty($modules[$module]) && $modules[$module] === true;
    }

    public static function createRolePermission($rolesStructure, $moduleName, $command)
    {
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        foreach ($rolesStructure as $key => $modules) {
            // Create a new role
            $role = Role::firstOrCreate([
                'name'         => $key,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description'  => ucwords(str_replace('_', ' ', $key))
            ]);

            $command->info('Creating Role ' . strtoupper($key));

            $permissions = [];

            // Reading role permission modules
            foreach ($modules as $module => $value) {
                foreach (explode(',', $value) as $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permissions[] = Permission::firstOrCreate([
                        'module_name'  => $moduleName,
                        'group'        => $module,
                        'name'         => $module . '-' . $permissionValue,
                        'display_name' => ucfirst($module)  . ' ' . ucfirst($permissionValue),
                        'description'  => ucfirst($module) . ' ' . ucfirst($permissionValue),
                    ])->id;

                    $command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);
                }
            }

            // Add all permissions to the role
            $role->permissions()->syncWithoutDetaching($permissions);
        }
    }
    
    public static function generateRandomIp() {
        return mt_rand(1, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(1, 254);
    }
}
