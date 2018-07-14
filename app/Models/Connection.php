<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Phoenix\EloquentMeta\MetaTrait;

class Connection extends Model
{

    use MetaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'connections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

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
