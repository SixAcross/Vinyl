<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\Collection\Records\GetsRelatives;

use SixAcross\Vinyl\V1 as Vinyl;
use SixAcross\Vinyl\V1\Exception\RecordNotFound;
use SixAcross\Vinyl\V1\Exception\TooManyRecords;
use SixAcross\Vinyl\V1\Exception\RelationshipNotFound;


trait Implementation /* implements Vinyl\GetsRelatives */
{
    use Vinyl\Record\GetsRelatives\Implementation;
    
    
    abstract public function getRelator(): Vinyl\Relator;
    
    abstract protected function getRecordProducer() : Vinyl\Record;
    
    
    protected function getSourceRecord() : Vinyl\Record
    {
        return $this->getRecordProducer()->getMasterRecord();
    }
    
    public function getRelatives( string $relationship_name ) : Vinyl\RecordProducer 
    {
        $relationship = $this->getRelator()
            ->getRelationship( $this->getSourceRecord(), $relationship_name );

        $records = $relationship->destinationRecordStore()
            ->getRecordsByFieldValues(
                $relationship->destinationField(), 
                array_column( 
                    $this->getRecordProducer()->asArrays(), 
                    $relationship->sourceField() 
                  )
              );
              
        return $records;
    }
    

}
