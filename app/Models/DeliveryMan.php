<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RandomLib\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Subscription\Traits\HasCreditWallet;
use Modules\Subscription\Traits\HasSubscription;

class DeliveryMan extends Authenticatable
{
    use Notifiable, HasFactory, HasSubscription, HasCreditWallet;

    protected $casts = [
        'zone_id' => 'integer',
        'status' => 'boolean',
        'active' => 'integer',
        'available' => 'integer',
        'earning' => 'float',
        'store_id' => 'integer',
        'current_orders' => 'integer',
        'delivery' => 'integer',
        'ride_sharing' => 'integer',
        'ride_zone_id' => 'integer',
        'ride_category_id' => 'integer',
        'subscription_period_start' => 'datetime:Y-m-d H:i:s',
        'subscription_period_end' => 'datetime:Y-m-d H:i:s',
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    protected $hidden = [
        'password',
        'auth_token',
    ];

    protected $attributes = [
        'status' => true,
        'active' => 1,
        'earning' => 1,
        'current_orders' => 0,
        'vehicle_id' => 0,
        'delivery' => 0,
        'ride_sharing' => 0,
    ];

    protected $appends = ['image_full_url', 'identity_image_full_url'];

    protected static function booted()
    {
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });
        static::addGlobalScope(new ZoneScope);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->generateCode();
        });
        static::saved(function ($model) {
            if ($model->isDirty('image')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'image',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return $this->f_name . ' ' . $this->l_name;
    }

    public function total_canceled_orders()
    {
        return $this->hasMany(Order::class)->where('order_status', 'canceled');
    }
    public function total_ongoing_orders()
    {
        return $this->hasMany(Order::class)->whereIn('order_status', ['handover', 'picked_up']);
    }

    public function userinfo()
    {
        return $this->hasOne(UserInfo::class, 'deliveryman_id', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(DMVehicle::class);
    }

    public function wallet()
    {
        return $this->hasOne(DeliveryManWallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function order_transaction()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function todays_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereDate('created_at', now());
    }

    public function this_week_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function this_month_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
    }

    public function todaysorders()
    {
        return $this->hasMany(Order::class)->whereDate('accepted', now());
    }

    public function total_delivered_orders()
    {
        return $this->hasMany(Order::class)->where('order_status', 'delivered');
    }

    public function this_week_orders()
    {
        return $this->hasMany(Order::class)->whereBetween('accepted', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function delivery_history()
    {
        return $this->hasMany(DeliveryHistory::class, 'delivery_man_id');
    }

    public function last_location()
    {
        return $this->hasOne(DeliveryHistory::class, 'delivery_man_id')->latestOfMany();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class);
    }

    // NOTE: We cant remove this cuz of migration.
    public function ride_zone()
    {
        return $this->belongsTo(RideZone::class);
    }

    public function ride_zones(): BelongsToMany
    {
        return $this->belongsToMany(RideZone::class);
    }

    public function ride_requsts()
    {
        return $this->hasMany(RideRequest::class);
    }

    public function used_prepaid_cards()
    {
        return $this->morphMany(PrepaidCard::class, 'usable');
    }

    public function on_going_rides()
    {
        return $this->hasMany(RideRequest::class)->whereIn('ride_status', ['accepted', 'ignored', 'picked_up', 'arrived', 'reached']);
    }

    public function usePrepaidCard(PrepaidCard $prepaidCard)
    {
        if (
            $this->subscription_period_start == null
            || ($this->subscription_period_end != null && now()->gt($this->subscription_period_end))
        ) { // new subscription.
            $this->subscription_period_start = now();
            $this->subscription_period_end = now()->addDays($prepaidCard->duration_days);
        } else { // existing subscription
            $this->subscription_period_end = $this->subscription_period_end->addDays($prepaidCard->duration_days);
        }
    }

    public function getSubscriptionDaysAttribute()
    {
        if ($this->subscription_period_start != null && $this->subscription_period_end != null) {
            $now = now();
            if ($this->subscription_period_start->lte($now) && $this->subscription_period_end->gte($now)) {
                return $this->subscription_period_end->diffInDays($now);
            }
        }
        return 0;
    }

    public function reviews()
    {
        return $this->hasMany(DMReview::class);
    }

    public function disbursement_method()
    {
        return $this->hasOne(DisbursementWithdrawalMethod::class)->where('is_default', 1);
    }

    public function rating()
    {
        return $this->hasMany(DMReview::class)
            ->select(DB::raw('avg(rating) average, count(delivery_man_id) rating_count, delivery_man_id'))
            ->groupBy('delivery_man_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('application_status', 'approved');
    }
    public function scopeInActive($query)
    {
        return $query->where('active', 0)->where('application_status', 'approved');
    }

    public function scopeEarning($query)
    {
        return $query->where('earning', 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('current_orders', '<', config('dm_maximum_orders') ?? 1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('current_orders', '>', config('dm_maximum_orders') ?? 1);
    }

    public function scopeZonewise($query)
    {
        return $query->where('type', 'zone_wise');
    }

    public function scopeAvailableRider($query, $ride_request_id, $zone_id, $ride_category_id)
    {
        return $query->where(function ($query) use ($ride_request_id, $zone_id, $ride_category_id) {
            $query->whereDoesntHave('ignore_logs', function ($query) use ($ride_request_id) {
                $query->where('ride_request_id', $ride_request_id);
            })->whereDoesntHave('ongoing_logs', function ($query) {
                $query->where('updated_at', '>=', now()->subMinutes(1));
            })->whereDoesntHave('ongoing_logs', function ($query) {
                $query->where('updated_at', '>=', now()->subMinutes(1));
            })->whereDoesntHave('on_going_rides')
                ->whereHas('zones', function ($q) use ($zone_id) {
                    $q->where('zones.id', $zone_id);
                })->where('ride_category_id', $ride_category_id);;
        });
    }

    public function getImageFullUrlAttribute()
    {
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('delivery-man', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('delivery-man', $value, 'public');
    }
    public function getIdentityImageFullUrlAttribute()
    {
        $images = [];
        $value = is_array($this->identity_image)
            ? $this->identity_image
            : ($this->identity_image && is_string($this->identity_image) && $this->isValidJson($this->identity_image)
                ? json_decode($this->identity_image, true)
                : []);
        if ($value) {
            foreach ($value as $item) {
                $item = is_array($item) ? $item : (is_object($item) && get_class($item) == 'stdClass' ? json_decode(json_encode($item), true) : ['img' => $item, 'storage' => 'public']);
                $images[] = Helpers::get_full_url('delivery-man', $item['img'], $item['storage']);
            }
        }

        return $images;
    }

    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    public function generateCode()
    {
        $randomFactory = new Factory;
        $mediumGenerator = $randomFactory->getMediumStrengthGenerator();
        $characters = '0123456789';

        $code = $mediumGenerator->generateString(6, $characters);
        while (self::where('code', $code)->exists()) {
            $code = $mediumGenerator->generateString(6, $characters);
        }
        $this->code = $code;
    }
}
