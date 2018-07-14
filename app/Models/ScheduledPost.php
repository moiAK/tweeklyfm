<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scheduled';

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

    public function connection()
    {
        return $this->hasOne('App\Models\Connection', 'id', 'connection_id');
    }

    public function source()
    {
        return $this->hasOne('App\Models\Source', 'id', 'source_id');
    }
}
