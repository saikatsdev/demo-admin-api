<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function index($request)
    {
        $paginateSize   = Helper::checkPaginateSize($request);
        $searchKey      = $request->input('search_key', null);
        $userCategoryId = $request->input('user_category_id', null);

        $users = $this->model->with([
            "userCategory:id,name",
            "manager:id,username",
            "roles:id,name,display_name",
        ])
            ->where('phone_number', '!=', '01700000017')
            ->when(Helper::isTeamLead(), fn($query) => $query->where("manager_id", Auth::id()))
            ->when($userCategoryId, fn($query) => $query->where("user_category_id", $userCategoryId))
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("username", "like", "%$searchKey%")
                    ->orWhere("phone_number", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

        return $users;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $user = new $this->model();

            $user->username         = $request->username;
            $user->email            = $request->email;
            $user->phone_number     = $request->phone_number;
            $user->status           = $request->status;
            $user->is_verified      = true;
            $user->user_category_id = $request->user_category_id;
            $user->manager_id       = $request->manager_id;
            $user->salary           = $request->salary ?? 0;
            $user->password         = Hash::make($request->password);

            if ($user) {
                $user->img_path = Helper::uploadFile($request->image, $user->uploadPath, $request->height, $request->width);
            }

            $user->save();

            $user->syncRoles($request->role_ids ?? []);

            return $user;
        });
    }

    public function show($id)
    {
        $user = $this->model->with([
            "userCategory:id,name",
            "manager:id,username",
            "roles:id,name,display_name",
            "roles.permissions:id,name,display_name"
        ])->find($id);

        if (!$user) {
            throw new CustomException("User not found");
        }

        return $user;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = $this->model->find($id);

            if (!$user) {
                throw new CustomException('User not found');
            }

            $user->username         = $request->username;
            $user->email            = $request->email;
            $user->phone_number     = $request->phone_number;
            $user->status           = $request->status;
            $user->user_category_id = $request->user_category_id;
            $user->manager_id       = $request->manager_id;
            $user->salary           = $request->salary ?? 0;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            if ($request->image) {
                $user->img_path = Helper::uploadFile($request->image, $user->uploadPath, $request->height, $request->width, $user->img_path);
            }

            $user->save();

            $user->syncRoles($request->role_ids ?? []);

            return $user;
        });
    }

    public function getCustomerSummary()
    {
        $customers = Order::select('phone_number', DB::raw('MIN(created_at) as first_order_date'), DB::raw('COUNT(*) as total_orders'))->groupBy('phone_number')->get();

        $totalCustomers = $customers->count();

        $currentMonthCustomers = $customers->filter(function ($customer) {
            return Carbon::parse($customer->first_order_date)->isCurrentMonth();
        })->count();

        $activeCustomers = Order::where('current_status_id', 3)->select('phone_number')->distinct()->count();

        return [
            'total_customers'         => $totalCustomers,
            'current_month_customers' => $currentMonthCustomers,
            'active_customers'        => $activeCustomers,
            'customers'               => $customers,
        ];
    }
    
    public function getCustomers($request)
    {
        $customers = Order::select(
            'phone_number',
            DB::raw('MIN(customer_name) as customer_name'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(payable_price) as total_spent'),
            DB::raw('MAX(created_at) as last_order_date'),
            DB::raw('MAX(invoice_number) as last_invoice_number'),
            DB::raw('MAX(id) as last_order_id')
        )
        ->when($request->search, function ($q) use ($request) {
            $q->where('phone_number', 'like', '%'.$request->search.'%');
        })
        ->groupBy('phone_number')
        ->orderByDesc('last_order_date')
        ->paginate($request->per_page ?? 50);

        return $customers;
    }

    public function delete($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw new CustomException('User not found');
        }

        //  Delete old image
        if ($user->img_path) {
            Helper::deleteFile($user->img_path);
        }

        return $user->delete();
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        $users = $this->model->with('roles')
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("username", "like", "%$searchKey%")
                    ->orWhere("phone_number", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

        return $users;
    }

    public function restore($id)
    {
        $user = $this->model->onlyTrashed()->find($id);
        if (!$user) {
            throw new CustomException('User not found');
        }

        $user->restore();

        return $user;
    }

    public function permanentDelete($id)
    {
        $user = $this->model->onlyTrashed()->find($id);
        if (!$user) {
            throw new CustomException('User not found');
        }

        return $user->forceDelete();
    }
}
