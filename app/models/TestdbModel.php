<?php

/**
 *You can  use eloquent along with the inbuilt tableDataObject trait
 *Create something  awesome.
 **/

use Illuminate\Database\Eloquent\Model;

class TestdbModel extends Model
{
    protected $table = "sases";
    protected $guarded = [];
    public $timestamps = false;

    //protected $primaryKey = 'testdbid';
    //protected $timestamp = null;

}
