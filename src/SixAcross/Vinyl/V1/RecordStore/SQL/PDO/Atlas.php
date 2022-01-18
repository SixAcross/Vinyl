<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\RecordStore\SQL\PDO;


use SixAcross\Vinyl\V1 as Vinyl;


class Atlas implements Vinyl\RecordStore
{
    use Atlas\Implementation;
}
