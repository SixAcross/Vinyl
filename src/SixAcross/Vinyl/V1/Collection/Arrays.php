<?php
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\Collection;


use SixAcross\Vinyl\V1 as Vinyl;


class Arrays extends Vinyl\Collection
{
    public function validItem( $item ) : array
    {
        return $item;
    }
}
