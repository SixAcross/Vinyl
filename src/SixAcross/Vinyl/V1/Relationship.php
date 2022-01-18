<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1;


interface Relationship
{
    public function sourceClass() : string ;
    public function sourceField();
    public function destinationRecordStore() : RecordStore ;
    public function destinationField();
}
