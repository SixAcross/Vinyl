<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\RecordProducer;

use OuterIterator;

use PDOStatement;

use SixAcross\Vinyl\V1 as Vinyl;


interface PDO extends Vinyl\RecordProducer, OuterIterator
{
    public function withStatement( PDOStatement $statement, string $id_field = 'id' ) : Vinyl\RecordProducer\PDO ;
    
    public function withRecord( Vinyl\Record $prototype ) : Vinyl\RecordProducer\PDO ;
    
}
