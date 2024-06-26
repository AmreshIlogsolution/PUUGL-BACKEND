<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\Sluggable\HasSlug;
//use Spatie\Sluggable\SlugOptions;
class Survey extends Model
{
    use HasFactory;

    protected $fillable =['user_id','title','slug','status','description','expire_date'];

    // public function getSlugOptions()
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsForm('title')
    //         ->saveSlugsTo('slug');
    // }
}
