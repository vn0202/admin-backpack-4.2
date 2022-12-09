<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;


class Post extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'posts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
 public function user()
 {
     return $this->belongsTo(User::class,'author');
 }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setThumbAttribute($value)
    {

        $attribute_name = "thumb";
        // or use your own disk, defined in config/filesystems.php
        $disk = 'public';
        // destination path relative to the disk above
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $destination_path = "uploads/thumbs/" . $year ."-".$month .'-'.$day;

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete('thumb');

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.


            if($this->thumb)
            {
                \Storage::disk($disk)->delete($this->thumb);

            }


            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
        }

    }
    protected function getThumbAttribute($value)
    {
        return $value;
    }
    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }
    public function scopeDraf($query)
    {
        return $query->where('active', '0');
    }
    protected static function booted()
    {
        static::deleting(function ($post) {
            PostTag::where('post_id', $post->id)->delete();
             if($post->thumb)
             {
                 Storage::disk('public')->delete($post->thumb);

             }

            // ...
        });

    }
    }
