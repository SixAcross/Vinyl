<?php
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\Collection;

use PDO;

use SixAcross\Vinyl\V1 as Vinyl;


class PDOs extends Vinyl\Collection
{
    public function validItem( $item ) : PDO
    {
        return $item;
    }
}
