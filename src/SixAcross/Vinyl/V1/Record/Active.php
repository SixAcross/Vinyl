<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\Record;


use SixAcross\Vinyl\Record;


interface Active extends Record
{
    public function saveRecord();
    public function removeRecord();
}
