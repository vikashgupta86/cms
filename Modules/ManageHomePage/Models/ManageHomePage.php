<?php

namespace Modules\ManageHomePage\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageHomePage extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'managehomepages';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
  

    protected static function newFactory()
    {
        return \Modules\ManageHomePage\database\factories\ManageHomePageFactory::new();
    }
}
