<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
*
*/
class PostModel extends Model
{

  /**
   * The table associated with the model.
   */
  protected $table = 'post';

  /**
  * The primary key associated with the table.
  */
  protected $primaryKey = 'id_post';
  const CREATED_AT = 'date_created';
  const UPDATED_AT = 'date_modified';
}
