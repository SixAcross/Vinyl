<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\RecordProducer\PDO;

use Countable;
use IteratorIterator;
use PDO;
use PDOStatement;


use SixAcross\Vinyl\V1 as Vinyl;


class Statement extends IteratorIterator implements Vinyl\RecordProducer\PDO, Countable
{
    protected /*PDOStatement*/ $statement;
    protected /*Vinyl\Record*/ $record;
    protected /*string*/ $id_field;
    
    
    public function __construct( Vinyl\Record $prototype )
    {
        $this->record     = $prototype;
    }
    
    protected function setStatement( PDOStatement $statement )
    {
        $this->statement = $statement;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        parent::__construct( $statement );
    }
    
    public function withStatement( PDOStatement $statement, string $id_field = 'id' ) : Vinyl\RecordProducer\PDO
    {
        $producer = new self( $this->getMasterRecord() );
        $producer->setStatement($statement);
        $producer->id_field = $id_field;
        return $producer;
    }
    
    public function withRecord( Vinyl\Record $prototype ) : Vinyl\RecordProducer\PDO
    {
        $producer = new self( $prototype );
        if ( ! empty( $this->statement ) ) { $producer->setStatement( $this->statement ); }
        return $producer;
    }
    
    
    public function current() : Vinyl\Record 
    {
        $row = parent::current();
        $record = $this->getMasterRecord()->withValues( $row[$this->id_field], $row );
        return $record;
    }
    
    public function count() 
    {
        #TODO: throw if statement has not executed?
        return $this->statement->rowCount();
    }
    
    public function asArray() : array
    {
        return iterator_to_array($this);
    }
    
    public function asArrays() : array
    {
        return array_map( 
            function( Vinyl\Record $record ) { return $record->getAllValues(); }, 
            $this->asArray()
          );
    }

    public function getMasterRecord() : Vinyl\Record
    {
        if ( $this->record instanceof Vinyl\Record\Contract ) {
            $this->record = $this->record->getRecord();
        }
        
        return $this->record;
    }
}
