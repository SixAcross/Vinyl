<?php 
declare (strict_types=1);

namespace SixAcross\Vinyl\V1\Tests\RecordStore\SQL;

use PDO as PDOConnection;

use Phinx\Db\Table;

use Atlas\Pdo\Connection;
use Atlas\Query\QueryFactory;


use SixAcross\Vinyl\V1 as Vinyl;
use SixAcross\Vinyl\V1\Exception\RecordNotFound;
use SixAcross\Vinyl\V1\Exception\TooManyRecords;

use SixAcross\Vinyl\V1\Tests\SQL\PDO\Schema;


abstract class PDO extends Vinyl\Tests\RecordStore\SQL
{
    use Vinyl\Tests\SQL\PDO\DataProviders;
    
    /**
     * @dataProvider getRecordStoreScenarios
     */    
    public function testProducesPDO( Vinyl\RecordStore $store )
    {
        $this->assertNotEmpty(
            $store->getPDO(),
            "The PDO RecordStore should produce a non-empty PDO instance. "
          );
    }
    
        /**
     * @dataProvider getRecordStoreScenarios
     */    
    public function testGetRecordByStatementThrowsRecordNotFound( Vinyl\RecordStore $store )
    {
        $exception = null;
        try {
            $store->getRecordByStatement( 
                $store->getPDO()->prepare( "SELECT * from {$store->getTable()} where {$store->getPrimaryKey()} = ? " ), 
                [ 'TEST_bogus_value' ] 
              );
        } catch ( RecordNotFound $exception ) {}
        
        $this->assertNotEmpty(
            $exception,
            "The PDO RecordStore should throw an exception when it can't find matching records. "
          );
        
    }
    
    /**
     * @dataProvider getRecordStoreScenarios
     */    
    public function testGetRecordByStatementThrowsTooManyRecords( Vinyl\RecordStore $store, $record_id )
    {
        $record = $store->getRecord( $record_id ) ;
        
        // strip field-value pairs containing ids, if any
        $insert_values = array_diff_key(
            $this->mutateValues($record),
            array_flip( $this->getIdFields( $store, $record_id ) )
          );
        $store->insertRecord($insert_values);
        $store->insertRecord($insert_values);
        
        $field = current( array_keys( $insert_values ) );
        $value = $insert_values[$field];
        
        $exception = null;
        try {
            $store->getRecordByStatement( 
                $store->getPDO()->prepare( "SELECT * from {$store->getTable()} where {$field} = ? " ), 
                [ $value ]
              );
        } catch ( TooManyRecords $exception ) {}
        
        $this->assertNotEmpty(
            $exception,
            "The PDO RecordStore should throw an exception when it finds more than one record for single-record call. "
          );
    }
    
    /**
     * @dataProvider getRecordStoreScenarios
     */
    public function testGetRecordByStatement( Vinyl\RecordStore $store, $record_id )
    {
        $record = $store->getRecord( $record_id );
        
        // strip field-value pairs containing ids, if any
        $insert_values = array_diff_key(
            $this->mutateValues($record),
            array_flip( $this->getIdFields( $store, $record_id ) )
          );
        $store->insertRecord($insert_values);
        
        $field = current( array_keys( $insert_values ) );
        $value = $insert_values[$field];
        
        $new_record = $store->getRecordByStatement( 
                $store->getPDO()->prepare( "SELECT * from {$store->getTable()} where {$field} = ? " ), 
                [ $value ]
              );
        
        $this->assertTrue(true);
              
    }
    
    
    /**
     * @dataProvider getRecordStoreScenarios
     */
    public function testGetRecordsByStatement( Vinyl\RecordStore $store, $record_id )
    {
        $record = $store->getRecord( $record_id );
        
        $id_fields = $this->getIdFields( $store, $record_id );
        
        // strip field-value pairs containing ids, if any
        $insert_values = array_diff_key(
            $this->mutateValues($record),
            array_flip( $id_fields )
          );
        $inserted_ids[] = $store->insertRecord($insert_values)->getRecordId();
        $inserted_ids[] = $store->insertRecord($insert_values)->getRecordId();
        
        foreach ( $insert_values as $field => $value ) {
            
            $producer = $store->getRecordsByStatement( 
                $store->getPDO()->prepare( "SELECT * from {$store->getTable()} where {$field} = ? " ), 
                [ $value ]
              );
            $records = [];
            foreach ( $producer as $key => $r ) { $records[$key] = $r; }
            
            $this->assertGreaterThan(
                1,
                count($records),
                "The PDO Recordstore should be able to get multiple records by a query string. "
              );
            
            $retrieved_ids = [];
            foreach ( $records as $record ) {
                $retrieved_ids[] = $record->getRecordId();
            }

            foreach ( $inserted_ids as $inserted_id ) {
                $this->assertContains(
                    $inserted_id,
                    $retrieved_ids,
                    "The PDO RecordStore should get all records inserted with a particular field value. "
                  );
            }
        }
    }
    
    /**
     * @dataProvider getRecordStoreScenarios
     */
    public function testInsertRecordHandlesNonIncremementingPrimaryKeys( 
        Vinyl\RecordStore $store, 
        $record_id 
      )
    {
        $prikey = $store->getPrimaryKey();
        
        // strip auto-increment from the primary key
        $table = new Table( 
            $store->getTable(), 
            [], 
            Schema::getAdapter( $store->getPDO() )
          );
        $table
            ->changeColumn( $prikey, 'integer', [ 'identity' => false, ] )
            ->save();
            
        $records = $store->getRecords()->asArray();
        $insert_values = $this->mutateValues( end($records) );
        $insert_values[$prikey] = max( array_map( 
            function($record) { return $record->getRecordId(); }, 
            $records 
          ) ) +1;
        
        $inserted_record = $store->insertRecord( $insert_values );
        
        $this->assertNotEmpty(
            $inserted_record->getAllValues()[$prikey],
            "The PDO RecordStore should properly handle insertion of records "
            ."even when the primary key is not auto-incremented. "
          );

    }
}
