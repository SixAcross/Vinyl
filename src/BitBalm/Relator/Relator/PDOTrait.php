<?php

namespace BitBalm\Relator\Relator;

use Exception;
use InvalidArgumentException;
use PDO;
use PDOStatement;

use BitBalm\Relator\Relator;
use BitBalm\Relator\Relationship;
use BitBalm\Relator\RecordSet;
use BitBalm\Relator\GetsRelatedRecords;
use BitBalm\Relator\Record;
use BitBalm\Relator\Mappable;
use BitBalm\Relator\Relatable;
use BitBalm\Relator\Mapper\PDO\SchemaValidator;


trait PDOTrait 
{
    abstract function getPdo() : PDO ;
    abstract function getValidator() : SchemaValidator ;
    
    public function getRelated( GetsRelatedRecords $related_from, Relationship $relationship  ) : RecordSet
    {
        list ( $query_string, $parameters ) = $this->getRelatedQuery( $related_from, $relationship );
        return $this->getRelatedFromQuery( $query_string, $parameters, $relationship->getToTable() );
    }
    
    public function getRelatedFromQuery( string $query_string, array $query_parameters, Relatable $to_table ) : RecordSet
    {
        return $this->getRelatedFromStatement(
            $this->getStatementFromQuery( $query_string, $query_parameters ),
            $to_table
          );
    }
    
    public function getStatementFromQuery( string $query_string, array $query_parameters ) : PDOStatement
    {
        $statement = $this->getPdo()->prepare( $query_string );
        
        foreach ( $query_parameters as $index => $value ) {
            $statement->bindValue( $index+1, $value );
        }

        $statement->setFetchMode(PDO::FETCH_ASSOC);
        
        return $statement;
    }

    public function getRelatedFromStatement( PDOStatement $statement, Relatable $to_table ) : RecordSet
    {
        $statement->execute();
        $results = $statement->fetchAll();

        foreach ( $results as $index => $result ) {
            if ( ! $result instanceof Mappable ) {
                $results[$index] = $to_table->newRecord()->setValues($result);
            }
        }
        
        $to_recordset = $to_table->asRecordSet();
        
        $resultset = new $to_recordset( $results );

        return $resultset;
    }
    
    /* returns array of: 1) query string and 2) array of query parameters */
    public function getRelatedQuery( GetsRelatedRecords $related_from, Relationship $relationship ) : array
    {
        $to_table  = $relationship->getToTable();
        $to_table_name = $this->getValidator()->validTable($to_table->getTableName());
        $to_column = $this->getValidator()->validColumn( $to_table_name, $relationship->getToColumn() );
        $query_string = "SELECT * from {$to_table_name} where {$to_column} in ( ? ) ";
        
        $values = array_column( $related_from->asRecordSet()->asArrays(), $relationship->getFromColumn() );
        
        // Replace the placeholder with as many placeholders as we have values
        $query_string = str_replace( 
            '?', 
            implode( ', ', array_pad( [], count( $values ), '?' ) ), 
            $query_string 
          );
        
        return [ $query_string, $values ];
    }
    
}
