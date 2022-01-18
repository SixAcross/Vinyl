<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\RecordStore;

use SixAcross\Vinyl\V1\RecordStore;
use SixAcross\Vinyl\V1\Record;
use SixAcross\Vinyl\V1\Collection;


interface SQL extends RecordStore
{
    public function getTable();
    public function getPrimaryKey();
    
    public function getRecordByQueryString(  string $query, array $parameters ) : Record ;
    public function getRecordsByQueryString( string $query, array $parameters ) : Vinyl\RecordProducer ;
    
}
