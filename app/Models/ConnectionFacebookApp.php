<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Phoenix\EloquentMeta\MetaTrait;

class ConnectionFacebookApp extends Model
{

    use MetaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'connections_apps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'app_id', 'app_secret'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['app_id', 'app_secret'];

    public static function findByUserId($id)
    {
        return static::where("user_id", "=", $id)->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function network()
    {
        return $this->belongsTo('App\Models\Network', 'network_id', 'id');
    }
}
