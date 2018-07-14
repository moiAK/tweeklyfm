<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sources';

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
