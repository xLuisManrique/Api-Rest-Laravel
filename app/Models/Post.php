<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    
    // Relation of one to many but inverse( many to one )
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
            
}
