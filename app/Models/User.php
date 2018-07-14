<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;
use Phoenix\EloquentMeta\MetaTrait;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, BillableContract
{

    use Authenticatable, CanResetPassword, Billable, MetaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'username', 'timezone' ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array
     */
    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    /**
     * @param $username
     * @return mixed
     */
    public static function findByUsername($username)
    {
        return static::where("username", "=", $username)->where("status", "=", "active")->firstOrFail();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updates()
    {
        return $this->hasMany('App\Models\Update');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function connections()
    {
        return $this->hasMany('App\Models\Connection');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function connection_facebook_app()
    {
        return $this->hasOne('App\Models\ConnectionFacebookApp');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scheduled()
    {
        return $this->hasMany('App\Models\ScheduledPost');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sources()
    {
        return $this->hasMany('App\Models\Source');
    }

    /**
     * @return bool
     */
    public function isPremium()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canSchedulePost()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getPlan()
    {
        return $this->subscription_plan;
    }
}
