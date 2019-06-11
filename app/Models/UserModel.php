<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class UserModel extends Model
{
  /**
   * The table associated with the model.
   */
  protected $table = 'user';

  /**
  * The primary key associated with the table.
  */
  protected $primaryKey = 'id_user';
  /**
  * Indicates if the model should be timestamped.
  */
  public $timestamps = false;
}
