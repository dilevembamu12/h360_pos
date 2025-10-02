<?php

/**
 * @package FormattedDate
 * @author TechVillage <support@techvill.org>
 * @contributor Millat <[millat.techvill@gmail.com]>
 * @created 06-09-2021
 */

namespace App\Http\Controllers\H360ai\AI\app\Traits;

use App\Http\Controllers\H360ai\AI\app\Traits\ModelTraits\{
    FormatDateTime,
    Cachable,
    EloquentHelper,
    Filterable
};


trait ModelTrait
{
    use FormatDateTime, Cachable, EloquentHelper, Filterable;
}
