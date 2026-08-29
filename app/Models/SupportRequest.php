<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportRequest extends Model { protected $fillable=['user_id','target','message','status','resolved_by','resolved_at']; protected $casts=['resolved_at'=>'datetime']; public function user(){return $this->belongsTo(User::class);} public function resolver(){return $this->belongsTo(User::class,'resolved_by');} }
