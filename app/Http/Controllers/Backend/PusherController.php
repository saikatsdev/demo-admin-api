<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class PusherController extends BaseController
{
    public function index()
    {
        return response()->json([
            'app_id'     => config('broadcasting.connections.pusher.app_id'),
            'app_key'    => config('broadcasting.connections.pusher.key'),
            'app_secret' => config('broadcasting.connections.pusher.secret'),
        ]);
    }

    public function update(Request $request)
    {
        $this->setEnv('PUSHER_APP_ID', $request->app_id);
        $this->setEnv('PUSHER_APP_KEY', $request->app_key);
        $this->setEnv('PUSHER_APP_SECRET', $request->app_secret);

        return $this->sendResponse([], "Pusher config updated", 200);
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents(
                $path,
                preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    file_get_contents($path)
                )
            );
        }
    }
}
