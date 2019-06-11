<?php
  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class CategoryModel extends Model
  {
    /**
     * The table associated with the model.
     */
    protected $table = 'category';

    /**
    * The primary key associated with the table.
    */
    protected $primaryKey = 'id_category';
    /**
    * Indicates if the model should be timestamped.
    */
    public $timestamps = false;
  }
